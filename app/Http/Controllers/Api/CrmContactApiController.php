<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;

class CrmContactApiController extends Controller
{
    public function index()
    {
        $contacts = CrmContact::latest()->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $contacts,
            ],
            200,
        );
    }

    public function store(Request $request)
    {
        // if bulk request
        if ($request->has('data') && is_array($request->input('data'))) {
            $validated = $request->validate([
                'data' => 'required|array',
                'data.*.company_name' => 'required|string|max:255',
                'data.*.address' => 'nullable|string',
                'data.*.phone' => 'nullable|string|max:50',
                'data.*.has_website' => 'boolean',
                'data.*.website_url' => 'nullable|string|max:255',
                'data.*.email' => 'nullable|string|max:255',
                'data.*.source' => 'nullable|string|max:255',
            ]);

            $insertedCount = 0;
            $skippedCount = 0;

            $insertedList = []; // to store successfully inserted items
            $skippedList = []; // to store skipped items

            foreach ($validated['data'] as $item) {
                $contact = $this->processContact($item);

                // create a readable identifier (email, or company name if no email)
                $identifier =
                    !empty($item['email']) && $item['email'] !== '-'
                        ? $item['email']
                        : 'No Email - ' . $item['company_name'];

                if ($contact) {
                    $insertedCount++;
                    $insertedList[] = $identifier;
                } else {
                    $skippedCount++;
                    $skippedList[] = $identifier;
                }
            }

            // condition 1: total failure (all duplicates)
            if ($insertedCount === 0) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'All submitted data already exists.',
                        'meta' => [
                            'total_inserted' => 0,
                            'total_skipped' => $skippedCount,
                            'skipped_data' => $skippedList,
                        ],
                    ],
                    409,
                ); // 409 conflict
            }

            // condition 2: partial success
            if ($skippedCount > 0) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Bulk insert completed with exceptions: Some data was skipped due to duplicates.',
                        'meta' => [
                            'total_inserted' => $insertedCount,
                            'total_skipped' => $skippedCount,
                            'inserted_data' => $insertedList,
                            'skipped_data' => $skippedList,
                        ],
                    ],
                    207,
                ); // 207 multi-status
            }

            // condition 3: total success without exceptions
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'All contact data successfully saved.',
                    'meta' => [
                        'total_inserted' => $insertedCount,
                        'total_skipped' => 0,
                        'inserted_data' => $insertedList,
                        'skipped_data' => [],
                    ],
                ],
                201,
            ); // 201 created
        }

        // if single request
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'has_website' => 'boolean',
            'website_url' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        $contact = $this->processContact($validated);

        // if the return value is false, it means the data already exists.
        if (!$contact) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to save. This email or company name already exists.',
                ],
                409,
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Contact data successfully saved.',
                'data' => $contact,
            ],
            201,
        );
    }

    private function processContact(array $item)
    {
        $rawEmail = $item['email'] ?? null;

        // if the scraper email is '-' or empty, convert it to actual null
        $email = $rawEmail === '-' || $rawEmail === '' ? null : $rawEmail;
        $companyName = $item['company_name'];

        // database checking criteria
        if ($email !== null) {
            // if a valid email exists, check for duplicates by email
            $existingContact = CrmContact::where('email', $email)->first();
        } else {
            // if there is no email (null), check for duplicates by company name
            $existingContact = CrmContact::where('company_name', $companyName)->first();
        }

        if ($existingContact) {
            return false;
        }

        // insert into database
        return CrmContact::create([
            'company_name' => $companyName,
            'address' => $item['address'] ?? null,
            'phone' => $item['phone'] ?? null,
            'has_website' => $item['has_website'] ?? false,
            'website_url' => $item['website_url'] ?? '-',
            'email' => $email,
            'source' => $item['source'] ?? 'aratech gmaps scraper',
        ]);
    }
}
