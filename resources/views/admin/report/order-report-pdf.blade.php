<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - ORDER REPORT</title>
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

    <p style="text-align:center; font-weight:bold; padding-top:5mm;">
        <img src="{{ asset('assets/media/logos/boxleo-logo.png') }}" alt="" style="width: 200px; height: 90px;"> <br>
        BOXLEO COURIER & FULFILLMENT SERVICES <br>
        AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI <br>
        EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM <br> <br>

        ORDER REPORT</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">No</th>
            <th>Order No</th>
            <th>Sender Name</th>
            <th>Sender Phone</th>
            <th>Receiver Name</th>
            <th>Receiver Phone</th>
            <th>Receiver Address</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $order['order_no'] }}</td>
                <td> {{ $order['sender_name'] }}</td>
                <td> {{ $order['sender_phone'] }}</td>
                <td> {{ $order['receiver_name'] }}</td>
                <td> {{ $order['receiver_phone'] }}</td>
                <td> {{ $order['receiver_address'] }}</td>
                <td> Ksh {{ $order['amount'] }}</td>
                <td>
                   @if($order['order_status'] == 'order_pending')
                       Order pending
                   @elseif($order['order_status'] == 'follow_up')
                        Follow up
                   @elseif($order['order_status'] == 'scheduled')
                        Scheduled
                   @elseif($order['order_status'] == 'dispatched')
                        Dispatched
                   @elseif($order['order_status'] == 'delivery_pending')
                        Delivery pending
                   @elseif($order['order_status'] == 'delivered')
                        Delivered
                    @elseif($order['order_status'] == 'returned')
                        Returned
                    @elseif($order['order_status'] == 'cancelled')
                        Order cancelled
                   @endif
                </td>
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
