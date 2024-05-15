<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - ZONE(INBOUND) REPORT</title>
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
        ZONE(INBOUND) REPORT</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">No</th>
            <th>Name</th>
            <th>Total(Same Day)</th>
            <th>Total(Overnight)</th>
            <th>Extra Weight</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($zones as $zone)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $zone['zone'] }}</td>
                <td> {{ $zone['same_day_total_amount'] }}</td>
                <td> {{ $zone['overnight_total_amount'] }}</td>
                <td> {{ $zone['extra_weight'] }}</td>
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
