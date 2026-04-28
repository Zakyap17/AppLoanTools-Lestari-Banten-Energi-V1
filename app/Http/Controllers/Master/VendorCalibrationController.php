<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\VendorCalibration;
use Illuminate\Http\Request;

class VendorCalibrationController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorCalibration::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('code', 'ilike', "%{$search}%")
                ->orWhere('name', 'ilike', "%{$search}%")
                ->orWhere('service', 'ilike', "%{$search}%")
                ->orWhere('post_code', 'ilike', "%{$search}%");
        }

        $vendors = $query->latest()->paginate(10)->withQueryString();

        return view('master.vendor-calibration.index', [
            'title' => 'Vendor Calibration',
            'breadcrumb' => 'Vendor Calibration',
            'vendors' => $vendors
        ]);
    }

    public function create()
    {
        // Auto generate code VCXXXXX
        $lastVendor = VendorCalibration::latest('id')->first();
        $nextNumber = $lastVendor ? ((int) substr($lastVendor->code, 2)) + 1 : 12; // Start from 12 as per image VC00012
        $nextCode = 'VC' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('master.vendor-calibration.create', [
            'title' => 'New Vendor Calibration',
            'breadcrumb' => 'New Vendor Calibration',
            'nextCode' => $nextCode
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:vendor_calibrations,code',
            'name' => 'required',
            'address' => 'nullable',
            'service' => 'nullable',
            'phone' => 'nullable',
            'post_code' => 'nullable',
        ]);

        VendorCalibration::create($validated);

        return redirect()->route('vendors-calibration')->with('success', 'Vendor created successfully.');
    }

    public function edit(VendorCalibration $vendors_calibration)
    {
        return view('master.vendor-calibration.edit', [
            'title' => 'Edit Vendor Calibration',
            'breadcrumb' => 'Edit Vendor Calibration',
            'vendor' => $vendors_calibration
        ]);
    }

    public function update(Request $request, VendorCalibration $vendors_calibration)
    {
        $validated = $request->validate([
            'code' => 'required|unique:vendor_calibrations,code,' . $vendors_calibration->id,
            'name' => 'required',
            'address' => 'nullable',
            'service' => 'nullable',
            'phone' => 'nullable',
            'post_code' => 'nullable',
        ]);

        $vendors_calibration->update($validated);

        return redirect()->route('vendors-calibration')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(VendorCalibration $vendors_calibration)
    {
        $password = request('password_confirmation_val');

        if (!auth()->user()->validatePassword($password)) {
            return redirect()->back()->with('error', 'Incorrect password. Deletion cancelled.');
        }

        try {
            $vendors_calibration->delete();
            return redirect()->route('vendors-calibration')->with('success', 'Vendor deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Cannot delete vendor because it is in use.');
            }
            return redirect()->back()->with('error', 'Error deleting vendor: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=VendorCalibration_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Code', 'Name', 'Address', 'Service', 'Phone', 'Post Code'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');
            fputcsv($file, ['VC00013', 'PT. Kalibrasi Utama', 'Jl. Merdeka No. 1', 'Calibration Service', '021-1234567', '12345'], ';');
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

                    // Filter out empty rows that might be read
                    if (array_filter($data)) {
                        $this->importRow($data);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Vendors imported successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function importRow($data)
    {
        $code = trim($data[0]);
        $name = trim($data[1]);
        $addr = isset($data[2]) ? trim($data[2]) : null;
        $service = isset($data[3]) ? trim($data[3]) : null;
        $phone = isset($data[4]) ? trim($data[4]) : null;
        $postCode = isset($data[5]) ? trim($data[5]) : null;

        \App\Models\VendorCalibration::firstOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'address' => $addr,
                'service' => $service,
                'phone' => $phone,
                'post_code' => $postCode
            ]
        );
    }
}
