<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index() {
        return view('tags.index', ['tags' => Tag::orderBy('name')->paginate(20)]);
    }

    public function store(StoreTagRequest $request) {
        Tag::create($request->validated());
        return redirect()->back();
    }
}
