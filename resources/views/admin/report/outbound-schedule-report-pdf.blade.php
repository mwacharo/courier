<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - OUTBOUND SCHEDULE REPORT</title>
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

        OUTBOUND SCHEDULE REPORT</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">No</th>
            <th>From</th>
            <th>Destination</th>
            <th>Charge</th>
            <th>Tax</th>
            <th>Total Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($schedules as $schedule)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $schedule['from_name'] }}</td>
                <td> {{ $schedule['destination_name'] }}</td>
                <td> {{ $schedule['charge'] }}</td>
                <td> {{ $schedule['tax'] }}</td>
                <td> {{ $schedule['total_amount'] }}</td>
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
