<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;

class NuevoController extends Controller
{
    public function index2()
    {
        $productos = Producto::orderBy('producto')->get(); 
        return view('graficos.index2', compact('productos')); 
    }

    public function filtrar2(Request $request)
    {
        $productos = $request->input('productos');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $productosSeleccionados = Producto::whereIn('id', $productos)->get()->sortBy('producto');

        $resultados = $this->getDatosPorProductoYTipoLugar($productos, $fechaInicio, $fechaFin);

        $totalGeneralVallas = $this->getTotalGeneralPorTipoLugar(1, $fechaInicio, $fechaFin);
        $totalGeneralTransmilenio = $this->getTotalGeneralPorTipoLugar(3, $fechaInicio, $fechaFin);

        $porcentajesTipolugar1 = $this->calcularPorcentajes($resultados, $totalGeneralVallas, 1, $productos);
        $porcentajesTipolugar3 = $this->calcularPorcentajes($resultados, $totalGeneralTransmilenio, 3, $productos);

        $porcentajesTipolugar1List = array_values($porcentajesTipolugar1);
        $porcentajesTipolugar3List = array_values($porcentajesTipolugar3);

        return view('graficos.filtrados', compact('productosSeleccionados', 'porcentajesTipolugar1List', 'porcentajesTipolugar3List', 'fechaInicio', 'fechaFin'));
    }

    private function getDatosPorProductoYTipoLugar($productos, $fechaInicio, $fechaFin)
    {
        return DB::table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->selectRaw('datos.producto2, lugares.tipolugares_id as lugar_id, COUNT(*) as total')
            ->whereIn('datos.producto2', $productos)
            ->whereBetween('datos.created_at', [$fechaInicio, $fechaFin])
            ->groupBy('datos.producto2', 'lugares.tipolugares_id')
            ->get()
            ->groupBy('producto2');
    }

    private function getTotalGeneralPorTipoLugar($lugarId, $fechaInicio, $fechaFin)
    {
        return DB::table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->selectRaw('COUNT(*) as total')
            ->where('lugares.tipolugares_id', $lugarId)
            ->whereBetween('datos.created_at', [$fechaInicio, $fechaFin])
            ->value('total');
    }

    private function calcularPorcentajes($resultados, $totalGeneral, $lugarId, $productos)
    {
        $porcentajes = [];

        foreach ($productos as $productoId) {
            if (isset($resultados[$productoId])) {
                $totalProducto = $resultados[$productoId]->where('lugar_id', $lugarId)->sum('total');
                $porcentaje = $totalGeneral ? round(($totalProducto * 100.0 / $totalGeneral), 1) : 0;
            } else {
                $porcentaje = 0;
            }
            $porcentajes[$productoId] = $porcentaje;
        }

        return $porcentajes;
    }
}