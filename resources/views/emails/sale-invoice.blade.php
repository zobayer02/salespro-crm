<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SalesPro Invoice</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a">
    <div style="max-width:720px;margin:0 auto;padding:28px">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:24px">
            <h1 style="margin:0 0 8px;font-size:24px">SalesPro Invoice</h1>
            <p style="margin:0 0 22px;color:#64748b">Order #{{ $sale->order_number }}</p>

            <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
                <tr>
                    <td style="padding:8px 0;color:#64748b">Customer</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700">{{ $sale->customer->name }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#64748b">Branch</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700">{{ $sale->branch?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#64748b">Date</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700">{{ $sale->sold_at->format('M d, Y h:i A') }}</td>
                </tr>
            </table>

            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left">Product</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:left">SKU</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right">Qty</th>
                        <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #f1f5f9">{{ $item->product_name }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f1f5f9">{{ $item->sku }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f1f5f9;text-align:right">{{ number_format($item->quantity) }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f1f5f9;text-align:right">Tk {{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:18px;text-align:right">
                <span style="display:block;color:#64748b">Total Amount</span>
                <strong style="font-size:24px">Tk {{ number_format((float) $sale->total_amount, 2) }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
