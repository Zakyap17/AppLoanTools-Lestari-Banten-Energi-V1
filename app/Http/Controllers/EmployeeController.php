<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\LdapService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });

        }

        $employees = $query->latest()->paginate(10)->withQueryString();
        return view('employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:employees,nik',
            'name' => 'required',
            // Add other validations as needed
        ]);

        Employee::create($request->all());

        return redirect()->route('employee.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        return view('employee.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nik' => 'required|unique:employees,nik,' . $id,
            'name' => 'required',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
    }

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headlines
        $headers = ['NIK', 'Name', 'Department', 'Position', 'Email'];
        $sheet->fromArray([$headers], NULL, 'A1');

        // Style the header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $fileName = 'employee_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove Header
            array_shift($rows);

            $count = 0;
            foreach ($rows as $row) {
                // Ensure required fields (NIK, Name) exist
                if (empty($row[0]) || empty($row[1]))
                    continue;

                Employee::updateOrCreate(
                    ['nik' => $row[0]], // Check NIK
                    [
                        'name' => $row[1],
                        'department' => $row[2] ?? '-',
                        'position' => $row[3] ?? '-',
                        'email' => $row[4] ?? null,
                        // 'phone' => $row[5] ?? null, // REMOVED
                        // 'address' => $row[6] ?? null, // REMOVED
                    ]
                );
                $count++;
            }

            return redirect()->back()->with('success', "Imported {$count} employees successfully.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function syncFromLdap(LdapService $ldap)
    {
        try {
            $users = $ldap->getAllUsers();
            $totalFound = count($users);
            \Log::info("LDAP Sync: Total users found in AD: " . $totalFound);

            if ($totalFound > 0) {
                \Log::info("Sample User from LDAP: " . json_encode($users[0]));
            }

            $count = 0;
            $skipped = 0;

            foreach ($users as $user) {
                // We only sync if they have a NIK (employeeid)
                if (empty($user['nik'])) {
                    $skipped++;
                    continue;
                }

                Employee::updateOrCreate(
                    ['nik' => $user['nik']],
                    [
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'department' => $user['department'] ?? '-',
                        'position' => $user['position'] ?? '-',
                        'phone' => $user['phone'] ?? null,
                    ]
                );
                $count++;
            }

            \Log::info("LDAP Sync Result: Updated {$count}, Skipped {$skipped}");

            return redirect()->back()->with('success', "Synchronized {$count} employees from LDAP successfully. (Total found: {$totalFound}, Skipped: {$skipped})");
        } catch (\Exception $e) {
            \Log::error("LDAP Sync Controller Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'LDAP Sync failed: ' . $e->getMessage());
        }
    }
}
