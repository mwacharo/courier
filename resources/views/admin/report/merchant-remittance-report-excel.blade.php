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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>MERCHANT REMITTANCE REPORT - {{ strtoupper($merchant->name) }}</b></th>
    </tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Total Amount</th>
        <th>Item Quantity</th>
        <th>Item Description</th>
        <th>Client Name</th>
        <th>Location</th>
        <th>Town</th>
        <th>Phone</th>
        <th>Upsell(Yes or No)</th>
        <th>Status</th>
        <th>Status Date</th>
        <th>Delivery Fee</th>
        <th>Fulfillment Fee</th>
        <th>CoD</th>
        <th>Order Total</th>
        <th>Special Remark</th>
        <th>Custom Reason</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order->order_no }}</td>
            <td> {{ $order->amount }}</td>
            <td> {{ $order->quantity }}</td>
            <td> {{ $order->item_name }}</td>
            <td> {{ $order->receiver_name }}</td>
            <td> {{ $order->receiver_address }}</td>
            <td> {{ $order->receiver_town_name }}</td>
            <td> {{ $order->receiver_phone }}</td>
            <td>
                @if($order->upsell == 1)
                    YES
                @else
                    NO
                @endif
            </td>
            <td> {{ $order->order_status }}</td>
            <td> {{ $order->status_date }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $order->special_instruction }}</td>
             @if($order->custom_reason == 'null')
                <td> {{ strtoupper(str_replace('_', ' ', $order->status_reason)) }}</td>
            @else
                <td> {{ $order->custom_reason }}</td>
            @endif

        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
