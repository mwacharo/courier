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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>TOWNS REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Name</th>
        <th>Country</th>
    </tr>

    @foreach ($towns as $town)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $town['name'] }}</td>
            <td> {{ $town['country_name'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
