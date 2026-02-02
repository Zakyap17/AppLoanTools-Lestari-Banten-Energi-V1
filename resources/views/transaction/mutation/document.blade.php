<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutation Document - {{ $log->id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            background: #e0e0e0;
            padding: 20px;
            color: #000;
        }

        .document-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .document-page {
            padding: 15mm;
            min-height: 297mm;
            position: relative;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .doc-title {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            margin-bottom: 5px;
        }

        .doc-meta {
            font-size: 11pt;
            line-height: 1.4;
        }

        .divider {
            border: 0;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        /* INFO TABLES */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #000;
        }

        .info-table td {
            padding: 10px;
            border: 1px solid #000;
        }

        .box-header {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .box-content {
            font-size: 11pt;
            line-height: 1.4;
        }

        /* DATA TABLE */
        .section-header {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-size: 11pt;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11pt;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center !important;
        }

        .reason-cell {
            background-color: #f9f9f9;
            font-style: italic;
        }

        /* USER INFO */
        .user-info-section {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .user-info-box {
            font-size: 11pt;
        }

        .user-info-box .label {
            font-weight: bold;
            margin-right: 5px;
        }

        .footer-note {
            margin-top: 40px;
            font-size: 9pt;
            color: #555;
            font-style: italic;
            text-align: center;
        }

        /* PRINT */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-family: inherit;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .document-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }

            .document-page {
                padding: 0;
                min-height: auto;
            }

            .no-print {
                display: none;
            }

            .header-table,
            .info-table,
            .data-table {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <button onclick="window.print()" class="print-button no-print">
        <i class="bi bi-printer"></i> Print Document
    </button>

    <div class="document-container">
        <div class="document-page">
            <!-- FORMAL HEADER -->
            <!-- FORMAL HEADER -->
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo" style="margin-bottom: 15px;">
                <h1 class="doc-title">MUTATION FORM</h1>
                <div class="doc-meta">
                    No: MUT-{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }} <br>
                    Date: {{ $log->created_at->format('d F Y') }}
                </div>
            </div>

            <hr class="divider">

            <!-- MOVEMENT INFO -->
            <table class="info-table">
                <tr>
                    <td width="50%" valign="top">
                        <div class="box-header">FROM (ORIGIN)</div>
                        <div class="box-content">
                            <strong>{{ $log->fromLocation->name ?? '-' }}</strong><br>
                            Rack/Bin: {{ $log->fromRack->code ?? '-' }}
                        </div>
                    </td>
                    <td width="50%" valign="top">
                        <div class="box-header">TO (DESTINATION)</div>
                        <div class="box-content">
                            <strong>{{ $log->toLocation->name ?? '-' }}</strong><br>
                            Rack/Bin: {{ $log->toRack->code ?? '-' }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- ITEM DETAILS -->
            <div class="section-header">ITEM DETAILS</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="20%">ITEM CODE</th>
                        <th width="40%">ITEM NAME</th>
                        <th width="20%">QUANTITY</th>
                        <th width="20%">UOM</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $log->tool->code ?? '-' }}</td>
                        <td>{{ $log->tool->name ?? '-' }}</td>
                        <td class="text-center">{{ $log->tool->quantity ?? 1 }}</td>
                        <td class="text-center">{{ $log->tool->uom ?? 'Unit' }}</td>
                    </tr>
                    @if($log->reason)
                        <tr>
                            <td colspan="4" class="reason-cell">
                                <strong>Reason/Notes:</strong> <br>
                                {{ $log->reason }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- USER INFO -->
            <div class="user-info-section">
                <div class="user-info-box">
                    <span class="label">Processed By:</span>
                    <span class="value">{{ $log->user->name ?? '-' }}</span>
                </div>
                <div class="user-info-box">
                    <span class="label">Date:</span>
                    <span class="value">{{ $log->created_at->format('d F Y, H:i') }}</span>
                </div>
            </div>

            <div class="footer-note">
                * This document is computer generated and valid for warehouse administration.
            </div>
        </div>
    </div>
</body>

</html>