<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signature;
use App\Models\SignatureVerification;
use App\Models\Letter;
use App\Models\LetterConfiguration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Constants\Roles;

class SignatureController extends Controller
{
    // show signature pad for signing a document
    public function pad($signable, $id)
    {
        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            abort(404, 'Document not found.');
        }

        $user = auth()->user();

        // check if user exists in signatures table
        $signature = $model->signatures()->where('user_id', $user->id)->first();

        // vip logic: if not registered, check if creator or admin
        if (!$signature) {
            $isOwner = $model->user_id == $user->id;
            $isAdmin =
                $user->employee &&
                in_array($user->employee->role->title, ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]);

            if ($isOwner || $isAdmin) {
                // automatically register them to the signatures table
                $signature = $model->signatures()->create([
                    'user_id' => $user->id,
                    'signature_image' => 'PENDING',
                    'signature_hash' => \Illuminate\Support\Str::random(64),
                    'token' => \Illuminate\Support\Str::uuid()->toString(),
                ]);
            } else {
                abort(403, 'You are not registered as a signer for this document.');
            }
        }

        // pull the last signature used by this user
        $lastUsedSignature = \App\Models\Signature::where('user_id', $user->id)
            ->whereNotNull('signature_image')
            ->where('signature_image', '!=', 'PENDING')
            ->latest('signed_date')
            ->first();

        return view('signatures.pad', compact('model', 'signature', 'signable', 'id', 'lastUsedSignature'));
    }

    // store a new signature
    public function store(Request $request, $signable, $id)
    {
        $request->validate([
            'signature_image' => 'required|string',
            'signature_reason' => 'nullable|string|max:500',
        ]);

        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->route('letters.index')->with('error', 'Document not found.');
        }

        // find their signature registration data
        $existingSignature = $model->signatures()->where('user_id', Auth::id())->first();

        if (!$existingSignature) {
            return redirect()
                ->route('letters.show', $id)
                ->with('error', 'You are not registered as a signer for this document.');
        }

        if ($existingSignature->signature_image !== 'PENDING' && $existingSignature->signature_image !== null) {
            return redirect()->route('letters.show', $id)->with('error', 'You have already signed this document.');
        }

        // generate initial signature hash
        $signatureHash = Signature::generateSignatureHash($request->signature_image, Auth::id(), $model->id);

        // update the existing pending signature data instead of creating a new one
        $existingSignature->update([
            'signature_image' => $request->signature_image,
            'signature_hash' => $signatureHash,
            'signature_reason' => $request->signature_reason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'signed_date' => now(),
        ]);

        // integrate openssl digital signature
        $existingSignature->refresh();
        $dataToSign = $model->id . '|' . Auth::id() . '|' . $existingSignature->signed_date->toDateTimeString();

        $existingSignature->signWithOpenSSL($dataToSign);

        // explicit redirect to the letter page
        if ($signable === 'letter') {
            return redirect()->route('letters.show', $id)->with('success', 'Document signed digitally successfully.');
        }

        return redirect()->back()->with('success', 'Document signed successfully with OpenSSL digital signature.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $signature = Signature::findOrFail($id);

        $userRole = $user->employee->role->title ?? null;
        if (!Roles::isAdmin($userRole)) {
            abort(403, 'Only HR Administrators or Master Admins can delete signatures.');
        }

        $signableType = $signature->signable_type;
        $signableId = $signature->signable_id;

        $signature->delete();

        // redirect back to signature list
        if ($signableType === Letter::class) {
            return redirect()
                ->route('signatures.list', ['signable' => 'letter', 'id' => $signableId])
                ->with('success', 'Signature successfully removed from the document.');
        }

        return redirect()->back()->with('success', 'Signature successfully removed from the document.');
    }

    // list signatures for a document
    public function list($signable, $id)
    {
        // block access: only admins can manage signatures
        $user = Auth::user();
        $userRole = $user->employee->role->title ?? null;
        if (!Roles::isAdmin($userRole)) {
            abort(403, 'Access Denied! Only HR Administrators or Master Admins can manage signatures.');
        }

        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        $signatures = $model->signatures()->with('signer', 'verifications')->get();

        // pull employee data for internal modal
        $users = \App\Models\User::whereHas('employee')->with('employee.position')->get();

        return view('signatures.list', compact('model', 'signatures', 'signable', 'id', 'users'));
    }

    public function mySignatures()
    {
        $user = auth()->user();

        // pull all data (signed and unsigned)
        $mySignatures = Signature::with('signable')->where('user_id', $user->id)->latest()->get();

        return view('signatures.my-signatures', compact('mySignatures'));
    }

    // view verification logs
    public function logs()
    {
        $user = Auth::user();

        $query = Signature::with(['signer.employee.role', 'signable', 'verifications']);

        // hr administrator/master admin see all signatures
        if ($user->employee && !Roles::isAdmin($user->employee->role->title)) {
            $query->where('user_id', $user->id);
        }

        $signatures = $query->latest()->get();

        return view('signatures.logs', compact('signatures'));
    }

    // verify a signature (hr administrator/master admin only)
    public function verify(Request $request, Signature $signature)
    {
        // authorization - hr administrator/master admin only
        $user = Auth::user();
        $userRole = $user->employee->role->title ?? null;
        if (!Roles::isAdmin($userRole)) {
            abort(403, 'Only HR Administrators or Master Admins can verify signatures.');
        }

        $request->validate([
            'status' => 'required|in:verified,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        // create verification record
        SignatureVerification::create([
            'signature_id' => $signature->id,
            'verified_by_id' => Auth::id(),
            'status' => $request->status,
            'remarks' => $request->remarks,
            'verification_date' => now(),
        ]);

        // update signature status
        $signature->update([
            'is_verified' => $request->status === 'verified',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Signature ' . $request->status . ' successfully.');
    }

    // download signed document as pdf
    public function download(Signature $signature)
    {
        // authorization check
        $user = Auth::user();
        if (
            $signature->user_id !== $user->id &&
            ($user->employee &&
                !\App\Constants\Roles::isAdmin($user->employee->role->title) &&
                $user->employee->role->title !== 'Manager / Unit Head')
        ) {
            abort(403, 'Unauthorized action.');
        }

        // eager load relationships for pdf rendering
        $signature->load('signer', 'verifications.verifier');

        // get the signable model
        $model = $signature->signable;

        if ($model instanceof Letter) {
            // fetch all signatures for this document to show in pdf
            $allSignatures = $model->signatures()->with('signer.employee.position')->get();

            // build verification url for qr code (for the specific signature being downloaded)
            $verificationUrl = route('signatures.public-verify', [
                'id' => $signature->id,
                'token' => $signature->verification_token,
            ]);

            // generate pdf with signatures
            $config = LetterConfiguration::first();
            $html = view('signatures.signed-letter-pdf', [
                'letter' => $model,
                'signatures' => $allSignatures,
                'signature' => $signature, // keep original for primary qr/context
                'verificationUrl' => $verificationUrl,
                'config' => $config,
            ])->render();

            // load pdf options to disable image processing if gd is not available
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', public_path());

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);

            $filename = 'Signed_Letter_' . str_replace('/', '_', $model->letter_number ?? 'Draft') . '.pdf';
            return $pdf->download($filename);
        }

        return redirect()->back()->with('error', 'Cannot download this document type.');
    }

    // validate/verify signature authenticity
    public function validate(Signature $signature)
    {
        if ($signature->isValid()) {
            return response()->json([
                'valid' => true,
                'message' => 'Signature is authentic and has not been tampered with.',
            ]);
        }

        return response()->json(
            [
                'valid' => false,
                'message' => 'Signature validation failed. Document may have been tampered with.',
            ],
            422,
        );
    }

    // public verification page (for qr code scans)
    public function publicVerify(Request $request)
    {
        $signatureId = $request->query('id');

        if (!$signatureId) {
            return view('signatures.public-verify', [
                'isValid' => false,
                'document' => null,
                'signatures' => [],
            ]);
        }

        $signature = Signature::with('signable')->find($signatureId);

        if (!$signature || !$signature->signable) {
            return view('signatures.public-verify', [
                'isValid' => false,
                'document' => null,
                'signatures' => [],
            ]);
        }

        $document = $signature->signable;

        $allSignatures = $document->signatures()->with('signer')->get();

        return view('signatures.public-verify', [
            'isValid' => true,
            'document' => $document,
            'signatures' => $allSignatures,
        ]);
    }

    // find signable model based on type and id
    private function findSignableModel($signable, $id)
    {
        switch ($signable) {
            case 'letter':
                return Letter::find($id);
            default:
                return null;
        }
    }

    public function publicPad($token)
    {
        $signature = Signature::where('token', $token)->firstOrFail();

        if ($signature->signature_image !== null && $signature->signature_image !== 'PENDING') {
            return view('signatures.public-success', [
                'title' => 'Link Expired',
                'message' => 'You have already signed this document. Thank you!',
            ]);
        }

        // check if user passed otp in this session
        if (!session('signature_otp_verified_' . $token)) {
            return view('signatures.public-otp', compact('signature'));
        }

        return view('signatures.public-pad', compact('signature'));
    }

    // process external signature submission
    public function publicSubmit(Request $request, $token)
    {
        $signature = Signature::where('token', $token)->firstOrFail();

        if ($signature->signature_image !== null && $signature->signature_image !== 'PENDING') {
            abort(403, 'Access Denied. This document has already been signed.');
        }

        $request->validate([
            'signature_image' => 'required|string',
        ]);

        $signature->update([
            'signature_image' => $request->signature_image,
            'signed_date' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_verified' => true,
        ]);

        // refresh data directly from database to ensure precision
        $signature->refresh();

        // use model method to ensure matching encrypted text structure
        $signature->signWithOpenSSL($signature->getSignableData());

        return redirect()->route('signatures.public.success')->with('success', 'Signature saved successfully.');
    }

    public function generatePublicLink(Request $request, $signable, $id)
    {
        // block access
        $user = Auth::user();
        $userRole = $user->employee->role->title ?? null;
        if (!Roles::isAdmin($userRole)) {
            abort(403, 'Access Denied! Only HR Administrators or Master Admins can create public links.');
        }

        // validate external data
        $request->validate([
            'external_name' => 'required|string|max:255',
            'external_email' => 'required|email|max:255',
            'external_title' => 'nullable|string|max:255',
            'external_company' => 'required|string|max:255',
        ]);

        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->route('letters.index')->with('error', 'Document not found.');
        }

        $token = Str::random(64);

        // generate 6-digit otp code
        $otp = (string) mt_rand(100000, 999999);

        $signature = Signature::create([
            'user_id' => null,
            'signable_type' => get_class($model),
            'signable_id' => $model->id,
            'token' => $token,
            'otp_code' => $otp, // save otp to database
            'verification_token' => Str::random(64),
            'external_name' => $request->external_name,
            'external_email' => $request->external_email,
            'external_title' => $request->external_title,
            'external_company' => $request->external_company,
            'signature_image' => 'PENDING',
            'signature_hash' => 'PENDING',
            'is_verified' => false,
        ]);

        $publicUrl = route('signatures.public.pad', ['token' => $token]);

        return redirect()
            ->route('signatures.list', ['signable' => $signable, 'id' => $id])
            ->with('success', 'Public Signature Link & OTP successfully created!')
            ->with('generated_link', $publicUrl)
            ->with('generated_otp', $otp); // pass otp to view
    }

    // success page (after signing)
    public function publicSuccess()
    {
        return view('signatures.public-success');
    }

    public function storeInternal(Request $request, $signable, $id)
    {
        // block access
        $user = Auth::user();
        $userRole = $user->employee->role->title ?? null;
        if (!Roles::isAdmin($userRole)) {
            abort(403, 'Access Denied! Only HR Administrators or Master Admins can add signers.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $model = $this->findSignableModel($signable, $id);
        if (!$model) {
            return redirect()->route('letters.index')->with('error', 'Document not found.');
        }

        // check if user was already added to prevent duplicates
        $exists = Signature::where('signable_type', get_class($model))
            ->where('signable_id', $model->id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('signatures.list', ['signable' => $signable, 'id' => $id])
                ->with('error', 'This employee is already on the signer list.');
        }

        // save pending internal signature to database
        Signature::create([
            'signable_type' => get_class($model),
            'signable_id' => $model->id,
            'user_id' => $request->user_id,
            'signature_image' => 'PENDING',
            'signature_hash' => \Illuminate\Support\Str::random(64),
            'token' => Str::random(32),
            'verification_token' => Str::random(64),
            'is_verified' => false,
        ]);

        return redirect()
            ->route('signatures.list', ['signable' => $signable, 'id' => $id])
            ->with('success', 'Employee successfully added as an internal signer.');
    }

    public function verifyPublicOtp(Request $request, $token)
    {
        $signature = Signature::where('token', $token)->firstOrFail();

        $request->validate([
            'otp_code' => 'required|string|max:6',
        ]);

        // check if otp matches
        if ($request->otp_code === $signature->otp_code) {
            // if correct, set special session to allow access to pad
            session(['signature_otp_verified_' . $token => true]);

            return redirect()->route('signatures.public.pad', $token);
        }

        return redirect()
            ->route('signatures.public.pad', $token)
            ->with('error', 'Incorrect OTP code. Please check again.');
    }
}
