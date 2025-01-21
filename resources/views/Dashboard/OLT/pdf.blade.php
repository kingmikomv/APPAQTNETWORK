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
        background-color: #f4f6f9;
        color: #333;
        line-height: 1.6;
        padding: 40px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    h3 {
        color: #2c3e50;
        font-size: 30px;
    }

    /* Header Styling */
    .header-table {
        background-color: #2980b9;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        color: white;
    }

    .header-table td {
        padding: 10px;
    }

    .header-table img {
        width: 150px;
    }

    .header-table h3 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .header-table .address {
        font-size: 14px;
        color: #000000;
        margin-top: 10px;
    }

    /* Invoice Information */
    .info-table {
        margin-top: 20px;
        font-size: 16px;
    }

    .info-table td {
        padding: 10px;
        font-size: 14px;
    }

    .info-table strong {
        color: #2980b9;
    }

    /* Table Styling */
    th, td {
        padding: 15px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 14px;
        color: #34495e;
    }

    th {
        background-color: #3498db;
        color: white;
        font-weight: normal;  /* Changed from bold to normal */
    }

    td {
        background-color: #fff;
        transition: background-color 0.3s ease;
    }

    td:hover {
        background-color: #f9f9f9;
    }

    /* Footer Styling */
    .footer-table {
        margin-top: 30px;
        background-color: #f1f1f1;
        border-top: 2px solid #ddd;
    }

    .footer-table td {
        padding: 15px;
        text-align: right;
        font-size: 16px;
        color: #34495e;
    }

    .footer-table .gray {
        background-color: #f4f4f4;
        font-weight: bold;
        color: #e74c3c;
    }

    .footer-table td span {
        font-weight: bold;
        color: #2980b9;
    }

    /* Hover effect for rows */
    tbody tr:hover {
        background-color: #ecf0f1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .header-table {
            text-align: center;
        }

        .header-table img {
            margin: 0 auto;
        }

        .footer-table td {
            font-size: 14px;
        }

        .info-table td {
            font-size: 12px;
        }
    }

</style>

</head>
<body>

  <table class="header-table">
    <tr>
        <td valign="top"><img src="{{ url('/assets/ll.png')}}" alt="Logo AQT Network" /></td>
        <td>
            <h3>AQT Network</h3>
            <div class="address">
                Harga Rakyat Kualitas Pejabat<br>
                Sindangkerta, 45252<br>
                Indramayu, Jawa Barat<br>
                Email: official@aqtnetwork.my.id
            </div>
        </td>
    </tr>
  </table>

  <table class="info-table">
    <tr>
        <td><strong>Dari:</strong> AQT Network</td>
        <td><strong>Untuk:</strong> {{$user}}</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>Jumlah Coin</th>
        <th>Total (Rp)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{$transaction->coin_amount}} Coin</td>
        <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
      </tr>
    </tbody>

    <tfoot class="footer-table">
        <tr>
            <td colspan="1"><span>Subtotal</span></td>
            <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="1"><span>Diskon</span></td>
            <td>-</td>
        </tr>
        <tr>
            <td colspan="1" class="gray"><span>Total</span></td>
            <td class="gray">Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
  </table>

  <div class="footer-table">
    <table>
        <tr>
            <td colspan="2" style="text-align: center;">Invoice - AQT Network - {{$transaction->external_id}}</td>
        </tr>
    </table>
  </div>

</body>
</html>
