<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - RIDERS OUTSCAN REPORT</title>
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

        {{ strtoupper($rider->first_name) }} {{  strtoupper($rider->last_name) }} OUTSCAN REPORT - DATE {{ $date }}</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Order</th>
            <th>Receiver Name</th>
            <th>Receiver Phone</th>
            <th>Address</th>
            <th>Order Items</th>
            <th style="width: 30%">Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($orders as $order)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $order->order_no }}</td>
                <td> {{ $order->receiver_name }}</td>
                <td> {{ $order->receiver_phone }}</td>
                <td> {{ $order->receiver_address }}</td>
                <td>
                    @foreach($order->items as $order_item)
                     {{ $order_item->description }}  {{ $order_item->quantity }}
                    @endforeach
                </td>
                <td> {{ $date }}</td>
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
