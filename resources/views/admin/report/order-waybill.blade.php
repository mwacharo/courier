<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="HTML,CSS,XML,JavaScript, jQuery">
    <meta name="author" content="C.G. 'Caprice' Johnson">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<meta http-equiv="refresh" content="30">-->
    <title>Boxleo Waybill #{{ $order['order_no'] }}</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <style>

        @page { margin: 0px; }
        * {
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .container {
            width: 100%;
            margin-right: 20px;
            padding: 35px;
        }

        footer {
            font-size: 12px;
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

        table {
            width: 100%;
        }

        .container {
            width: 100%;
        }

        .head {
            text-align: center;
        }

        .text {
            padding: 15px;
            height: 40px;
        }
    </style>
    <!--jquery-->


</head>
<body>
<div class="container">
    <div class="head">
        <img src="{{ asset('assets/media/logos/boxleo-logo.png') }}" alt="" style="width: 200px; height: 90px;">
        <h3>BOXLEO COURIER & FULFILLMENT SERVICES</h3>
        Email: operations@boxleocourier.com <br>
        Phone: +254 746 078 049 / 0759 142 032
    </div>
    <table class ='table table-bordered'>
        <tr>
            <td style="text-align:right;">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($order['order_no'], 'C39')}}" alt="barcode" />
            </td>
        </tr>
    </table>

    @if($order['is_sender_merchant'] == 0)
        <table class ='table table-bordered'>
            <tr>
                <th>SHIPPER NAME</th>
                <th>SHIPPER ADDRESS</th>
                <th>SHIPPER CONTACT</th>
                <th>SHIPPER TOWN</th>
            </tr>
            <tr>
                <td>{{ $order['sender_name'] }}</td>
                <td>{{ $order['sender_address'] }}</td>
                <td>+{{ $order['sender_phone'] }}</td>
                <td>{{ $order['sender_town_name'] }}</td>
            </tr>
        </table>

    @else

        <table class ='table table-bordered'>
            <tr style="text-align: center">
                <th>SHIPPER</th>
            </tr>
            <tr style="text-align: center">
                <td>Boxleo Courier Services</td>
            </tr>
        </table>

    @endif


    <table class ='table table-bordered'>
        <tr>
            <th>RECEIVER NAME</th>
            <th>RECEIVER ADDRESS</th>
            <th>RECEIVER CONTACT</th>
            <th>RECEIVER TOWN</th>
        </tr>
        <tr>
            <td>{{ $order['receiver_name'] }}</td>
            <td>{{ $order['receiver_address'] }}</td>
            <td>+{{ $order['receiver_phone'] }} / {{ $order['receiver_phone_alternative'] }}</td>
            <td>{{ $order['receiver_town_name'] }}</td>
        </tr>
    </table>
    <table class ='table table-bordered'>
        <tr>
            <th>SHIP DATE</th>
            <th>WAYBILL REFERENCE #</th>
            <th>COD AMOUNT</th>
            <th>CARRIER</th>
            <th>AGENT/DRIVER/RIDER</th>

        </tr>

        <tr>
            <td>{{ $order['scheduled_date'] }}</td>
            <td>#{{ $order['order_no'] }}</td>
            <td>Kshs. {{ $order['cash_on_delivery_amount'] }}</td>
            <td>Boxleo Courier</td>
            <td>{{ $order['rider_name'] }} <br>
                +{{ $order['rider_phone'] }}
            </td>
        </tr>
    </table>

    <table class ='text table table-bordered'>
        <tr>
            <th>DESCRIPTION</th>
            <th>QUANTITY</th>
        </tr>
        @foreach($order_items as $order_item)
            <tr>
                <td style="font-size: 14px;">{{ $order_item->description }}</td>
                <td>{{ $order_item->quantity }}</td>
            </tr>
        @endforeach
    </table>
    <table class ='table table-bordered'>
        <tr>
            <th>COMMENTS</th>
        </tr>

        <tr>
            <td>{{ $order['special_instruction'] }}</td>

        </tr>
    </table>
    <br />
    <div class="disclosure">
        Clients are requested to pay through M-PESA (However cash payments will be acceptable in case to case basis)
        <br>
        Payment method: Mpesa Paybill number. 4032407, Account: {{ $order['order_no'] }}, Amount: {{ $order['amount'] }}
        <br>
    </div>



</div><!--end container-->



</body>
<!-- Latest compiled and minified JavaScript -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="script.js"></script>
</html>
