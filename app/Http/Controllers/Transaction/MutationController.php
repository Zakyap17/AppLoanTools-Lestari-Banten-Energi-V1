<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Location;
use App\Models\Rack;
use App\Models\Tool;
use App\Models\MutationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::whereIn('storage_type', ['Room', 'Loan Tools', 'Department'])->get();
        $racks = Rack::whereHas('location', function ($q) {
            $q->whereIn('storage_type', ['Room', 'Loan Tools', 'Department']);
        })->with('location')->get();

        $query = Tool::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%")
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->select(DB::raw(1))
                            ->from('tool_items')
                            ->whereColumn('tool_items.tool_id', 'tools.id')
                            ->where('tool_items.serial_number', 'ilike', "%{$search}%");
                    });
            });
        }

        $tools = $query->with(['location', 'rack'])->latest()->paginate(10)->withQueryString();

        return view('transaction.mutation.index', compact('locations', 'racks', 'tools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tool_ids' => 'required|array',
            'to_location_id' => 'required|exists:locations,id',
            'to_rack_id' => 'required|exists:racks,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->tool_ids as $toolId) {
                $tool = Tool::findOrFail($toolId);

                // Create Log
                MutationLog::create([
                    'tool_id' => $tool->id,
                    'from_location_id' => $tool->location_id,
                    'from_rack_id' => $tool->rack_id,
                    'to_location_id' => $request->to_location_id,
                    'to_rack_id' => $request->to_rack_id,
                    'reason' => $request->reason,
                    'user_id' => Auth::id(),
                ]);

                // Update Tool
                $tool->update([
                    'location_id' => $request->to_location_id,
                    'rack_id' => $request->to_rack_id,
                ]);
            }

            DB::commit();

            return redirect()->route('mutation')->with('success', count($request->tool_ids) . ' tools mutated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mutate tools: ' . $e->getMessage());
        }
    }
    public function searchAsset(Request $request)
    {
        $search = $request->get('q');
        $tools = Tool::with(['location', 'rack'])
            ->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%$search%")
                    ->orWhere('name', 'ilike', "%$search%")
                    ->orWhereHas('items', function ($sub) use ($search) {
                        $sub->where('serial_number', 'ilike', "%$search%");
                    });
            })
            ->take(50)
            ->get();
        return response()->json($tools);
    }

    public function viewDocument($id)
    {
        $log = MutationLog::with([
            'tool',
            'fromLocation',
            'fromRack',
            'toLocation',
            'toRack',
            'user'
        ])->findOrFail($id);

        return view('transaction.mutation.document', compact('log'));
    }
}
