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
        <th>ORDERS REPORT EXCEL</th>
    </tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Destination Type</th>
        <th>Sender Name</th>
        <th>Sender Email</th>
        <th>Sender Phone</th>
        <th>Sender Country</th>
        <th>Sender Town</th>
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
        <th>Rider</th>
        <th>Delivery Date</th>
        <th>Amount</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order['order_no'] }}</td>
            <td> {{ $order['destination_type'] }}</td>
            <td> {{ $order['sender_name'] }}</td>
            <td> {{ $order['sender_email'] }}</td>
            <td> {{ $order['sender_phone'] }}</td>
            <td> {{ $order['sender_country_name'] }}</td>
            <td> {{ $order['sender_town_name'] }}</td>
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
            <td>
                 @if($order['order_status'] == 'order_pending')
                    Order pending
                @elseif($order['order_status'] == 'follow_up')
                    Follow up
                @elseif($order['order_status'] == 'awaiting_dispatch')
                    Awaiting Dispatch
                @elseif($order['order_status'] == 'scheduled')
                    Scheduled
                @elseif($order['order_status'] == 'dispatched')
                    Dispatched
                @elseif($order['order_status'] == 'not_dispatched')
                    Not Dispatched
                @elseif($order['order_status'] == 'out_of_stock')
                    Out of Stock
                @elseif($order['order_status'] == 'delivery_pending')
                    Delivery pending
                @elseif($order['order_status'] == 'expired')
                    Expired
                @elseif($order['order_status'] == 'delivered')
                    Delivered
                @elseif($order['order_status'] == 'returned')
                    Returned
                @elseif($order['order_status'] == 'cancelled')
                    Order cancelled
                @endif
            </td>
            <td> {{ $order['rider_name'] }}</td>
            <td> {{ $order['delivery_date'] }}</td>
            <td> Ksh {{ $order['amount'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
