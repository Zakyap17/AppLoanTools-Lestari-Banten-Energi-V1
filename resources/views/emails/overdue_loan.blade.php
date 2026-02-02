<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Return Notice</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- MAIN CONTAINER -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- EMAIL CARD -->
                <table width="640" border="0" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    <!-- HEADER -->
                    <tr>
                        <td align="center"
                            style="padding: 40px 40px 30px 40px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                            <!-- BRANDING / LOGO -->
                            <img src="{{ $message->embed(public_path('images/logo_email.png')) }}" alt="LBE Logo"
                                style="max-width: 150px; height: auto; display: block; margin-bottom: 10px; border-radius: 20px;">
                            <div
                                style="color: #94a3b8; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;">
                                Asset Management System</div>
                        </td>
                    </tr>

                    <!-- RED ALERT BANNER -->
                    <tr>
                        <td align="center"
                            style="background-color: #fee2e2; padding: 12px 0; border-bottom: 1px solid #fecaca;">
                            <span
                                style="color: #dc2626; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                &#9888; Action Required: Overdue Items
                            </span>
                        </td>
                    </tr>

                    <!-- CONTENT BODY -->
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="color: #1e293b; font-size: 20px; font-weight: 700; margin: 0 0 20px 0;">
                                Hello, {{ $borrowerName }}
                            </h1>
                            <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                                Our records indicate that you have equipment currently marked as
                                <strong>Overdue</strong>.
                                Please arrange for the return of these items to the warehouse immediately to avoid
                                penalties or operational disruptions.
                            </p>

                            <!-- ITEM LIST TABLE -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px;">
                                <thead>
                                    <tr>
                                        <th align="left"
                                            style="background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 15px; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0;">
                                            Item Details</th>
                                        <th align="left"
                                            style="background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 15px; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0;">
                                            Due Date</th>
                                        <th align="center"
                                            style="background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 15px; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #e2e8f0;">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loans as $loan)
                                        <tr>
                                            <td
                                                style="padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 14px; line-height: 1.4;">
                                                <div style="font-weight: 600;">{{ $loan->tool->name }}</div>
                                                <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">
                                                    Code: {{ $loan->loan_code }} <br>
                                                    SN:
                                                    {{ $loan->item->serial_number ?? ($loan->tool_item_id ? 'SN-' . $loan->tool_item_id : 'Qty: ' . $loan->loan_quantity) }}
                                                </div>
                                            </td>
                                            <td
                                                style="padding: 15px; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 14px; vertical-align: top;">
                                                {{ \Carbon\Carbon::parse($loan->expected_return_date)->format('d M, Y') }}
                                            </td>
                                            <td align="center"
                                                style="padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                                                @php
                                                    $daysLate = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($loan->expected_return_date));
                                                @endphp
                                                <span
                                                    style="background-color: #fee2e2; color: #ef4444; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 4px; white-space: nowrap;">
                                                    {{ (int) $daysLate }} Days Late
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- CTA BUTTON -->


                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td
                            style="background-color: #f8fafc; padding: 30px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 10px 0;">
                                Please ignore this message if you have already returned these items.
                            </p>
                            <p style="color: #cbd5e1; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                                Automated System Notification. Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- END EMAIL CARD -->

                <!-- BOTTOM SPACER -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td height="40" style="font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>