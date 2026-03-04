<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;

class ReportingCenterController extends Controller
{
    public function index(Request $request)
    {
        // Fetch data for filters
        $employees = Employee::orderBy('name')->get();
        $locations = Location::where('storage_type', 'Room')->orderBy('name')->get();

        $reportType = $request->query('report_type');
        $locationId = $request->query('location');
        $rackId = $request->query('rack');
        $toolId = $request->query('tool_id'); // Select2 Tool ID
        $search = $request->query('search'); // Global Search (Text)
        $employeeId = $request->query('employee');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Fetch the selected tool for display in Select2 (if any)
        $selectedTool = null;
        if ($toolId) {
            $selectedTool = \App\Models\Tool::find($toolId);
        }

        $data = [];

        if ($reportType === 'Stock Opname') {
            // Optimization: Use Left Joins instead of whereHas for performance
            $query = \App\Models\ToolItem::select('tool_items.*')
                ->join('tools', 'tool_items.tool_id', '=', 'tools.id')
                ->leftJoin('locations', 'tools.location_id', '=', 'locations.id')
                ->leftJoin('racks', 'tools.rack_id', '=', 'racks.id')
                ->with(['tool.location', 'tool.rack', 'activeDamageReport']);

            if ($locationId) {
                $query->where('tools.location_id', $locationId);
            }
            if ($rackId) {
                $query->where('tools.rack_id', $rackId);
            }
            if ($toolId) {
                $query->where('tool_items.tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('tools.code', 'ilike', "{$exactSearch}")
                        ->orWhere('tools.name', 'ilike', "%{$search}%")
                        ->orWhere('tools.serial_number', 'ilike', "{$exactSearch}")
                        ->orWhere('locations.name', 'ilike', "%{$search}%")
                        ->orWhere('racks.code', 'ilike', "%{$search}%")
                        ->orWhere('tool_items.serial_number', 'ilike', "{$exactSearch}");
                });
            }
            if ($employeeId) {
                $query->whereHas('tool.loans', function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId)->where('status', 'ongoing');
                });
            }

            $data = $query->latest('tool_items.created_at')->paginate(50)->withQueryString();
        } elseif ($reportType === 'Loan') {
            // Optimization: Use Left Joins for Loan Report too
            $query = \App\Models\Loan::select('loans.*')
                ->join('tools', 'loans.tool_id', '=', 'tools.id')
                ->leftJoin('locations', 'tools.location_id', '=', 'locations.id')
                ->leftJoin('racks', 'tools.rack_id', '=', 'racks.id')
                ->leftJoin('employees as borrowers', 'loans.employee_id', '=', 'borrowers.id')
                ->leftJoin('tool_items', 'loans.tool_item_id', '=', 'tool_items.id')
                ->with(['tool.location', 'tool.rack', 'borrower']);

            if ($locationId) {
                $query->where('tools.location_id', $locationId);
            }
            if ($rackId) {
                $query->where('tools.rack_id', $rackId);
            }
            if ($toolId) {
                $query->where('loans.tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('loans.loan_code', 'ilike', "{$exactSearch}")
                        ->orWhere('tools.code', 'ilike', "{$exactSearch}")
                        ->orWhere('tools.name', 'ilike', "%{$search}%")
                        ->orWhere('tools.serial_number', 'ilike', "{$exactSearch}")
                        ->orWhere('locations.name', 'ilike', "%{$search}%")
                        ->orWhere('racks.code', 'ilike', "%{$search}%")
                        ->orWhere('borrowers.name', 'ilike', "%{$search}%")
                        ->orWhere('tool_items.serial_number', 'ilike', "{$exactSearch}");
                });
            }
            if ($employeeId) {
                $query->where('loans.employee_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('loans.loan_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('loans.loan_date', '<=', $dateTo);
            }

            $data = $query->latest('loans.created_at')->paginate(50)->withQueryString();
        } elseif ($reportType === 'Return / Damage') {
            $query = \App\Models\DamageLost::with(['tool.location', 'tool.rack', 'employee']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('report_code', 'ilike', "{$exactSearch}")
                        ->orWhere('description', 'ilike', "%{$search}%")
                        ->orWhereHas('tool', function ($sub) use ($search, $exactSearch) {
                            $sub->where('code', 'ilike', "{$exactSearch}")
                                ->orWhere('name', 'ilike', "%{$search}%")
                                ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                                ->orWhereHas('location', function ($loc) use ($search) {
                                    $loc->where('name', 'ilike', "%{$search}%");
                                })
                                ->orWhereHas('rack', function ($rack) use ($search) {
                                    $rack->where('code', 'ilike', "%{$search}%");
                                });
                        })
                        ->orWhereHas('employee', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('item', function ($sub) use ($exactSearch) {
                            $sub->where('serial_number', 'ilike', "{$exactSearch}");
                        });
                });
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('report_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('report_date', '<=', $dateTo);
            }

            $data = $query->latest()->paginate(50)->withQueryString();
        } elseif ($reportType === 'Calibration') {
            $query = \App\Models\CalibrationIssuing::with(['tool.location', 'tool.rack', 'vendor']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('issue_code', 'ilike', "{$exactSearch}")
                        ->orWhereHas('tool', function ($sub) use ($search, $exactSearch) {
                            $sub->where('code', 'ilike', "{$exactSearch}")
                                ->orWhere('name', 'ilike', "%{$search}%")
                                ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                                ->orWhereHas('location', function ($loc) use ($search) {
                                    $loc->where('name', 'ilike', "%{$search}%");
                                })
                                ->orWhereHas('rack', function ($rack) use ($search) {
                                    $rack->where('code', 'ilike', "%{$search}%");
                                });
                        })
                        ->orWhereHas('vendor', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        });
                });
            }
            if ($dateFrom) {
                $query->whereDate('issue_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('issue_date', '<=', $dateTo);
            }

            $data = $query->latest()->paginate(50)->withQueryString();
        } elseif ($reportType === 'Tools Transaction') {
            $query = \App\Models\ToolItem::with(['tool.location', 'tool.rack', 'createdBy', 'loans', 'loans.borrower']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->whereHas('tool', function ($sub) use ($search, $exactSearch) {
                        $sub->where('code', 'ilike', "{$exactSearch}")
                            ->orWhere('name', 'ilike', "%{$search}%")
                            ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                            ->orWhere('po_number', 'ilike', "{$exactSearch}")
                            ->orWhereHas('location', function ($loc) use ($search) {
                                $loc->where('name', 'ilike', "%{$search}%");
                            })
                            ->orWhereHas('rack', function ($rack) use ($search) {
                                $rack->where('code', 'ilike', "%{$search}%");
                            });
                    })
                        ->orWhere('serial_number', 'ilike', "{$exactSearch}");
                });
            }
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $data = $query->latest()->paginate(50)->withQueryString();
        } elseif ($reportType === 'Mutation') {
            $query = \App\Models\MutationLog::with(['tool', 'fromLocation', 'fromRack', 'toLocation', 'toRack', 'user']);

            if ($locationId) {
                $query->where('to_location_id', $locationId);
            }
            if ($rackId) {
                $query->where('to_rack_id', $rackId);
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->whereHas('tool', function ($sub) use ($search, $exactSearch) {
                        $sub->where('code', 'ilike', "{$exactSearch}")
                            ->orWhere('name', 'ilike', "%{$search}%")
                            ->orWhere('serial_number', 'ilike', "{$exactSearch}");
                    })
                        ->orWhereHas('user', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('fromLocation', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('toLocation', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhere('reason', 'ilike', "%{$search}%");
                });
            }
            if ($employeeId) {
                $query->where('user_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $data = $query->latest()->paginate(50)->withQueryString();
        }

        return view('reporting.index', compact('employees', 'locations', 'data', 'reportType', 'selectedTool'));
    }

    public function exportExcel(Request $request)
    {
        $reportType = $request->query('report_type');

        if (!$reportType) {
            return redirect()->back()->with('error', 'Please select a report type first');
        }

        // Get the same filtered data as index method
        $locationId = $request->query('location');
        $rackId = $request->query('rack');
        $toolId = $request->query('tool_id');
        $search = $request->query('search'); // Global Search
        $employeeId = $request->query('employee');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $data = [];
        $headers = [];
        $filename = '';

        // Prepare data based on report type
        if ($reportType === 'Stock Opname') {
            $query = \App\Models\ToolItem::with(['tool.location', 'tool.rack', 'activeDamageReport']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->whereHas('tool', function ($sub) use ($search, $exactSearch) {
                        $sub->where('code', 'ilike', "{$exactSearch}")
                            ->orWhere('name', 'ilike', "%{$search}%")
                            ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                            ->orWhereHas('location', function ($loc) use ($search) {
                                $loc->where('name', 'ilike', "%{$search}%");
                            })
                            ->orWhereHas('rack', function ($rack) use ($search) {
                                $rack->where('code', 'ilike', "%{$search}%");
                            });
                    })
                        ->orWhere('serial_number', 'ilike', "{$exactSearch}");
                });
            }

            $items = $query->latest()->get();
            $headers = ['No', 'Storage', 'Bin', 'Tool No', 'Description', 'Asset No (Serial)', 'UOM', 'Qty', 'Status'];

            foreach ($items as $index => $item) {
                $status = ucfirst(str_replace('_', ' ', $item->status));
                if ($item->status === 'damage' && $item->activeDamageReport && $item->activeDamageReport->is_repairable) {
                    $status = 'In Service';
                }

                $data[] = [
                    $index + 1,
                    $item->tool?->location?->name ?? '-',
                    $item->tool?->rack?->code ?? '-',
                    $item->tool?->code ?? '-',
                    $item->tool?->name ?? '-',
                    $item->serial_number ?? '-',
                    $item->tool?->uom ?? '-',
                    1,
                    $status
                ];
            }
            $filename = 'Stock_Opname_Report_' . date('Y-m-d_His') . '.xlsx';

        } elseif ($reportType === 'Loan') {
            $query = \App\Models\Loan::with(['tool.location', 'tool.rack', 'borrower']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('loan_code', 'ilike', "{$exactSearch}")
                        ->orWhereHas('tool', function ($sub) use ($search, $exactSearch) {
                            $sub->where('code', 'ilike', "{$exactSearch}")
                                ->orWhere('name', 'ilike', "%{$search}%")
                                ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                                ->orWhereHas('location', function ($loc) use ($search) {
                                    $loc->where('name', 'ilike', "%{$search}%");
                                })
                                ->orWhereHas('rack', function ($rack) use ($search) {
                                    $rack->where('code', 'ilike', "%{$search}%");
                                });
                        })
                        ->orWhereHas('borrower', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('item', function ($sub) use ($search) {
                            $sub->where('serial_number', 'ilike', "%{$search}%");
                        });
                });
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('loan_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('loan_date', '<=', $dateTo);
            }

            $loans = $query->latest()->get();
            $headers = ['No', 'Storage', 'Bin', 'Tool No', 'Description', 'Asset No (Serial)', 'UOM', 'Qty', 'Employee Name'];

            foreach ($loans as $index => $loan) {
                $data[] = [
                    $index + 1,
                    $loan->tool->location->name ?? '-',
                    $loan->tool->rack->code ?? '-',
                    $loan->tool->code ?? '-',
                    $loan->tool->name ?? '-',
                    $loan->tool->serial_number ?? '-',
                    $loan->tool->uom ?? '-',
                    $loan->loan_quantity,
                    $loan->borrower->name ?? '-'
                ];
            }
            $filename = 'Loan_Report_' . date('Y-m-d_His') . '.xlsx';

        } elseif ($reportType === 'Return / Damage') {
            $query = \App\Models\DamageLost::with(['tool.location', 'tool.rack', 'employee']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->where('report_code', 'ilike', "{$exactSearch}")
                        ->orWhere('description', 'ilike', "%{$search}%")
                        ->orWhereHas('tool', function ($sub) use ($search, $exactSearch) {
                            $sub->where('code', 'ilike', "{$exactSearch}")
                                ->orWhere('name', 'ilike', "%{$search}%")
                                ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                                ->orWhereHas('location', function ($loc) use ($search) {
                                    $loc->where('name', 'ilike', "%{$search}%");
                                })
                                ->orWhereHas('rack', function ($rack) use ($search) {
                                    $rack->where('code', 'ilike', "%{$search}%");
                                });
                        })
                        ->orWhereHas('employee', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('item', function ($sub) use ($exactSearch) {
                            $sub->where('serial_number', 'ilike', "{$exactSearch}");
                        });
                });
            }
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('report_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('report_date', '<=', $dateTo);
            }

            $reports = $query->latest()->get();
            $headers = ['No', 'Document No', 'Tools Room', 'Bin Location', 'Item Code', 'Asset No', 'Item Description', 'Qty', 'Employee Name', 'Status'];

            foreach ($reports as $index => $report) {
                $data[] = [
                    $index + 1,
                    $report->report_code,
                    $report->tool->location->name ?? '-',
                    $report->tool->rack->code ?? '-',
                    $report->tool->code ?? '-',
                    $report->tool->code ?? '-', // Redundant Item Code/Asset No in current code, keeping as is
                    $report->tool->name ?? '-',
                    $report->quantity,
                    $report->employee->name ?? '-',
                    ($report->type === 'lost') ? 'Lost' : (($report->status === 'open' && $report->type === 'damage') ? ($report->is_repairable ? 'In Service' : 'Unrepairable') : ucfirst($report->status))
                ];
            }
            $filename = 'Damage_Lost_Report_' . date('Y-m-d_His') . '.xlsx';

        } elseif ($reportType === 'Tools Transaction') {
            $query = \App\Models\ToolItem::with(['tool.location', 'tool.rack', 'createdBy', 'loans', 'loans.borrower']);

            if ($locationId) {
                $query->whereHas('tool', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                });
            }
            if ($rackId) {
                $query->whereHas('tool', function ($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                });
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->whereHas('tool', function ($sub) use ($search, $exactSearch) {
                        $sub->where('code', 'ilike', "{$exactSearch}")
                            ->orWhere('name', 'ilike', "%{$search}%")
                            ->orWhere('serial_number', 'ilike', "{$exactSearch}")
                            ->orWhere('po_number', 'ilike', "{$exactSearch}")
                            ->orWhereHas('location', function ($loc) use ($search) {
                                $loc->where('name', 'ilike', "%{$search}%");
                            })
                            ->orWhereHas('rack', function ($rack) use ($search) {
                                $rack->where('code', 'ilike', "%{$search}%");
                            });
                    })
                        ->orWhere('serial_number', 'ilike', "{$exactSearch}");
                });
            }
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $items = $query->latest()->get();
            $headers = ['No', 'Receive Date', 'PO Number', 'Storage', 'Bin', 'Tool No', 'Description', 'Asset No', 'Condition', 'Qty', 'Receiver'];

            foreach ($items as $index => $item) {
                $data[] = [
                    $index + 1,
                    $item->created_at ? $item->created_at->format('d M Y') : '-',
                    $item->tool?->po_number ?? '-',
                    $item->tool?->location?->name ?? '-',
                    $item->tool?->rack?->code ?? '-',
                    $item->tool?->code ?? '-',
                    $item->tool?->name ?? '-',
                    $item->serial_number ?? '-',
                    $item->condition ?? 'Good',
                    1,
                    $item->createdBy?->name ?? '-'
                ];
            }
            $filename = 'Tools_Transaction_Report_' . date('Y-m-d_His') . '.xlsx';

        } elseif ($reportType === 'Mutation') {
            $query = \App\Models\MutationLog::with(['tool', 'fromLocation', 'fromRack', 'toLocation', 'toRack', 'user']);

            if ($locationId) {
                $query->where('to_location_id', $locationId);
            }
            if ($rackId) {
                $query->where('to_rack_id', $rackId);
            }
            if ($toolId) {
                $query->where('tool_id', $toolId);
            }
            if ($search) {
                $exactSearch = trim($search);
                $query->where(function ($q) use ($search, $exactSearch) {
                    $q->whereHas('tool', function ($sub) use ($search, $exactSearch) {
                        $sub->where('code', 'ilike', "{$exactSearch}")
                            ->orWhere('name', 'ilike', "%{$search}%")
                            ->orWhere('serial_number', 'ilike', "{$exactSearch}");
                    })
                        ->orWhereHas('user', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('fromLocation', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('toLocation', function ($sub) use ($search) {
                            $sub->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhere('reason', 'ilike', "%{$search}%");
                });
            }
            if ($employeeId) {
                $query->where('user_id', $employeeId);
            }
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $logs = $query->latest()->get();
            $headers = ['No', 'Mutation From (Storage)', 'Mutation From (Bin)', 'Mutation To (Storage)', 'Mutation To (Bin)', 'Item Code', 'Asset No', 'Item Description', 'Qty', 'Keeper Name', 'Notes'];

            foreach ($logs as $index => $log) {
                $data[] = [
                    $index + 1,
                    $log->fromLocation->name ?? '-',
                    $log->fromRack->code ?? '-',
                    $log->toLocation->name ?? '-',
                    $log->toRack->code ?? '-',
                    $log->tool->code ?? '-',
                    $log->tool->code ?? '-',
                    $log->tool->name ?? '-',
                    $log->tool->quantity ?? 1,
                    $log->user->name ?? '-',
                    $log->reason
                ];
            }
            $filename = 'Mutation_Report_' . date('Y-m-d_His') . '.xlsx';
        } else {
            return redirect()->back()->with('error', 'Report type not supported for export');
        }

        // GENERATE EXCEL WITH PHPSPREADSHEET
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Set Header Row
        $sheet->fromArray($headers, NULL, 'A1');

        // 2. Format Header (Bold, Centered, Gray Background)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1a202c'] // Dark color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        // Calculate last column letter (e.g., 'J')
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyle);

        // 3. Set Data
        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        // 4. Auto Size Columns
        foreach (range('A', $highestColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 5. Add Borders to Data
        $lastRow = count($data) + 1;
        if ($lastRow > 1) {
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A2:' . $highestColumn . $lastRow)->applyFromArray($dataStyle);
        }

        // 6. Output as Download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $response = response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);

        return $response;
    }
}
