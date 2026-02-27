<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Good Receipt - {{ $tool->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .content-table th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .signatures {
            width: 100%;
            margin-top: 50px;
        }

        .signatures td {
            text-align: center;
            vertical-align: bottom;
            width: 50%;
            height: 100px;
        }

        @media print {
            @page {
                margin: 20px;
            }

            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
            <img src="{{ asset('images/logo_print_new.png') }}" alt="Company Logo" style="height: 60px;">
            <h1 style="margin: 0 0 0 15px; font-size: 20px; font-weight: bold; color: #000;">PT. Lestari Banten Energi
            </h1>
        </div>
        <h2>Good Receipt Document</h2>
        <p>Bukti Penerimaan Barang</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">MT No.</td>
            <td>: {{ $tool->code }}</td>
            <td class="label">Date Received</td>
            <td>: {{ $tool->created_at->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">PO Number</td>
            <td>: {{ $tool->po_number ?? '-' }}</td>
            <td class="label">Tools Room</td>
            <td>: {{ $tool->location->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Asset / Ref No.</td>
            <td>: {{ $tool->serial_number ?? '-' }}</td>
            <td class="label">Bin Location</td>
            <td>: {{ $tool->rack->code ?? '-' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Item Description</th>
                <th>Material Type</th>
                <th>Manufacturer</th>
                <th style="width: 50px;">Qty</th>
                <th style="width: 50px;">UOM</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>{{ $tool->name }}</td>
                <td style="text-align: center;">{{ $tool->type->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $tool->manufacturer->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $tool->quantity }}</td>
                <td style="text-align: center;">{{ $tool->uom ?? 'EA' }}</td>
                <td style="text-align: right;">{{ $tool->price ? number_format($tool->price, 0, ',', '.') : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 20px;">
        <strong>Notes / Long Description:</strong><br>
        {{ $tool->long_description ?? $tool->notes ?? '-' }}
    </div>

    <table class="signatures">
        <tr>
            <td>
                <br><br><br><br>
                ( ___________________ )<br>
                <strong>Receiver</strong>
            </td>
            <td>
                <br><br><br><br>
                ( ___________________ )<br>
                <strong>Supervisor / Manager</strong>
            </td>
        </tr>
    </table>

</body>

</html>