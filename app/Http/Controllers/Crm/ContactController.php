<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // using paginate(10) to synchronize with the pagination links {{ $contacts->links() }} in the view
        $contacts = CrmContact::latest()->paginate(10);
        return view('crm.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('crm.contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website_url' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        // since has_website is an html switch/checkbox type, it will not send data to the request if unchecked.
        // therefore, we manually set it to true/false using laravel's built-in has() method.
        $validated['has_website'] = $request->has('has_website');

        // set fallback values if the user leaves the input form empty
        $validated['website_url'] = $request->input('website_url') ?: '-';
        $validated['email'] = $request->input('email') ?: '-';
        $validated['source'] = $request->input('source') ?: 'aratech gmaps scraper';

        CrmContact::create($validated);

        return redirect()->route('crm.contacts.index')->with('success', 'New contact successfully added.');
    }

    public function show($id)
    {
        $contact = CrmContact::findOrFail($id);
        return view('crm.contacts.show', compact('contact'));
    }

    public function edit($id)
    {
        $contact = CrmContact::findOrFail($id);
        return view('crm.contacts.edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = CrmContact::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'website_url' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        // handle the website toggle and fallback text again for the update process
        $validated['has_website'] = $request->has('has_website');
        $validated['website_url'] = $request->input('website_url') ?: '-';
        $validated['email'] = $request->input('email') ?: '-';
        $validated['source'] = $request->input('source') ?: 'aratech gmaps scraper';

        $contact->update($validated);

        return redirect()->route('crm.contacts.index')->with('success', 'Contact data successfully updated.');
    }

    public function destroy($id)
    {
        $contact = CrmContact::findOrFail($id);
        $contact->delete();

        return redirect()->route('crm.contacts.index')->with('success', 'Contact successfully deleted.');
    }
}
