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
    <tr style="background-color: #ffb53a; color: #FFFFFF;">
        <th>Order No</th>
        <th>Receiver Name</th>
        <th>Receiver Address</th>
        <th>Receiver Email</th>
        <th>Receiver Phone</th>
    </tr>

    @foreach ($orders as $order)
        <tr>
            <td>{{ $order['order_no'] }}</td>
            <td>{{ $order['receiver_name'] }}</td>
            <td>
                @if ($order['receiver_address'] === null)
                    {{ $order['receiver_town_name'] }}
                @else
                    {{ $order['receiver_address'] }}
                @endif
            </td>
            <td>{{ $order['receiver_email'] }}</td>
            <td>{{ $order['receiver_phone'] }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
</body>
</html>
