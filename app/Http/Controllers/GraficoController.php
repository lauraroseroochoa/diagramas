<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraficoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('producto')->get(); 
        return view('graficos.index', compact('productos')); 
    }

    public function filtrar(Request $request)
    {
        $productoId = $request->input('producto_id');
        $producto = Producto::find($productoId);

        $data = $this->getDatosPorMesYTipoLugar($productoId);
        

        $totalGeneralVallas = $this->getTotalPorMesGeneral(1);
        $totalGeneralTransmilenio = $this->getTotalPorMesGeneral(3);
        $datosVallas = $this->getDatosVallasTransmilenio($data, 1);
        $datosTransmilenio = $this->getDatosVallasTransmilenio($data, 3);

        $datosVallasPorcentaje = $this->calcularPorcentaje($datosVallas, $totalGeneralVallas);
        $datosTransmilenioPorcentaje = $this->calcularPorcentaje($datosTransmilenio, $totalGeneralTransmilenio);

        return view('graficos.resultado', compact('producto', 'datosVallas', 'datosTransmilenio', 'datosVallasPorcentaje', 'datosTransmilenioPorcentaje'));
    }

    private function getDatosPorMesYTipoLugar($productoId)
    {
        return DB::table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->selectRaw('MONTH(datos.created_at) as mes, lugares.tipolugares_id as lugar_id, COUNT(*) as total')
            ->where('datos.producto2', $productoId)
            ->whereYear('datos.created_at', 2024)
            ->groupBy(DB::raw('MONTH(datos.created_at)'), 'lugares.tipolugares_id')
            ->get();
    }

    private function getTotalPorMesGeneral($lugarId)
    {
        return DB::table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->selectRaw('MONTH(datos.created_at) as mes, COUNT(*) as total')
            ->where('lugares.tipolugares_id', $lugarId)
            ->whereYear('datos.created_at', 2024)
            ->groupBy(DB::raw('MONTH(datos.created_at)'))
            ->get()
            ->pluck('total', 'mes')
            ->all();
    }

    private function getDatosVallasTransmilenio($data, $lugarId)
    {
        $groupedData = $data->groupBy('mes');
        $datos = array_fill(0, 12, 0);

        foreach ($groupedData as $mes => $items) {
            foreach ($items as $item) {
                if ($item->lugar_id == $lugarId) {
                    $datos[$mes - 1] = $item->total;
                }
            }
        }

        return $datos;
    }

    // private function getTotalPorMes($data, $lugarId)
    // {
    //     $groupedData = $data->groupBy('mes');
    //     $totalPorMes = array_fill(0, 12, 0);

    //     foreach ($groupedData as $mes => $items) {
    //         foreach ($items as $item) {
    //             $totalPorMes[$mes - 1] += $item->total;
    //         }
    //     }

    //     return $totalPorMes;
    // }

    private function calcularPorcentaje($datos, $totalGeneral)
    {
        $porcentaje = array_fill(0, 12, 0);

        for ($i = 0; $i < 12; $i++) {
            if (isset($totalGeneral[$i + 1]) && $totalGeneral[$i + 1] > 0) {
                $porcentaje[$i] = round(($datos[$i] * 100.0 / $totalGeneral[$i + 1]), 1);
            }
        }

        return $porcentaje;
    }
}
