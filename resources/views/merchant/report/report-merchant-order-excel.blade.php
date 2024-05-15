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
        <th>MERCHANT ({{ $merchant->name }}) ORDERS REPORT EXCEL</th>
    </tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Destination Type</th>
        <th>Receiver Name</th>
        <th>Receiver Address</th>
        <th>Receiver Email</th>
        <th>Receiver Phone</th>
        <th>Receiver Country</th>
        <th>Receiver Town</th>
        <th>Receiver Latitude</th>
        <th>Receiver Longitude</th>
        <th>Special Instruction</th>
        <th>Payment Type</th>
        <th>Cash On Delivery</th>
        <th>Insurance</th>
        <th>Order Status</th>
        <th>Delivery Date</th>
        <th>Amount</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order['order_no'] }}</td>
            <td> {{ $order['destination_type'] }}</td>
            <td> {{ $order['receiver_name'] }}</td>
            <td> {{ $order['receiver_address'] }}</td>
            <td> {{ $order['receiver_email'] }}</td>
            <td> {{ $order['receiver_phone'] }}</td>
            <td> {{ $order['receiver_country_name'] }}</td>
            <td> {{ $order['receiver_town_name'] }}</td>
            <td> {{ $order['receiver_latitude'] }}</td>
            <td> {{ $order['receiver_longitude'] }}</td>
            <td> {{ $order['special_instruction'] }}</td>
            <td> {{ $order['payment_type'] }}</td>
            <td> {{ $order['cash_on_delivery_amount'] }}</td>
            <td> {{ $order['insurance'] }}</td>
            <td> {{ $order['order_status'] }}</td>
            <td> {{ $order['delivery_date'] }}</td>
            <td> Ksh {{ $order['amount'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
