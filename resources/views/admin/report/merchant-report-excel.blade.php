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
        <th colspan="5"><b>BOXLEO COURIER AND FULFILLMENT SERVICES</b><br><b>AKSHRAP GODOWNS GATE A-2, JKIA JUNCTION, NAIROBI</b><br><b>EMAIL: INFO@BOXLEOCOURIER.COM WEBSITE:BOXLEOCOURIER.COM</b><br><b>MERCHANTS REPORT EXCEL</b></th>
    </tr>
    <tr></tr>
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>No</th>
        <th>Name</th>
        <th>Address</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Country</th>
        <th>Enable Cash On Delivery</th>
        <th>Cash On Delivery Amount</th>
        <th>Enable Delivery Fee Nairobi</th>
        <th>Delivery Fee Nairobi Amount</th>
        <th>Enable Delivery Fee Outbound</th>
        <th>Delivery Fee Outbound Amount</th>
        <th>Enable Enable Returns Management</th>
        <th>Enable Warehousing Fee</th>
        <th>Warehousing Fee Amount</th>
        <th>Enable Packaging Fee</th>
        <th>Packaging Fee Amount</th>
        <th>Enable Call Centre Fee</th>
        <th>Call Centre Fee Amount</th>
        <th>Enable Label Printing Fee</th>
        <th>Label Printing Fee Amount</th>

    </tr>

    @foreach ($merchants as $merchant)
        <tr>
            <td> {{ $loop->iteration }}</td>
            <td> {{ $merchant['name'] }}</td>
            <td> {{ $merchant['address'] }}</td>
            <td> {{ $merchant['email'] }}</td>
            <td> {{ $merchant['phone_number'] }}</td>
            <td> {{ $merchant['country_name'] }}</td>
            <td>
                @if($merchant['enable_cash_on_delivery_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['cash_on_delivery_fee'] }}</td>
            <td>
                @if($merchant['enable_delivery_fee_nairobi'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['delivery_fee_nairobi'] }}</td>
            <td>
                @if($merchant['enable_delivery_fee_outbound'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['delivery_fee_outbound'] }}</td>
            <td>
                @if($merchant['enable_returns_management_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>
                @if($merchant['enable_warehousing_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['warehousing_fee'] }}</td>
            <td>
                @if($merchant['enable_packaging_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['packaging_fee'] }}</td>
            <td>
                @if($merchant['enable_call_centre_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['call_centre_fee'] }}</td>
            <td>
                @if($merchant['enable_label_printing_fee'] == true)
                    ENABLED
                @else
                    DISABLED
                @endif
            </td>
            <td>Kshs {{ $merchant['label_printing_fee'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
