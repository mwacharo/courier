<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <style>
         * {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }


        table {
            border: 1px dashed #000;
        }
        tr {
            height: 20px;
        }

        th{
            font-weight: bold;
            font-size:12px;

        }

        td {
            border: 1px solid black;
            padding: 5px;
            height: 40px;
        }
    </style>
</head>
<body>
<table>
    <tbody>

        <th><img src="./assets/media/logos/boxleo-logo.png" alt="" width="150px" height="90px"> <br></th>
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br>
            <b>RIDER ({{ $rider->first_name }} {{ $rider->last_name }}) IN TRANSIT REPORT </b> <br><b>DATE:{{ date('Y-m-d') }}</b></th>


    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Order No</th>
        <th>Receiver Name</th>
        <th>Receiver Address</th>
        <th>Receiver Phone</th>
        <th>Product</th>
        <th>Amount</th>
        <th>MPESA CODE</th>

    </tr>

    @foreach ($orders as $order)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $order->order_no }}</td>
            <td> {{ $order->receiver_name }}</td>
            <td> {{ $order->receiver_address }}</td>
            <td> {{ $order->receiver_phone }}</td>
            <td>
                @foreach ($order->items as $item)
                  {{ $item->description}} - Qty ({{ $item->quantity }})
               @endforeach
            </td>
            <td> Ksh {{ $order->amount}}</td>
            <td></td>
        </tr>
    @endforeach

    </tbody>
</table>

<table class='table table-bordered'>
    <tr>
        <th colspan="3">ORDER STATS</th>
    </tr>
    <tr>
        <th>DISPATCHED</th>
        <th>DELIVERED</th>
        <th>RETURNED</th>
    </tr>
    <tr>
        <td>{{ count($orders) }}</td>
        <td></td>
        <td></td>
    </tr>
</table>

<table class='table table-bordered'>

    <tr>
        <th>DISPATCHED BY</th>

        <th>SIGNATURE</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
</table>
</body>
</html>

