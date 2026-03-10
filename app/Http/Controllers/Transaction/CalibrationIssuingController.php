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

    public function template()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Define columns
        $columns = ['Tool Code', 'Vendor Name', 'Issue Date (YYYY-MM-DD)', 'Expected Return Date (YYYY-MM-DD)', 'Cost', 'Notes'];
        
        // Set Headers and adjust width manually before auto-sizing to prevent collapse
        $colIndex = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($colIndex . '1', $column);
            $sheet->getColumnDimension($colIndex)->setAutoSize(true);
            $colIndex++;
        }

        // Style the Header Row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0D6EFD'], // Bootstrap Primary Blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add Sample Data
        $sampleData = [
            ['2600001', 'PT. Vendor Kalibrasi A', date('Y-m-d'), date('Y-m-d', strtotime('+15 days')), '500000', 'Annual Calibration'],
            ['2600002', 'CV. Global Teknik', date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), '350000', 'Routine Check'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $colIndex = 'A';
            foreach ($data as $value) {
                // Ensure text format for numbers to prevent scientific notation and date shifts
                $sheet->setCellValueExplicit($colIndex . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $colIndex++;
            }
            $row++;
        }
        
        // Data alignment & subtle borders
        $dataStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ],
        ];
        $sheet->getStyle('A2:F3')->applyFromArray($dataStyle);

        // Lock the header row so it stays visible when scrolling down
        $sheet->freezePane('A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Calibration_Issuing_Template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getPathname();
        $extension = strtolower($file->getClientOriginalExtension());
        $validExtensions = ['csv', 'txt', 'xlsx', 'xls'];

        if (!in_array($extension, $validExtensions)) {
            return redirect()->back()->with('error', 'Invalid file type. Supported: CSV, TXT, XLSX, XLS.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            /** @var \PhpOffice\PhpSpreadsheet\Reader\BaseReader */
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);

            if (in_array($extension, ['csv', 'txt'])) {
                $reader->setReadDataOnly(true);
                if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
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

            $successCount = 0;
            $errors = [];
            $warnings = [];

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex == 1) continue; // Skip header

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = trim($cell->getValue());
                }

                if (empty(array_filter($data))) continue;

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
                if (count($errors) > 5) $errorMsg .= '<br>...and ' . (count($errors) - 5) . ' more errors.';
                return redirect()->back()->with('error', 'Import failed with errors:<br>' . $errorMsg);
            }

            \Illuminate\Support\Facades\DB::commit();

            $msg = "Import successful! Processed $successCount records.";
            if (count($warnings) > 0) {
                $uniqueWarnings = array_unique($warnings);
                $warningMsg = implode('<br>', array_slice($uniqueWarnings, 0, 5));
                if (count($uniqueWarnings) > 5) $warningMsg .= '<br>...and ' . (count($uniqueWarnings) - 5) . ' more warnings.';
                return redirect()->back()->with('warning', $msg . '<br><strong>Warnings:</strong><br>' . $warningMsg);
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Calibration Import Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'System Error during import: ' . $e->getMessage());
        }
    }

    private function importRow($data, $rowLine)
    {
        $warnings = [];
        // Mapping: 0=Tool Code, 1=Vendor Name, 2=Issue Date, 3=Expected Return Date, 4=Cost, 5=Notes
        $toolCode = isset($data[0]) ? $data[0] : '';
        $vendorName = isset($data[1]) ? $data[1] : '';
        
        $issueDateRaw = isset($data[2]) ? $data[2] : '';
        $expectedDateRaw = isset($data[3]) ? $data[3] : '';
        
        $costRaw = isset($data[4]) ? $data[4] : '';
        $notes = isset($data[5]) ? $data[5] : '';

        if (empty($toolCode)) {
            throw new \Exception("Tool Code is required.");
        }
        if (empty($vendorName)) {
            throw new \Exception("Vendor Name is required.");
        }
        if (empty($issueDateRaw)) {
            throw new \Exception("Issue Date is required.");
        }

        // 1. Lookup Tool
        $tool = \App\Models\Tool::where('code', $toolCode)->first();
        if (!$tool) {
            throw new \Exception("Tool with Code '{$toolCode}' not found.");
        }

        // 2. Lookup or Create Vendor
        $vendor = \App\Models\VendorCalibration::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($vendorName)])->first();
        if (!$vendor) {
            $vendor = \App\Models\VendorCalibration::create([
                'name' => $vendorName, // Assuming default fields are fine
            ]);
        }

        // 3. Format Dates
        $issueDate = $this->parseExcelDate($issueDateRaw);
        $expectedDate = $this->parseExcelDate($expectedDateRaw);
        if (!$issueDate) {
            throw new \Exception("Invalid Issue Date format.");
        }

        // 4. Generate Doc No: CI-YYYYMMDD-XXXX
        $lastDoc = \App\Models\CalibrationIssuing::where('issue_code', 'like', 'CI-' . date('Ymd') . '-%')->orderBy('id', 'desc')->first();
        $lastNum = $lastDoc ? intval(substr($lastDoc->issue_code, -4)) : 0;
        $docNo = 'CI-' . date('Ymd') . '-' . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);

        $cost = is_numeric($costRaw) ? (float) $costRaw : null;

        // 5. Create Calibration Issuing
        \App\Models\CalibrationIssuing::create([
            'issue_code' => $docNo,
            'tool_id' => $tool->id,
            'vendor_calibration_id' => $vendor->id,
            'issue_date' => $issueDate,
            'expected_completion_date' => $expectedDate,
            'cost' => $cost,
            'notes' => $notes,
            'status' => 'in_progress'
        ]);

        // 6. Update tool status
        $tool->update([
            'status' => 'calibration',
            'next_calibration_date' => $expectedDate ?: $tool->next_calibration_date,
        ]);

        return $warnings;
    }

    private function parseExcelDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
