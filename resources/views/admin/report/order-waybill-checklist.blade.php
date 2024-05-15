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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>DATE:{{ date('Y-m-d') }}</b><b> WAYBILL CHECKLIST</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Waybill Check</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order['order_no'] }}</td>
            <td> </td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>

