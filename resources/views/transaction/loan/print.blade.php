<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Agreement - {{ $firstLoan->loan_code }}</title>
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
            width: 33%;
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
        <img src="{{ asset('images/logo_print_new.png') }}" alt="Company Logo"
            style="height: 80px; margin-bottom: 10px;">
        <h2>Berita Acara Peminjaman Barang</h2>
        <p>Loan Agreement Form</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Document No</td>
            <td>: {{ $firstLoan->loan_code }}</td>
            <td class="label">Date</td>
            <td>: {{ $firstLoan->loan_date->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Borrower Name</td>
            <td>: {{ $firstLoan->borrower->name ?? '-' }}</td>
            <td class="label">Department</td>
            <td>: {{ $firstLoan->borrower->department ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Expected Return</td>
            <td>: {{ $firstLoan->expected_return_date ? $firstLoan->expected_return_date->format('d F Y') : '-' }}</td>
            <td class="label">Issued By</td>
            <td>: {{ $firstLoan->keeper->name ?? 'Warehouse' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Asset / Serial No</th>
                <th style="width: 50px;">Qty</th>
                <th>Condition</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $index => $loan)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $loan->tool->code }}</td>
                    <td>{{ $loan->tool->name }}</td>
                    <td>{{ $loan->item ? $loan->item->serial_number : '-' }}</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">Good</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-bottom: 20px;">
        <strong>Notes:</strong><br>
        {{ $firstLoan->notes ?? '-' }}
    </div>

    <table class="signatures">
        <tr>
            <td style="width: 50%;">
                <br><br><br><br>
                ( {{ $firstLoan->borrower->name ?? '___________________' }} )<br>
                <strong>Borrower</strong>
            </td>
            <td style="width: 50%;">
                <br><br><br><br>
                ( {{ $firstLoan->keeper->name ?? '___________________' }} )<br>
                <strong>Storekeeper</strong>
            </td>
        </tr>
    </table>

</body>

</html>