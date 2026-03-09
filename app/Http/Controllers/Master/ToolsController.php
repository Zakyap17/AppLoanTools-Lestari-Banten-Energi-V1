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
        // Updated filter to include other storage types
        $locations = Location::whereIn('storage_type', ['Room', 'Loan Tools', 'Department'])->get();
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tools' => 'required|array|min:1',
            'tools.*.name' => 'required|string',
            'tools.*.quantity' => 'required|integer|min:0',
            'tools.*.type_id' => 'nullable|exists:tools_types,id',
            'tools.*.manufacturer_id' => 'nullable|exists:manufacturers,id',
            'tools.*.location_id' => 'nullable|exists:locations,id',
            'tools.*.rack_id' => 'nullable|exists:racks,id',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $createdCount = 0;
            $batchId = (string) \Illuminate\Support\Str::uuid();

            foreach ($request->tools as $toolData) {
                // Generate Unique Code (MT)
                $toolData['code'] = $this->generateNextCode();

                // Generate Unique Asset No (TAS)
                $toolData['serial_number'] = $this->generateNextAssetNo();

                // Assign Batch ID
                $toolData['batch_id'] = $batchId;

                // Create Tool
                $tool = Tool::create($toolData);

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
                $createdCount++;
            }

            \Illuminate\Support\Facades\DB::commit();

            // Redirect Logic
            if ($createdCount > 1) {
                return response()->json([
                    'status' => 'success',
                    'message' => "$createdCount tools created successfully.",
                    'redirect' => route('tools.index')
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => "$createdCount tool created successfully.",
                'redirect' => route('tools.index')
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create tools: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printBatch($batchId)
    {
        $tools = Tool::with(['type', 'manufacturer', 'location', 'rack', 'subGroup.matGroup'])
            ->where('batch_id', $batchId)
            ->get();

        if ($tools->isEmpty()) {
            return redirect()->route('tools.index')->with('error', 'Batch not found.');
        }

        return view('master.tools.good-receipt-batch', compact('tools'));
    }

    private function generateNextCode()
    {
        $prefix = '26'; // Year Prefix
        // We need to check the DB *and* potentially any locked/reserved codes if high concurrency, 
        // but for this app, checking latest DB record is usually sufficient.
        // However, since we are doing a loop, we might collide if we just query DB.
        // A better approach is to lock or use a sequence, but for simplicity:
        // We will query, but since we are in a transaction, we should be careful.
        // Actually, for bulk import in a loop, we can just increment from the last one found ONCE
        // at the start, but here we do it per item. 
        // To avoid collision within the SAME request loop, we can't trust the DB query alone 
        // because the previous iteration's insert might not be visible or committed yet? 
        // Actually inside transaction it might be visible. Let's try direct query.

        $lastTool = Tool::withTrashed()->where('code', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(code) desc')
            ->orderBy('code', 'desc')
            ->first();

        $lastNum = 0;
        if ($lastTool) {
            if (str_starts_with($lastTool->code, $prefix)) {
                $lastNum = intval(substr($lastTool->code, strlen($prefix)));
            }
        }
        return $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
    }

    private function generateNextAssetNo()
    {
        $lastAsset = Tool::withTrashed()->where('serial_number', 'like', 'TAS%')
            ->orderByRaw('LENGTH(serial_number) desc')
            ->orderBy('serial_number', 'desc')
            ->first();

        $lastAssetNum = $lastAsset ? intval(substr($lastAsset->serial_number, 3)) : 0;
        return 'TAS' . str_pad($lastAssetNum + 1, 5, '0', STR_PAD_LEFT);
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
        // Updated filter to include other storage types
        $locations = Location::whereIn('storage_type', ['Room', 'Loan Tools', 'Department'])->get();
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
            'po_number' => 'nullable|string',
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
        $password = request('password_confirmation_val');

        if (!\Illuminate\Support\Facades\Hash::check($password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Incorrect password. Deletion cancelled.');
        }

        $tool = Tool::findOrFail($id);

        $tool->deleted_by = auth()->id();
        $tool->save();
        
        // Ensure all related tool items are marked with deleted_by before cascade deletes
        $tool->items()->update(['deleted_by' => auth()->id()]);

        $tool->delete();

        return redirect()->back()->with('success', 'Tool deleted successfully.');
    }

    public function goodReceipt($id)
    {
        $tool = Tool::with(['type', 'manufacturer', 'location', 'rack', 'subGroup.matGroup'])->findOrFail($id);
        return view('master.tools.good-receipt', compact('tool'));
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

        // Auto Generate Code if empty/dash
        if (empty($code) || $code === '-' || strtolower($code) === 'auto') {
            $code = $this->generateNextCode();
        }

        // Auto Generate Asset No if empty/dash
        if (empty($assetNo) || $assetNo === '-' || strtolower($assetNo) === 'auto') {
            $assetNo = $this->generateNextAssetNo();
        }

        // Basic validation
        if (empty($desc)) {
            // Check if it's just a trailing empty row
            if (empty(implode('', $data)))
                return [];
            throw new \Exception("Description is required.");
        }

        $typeName = isset($data[3]) ? $data[3] : '';
        $groupName = isset($data[4]) ? $data[4] : '';
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
                $acronym = '';
                foreach (explode(' ', trim(preg_replace('/[^A-Za-z0-9 ]/', '', $typeName))) as $word) {
                    if (!empty($word)) $acronym .= strtoupper($word[0]);
                }
                $baseCode = $acronym ?: 'TYP';
                $typeCode = $baseCode . '-' . rand(100, 999);
                while (ToolType::where('code', $typeCode)->exists()) {
                    $typeCode = $baseCode . '-' . rand(100, 999);
                }
                $type = ToolType::create([
                    'code' => $typeCode,
                    'name' => $typeName,
                    'description' => $typeName
                ]);
                $typeId = $type->id;
            }
        }

        $subGroupId = null;
        if ($groupName) {
            $subGroup = \App\Models\ToolsMatSubGroup::whereRaw('LOWER(TRIM(sub_description)) = ?', [strtolower($groupName)])
                ->orWhereRaw('LOWER(TRIM(sub_code)) = ?', [strtolower($groupName)])
                ->first();
                
            if ($subGroup) {
                $subGroupId = $subGroup->id;
            } else {
                $mainGroup = \App\Models\ToolsMatGroup::whereRaw('LOWER(TRIM(description)) = ?', [strtolower($groupName)])
                    ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($groupName)])
                    ->first();
                    
                if (!$mainGroup) {
                    $acronym = '';
                    foreach (explode(' ', trim(preg_replace('/[^A-Za-z0-9 ]/', '', $groupName))) as $word) {
                        if (!empty($word)) $acronym .= strtoupper($word[0]);
                    }
                    $baseCode = $acronym ?: 'MAT';
                    $mainCode = $baseCode . '-' . rand(100, 999);
                    while (\App\Models\ToolsMatGroup::where('code', $mainCode)->exists()) {
                        $mainCode = $baseCode . '-' . rand(100, 999);
                    }
                    $mainGroup = \App\Models\ToolsMatGroup::create([
                        'code' => $mainCode,
                        'description' => $groupName
                    ]);
                }
                
                $subAcronym = '';
                foreach (explode(' ', trim(preg_replace('/[^A-Za-z0-9 ]/', '', $groupName))) as $word) {
                    if (!empty($word)) $subAcronym .= strtoupper($word[0]);
                }
                $subBaseCode = $subAcronym ?: 'SUB';
                $subCode = $subBaseCode . '-' . rand(1000, 9999);
                while (\App\Models\ToolsMatSubGroup::where('sub_code', $subCode)->exists()) {
                    $subCode = $subBaseCode . '-' . rand(1000, 9999);
                }
                
                $subGroup = \App\Models\ToolsMatSubGroup::create([
                    'sub_code' => $subCode,
                    'mat_group_code' => $mainGroup->code,
                    'sub_description' => $groupName
                ]);
                
                $subGroupId = $subGroup->id;
            }
        }

        $manfId = null;
        if ($manfName) {
            $manf = Manufacturer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($manfName)])
                ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($manfName)])
                ->first();

            if (!$manf) {
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $manfName), 0, 3));
                if (empty($baseCode))
                    $baseCode = 'MFG';
                $manfCode = $baseCode . '-' . rand(100, 999);
                while (Manufacturer::where('code', $manfCode)->exists()) {
                    $manfCode = $baseCode . '-' . rand(100, 999);
                }
                $manf = Manufacturer::create([
                    'code' => $manfCode,
                    'name' => $manfName,
                ]);
            }
            $manfId = $manf->id;
        }

        $locId = null;
        $rackId = null;

        if ($locName) {
            $loc = Location::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($locName)])
                ->orWhereRaw('LOWER(TRIM(code)) = ?', [strtolower($locName)])
                ->first();

            if (!$loc) {
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $locName), 0, 3));
                if (empty($baseCode))
                    $baseCode = 'LOC';
                $locCode = $baseCode . '-' . rand(100, 999);
                while (Location::where('code', $locCode)->exists()) {
                    $locCode = $baseCode . '-' . rand(100, 999);
                }
                $lowerLocName = strtolower($locName);
                $detectedWarehouse = 'Indoor'; // Default to Indoor
                if (str_contains($lowerLocName, 'outdoor') || str_contains($lowerLocName, 'container')) {
                    $detectedWarehouse = 'Outdoor';
                }

                $loc = Location::create([
                    'code' => $locCode,
                    'name' => $locName,
                    'storage_type' => 'Loan Tools',
                    'warehouse' => $detectedWarehouse,
                    'total_bin' => 0
                ]);
            }
            $locId = $loc->id;

            if ($rackCode) {
                // Rack search needs to span the location
                $rack = \App\Models\Rack::where('location_id', $locId)
                    ->where(function ($q) use ($rackCode) {
                        $q->whereRaw('LOWER(TRIM(code)) = ?', [strtolower($rackCode)])
                            ->orWhereRaw('LOWER(TRIM(description)) = ?', [strtolower($rackCode)]);
                    })->first();

                if (!$rack) {
                    $rack = \App\Models\Rack::create([
                        'location_id' => $locId,
                        'code' => $rackCode,
                        'description' => $rackCode
                    ]);
                    $loc->increment('total_bin');
                }
                $rackId = $rack->id;
            }
        }

        // 2. Create or Update Tool
        $tool = Tool::updateOrCreate(
            ['code' => $code],
            [
                'serial_number' => $assetNo,
                'po_number' => isset($data[11]) ? $data[11] : null, // Add mapping for Column 11
                'name' => $desc,
                'type_id' => $typeId,
                'sub_group_id' => $subGroupId,
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
