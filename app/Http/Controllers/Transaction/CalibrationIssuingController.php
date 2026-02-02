<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalibrationIssuingController extends Controller
{
    public function index()
    {
        $calibrations = \App\Models\CalibrationIssuing::with(['tool'])->latest()->paginate(10);
        return view('transaction.calibration-issuing.index', compact('calibrations'));
    }

    public function create()
    {
        // Generate Doc No: CI-YYYYMMDD-XXXX
        $count = \App\Models\CalibrationIssuing::whereDate('created_at', today())->count() + 1;
        $docNo = 'CI-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('transaction.calibration-issuing.create', compact('docNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'issue_code' => 'required|unique:calibration_issuings,issue_code',
            'tool_id' => 'required|exists:tools,id',
            'issue_date' => 'required|date',
        ]);

        // Create Record
        \App\Models\CalibrationIssuing::create([
            'issue_code' => $request->issue_code,
            'tool_id' => $request->tool_id,
            'issue_date' => $request->issue_date,
            'expected_completion_date' => $request->expected_completion_date,
            'notes' => $request->notes,
            'status' => 'in_progress'
        ]);

        // Update Tool Status and Calibration Date
        $tool = \App\Models\Tool::find($request->tool_id);
        if ($tool) {
            $tool->update([
                'status' => 'calibration',
                'next_calibration_date' => $request->expected_completion_date, // Sync with dashboard KPI
            ]);
        }

        return redirect()->route('calibration-issuing')->with('success', 'Calibration Created Successfully');
    }

    public function complete($id)
    {
        $calibration = \App\Models\CalibrationIssuing::findOrFail($id);

        // 1. Update Calibration Record
        $calibration->update([
            'status' => 'completed',
            'completion_date' => now(),
        ]);

        // 2. Update Tool Schedule
        $tool = \App\Models\Tool::find($calibration->tool_id);
        if ($tool) {
            $tool->update([
                'status' => 'available', // Release back to inventory
                'last_calibration_date' => now(),
                'next_calibration_date' => now()->addYear(), // Default 1 year interval
            ]);
        }

        return redirect()->back()->with('success', 'Calibration Completed successfully. Next date scheduled.');
    }
}
