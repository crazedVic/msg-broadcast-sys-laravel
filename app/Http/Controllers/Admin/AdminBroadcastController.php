<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBroadcastController extends Controller
{
    /**
     * Display a listing of the broadcasts.
     */
    public function index()
    {
        $broadcasts = \App\Models\Broadcast::paginate(10);
        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    /**
     * Show the form for creating a new broadcast.
     */
    public function create()
    {
        return view('admin.broadcasts.create');
    }

    /**
     * Store a newly created broadcast in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        \App\Models\Broadcast::create($validated);

        session()->flash('success', 'Broadcast created successfully.');

        return redirect()->route('admin.broadcasts.index');
    }

    /**
     * Display the specified broadcast.
     */
    public function show(\App\Models\Broadcast $broadcast)
    {
     $states = $broadcast->states()->paginate(10);

     return view('admin.broadcasts.show', compact('broadcast', 'states'));
    }
   
    /**
     * Show the form for editing the specified broadcast.
     */
    public function edit(\App\Models\Broadcast $broadcast)
    {
        return view('admin.broadcasts.edit', compact('broadcast'));
    }
   
    /**
     * Update the specified broadcast in storage.
     */
    public function update(Request $request, \App\Models\Broadcast $broadcast)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
   
        $broadcast->update($validated);
   
        session()->flash('success', 'Broadcast updated successfully.');
   
        return redirect()->route('admin.broadcasts.index');
    }

    /**
     * Remove the specified broadcast from storage.
     */
    public function destroy(\App\Models\Broadcast $broadcast)
    {
        $broadcast->delete();

        session()->flash('success', 'Broadcast deleted successfully.');

        return redirect()->route('admin.broadcasts.index');
    }
}
