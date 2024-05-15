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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>ZONES REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Name</th>
        <th>Charge(Same Day)</th>
        <th>Tax(Same Day)</th>
        <th>Total(Same Day)</th>
        <th>Charge(Overnight)</th>
        <th>Tax(Overnight)</th>
        <th>Total(Overnight)</th>
        <th>Extra Weight</th>
    </tr>

    @foreach ($zones as $zone)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $zone['zone'] }}</td>
            <td> {{ $zone['same_day_charge'] }}</td>
            <td> {{ $zone['same_day_tax'] }}</td>
            <td> {{ $zone['same_day_total_amount'] }}</td>
            <td> {{ $zone['overnight_charge'] }}</td>
            <td> {{ $zone['overnight_tax'] }}</td>
            <td> {{ $zone['overnight_total_amount'] }}</td>
            <td> {{ $zone['extra_weight'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
