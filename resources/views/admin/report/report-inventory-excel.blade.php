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
        <th><img src="./assets/media/logos/boxleo-logo.png" alt="" width="150px" height="90px"> <br></th>
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>INVENTORY REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
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
