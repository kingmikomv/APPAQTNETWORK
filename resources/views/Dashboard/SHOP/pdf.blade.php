<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            overflow: hidden;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center; /* Vertikal rata tengah */
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header img {
            height: 100px; /* Proporsional */
            width: 250px;
            margin: 0;
        }
        .header-info {
            text-align: right; /* Rata kanan */
        }
        .header-info h2 {
            margin: 0;
            font-size: 24px;
        }
        .header-info p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .details {
            margin-bottom: 20px;
        }
        .details h2 {
            font-size: 24px;
            margin: 0 0 10px;
            color: #6200ff;
        }
        .details p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #eaeaea;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }
        table th {
            font-weight: bold;
            text-transform: uppercase;
        }
        table tr td strong {
            color: #6200ff;
        }
        .transactions th {
            background: #eeeeee;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            padding-top: 10px;
            border-top: 1px solid #eaeaea;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header" style="display: flex; align-items: flex-end;">
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="border: none;">
                        <img src="{{ public_path('assets/ll.png') }}" alt="AQT Network Logo">
                    </td>
                    <td class="header-info" style="border: none;">
                        <h2>Invoice #{{$transaction->external_id}}</h2>
                        <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($transaction->created_at)->format('l, F jS, Y') }}</p>
                        <p><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($transaction->paid_at)->format('l, F jS, Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="details">
            <p><strong>Invoiced To:</strong></p>
            <p>
                {{$transaction->user->name}}<br>
                {{$transaction->user->email}}<br>
                {{$transaction->user->telefon}} <!-- Change 'telefon' to 'phone' to match your database field -->
            </p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{"Pembelian Coin Sebanyak ".$transaction->coin_amount}} Coins</td>
                    <td>Rp {{number_format($transaction->price, 0, ',', '.')}},-</td> <!-- Format the total amount dynamically -->
                </tr>
                <tr>
                    <td><strong>Sub Total</strong></td>
                    <td>Rp 0,-</td> <!-- Example for sub total -->
                </tr>
                <tr>
                    <td><strong>Credit</strong></td>
                    <td>Rp 0,-</td> <!-- Static Credit, you can adjust if there's credit data -->
                </tr>
                <tr style="text-align: right">
                    <td><strong>Total</strong></td>
                    <td><strong>Rp {{number_format($transaction->price, 0, ',', '.')}},-</strong></td> <!-- Total amount -->
                </tr>
            </tbody>
        </table>
        <h3 style="color: #333; margin: 10px 0;">Transactions</h3>
        <table class="transactions">
            <thead>
                <tr>
                    <th>Transaction Date</th>
                    <th>Gateway</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('l, F jS, Y') }}</td>
                    <td>
                        @if($transaction->payment_method == 'BANK_TRANSFER')
                            Pembayaran melalui Transfer Bank by {{$transaction->payment_channel}}.
                        @elseif($transaction->payment_method == 'QRIS')
                            Pembayaran melalui QRIS menggunakan {{$transaction->payment_channel}}.
                        @elseif($transaction->payment_method == 'QR_CODE')
                            Pembayaran menggunakan QR Code melalui {{$transaction->payment_channel}}.
                        @elseif($transaction->payment_method == 'EWALLET')
                            @if($transaction->ewallet_type == 'ShopeePay')
                                Pembayaran melalui ShopeePay menggunakan {{$transaction->payment_channel}}.
                            @elseif($transaction->ewallet_type == 'OVO')
                                Pembayaran melalui OVO menggunakan {{$transaction->payment_channel}}.
                            @elseif($transaction->ewallet_type == 'DANA')
                                Pembayaran melalui DANA menggunakan {{$transaction->payment_channel}}.
                            @else
                                Pembayaran menggunakan E-wallet {{$transaction->ewallet_type}} melalui {{$transaction->payment_channel}}.
                            @endif
                        @elseif($transaction->payment_method == 'CREDIT_CARD')
                            Pembayaran melalui Kartu Kredit menggunakan {{$transaction->payment_channel}}.
                        @elseif($transaction->payment_method == 'RETAIL_OUTLET')
                            @if($transaction->payment_channel == 'Alfamart')
                                Pembayaran melalui Alfamart dengan metode {{$transaction->payment_method}}.
                            @elseif($transaction->payment_channel == 'Indomart')
                                Pembayaran melalui Indomart dengan metode {{$transaction->payment_method}}.
                            @else
                                Pembayaran melalui outlet retail {{$transaction->payment_channel}}.
                            @endif
                        @else
                            Pembayaran menggunakan metode {{$transaction->payment_method}} via {{$transaction->payment_channel}}.
                        @endif
                    
                        @if(!empty($transaction->payment_source))
                            <br><strong>Sumber Pembayaran:</strong> {{$transaction->payment_source}}
                        @endif
                    </td>
                    
                    
                                        <td>{{$transaction->external_id}}</td>
                    <td>Rp {{number_format($transaction->price, 0, ',', '.')}},-</td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            PDF Generated on {{ \Carbon\Carbon::now()->format('l, F jS, Y') }}
        </div>
    </div>
</body>
</html>
