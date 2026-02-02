<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ToolsMatSubGroup;
use App\Models\ToolsMatGroup;
use Illuminate\Http\Request;

class ToolsMatSubGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = ToolsMatSubGroup::with('matGroup');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('sub_code', 'ilike', "%{$search}%")
                ->orWhere('sub_description', 'ilike', "%{$search}%")
                ->orWhere('mat_group_code', 'ilike', "%{$search}%");
        }

        $subGroups = $query->latest()->paginate(10)->withQueryString();
        return view('master.tools-mat-sub-group.index', compact('subGroups'));
    }

    public function create()
    {
        $groups = ToolsMatGroup::all();
        return view('master.tools-mat-sub-group.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mat_group_code' => 'required|exists:tools_mat_groups,code',
            'sub_code' => 'required',
            'sub_description' => 'required',
        ]);

        ToolsMatSubGroup::create($validated);

        return redirect()->route('tools-mat-group.index')->with('success', 'MAT Sub Group created successfully.');
    }

    public function edit($id)
    {
        $subGroup = ToolsMatSubGroup::findOrFail($id);
        $groups = ToolsMatGroup::all();
        return view('master.tools-mat-sub-group.edit', compact('subGroup', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $subGroup = ToolsMatSubGroup::findOrFail($id);

        $validated = $request->validate([
            'mat_group_code' => 'required|exists:tools_mat_groups,code',
            'sub_code' => 'required',
            'sub_description' => 'required',
        ]);

        $subGroup->update($validated);

        return redirect()->route('tools-mat-group.index')->with('success', 'MAT Sub Group updated successfully.');
    }

    public function destroy($id)
    {
        $subGroup = ToolsMatSubGroup::findOrFail($id);

        // Check if any tools are using this sub group
        // Assuming relationship name is 'tools', but we check the Tool model manually to be safe
        // Or check via query if relationship not defined in SubGroup model
        $isUsed = \App\Models\Tool::where('sub_group_id', $id)->exists();

        if ($isUsed) {
            return back()->with('error', 'Cannot delete Sub Group because it is used by existing Tools.');
        }

        $subGroup->delete();

        // Redirect back to the Merged Group View
        return redirect()->route('tools-mat-group.index')->with('success', 'MAT Sub Group deleted successfully.');
    }
}
