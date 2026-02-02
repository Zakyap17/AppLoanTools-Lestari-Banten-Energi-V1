<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\Manufacturer;
use App\Models\Location;
use App\Models\ToolsMatGroup;
use App\Models\ToolsMatSubGroup;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tool::with(['type', 'manufacturer', 'location', 'rack', 'subGroup.matGroup', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%")
                    ->orWhere('long_description', 'ilike', "%{$search}%");
            });
        }

        $tools = $query->latest()->paginate(10)->withQueryString();

        // KPI Calculations
        $totalMasterData = Tool::count();
        $totalItems = \App\Models\ToolItem::count();

        return view('master.tools.index', compact('tools', 'totalMasterData', 'totalItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = ToolType::all();
        $manufacturers = Manufacturer::all();
        // Updated filter to 'Room' as per new requirement
        $locations = Location::where('storage_type', 'Room')->get();
        $matGroups = ToolsMatGroup::all();
        $matSubGroups = ToolsMatSubGroup::all();

        $uoms = ['EA', 'SET', 'BOX', 'KG', 'M'];

        // Generate MT No (Internal ID style)
        // Generate MT No (Internal ID style) - Changed to Year Prefix '26'
        $prefix = '26';
        $lastTool = Tool::where('code', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(code) desc')
            ->orderBy('code', 'desc')
            ->first();

        $lastNum = 0;
        if ($lastTool) {
            // Check if it follows the 26xxxxx pattern
            if (str_starts_with($lastTool->code, $prefix)) {
                $lastNum = intval(substr($lastTool->code, strlen($prefix)));
            }
        }
        // Format: 2600001 (26 + 5 digits)
        $mtCode = $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);

        // Generate Asset No (TAS style)
        $lastAsset = Tool::where('serial_number', 'like', 'TAS%')->orderBy('id', 'desc')->first();
        $lastAssetNum = $lastAsset ? intval(substr($lastAsset->serial_number, 3)) : 0;
        $assetNo = 'TAS' . str_pad($lastAssetNum + 1, 5, '0', STR_PAD_LEFT);

        return view('master.tools.create', compact('types', 'manufacturers', 'locations', 'matGroups', 'matSubGroups', 'mtCode', 'uoms', 'assetNo'));
    }

    public function getBins(Request $request, $locationId)
    {
        $racks = \App\Models\Rack::where('location_id', $locationId)->get();
        return response()->json($racks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Simple validation for now, can be expanded
        $validated = $request->validate([
            'code' => 'required|unique:tools,code',
            'name' => 'required',
            'quantity' => 'required|integer|min:0',
            // Add other validations as needed
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $tool = Tool::create($request->all());

            // Generate Serialized Items
            if ($tool->quantity > 0) {
                for ($i = 1; $i <= $tool->quantity; $i++) {
                    $seq = str_pad($i, 3, '0', STR_PAD_LEFT);
                    \App\Models\ToolItem::create([
                        'tool_id' => $tool->id,
                        'serial_number' => $tool->code . '-' . $seq,
                        'status' => 'available',
                        'condition' => 'Good',
                        'purchase_date' => now(),
                        'created_by' => auth()->id()
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('tools.index')->with('success', 'Tool created with ' . $tool->quantity . ' serialized items.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create tool: ' . $e->getMessage())->withInput();
        }
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
        $tool = Tool::findOrFail($id);
        $types = ToolType::all();
        $manufacturers = Manufacturer::all();
        // Updated filter to 'Room'
        $locations = Location::where('storage_type', 'Room')->get();
        $matGroups = ToolsMatGroup::all();
        $matSubGroups = ToolsMatSubGroup::all();

        $uoms = ['EA', 'SET', 'BOX', 'KG', 'M'];

        return view('master.tools.edit', compact('tool', 'types', 'manufacturers', 'locations', 'matGroups', 'matSubGroups', 'uoms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required|unique:tools,code,' . $id,
            'name' => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $tool = Tool::findOrFail($id);
            $oldQty = $tool->quantity;

            $tool->update($request->all());

            // Sync Items if Quantity Increased
            if ($tool->quantity > $oldQty) {
                // Find highest existing sequence
                $lastKeyItem = \App\Models\ToolItem::where('tool_id', $tool->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $startSeq = 1;
                if ($lastKeyItem) {
                    // Extract Sequence from "MT00001-005" -> 5
                    $parts = explode('-', $lastKeyItem->serial_number);
                    $lastSeq = intval(end($parts));
                    $startSeq = $lastSeq + 1;
                }

                $diff = $tool->quantity - $oldQty;

                for ($i = 0; $i < $diff; $i++) {
                    $currentSeq = $startSeq + $i;
                    $seqStr = str_pad($currentSeq, 3, '0', STR_PAD_LEFT);

                    \App\Models\ToolItem::create([
                        'tool_id' => $tool->id,
                        'serial_number' => $tool->code . '-' . $seqStr,
                        'status' => 'available',
                        'condition' => 'Good',
                        'purchase_date' => now(),
                        'created_by' => auth()->id()
                    ]);
                }
            }
            // Note: We do NOT auto-delete items if Qty reduced, for safety.

            \Illuminate\Support\Facades\DB::commit();
            \Illuminate\Support\Facades\DB::commit();

            $redirectParams = [];
            if ($request->has('page')) {
                $redirectParams['page'] = $request->input('page');
            }

            return redirect()->route('tools.index', $redirectParams)->with('success', 'Tool updated successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $password = request('password_confirmation_val');

        // if (!\Illuminate\Support\Facades\Hash::check($password, auth()->user()->password)) {
        //     return redirect()->back()->with('error', 'Incorrect password. Deletion cancelled.');
        // }

        $tool = Tool::findOrFail($id);
        $tool->delete();

        return redirect()->back()->with('success', 'Tool deleted successfully.');
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=ToolsMaster_Template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        // Headers matched to Table View for clarity
        $columns = ['Code', 'Ref No.', 'Description', 'Type', 'Material Group', 'Manufacturer', 'UOM', 'Price', 'Location', 'Bin', 'Initial Quantity'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');
            // Sample data updated to use VALID Database values
            fputcsv($file, ['2600001', 'TAS00001', 'Hand Drill X100', 'Hand Tools', 'Power Tools', 'Bosch', 'EA', '1500000', 'TOOLS ROOM B', 'B1-01', '10'], ';');
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getPathname();

        // Supported extensions
        $extension = strtolower($file->getClientOriginalExtension());
        $validExtensions = ['csv', 'txt', 'xlsx', 'xls'];

        if (!in_array($extension, $validExtensions)) {
            return redirect()->back()->with('error', 'Invalid file type. Supported: CSV, TXT, XLSX, XLS.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Load file using PhpSpreadsheet (Auto-detects CSV/Excel)
            /** @var \PhpOffice\PhpSpreadsheet\Reader\BaseReader */
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);

            // Explicitly detect delimiter for CSV files to ensure correct parsing
            if (in_array($extension, ['csv', 'txt'])) {
                $reader->setReadDataOnly(true);

                if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
                    // Read first line to detect delimiter
                    $handle = fopen($path, "r");
                    if ($handle) {
                        $firstLine = fgets($handle);
                        fclose($handle);

                        if ($firstLine) {
                            $semicolon = substr_count($firstLine, ';');
                            $comma = substr_count($firstLine, ',');
                            $tab = substr_count($firstLine, "\t");

                            if ($tab > $semicolon && $tab > $comma) {
                                $reader->setDelimiter("\t");
                            } elseif ($semicolon > $comma) {
                                $reader->setDelimiter(";");
                            } else {
                                $reader->setDelimiter(",");
                            }
                        }
                    }
                }
            } else {
                $reader->setReadDataOnly(true);
            }

            $spreadsheet = $reader->load($path);
            $worksheet = $spreadsheet->getActiveSheet();

            $loopCount = 0;
            $successCount = 0;
            $errors = [];

            $warnings = [];

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                // Skip Header (Row 1)
                if ($rowIndex == 1)
                    continue;

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = trim($cell->getValue()); // Trim data immediately
                }

                // Check empty rows
                if (empty(array_filter($data)))
                    continue;

                $loopCount++;

                try {
                    $rowWarnings = $this->importRow($data, $rowIndex);
                    if (!empty($rowWarnings)) {
                        $warnings = array_merge($warnings, $rowWarnings);
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row $rowIndex: " . $e->getMessage();
                }
            }

            if ($successCount == 0 && count($errors) == 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->with('error', 'Import failed. No valid rows found in file.');
            }

            if (count($errors) > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                $errorMsg = implode('<br>', array_slice($errors, 0, 5));
                if (count($errors) > 5)
                    $errorMsg .= '<br>...and ' . (count($errors) - 5) . ' more errors.';
                return redirect()->back()->with('error', 'Import failed with errors:<br>' . $errorMsg);
            }

            \Illuminate\Support\Facades\DB::commit();

            $msg = "Import successful! Processed $successCount records.";
            if (count($warnings) > 0) {
                $uniqueWarnings = array_unique($warnings);
                $warningMsg = implode('<br>', array_slice($uniqueWarnings, 0, 5));
                if (count($uniqueWarnings) > 5)
                    $warningMsg .= '<br>...and ' . (count($uniqueWarnings) - 5) . ' more warnings.';
                return redirect()->back()->with('warning', $msg . '<br><strong>Warnings:</strong><br>' . $warningMsg);
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Import Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'System Error during import: ' . $e->getMessage());
        }
    }

    private function importRow($data, $rowLine)
    {
        $warnings = [];
        // Mapping: 0=Code, 1=AssetNo, 2=Desc, 3=Type, 4=Group, 5=Manf, 6=UOM, 7=Price, 8=Loc, 9=Rack, 10=Qty
        $code = isset($data[0]) ? $data[0] : '';
        $assetNo = isset($data[1]) ? $data[1] : '';
        $desc = isset($data[2]) ? $data[2] : '';

        // Basic validation
        if (empty($code) || empty($desc)) {
            // Check if it's just a trailing empty row
            if (empty(implode('', $data)))
                return [];
            throw new \Exception("Code and Description are required.");
        }

        $typeName = isset($data[3]) ? $data[3] : '';
        // $groupCode = isset($data[4]) ? $data[4] : ''; // Unused for now
        $manfName = isset($data[5]) ? $data[5] : '';
        $uom = isset($data[6]) ? $data[6] : 'EA';
        $price = isset($data[7]) ? floatval($data[7]) : 0;
        $locName = isset($data[8]) ? $data[8] : '';
        $rackCode = isset($data[9]) ? $data[9] : '';
        $quantity = isset($data[10]) ? intval($data[10]) : 0;

        // 1. Resolve Foreign Keys (Case Insensitive & Trimmed)
        $typeId = null;
        if ($typeName) {
            $type = ToolType::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($typeName)])
                ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($typeName)])
                ->first();
            if ($type) {
                $typeId = $type->id;
            } else {
                $warnings[] = "Row $rowLine: Material Type '$typeName' not found.";
            }
        }

        $manfId = null;
        if ($manfName) {
            $manf = Manufacturer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($manfName)])
                ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($manfName)])
                ->first();
            $manfId = $manf ? $manf->id : null;
        }

        $locId = null;
        $rackId = null;

        if ($locName) {
            $loc = Location::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($locName)])
                ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($locName)])
                ->first();
            if ($loc) {
                $locId = $loc->id;
                if ($rackCode) {
                    // Rack search needs to span the location
                    $rack = \App\Models\Rack::where('location_id', $locId)
                        ->where(function ($q) use ($rackCode) {
                            $q->whereRaw('LOWER(TRIM(code)) = ?', [strtolower($rackCode)])
                                ->orWhereRaw('LOWER(TRIM(description)) = ?', [strtolower($rackCode)]);
                        })->first();
                    if ($rack) {
                        $rackId = $rack->id;
                    } else {
                        $warnings[] = "Row $rowLine: Bin '$rackCode' not found in '$locName'.";
                    }
                }
            } else {
                $warnings[] = "Row $rowLine: Location '$locName' not found.";
            }
        }

        // 2. Create or Update Tool
        $tool = Tool::updateOrCreate(
            ['code' => $code],
            [
                'serial_number' => $assetNo,
                'name' => $desc,
                'type_id' => $typeId,
                'manufacturer_id' => $manfId,
                'uom' => $uom,
                'price' => $price,
                'location_id' => $locId,
                'rack_id' => $rackId,
                'quantity' => $quantity
            ]
        );

        // 3. Generate Items
        if ($quantity > 0) {
            // Check existing items count
            $existingCount = \App\Models\ToolItem::where('tool_id', $tool->id)->count();

            if ($existingCount < $quantity) {
                $diff = $quantity - $existingCount;

                // Find start sequence
                $lastKeyItem = \App\Models\ToolItem::where('tool_id', $tool->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $startSeq = 1;
                if ($lastKeyItem) {
                    $parts = explode('-', $lastKeyItem->serial_number);
                    $lastSeq = intval(end($parts));
                    $startSeq = $lastSeq + 1;
                }

                for ($i = 0; $i < $diff; $i++) {
                    $currentSeq = $startSeq + $i;
                    $seqStr = str_pad($currentSeq, 3, '0', STR_PAD_LEFT);

                    \App\Models\ToolItem::create([
                        'tool_id' => $tool->id,
                        'serial_number' => $tool->code . '-' . $seqStr,
                        'status' => 'available',
                        'condition' => 'Good',
                        'purchase_date' => now(),
                        'created_by' => auth()->id()
                    ]);
                }
            }
        }

        return $warnings;
    }
}
