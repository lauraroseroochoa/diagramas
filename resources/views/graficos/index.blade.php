@extends('layouts.app')

@section('title', 'Filtrar Gráfico')

@section('content')
<div class="container">
    <form action="{{ url('/grafico/filtrar') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="producto">Producto:</label>
            <select name="producto_id" id="producto" class="form-control">
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->producto }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</div>

<style>
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .form-group label {
        font-weight: bold;
    }
    
    .form-control {
        margin-bottom: 15px;
        border-radius: 5px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        border-radius: 5px;
        padding: 10px 20px;
    }
    
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>
@endsection
