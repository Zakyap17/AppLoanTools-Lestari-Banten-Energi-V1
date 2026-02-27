<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Agreement - {{ $firstLoan->loan_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .document-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* --- Master Table Wrapper --- */
        .wrapper-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        .wrapper-table td, .wrapper-table th {
            border: 1px solid #000;
            padding: 5px;
        }

        /* --- Header Section --- */
        .header-logo {
            width: 130px;
            text-align: center;
            vertical-align: middle;
            border-right: 2px solid #000;
        }

        .header-logo img {
            width: 100px;
            height: auto;
        }

        .header-title-container {
            text-align: center;
            vertical-align: middle;
            padding: 0 !important;
        }

        .header-company {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            padding: 5px 0 2px 0;
            border-bottom: 0;
        }

        .header-plant {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }

        .header-form-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            padding: 10px 0;
        }

        /* --- Metadata Row --- */
        .metadata-row {
            text-align: center;
            font-size: 9px;
            padding: 0 !important;
        }
        
        .metadata-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .metadata-table td {
            border: none;
            border-right: 1px solid #000;
            width: 20%;
            padding: 2px 5px;
        }
        
        .metadata-table td:last-child {
            border-right: none;
        }

        /* --- Info Section --- */
        .info-section {
            border: none !important;
            padding: 10px 5px !important;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .info-col {
            width: 48%;
        }

        .info-item {
            display: flex;
            margin-bottom: 5px;
            align-items: flex-end; /* Align to bottom for the dotted line effect */
        }

        .info-label {
            font-weight: bold;
            width: 110px;
            flex-shrink: 0;
        }
        
        .info-separator {
            margin: 0 5px;
            font-weight: bold;
        }

        .info-value {
            flex-grow: 1;
            font-family: "Courier New", Courier, monospace;
            font-size: 13px;
            border-bottom: 1px dotted #000;
            min-height: 15px;
            text-transform: uppercase;
        }

        /* --- Items Table --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .items-table th {
            font-weight: bold;
        }
        
        .items-table .td-left {
            text-align: left;
            padding-left: 5px;
        }

        /* Set specific column widths mimicking the paper form */
        .col-no { width: 30px; }
        .col-code { width: 80px; }
        .col-desc { width: auto; }
        .col-qty { width: 40px; }
        .col-date { width: 60px; }
        .col-sloc { width: 70px; }
        .col-remarks { width: 60px; }

        .items-table tbody tr {
            height: 25px; /* Fixed height for rows to look like ruled paper */
        }

        /* --- Notes Section --- */
        .notes-section {
            padding: 5px;
            font-weight: bold;
            font-size: 11px;
            min-height: 20px;
        }
        .notes-line {
            border-bottom: 1px dotted #000;
            margin-top: 5px;
            height: 15px;
            width: 100%;
        }

        /* --- Signatures Table --- */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #000;
        }

        .signatures-table th, .signatures-table td {
            border: 1px solid #000;
            width: 20%;
            text-align: center;
        }

        .signatures-table th {
            padding: 5px;
            font-weight: bold;
        }

        .signatures-table td {
            height: 80px;
            vertical-align: bottom;
            padding-bottom: 5px;
            position: relative;
        }
        
        .sign-name-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 90%;
            text-align: center;
            font-family: "Courier New", Courier, monospace;
            text-transform: uppercase;
        }

        /* --- Footer --- */
        .footer-notice {
            font-size: 9px;
            padding: 4px;
            border: 1px solid #000;
            border-top: none; /* Merges with the signature table */
            text-align: left;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="document-container">
        
        <table class="wrapper-table">
            <!-- HEADER LOGO & TITLE -->
            <tr>
                <td class="header-logo" rowspan="2">
                    <img src="{{ asset('images/logo_print_new.png') }}" alt="Logo">
                </td>
                <td class="header-title-container">
                    <div class="header-company">PT Lestari Banten Energi</div>
                    <div class="header-plant">Banten Power Plant</div>
                    <div class="header-form-title">Common Tools Borrowing Request</div>
                </td>
            </tr>
            
            <!-- METADATA ROW -->
            <tr>
                <td class="metadata-row">
                    <table class="metadata-table">
                        <tr>
                            <td>
                                <div>Responsible Person</div>
                                <div style="font-weight: bold;">Materials Management</div>
                            </td>
                            <td>
                                <div>Issue Date</div>
                                <div>01/02/2019</div>
                            </td>
                            <td>
                                <div>Revision Date</div>
                                <div>0</div>
                            </td>
                            <td>
                                <div>File No.</div>
                                <div>R03PMM550</div>
                            </td>
                            <td>
                                <div>PAGE</div>
                                <div>1 of 1</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <!-- INFO SECTION -->
            <tr>
                <td colspan="2" class="info-section">
                    <div class="info-grid">
                        <!-- Left Column -->
                        <div class="info-col">
                            <div class="info-item">
                                <div class="info-label">Loan Tools No.</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $firstLoan->loan_code }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Loan Tools Date</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ $firstLoan->loan_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Current Date</div>
                                <div class="info-separator">:</div>
                                <div class="info-value">{{ now()->format('d/m/Y') }}</div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="info-col">
                            <div class="info-item">
                                <div class="info-label">Reservation No.</div>
                                <div class="info-separator">:</div>
                                <div class="info-value"></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Order No.</div>
                                <div class="info-separator">:</div>
                                <div class="info-value"></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Cost Center No.</div>
                                <div class="info-separator">:</div>
                                <div class="info-value"></div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            
            <!-- ITEMS TABLE -->
            <tr>
                <td colspan="2" style="padding: 0; border: none;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="col-no">NO</th>
                                <th rowspan="2" class="col-code">Item Code</th>
                                <th rowspan="2" class="col-desc">Description</th>
                                <th colspan="2">Borrow</th>
                                <th colspan="2">Return</th>
                                <th rowspan="2" class="col-sloc">SLoc</th>
                                <th rowspan="2" class="col-remarks">Remarks</th>
                            </tr>
                            <tr>
                                <th class="col-qty">Qty</th>
                                <th class="col-date">Date</th>
                                <th class="col-qty">Qty</th>
                                <th class="col-date">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Ensure minimum rows to mimic the paper's empty lines
                                $minRows = max(18, count($loans));
                            @endphp
                            
                            @for($i = 0; $i < $minRows; $i++)
                                @if($i < count($loans))
                                    @php 
                                        $loan = $loans[$i]; 
                                        $borrowQty = $loan->loan_quantity > 0 ? $loan->loan_quantity : 1; 
                                        $returnQty = in_array($loan->status, ['returned', 'returned_damage', 'returned_lost']) ? $borrowQty : '';
                                        $returnDate = $loan->actual_return_date ? \Carbon\Carbon::parse($loan->actual_return_date)->format('d/m/y') : '';
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $loan->item ? $loan->item->serial_number : $loan->tool->code }}</td>
                                        <td class="td-left">{{ $loan->tool->name }}</td>
                                        
                                        <!-- Borrow -->
                                        <td>{{ $borrowQty }} EA</td>
                                        <td>{{ $loan->loan_date->format('d/m/y') }}</td>
                                        
                                        <!-- Return -->
                                        <td>{{ $returnQty ? $returnQty . ' EA' : '' }}</td>
                                        <td>{{ $returnDate }}</td>
                                        
                                        <!-- SLoc -->
                                        <td>{{ $loan->tool->location->name ?? '' }}</td>
                                        
                                        <!-- Remarks -->
                                        <td>{{ in_array($loan->status, ['returned', 'returned_damage', 'returned_lost']) ? 'OK' : '' }}</td>
                                    </tr>
                                @else
                                    <!-- Empty Row FILLER to look like real paper -->
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endfor
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <!-- NOTES & SIGNATURES WRAPPER -->
            <tr>
                <td colspan="2" style="padding: 0; border: none;">
                    <div class="notes-section">
                        Note: 
                        {{ $firstLoan->notes ?? '' }}
                        <div class="notes-line"></div>
                    </div>
                    
                    <table class="signatures-table">
                        <thead>
                            <tr>
                                <th>Received by</th>
                                <th>Approved by</th>
                                <th>Issued by</th>
                                <th>Returned by</th>
                                <th>Received by</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    ( <span class="sign-name-line">{{ $firstLoan->borrower->name ?? '&nbsp;' }}</span> )
                                </td>
                                <td>
                                    ( <span class="sign-name-line">&nbsp;</span> )
                                </td>
                                <td>
                                    ( <span class="sign-name-line">{{ $firstLoan->keeper->name ?? '&nbsp;' }}</span> )
                                </td>
                                <td>
                                    ( <span class="sign-name-line">{{ $firstLoan->borrower->name ?? '&nbsp;' }}</span> )
                                </td>
                                <td>
                                    ( <span class="sign-name-line">{{ $firstLoan->keeper->name ?? '&nbsp;' }}</span> )
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        
        <!-- FOOTER NOTICE -->
        <div class="footer-notice">
            We reserve all rights of this document and the information therein. Reproduction, use or disclosure to third parties
            without authority is strictly forbidden. © PT Lestari Banten Energi.
        </div>

    </div>

</body>
</html>