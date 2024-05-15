<!DOCTYPE html>
<html>
<head>
    <title>BOXLEO - BRANCHES REPORT</title>
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
        BRANCHES REPORT</p>
    <br>
    <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th style="width: 30%">Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($branches as $branch)
            <tr>
                <td> {{ $loop->iteration }}</td>
                <td> {{ $branch['name'] }}</td>
                <td> {{ $branch['email'] }}</td>
                <td> {{ $branch['phone_number'] }}</td>
                <td> {{ $branch['created_at'] }}</td>
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
