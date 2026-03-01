<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::with('racks')->whereIn('storage_type', ['Room', 'Loan Tools', 'Department']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%")
                    ->orWhere('warehouse', 'ilike', "%{$search}%")
                    ->orWhereHas('racks', function ($r) use ($search) {
                        $r->where('code', 'ilike', "%{$search}%")
                            ->orWhere('description', 'ilike', "%{$search}%");
                    });
            });
        }

        $locations = $query->latest()->paginate(10)->withQueryString();
        return view('master.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:locations,code',
            'name' => 'required',
            'total_bin' => 'integer|min:0',
        ]);

        // Calculate Total Bin based on valid Racks
        $rackCount = 0;
        if ($request->has('racks') && is_array($request->racks)) {
            foreach ($request->racks as $rack) {
                if (!empty($rack['code'])) {
                    $rackCount++;
                }
            }
        }

        // Create Location (Storage)
        $location = Location::create([
            'code' => $request->code,
            'name' => $request->name,
            'storage_type' => $request->storage_type ?? 'Loan Tools',
            'warehouse' => $request->warehouse,
            'total_bin' => $rackCount,
        ]);

        // Create Initial Rack if Rack Code is provided
        // Create Initial Racks
        if ($request->has('racks') && is_array($request->racks)) {
            foreach ($request->racks as $rackData) {
                if (!empty($rackData['code'])) {
                    $location->racks()->create([
                        'code' => $rackData['code'],
                        'description' => $rackData['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
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
        $location = Location::findOrFail($id);
        return view('master.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:locations,code,' . $id,
            'name' => 'required',
            'total_bin' => 'integer|min:0',
        ]);

        $location = Location::findOrFail($id);

        // Handle Racks Sync & Calculate Total Bin
        $validRackCount = 0;

        if ($request->has('racks') && is_array($request->racks)) {
            foreach ($request->racks as $rackData) {
                // Check if marked for deletion
                if (isset($rackData['delete']) && $rackData['delete'] == 1) {
                    if (isset($rackData['id'])) {
                        $location->racks()->where('id', $rackData['id'])->delete();
                    }
                    continue;
                }

                if (!empty($rackData['code'])) {
                    $validRackCount++; // Count valid racks

                    if (isset($rackData['id'])) {
                        // Update existing
                        $location->racks()->where('id', $rackData['id'])->update([
                            'code' => $rackData['code'],
                            'description' => $rackData['description'] ?? null,
                        ]);
                    } else {
                        // Create new
                        $location->racks()->create([
                            'code' => $rackData['code'],
                            'description' => $rackData['description'] ?? null,
                        ]);
                    }
                }
            }
        }

        // Update Location including Total Bin
        $updateData = $request->only(['code', 'name', 'storage_type', 'warehouse']);
        $updateData['total_bin'] = $validRackCount;
        $location->update($updateData);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete(); // Cascade delete handles racks

            return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Cannot delete location because it is in use.');
            }
            return redirect()->back()->with('error', 'Error deleting location: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Download a CSV template for data import.
     */
    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Location_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Tools Room Code', 'Tools Room Name', 'Storage Type', 'Warehouse', 'Bin Code', 'Bin Description'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            // Example Row
            fputcsv($file, ['LOC-001', 'Main Room', 'Room', 'Indoor', 'A-01', 'Shelf 1'], ';');

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

            $lastRoomCode = null;
            $lastRoomName = null;
            $lastStorageType = null;
            $lastWarehouse = null;

            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($path, "r");

                // Detect delimiter
                $firstLine = fgets($handle);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ";" : ",";
                rewind($handle);

                fgetcsv($handle, 1000, $delimiter); // Skip Header

                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    $hasBin = !empty(trim($data[4] ?? ''));
                    $roomCode = trim($data[0] ?? '');

                    if (!empty($roomCode)) {
                        // New Location Row
                        $lastRoomCode = $roomCode;
                        $lastRoomName = trim($data[1] ?? '');
                        $lastStorageType = trim($data[2] ?? '');
                        $lastWarehouse = trim($data[3] ?? '');
                    } elseif ($hasBin && !empty($lastRoomCode)) {
                        // Inherit from previous
                        $data[0] = $lastRoomCode;
                        $data[1] = $lastRoomName;
                        $data[2] = $lastStorageType;
                        $data[3] = $lastWarehouse;
                    } else {
                        // Skip if no room code and no previous room context, or empty row
                        continue;
                    }

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

                    // Check if row is completely empty
                    if (empty(array_filter($data)))
                        continue;

                    $hasBin = !empty(trim($data[4] ?? ''));
                    $roomCode = trim($data[0] ?? '');

                    if (!empty($roomCode)) {
                        // New Location Row
                        $lastRoomCode = $roomCode;
                        $lastRoomName = trim($data[1] ?? '');
                        $lastStorageType = trim($data[2] ?? '');
                        $lastWarehouse = trim($data[3] ?? '');
                    } elseif ($hasBin && !empty($lastRoomCode)) {
                        // Inherit from previous
                        $data[0] = $lastRoomCode;
                        $data[1] = $lastRoomName;
                        $data[2] = $lastStorageType;
                        $data[3] = $lastWarehouse;
                    } else {
                        continue;
                    }

                    $this->importRow($data);
                }
            }

            // Recalculate Total Bins for affected locations
            // Optimization: Only update those touched? For now all is fine or we can skip this if we trust DB triggers (if any) or do it periodically.
            // But to be safe keeping it.
            $locations = \App\Models\Location::all();
            foreach ($locations as $loc) {
                $count = \App\Models\Rack::where('location_id', $loc->id)->count();
                $loc->update(['total_bin' => $count]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Locations imported successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function importRow($data)
    {
        $roomCode = trim($data[0]);
        $roomName = trim($data[1]);
        $storageType = trim($data[2]);
        $warehouseType = trim($data[3]);
        $binCode = isset($data[4]) ? trim($data[4]) : null;
        $binDesc = isset($data[5]) ? trim($data[5]) : null;

        // Find or Create Location
        $location = \App\Models\Location::firstOrCreate(
            ['code' => $roomCode],
            [
                'name' => $roomName,
                'storage_type' => $storageType,
                'warehouse' => $warehouseType,
                'total_bin' => 0 // Will auto update
            ]
        );

        // Create Bin if provided
        if ($binCode) {
            \App\Models\Rack::firstOrCreate(
                [
                    'location_id' => $location->id,
                    'code' => $binCode
                ],
                [
                    'description' => $binDesc
                ]
            );
        }
    }
}
