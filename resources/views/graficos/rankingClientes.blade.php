<table class="table">
    <thead>
        <tr>
            <th>Nombre del Cliente</th>
            <th>Total Tradicional</th>
            <th>Total LED</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rankingClientes as $cliente)
            <tr>
                <td>{{ $cliente['nombre'] }}</td>
                <td>{{ $cliente['totalTradicional'] }}</td>
                <td>{{ $cliente['totalLed'] }}</td>
                <td>{{ $cliente['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
