<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalesPro Update</title>
</head>
<body style="margin:0;background:#f7fbff;color:#07132d;font-family:Arial,sans-serif">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7fbff;padding:24px">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #dbeafe;border-radius:8px;padding:28px">
                    <tr>
                        <td>
                            <h1 style="margin:0 0 16px;font-size:24px;line-height:1.25;color:#07132d">Hello {{ $customer->name }},</h1>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#334155">{{ $bodyMessage }}</p>
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#64748b">Thank you,<br>SalesPro Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
