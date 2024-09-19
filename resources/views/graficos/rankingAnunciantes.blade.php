<table>
    <thead>
        <tr>
            <th>Anunciante</th>
            <th>Total Tradicional</th>
            <th>Total LED</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ranking as $item)
            <tr>
                <td>{{ $item['nombre'] }}</td>
                <td>{{ $item['tradicional'] }}</td>
                <td>{{ $item['led'] }}</td>
                <td>{{ $item['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
