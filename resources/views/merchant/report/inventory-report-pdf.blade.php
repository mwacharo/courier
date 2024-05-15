<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - INVENTORY REPORT</title>
    <style>
        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<div>

    <p style="text-align:center; font-weight:bold; padding-top:5mm;">INVENTORY REPORT</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>SKU</th>
            <th>Name</th>
            <th>Merchant</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($inventories as $inventory)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $inventory['sku'] }}</td>
                <td> {{ $inventory['name'] }}</td>
                <td> {{ $inventory['merchant_name'] }}</td>
                <td> {{ $inventory['quantity'] }}</td>
                <td> {{ $inventory['amount'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


</div>

<htmlpagefooter name="footer">

    <div id="footer">
        <p style="text-align: center;">Boxleo Courier Services</p> <b></b>
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="footer" value="on" />

</body>
</html>