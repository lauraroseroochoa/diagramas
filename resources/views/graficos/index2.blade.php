<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar Productos</title>
</head>
<body>
    <form action="{{ route('graficos.filtrar2') }}" method="POST">
        @csrf
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required>

        <label for="productos">Selecciona Productos:</label>
        <div id="productos">
            @foreach ($productos as $producto)
                <div>
                    <input type="checkbox" id="producto_{{ $producto->id }}" name="productos[]" value="{{ $producto->id }}">
                    <label for="producto_{{ $producto->id }}">{{ $producto->producto }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit">Filtrar</button>
    </form>
</body>
</html>
