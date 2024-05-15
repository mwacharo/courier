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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>OUTBOUND SCHEDULE REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>From</th>
        <th>Destination</th>
        <th>Charge</th>
        <th>Tax</th>
        <th>Total Amount</th>
    </tr>

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
</body>
</html>
