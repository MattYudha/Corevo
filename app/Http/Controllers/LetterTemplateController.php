<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterTemplate;
use App\Models\LetterTag;
use App\Constants\LetterTagConfig;

class LetterTemplateController extends Controller
{
    public function index()
    {
        $templates = LetterTemplate::all();
        return view('letter-templates.index', compact('templates'));
    }

    public function create()
    {
        $tags = LetterTag::all();
        return view('letter-templates.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:letter_templates',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'type' => 'required|in:official,memo,notice',
        ]);

        LetterTemplate::create($request->all());
        return redirect()->route('letter-templates.index')->with('success', 'Template created successfully.');
    }

    public function show(LetterTemplate $letterTemplate)
    {
        if (request()->wantsJson() || request()->ajax()) {
            preg_match_all('/\[(.*?)\]/', $letterTemplate->content, $matches);
            $foundTags = array_unique($matches[1]);

            $tagsData = \App\Models\LetterTag::whereIn('tag_name', $foundTags)
                ->get()
                ->map(function ($tag) {
                    $options = [];

                    // if the type is dropdown, we prepare the option data
                    if ($tag->input_type === 'dropdown') {
                        if ($tag->dropdown_type === 'manual' && $tag->dropdown_options) {
                            // split the string by comma, e.g., "sick, permission, leave"
                            $arr = array_map('trim', explode(',', $tag->dropdown_options));
                            $options = array_combine($arr, $arr); // make the key and value identical
                        } elseif ($tag->dropdown_type === 'model') {
                            $options = LetterTagConfig::getDropdownData($tag->dropdown_model);
                        }
                    }

                    // insert the options into the json response
                    $tag->dropdown_data = $options;
                    return $tag;
                });

            return response()->json([
                'template' => $letterTemplate,
                'content' => $letterTemplate->content,
                'tags' => $tagsData,
            ]);
        }
        return view('letter-templates.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        $tags = LetterTag::all();
        return view('letter-templates.edit', compact(['tags', 'letterTemplate']));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $request->validate([
            'name' => 'required|unique:letter_templates,name,' . $letterTemplate->id,
            'description' => 'nullable|string',
            'content' => 'required|string',
            'type' => 'required|in:official,memo,notice',
        ]);

        $letterTemplate->update($request->all());
        return redirect()->route('letter-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $letterTemplate->delete();
        return redirect()->route('letter-templates.index')->with('success', 'Template deleted successfully.');
    }
}
