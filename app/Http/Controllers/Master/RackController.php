<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Rack;
use App\Models\Location;
use Illuminate\Http\Request;

class RackController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rack = Rack::with('location')->findOrFail($id);
        return view('master.racks.edit', compact('rack'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required', // Rack code might not be unique globally, but unique per location? User didn't specify.
        ]);

        $rack = Rack::findOrFail($id);
        $rack->update($request->all());

        return redirect()->route('locations.index')->with('success', 'Rack updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $password = request('password_confirmation_val');

        if (!auth()->user()->validatePassword($password)) {
            return redirect()->back()->with('error', 'Incorrect password. Deletion cancelled.');
        }

        $rack = Rack::findOrFail($id);
        $rack->delete();

        return redirect()->route('locations.index')->with('success', 'Rack deleted successfully.');
    }
}
