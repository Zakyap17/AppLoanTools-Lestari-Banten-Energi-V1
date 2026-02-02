<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Manufacturer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $manufacturers = $query->latest()->paginate(10)->withQueryString();
        return view('master.manufacturer.index', compact('manufacturers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.manufacturer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:manufacturers,code',
            'name' => 'required',
        ]);

        Manufacturer::create($request->all());

        return redirect()->route('manufacture.index')->with('success', 'Manufacturer created successfully.');
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
        $manufacturer = Manufacturer::findOrFail($id);
        return view('master.manufacturer.edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:manufacturers,code,' . $id,
            'name' => 'required',
        ]);

        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->update($request->all());

        return redirect()->route('manufacture.index')->with('success', 'Manufacturer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $manufacturer = Manufacturer::findOrFail($id);
            $manufacturer->delete();

            return redirect()->route('manufacture.index')->with('success', 'Manufacturer deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Cannot delete manufacturer because it is associated with existing tools.');
            }
            return redirect()->back()->with('error', 'Error deleting manufacturer: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Manufacturer_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Code', 'Name', 'Phone', 'Email', 'Address'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');
            fputcsv($file, ['MFG-001', 'Bosch', '+49-123-456', 'info@bosch.com', 'Germany'], ';');
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
                    $this->paramCreate($data);
                }
                fclose($handle);
            } else {
                // Excel
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($path);
                $worksheet = $spreadsheet->getActiveSheet();

                // Get highest row/col (avoid loading everything if huge, but 2MB is small)
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
                    $this->paramCreate($data);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Manufacturers imported successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function paramCreate($data)
    {
        $code = trim($data[0]);
        $name = trim($data[1]);
        $phone = isset($data[2]) ? trim($data[2]) : null;
        $email = isset($data[3]) ? trim($data[3]) : null;
        $addr = isset($data[4]) ? trim($data[4]) : null;

        \App\Models\Manufacturer::firstOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $addr
            ]
        );
    }
}
