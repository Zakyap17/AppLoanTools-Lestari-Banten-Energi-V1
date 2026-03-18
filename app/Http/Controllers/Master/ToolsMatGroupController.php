<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ToolsMatGroup;
use Illuminate\Http\Request;

class ToolsMatGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = ToolsMatGroup::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('code', 'ilike', "%{$search}%")
                ->orWhere('description', 'ilike', "%{$search}%");
        }

        $groups = $query->with('subGroups')->latest()->paginate(10)->withQueryString();
        return view('master.tools-mat-group.index', compact('groups'));
    }

    public function create()
    {
        return view('master.tools-mat-group.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:tools_mat_groups,code',
            'description' => 'required',
        ]);

        ToolsMatGroup::create($validated);

        return redirect()->route('tools-mat-group.index')->with('success', 'MAT Group created successfully.');
    }

    public function edit($id)
    {
        $group = ToolsMatGroup::findOrFail($id);
        return view('master.tools-mat-group.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $group = ToolsMatGroup::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:tools_mat_groups,code,' . $id,
            'description' => 'required',
        ]);

        $group->update($validated);

        return redirect()->route('tools-mat-group.index')->with('success', 'MAT Group updated successfully.');
    }

    public function destroy($id)
    {
        $password = request('password_confirmation_val');

        if (!auth()->user()->validatePassword($password)) {
            return redirect()->back()->with('error', 'Incorrect password. Deletion cancelled.');
        }
        try {
            $group = ToolsMatGroup::findOrFail($id);

            // Check if it has sub groups before delete
            if ($group->subGroups()->count() > 0) {
                return back()->with('error', 'Cannot delete group that has sub groups.');
            }

            $group->delete();

            return redirect()->route('tools-mat-group.index')->with('success', 'MAT Group deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Cannot delete group because it is in use.');
            }
            return redirect()->back()->with('error', 'Error deleting group: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=MaterialGroup_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Code', 'Description'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');
            fputcsv($file, ['GRP-001', 'Hand Tools'], ';');
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
            return redirect()->back()->with('success', 'Material Groups imported successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function importRow($data)
    {
        $code = trim($data[0]);
        $desc = trim($data[1]);

        \App\Models\ToolsMatGroup::firstOrCreate(
            ['code' => $code],
            ['description' => $desc]
        );
    }
}
