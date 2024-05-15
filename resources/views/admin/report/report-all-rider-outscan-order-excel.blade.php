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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>ALL RIDERS  OUTSCAN REPORT </b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Destination Type</th>
        <th>Receiver Name</th>
        <th>Receiver Address</th>
        <th>Receiver Email</th>
        <th>Receiver Phone</th>
        <th>Receiver Town</th>
        <th>Product</th>
        <th>Special Instruction</th>
        <th>Payment Type</th>
        <th>Cash On Delivery</th>
        <th>Order Status</th>
        <th>Delivery Date</th>
        <th>Amount</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order->order_no }}</td>
            <td> {{ $order->destination_type }}</td>
            <td> {{ $order->receiver_name }}</td>
            <td> {{ $order->receiver_address }}</td>
            <td> {{ $order->receiver_email }}</td>
            <td> {{ $order->receiver_phone }}</td>
            <td> {{ $order->receiver_town_name }}</td>
            <td>
                @foreach ($order->items as $item)
                    {{ $item->description }} - Qty ({{ $item->quantity }})
                @endforeach
            </td>
            <td> {{ $order->special_instruction }}</td>
            <td> {{ $order->payment_type }}</td>
            <td> {{ $order->cash_on_delivery_amount }}</td>
            <td>
                @if($order->order_status == 'order_pending')
                    Order pending
                @elseif($order->order_status == 'follow_up')
                    Follow up
                @elseif($order->order_status == 'scheduled')
                    Scheduled
                @elseif($order->order_status == 'awaiting_dispatch')
                    Awaiting Dispatch
                @elseif($order->order_status == 'dispatched')
                    Dispatched
                @elseif($order->order_status == 'undispatched')
                    Undispatched
                @elseif($order->order_status == 'in_transit')
                   In Transit
                @elseif($order->order_status == 'delivery_pending')
                    Delivery pending
                @elseif($order->order_status == 'delivered')
                    Delivered
                @elseif($order->order_status == 'out_of_stock')
                    Out Of Stock
                @elseif($order->order_status == 'returned')
                    Returned
                @elseif($order->order_status == 'expired')
                   Expired
                @elseif($order->order_status == 'cancelled')
                    Order cancelled
                @endif
            </td>
            <td> {{ $order->delivery_date }}</td>
            <td> Ksh {{ $order->amount }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
