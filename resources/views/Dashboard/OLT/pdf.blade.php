<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur - AQT Network</title>

    <style type="text/css">
        * {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f9fafc;
            color: #333;
            line-height: 1.6;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px 30px;
        }

        h1,
        h2,
        h3 {
            margin-bottom: 10px;
        }

        h1 {
            font-size: 25px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 20px;
            color: #34495e;
        }

        h3 {
            font-size: 18px;
            color: #7f8c8d;
        }

        h5 {
            text-align: center;

            color: #7f8c8d;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3498db;
        }

        .header .logo img {
            width: 150px;
        }

        .header .details {
            text-align: right;
        }

        .header .details p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead th {
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table tbody td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        table tfoot td {
            padding: 10px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        table tfoot tr.total-row {
            background-color: #f4f4f4;
            color: #e74c3c;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header .details {
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>Invoice <br> {{ $transaction->external_id }} </h1>

        <div class="header">

            <div class="details">
                <h2>AQT Network</h2>
                <p>Sindangkerta, 45252</p>
                <p>Indramayu, Jawa Barat</p>
                <p>official@aqtnetwork.my.id</p>
            </div>
        </div>

        <h2>Informasi Transaksi</h2>
        <table>
            <tr>
                <td><strong>Untuk:</strong></td>
                <td>{{ $user }}</td>
            </tr>
            <tr>
                <td><strong>Transaksi Dibuat:</strong></td>
                <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Transaksi Lunas:</strong></td>
                <td> {{ \Carbon\Carbon::parse($transaction->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                </td>
            </tr>
            <tr>
                <td><strong>Metode Pembayaran:</strong></td>
                <td>
                    @if ($transaction->payment_method == 'BANK_TRANSFER')
                        Transfer Bank
                    @elseif($transaction->payment_method == 'EWALLET')
                        EWALLET
                    @elseif($transaction->payment_method == 'QR_CODE')
                        QRIS
                    @elseif($transaction->payment_method == 'RETAIL_OUTLET')
                        Retail Outlet
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Pembayaran By:</strong></td>
                <td>
                    {{ $transaction->payment_channel }}
                </td>
            </tr>
        </table>

        <h2>Rincian Pembelian</h2>
        <table>
            <thead>
                <tr>
                    <th>Jumlah Coin</th>
                    <th>Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $transaction->coin_amount }} Coin</td>
                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td align="right">Subtotal</td>
                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td align="right">Diskon</td>
                    <td>-</td>
                </tr>
                <tr class="total-row">
                    <td align="right">Total</td>
                    <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Invoice ID: {{ $transaction->external_id }}</p>
            <p>Terima kasih telah bertransaksi dengan AQT Network.</p>
        </div>
    </div>
</body>

</html>
