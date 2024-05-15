<html>
<head>
    <style>
        table {
            border: 1px dashed #000;
        }
    </style>
</head>
<body>
<table>
    <tbody>
    <tr>
        <th>INVENTORY REPORT EXCEL</th>
    </tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>SKU</th>
        <th>Name</th>
        <th>Description</th>
        <th>Merchant</th>
        <th>Quantity</th>
        <th>Low Count</th>
        <th>Amount</th>
    </tr>

    @foreach ($inventories as $inventory)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $inventory['sku'] }}</td>
            <td> {{ $inventory['name'] }}</td>
            <td> {{ $inventory['description'] }}</td>
            <td> {{ $inventory['merchant_name'] }}</td>
            <td> {{ $inventory['quantity'] }}</td>
            <td> {{ $inventory['low_count'] }}</td>
            <td> Kshs {{ $inventory['amount'] }}</td>

        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
