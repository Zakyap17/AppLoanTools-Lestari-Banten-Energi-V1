<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ToolType;
use Illuminate\Http\Request;

class ToolsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ToolType::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%");
            });
        }

        $toolTypes = $query->latest()->paginate(10)->withQueryString();
        return view('master.tools-type.index', compact('toolTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.tools-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:tools_types,code',
            'name' => 'required',
        ]);

        ToolType::create($request->all());

        return redirect()->route('tools-type.index')->with('success', 'Tool Type created successfully.');
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
        $toolType = ToolType::findOrFail($id);
        return view('master.tools-type.edit', compact('toolType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:tools_types,code,' . $id,
            'name' => 'required',
        ]);

        $toolType = ToolType::findOrFail($id);
        $toolType->update($request->all());

        return redirect()->route('tools-type.index')->with('success', 'Tool Type updated successfully.');
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

        try {
            $toolType = ToolType::findOrFail($id);
            $toolType->delete();

            return redirect()->route('tools-type.index')->with('success', 'Tool Type deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Cannot delete tool type because it is in use.');
            }
            return redirect()->back()->with('error', 'Error deleting tool type: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=ToolsType_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Type Code', 'Description'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');
            fputcsv($file, ['TYPE-001', 'Hand Drill'], ';');
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048'
        ]);

        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        $validExtensions = ['csv', 'txt', 'xlsx', 'xls'];

        if (!in_array($extension, $validExtensions)) {
            return redirect()->back()->with('error', 'Invalid file type. Supported: CSV, TXT, XLSX, XLS.');
        }

        $path = $request->file('file')->getPathname();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($path, "r");

                // Detect delimiter
                $firstLine = fgets($handle);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ";" : ",";
                rewind($handle);

                fgetcsv($handle, 1000, $delimiter); // Skip Header

                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    if (count($data) < 2)
                        continue;
                    $this->importRow($data);
                }
                fclose($handle);
            } else {
                // Excel
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($path);
                $worksheet = $spreadsheet->getActiveSheet();

                foreach ($worksheet->getRowIterator() as $index => $row) {
                    if ($index == 1)
                        continue; // Skip Header

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $data = [];
                    foreach ($cellIterator as $cell) {
                        $data[] = $cell->getValue();
                    }

                    if (empty($data[0]) || empty($data[1]))
                        continue;

                    if (array_filter($data)) {
                        $this->importRow($data);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Tool Types imported successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function importRow($data)
    {
        $code = trim($data[0]);
        $name = trim($data[1]);

        \App\Models\ToolType::firstOrCreate(
            ['code' => $code],
            ['name' => $name]
        );
    }
}
