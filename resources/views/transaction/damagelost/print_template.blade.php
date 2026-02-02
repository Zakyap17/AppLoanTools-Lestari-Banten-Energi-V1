<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($report->type === 'damage' && $report->is_repairable) ? 'Repair Job Ticket' : 'Tools Report' }} -
        {{ $report->report_code }}
    </title>
    <style>
        @if($report->type === 'damage' && $report->is_repairable)
            /* REPAIR TICKET STYLES */
            body {
                font-family: Arial, sans-serif;
                line-height: 1.5;
                color: #000;
                padding: 20px;
            }

            .ticket-container {
                page-break-after: always;
                margin-bottom: 20px;
                border-bottom: 1px dashed #ccc;
                padding-bottom: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }

            .header h1 {
                margin: 0;
                font-size: 24px;
                text-transform: uppercase;
            }

            .header p {
                margin: 5px 0 0;
                font-size: 14px;
            }

            .content {
                margin-bottom: 30px;
            }

            .section-title {
                font-weight: bold;
                background-color: #eee;
                padding: 5px 10px;
                margin-bottom: 10px;
                border-left: 5px solid #000;
            }

            .row {
                display: flex;
                margin-bottom: 15px;
            }

            .col {
                flex: 1;
            }

            .label {
                font-weight: bold;
                display: block;
                font-size: 12px;
                text-transform: uppercase;
                color: #555;
            }

            .value {
                font-size: 16px;
                font-weight: 500;
            }

            .description-box {
                border: 1px solid #ccc;
                padding: 15px;
                min-height: 100px;
                background: #fafafa;
            }

            @media print {
                .ticket-container {
                    border-bottom: none;
                    height: 100vh;
                    margin-bottom: 0;
                }
            }

        @else

            /* OFFICIAL REPORT STYLES */
            body {
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
                margin: 0;
                padding: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            .table-bordered td,
            .table-bordered th {
                border: 1px solid black;
                padding: 4px;
            }

            .text-center {
                text-align: center;
            }

            .text-bold {
                font-weight: bold;
            }

            .header-table {
                margin-bottom: 20px;
            }

            .header-logo {
                width: 80px;
            }

            .info-section {
                margin-bottom: 20px;
            }

            .info-row {
                margin-bottom: 5px;
            }

            .info-label {
                display: inline-block;
                width: 120px;
                font-weight: bold;
            }

            .main-table {
                margin-bottom: 40px;
            }

            .main-table th {
                background-color: #ffff00 !important;
                text-align: center;
                font-weight: bold;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer-disclaimer {
                border: 1px solid black;
                padding: 5px;
                font-size: 10px;
                margin-top: 20px;
            }

            .signatures-table {
                width: 100%;
                margin-top: 20px;
                border: 1px solid black;
            }

            .signatures-table td {
                vertical-align: top;
                padding: 5px;
                height: 100px;
                border: 1px solid black;
                width: 25%;
            }

            .sig-header {
                font-weight: bold;
                border-bottom: none;
                height: auto !important;
            }

        @endif
    </style>
</head>

<body {{ (isset($mode) && $mode === 'view') ? '' : 'onload="window.print()"' }}>

    @if($report->type === 'damage' && $report->is_repairable)
        {{-- REPAIR JOB TICKET LAYOUT --}}
        {{-- Iterate over all selected reports (items) even if they share the same Code --}}
        @foreach($reports as $rpt)
            <div class="ticket-container">
                <div class="header">
                    <img src="{{ asset('assets/img/logo_print.png') }}" alt="Logo" style="height: 80px; margin-bottom: 10px;">
                    <h1>Repair Evidence</h1>
                    <p>Document No: {{ $rpt->report_code }} | Date:
                        {{ \Carbon\Carbon::parse($rpt->report_date)->format('d F Y') }}
                    </p>
                </div>

                <div class="content">
                    <div class="section-title">Item Details</div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Tool / Asset Name</span>
                            <div class="value">{{ $rpt->tool->name }}</div>
                        </div>
                        <div class="col">
                            <span class="label">Serial Number</span>
                            <div class="value">{{ $rpt->item->serial_number ?? '-' }}</div>
                        </div>
                        <div class="col">
                            <span class="label">Storage Location</span>
                            <div class="value">{{ $rpt->tool->location->name ?? '-' }} ({{ $rpt->tool->rack->code ?? '-' }})
                            </div>
                        </div>
                    </div>

                    <div class="section-title">Damage Details</div>
                    <div class="row">
                        <div class="col">
                            <span class="label">Reported By</span>
                            <div class="value">{{ $rpt->employee->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="col">
                            <span class="label">Severity</span>
                            <div class="value" style="text-transform: capitalize;">{{ $rpt->severity }}</div>
                        </div>
                        <div class="col">
                            <span class="label">Quantity</span>
                            <div class="value">{{ $rpt->quantity }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <span class="label">Chronology / Description</span>
                            <div class="description-box">
                                <strong>Description / Chronology:</strong><br>
                                {{ $rpt->description ?: 'No description provided.' }}

                                @if($rpt->resolution_evidence)
                                    <div style="margin-top: 15px; text-align: center;">
                                        <img src="{{ asset('storage/' . $rpt->resolution_evidence) }}" alt="Evidence"
                                            style="max-width: 100%; max-height: 300px; border: 1px solid #ddd;">
                                        <p style="font-size: 12px; color: #777;">Evidence</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    @else
        {{-- OFFICIAL TOOLS DAMAGE REPORT LAYOUT (Unrepairable / Lost) --}}

        {{-- Header Table --}}
        <table class="table-bordered header-table">
            <tr>
                <td rowspan="2" class="text-center" style="width: 20%;">
                    <img src="{{ asset('assets/img/logo_print.png') }}" class="header-logo" alt="Logo">
                </td>
                <td colspan="4" class="text-center text-bold" style="font-size: 16px;">
                    PT. Lestari Banten Energi<br>
                    <span style="font-size: 14px; font-weight: normal;">Banten Power Plant</span>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-center text-bold" style="font-size: 14px; padding: 10px;">
                    {{ $report->type === 'lost' ? 'Tools Lost Report' : 'Tools Damage Report' }}
                </td>
            </tr>
            <tr class="text-center" style="font-size: 10px;">
                <td style="width: 20%;">Responsible person</td>
                <td style="width: 20%;">Issue Date :</td>
                <td style="width: 15%;">Revision No :</td>
                <td style="width: 35%;">File No.</td>
                <td style="width: 10%;">PAGE</td>
            </tr>
            <tr class="text-center text-bold" style="font-size: 11px;">
                <td>MM</td> {{-- Responsible Person Static --}}
                <td>01/02/2019</td> {{-- Issue Date Static (ISO Form Date) --}}
                <td>0</td> {{-- Revision No Static --}}
                <td>R01PMM550</td> {{-- File No Static (ISO Form No - Assumed same for now) --}}
                <td>1 of 1</td>
            </tr>
        </table>

        <div style="margin-bottom: 15px; font-weight: bold; text-decoration: underline;">
            {{ $report->type === 'lost' ? 'TOOL LOSS REPORT' : 'TOOL DAMAGE REPORT' }}
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">NAME</span>: {{ $report->employee->name ?? '-' }}
            </div>
            <div class="info-row">
                <span class="info-label">POSITION</span>: {{ $report->employee->position ?? 'Staff' }}
            </div>
            <div class="info-row">
                <span class="info-label">DEPARTEMENT</span>: {{ $report->employee->department ?? 'Maintenance/IT' }}
            </div>
            <div class="info-row">
                <span class="info-label">DATE</span>: {{ \Carbon\Carbon::parse($report->report_date)->format('d F Y') }}
            </div>
        </div>

        {{-- Main Data Table --}}
        <table class="table-bordered main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 15%;">ITEM CODE</th>
                    <th style="width: 30%;">ITEM DESCRIPTION</th>
                    <th style="width: 5%;">QTY</th>
                    <th style="width: 15%;">UNIT</th>
                    <th style="width: 30%;">{{ $report->type === 'lost' ? 'DESCRIPTION OF LOSS' : 'DESCRIPTION OF DAMAGE' }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $index => $rpt)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}.</td>
                        <td>{{ $rpt->tool->code }}</td>
                        <td>{{ $rpt->tool->name }}</td>
                        <td class="text-center">{{ $rpt->quantity }}</td>
                        <td>{{ $rpt->item->serial_number ?? 'UNIT' }}</td>
                        <td>{{ $rpt->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Signatures Table --}}
        <table class="signatures-table">
            <thead>
                <tr>
                    <td class="sig-header">Reported by</td>
                    <td class="sig-header">Verified by</td>
                    <td class="sig-header">Approved by</td>
                    <td class="sig-header">Acknowledged by</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <br><br><br>
                        Sign: ___________________<br>
                        Name: {{ $report->employee->name ?? '_' }}<br>
                        Section: {{ $report->employee->department ?? '_' }}<br>
                        Date: {{ \Carbon\Carbon::parse($report->report_date)->format('d/m/Y') }}
                    </td>
                    <td>
                        <br><br><br>
                        Sign: ___________________<br>
                        Name:<br>
                        Section:<br>
                        Date:
                    </td>
                    <td>
                        <br><br><br>
                        Sign: ___________________<br>
                        Name:<br>
                        Section:<br>
                        Date:
                    </td>
                    <td>
                        <br><br><br>
                        Sign: ___________________<br>
                        Name:<br>
                        Section:<br>
                        Date:
                    </td>
                </tr>
                <tr>
                    <td style="border-top: none; height: auto; font-weight: bold;">Borrower</td>
                    <td style="border-top: none; height: auto; font-weight: bold;">Department Manager</td>
                    <td style="border-top: none; height: auto; font-weight: bold;">Head of Department</td>
                    <td style="border-top: none; height: auto; font-weight: bold;">Material Management</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 5px;">
            <b>Proposed Action by Department Manager</b>
            <div style="border: 1px solid black; height: 100px; margin-top: 5px;"></div>
        </div>

        <div class="footer-disclaimer">
            We reserve all rights of this document and the information therein. Reproduction, use or disclosure to third
            parties without authority is strictly forbidden. © PT Lestari Banten Energi.
        </div>
    @endif

</body>

</html>