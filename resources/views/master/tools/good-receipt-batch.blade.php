<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&nbsp;</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        .container {
            padding: 0;
        }

        /* Header Layout */
        .header-top {
            text-align: center;
            margin-bottom: 5px;
        }

        .header-top img {
            height: 60px;
            margin-bottom: 5px;
        }

        .header-top h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header-top p {
            margin: 0;
            font-size: 13px;
            font-weight: normal;
        }

        .divider {
            border-top: 2.5px solid #000;
            margin: 10px 0 15px 0;
        }

        /* Info Grid */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 2px 0;
            font-size: 11px;
        }

        .info-grid .label-row {
            margin-bottom: 4px;
        }

        .info-grid .label {
            display: inline-block;
            width: 105px;
            font-weight: bold;
        }

        .info-grid .value {
            display: inline-block;
        }

        /* Main Table */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }

        .content-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
        }

        .content-table td.center {
            text-align: center;
        }

        .content-table td.right {
            text-align: right;
        }

        /* Notes & Signatures */
        .notes-section {
            margin-top: 15px;
            margin-bottom: 50px;
        }

        .notes-section h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 100px;
        }

        .signature-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-box p {
            margin: 0;
        }

        .signature-label {
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">Download
            / Print PDF</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 5px;">Close</button>
    </div>

    <div class="container">
        <!-- Logo and Title -->
        <div class="header-top">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <img src="{{ asset('assets/images/logo-new.png') }}"
                    onerror="this.src='/assets/images/logo-new.png'; this.onerror=null;" alt="Logo"
                    style="height: 60px;">
                <h2 style="margin: 0 0 0 15px; font-size: 18px; font-weight: bold; color: #000;">PT. Lestari Banten
                    Energi</h2>
            </div>
            <h1>GOOD RECEIPT DOCUMENT</h1>
            <p>Bukti Penerimaan Barang</p>
        </div>

        <div class="divider"></div>

        <!-- Meta Info -->
        <table class="info-grid">
            <tr>
                <td>
                    <div class="label-row"><span class="label">MT No.</span><span class="value">:
                            {{ $tools->first()->code }}</span></div>
                    <div class="label-row"><span class="label">PO Number</span><span class="value">:
                            {{ $tools->first()->po_number ?? '-' }}</span></div>
                    <div class="label-row"><span class="label">Asset / Ref No.</span><span class="value">:
                            {{ $tools->first()->serial_number ?? '-' }}</span></div>
                </td>
                <td>
                    <div class="label-row"><span class="label">Date Received</span><span class="value">:
                            {{ $tools->first()->created_at->format('d F Y') }}</span></div>
                    <div class="label-row"><span class="label">Tools Room</span><span class="value">:
                            {{ $tools->first()->location->name ?? '-' }}</span></div>
                    <div class="label-row"><span class="label">Bin Location</span><span class="value">:
                            {{ $tools->first()->rack->code ?? '-' }}</span></div>
                </td>
            </tr>
        </table>

        <!-- Main Items Table -->
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Item Description</th>
                    <th style="width: 15%;">Material Type</th>
                    <th style="width: 15%;">Manufacturer</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 8%;">UOM</th>
                    <th style="width: 14%;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tools as $index => $tool)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $tool->name }}</td>
                        <td class="center">{{ strtoupper($tool->type->name ?? 'TOOLS') }}</td>
                        <td class="center">{{ $tool->manufacturer->name ?? '-' }}</td>
                        <td class="center">{{ $tool->quantity }}</td>
                        <td class="center">EA</td>
                        <td class="right">{{ number_format($tool->price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="notes-section">
            <h3>Notes / Long Description:</h3>
            <p style="margin: 0;">-</p>
        </div>

        <!-- Double Signature -->
        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <p>( _______________________ )</p>
                    <p class="signature-label">Receiver</p>
                </td>
                <td class="signature-box">
                    <p>( _______________________ )</p>
                    <p class="signature-label">Supervisor / Manager</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>