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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>RIDERS REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Name</th>
        <th>Date of birth</th>
        <th>National ID</th>
        <th>Address</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Country</th>
    </tr>

    @foreach ($riders as $rider)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $rider['first_name'] }} {{ $rider['last_name'] }}</td>
            <td> {{ $rider['date_of_birth'] }}</td>
            <td> {{ $rider['national_id'] }}</td>
            <td> {{ $rider['address'] }}</td>
            <td> {{ $rider['email'] }}</td>
            <td> {{ $rider['phone_number'] }}</td>
            <td> {{ $rider['country_name'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
