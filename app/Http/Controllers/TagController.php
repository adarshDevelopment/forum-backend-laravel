<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends RootController
{

    public function index()
    {
        $tags = Tag::all();

        return $this->sendSuccess('Tags successfully fetched', 'tags');
    }

    public function store(TagRequest $request)
    {
        $tags = Tag::create($request->all());
        if (!$tags) {
            return $this->sendError('Error createing new tag');
        }

        return $this->sendSuccess('Tag successfully created', 'tags', $tags);
    }

    public function update(TagRequest $request)
    {
        $tag = Tag::find($request->id);

        if (!$tag) {
            return $this->sendError('Tag not found', 404);
        }

        if (!$tag->update($request->all())) {
            return $this->sendError('Error updating tag');
        }

        return $this->sendSuccess('Tag successfully updated');
    }

    public function destroy(Request $request)
    {
        $tag = Tag::find($request->id);
        if (!$tag) {
            return $this->sendError('Tag not found', 400);
        }

        if (!$tag->delete()) {
            return $this->sendError('Error deleting tag');
        }

        return $this->sendSuccess('Tag successfully deleted');
    }
}
