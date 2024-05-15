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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br>
             <b>MERCHANT ( {{ $merchant->name }}) ORDERS REPORT </b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Product Item</th>
        <th>Receiver Name</th>
        <th>Receiver Address</th>
        <th>Receiver Email</th>
        <th>Receiver Phone</th>
        <th>Receiver Gender</th>
        <th>Receiver Country</th>
        <th>Receiver Town</th>
        <th>Receiver Latitude</th>
        <th>Receiver Longitude</th>
        <th>Special Instruction</th>
        <th>Payment Type</th>
        <th>Cash On Delivery</th>
        <th>Insurance</th>
        <th>Order Status</th>
        <th>Custom Reason</th>
        <th>Created at</th>
        <th>Status Date</th>
        <th>Scheduled Date</th>
        <th>Delivery Date</th>
        <th>Quantity</th>
        <th>Amount</th>
        <th>Call Agent</th>
        <th>Followup Agent</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order['order_no'] }}</td>
            <td> {{ $order['item_name'] }}</td>
            <td> {{ $order['receiver_name'] }}</td>
            <td> {{ $order['receiver_address'] }}</td>
            <td> {{ $order['receiver_email'] }}</td>
            <td> {{ $order['receiver_phone'] }}</td>
            <td> {{ $order['receiver_gender'] }}</td>
            <td> {{ $order['receiver_country_name'] }}</td>
            <td> {{ $order['receiver_town_name'] }}</td>
            <td> {{ $order['receiver_latitude'] }}</td>
            <td> {{ $order['receiver_longitude'] }}</td>
            <td> {{ $order['special_instruction'] }}</td>
            <td> {{ $order['payment_type'] }}</td>
            <td> {{ $order['cash_on_delivery_amount'] }}</td>
            <td> {{ $order['insurance'] }}</td>
            <td> {{ $order['order_status'] }}</td>
            <td> {{ $order['custom_reason'] }}</td>
            <td> {{ $order['created_at'] }}</td>
            <td> {{ $order['status_date'] }}</td>
            <td> {{ $order['scheduled_date'] }}</td>
            <td> {{ $order['delivery_date'] }}</td>
            <td> {{ $order['quantity'] }}</td>
            <td> Ksh {{ $order['amount'] }}</td>
            <td> {{ $order['call_agent'] }}</td>
            <td> {{ $order['followup_agent'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
