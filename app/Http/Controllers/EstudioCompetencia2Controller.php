<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;

class EstudioCompetencia3Controller extends Controller
{
    function __construct()
    {
        $this->middleware('permission:estudio-competencia-list', ['only' => ['index', 'show']]);
    }

    public function ocupacion($unidad, $periodo, $empresa, $uso, $marca)
    {
        // Separar el período en mes y año
        $periodo = explode("-", $periodo);
        $month = (int)$periodo[0];
        $year = (int)$periodo[1];
        
        // Obtener el último día del mes
        $ultimoDiaMes = Carbon::create($year, $month)->endOfMonth();
        

        // Obtener los datos con el porcentaje para tipolugares_id = 1
        $datosTipo1 = DB::connection('mysql2')
            ->table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('propietarios', 'lugares.propietarios_id', '=', 'propietarios.id')
            ->join(DB::raw('(SELECT lugares.tipolugares_id, COUNT(*) AS total 
                             FROM datos 
                             JOIN lugares ON datos.lugares_id = lugares.id 
                             WHERE lugares.estado = 1
                             AND lugares.tipolugares_id = 1
                             AND MONTH(datos.created_at) = ' . $ultimoDiaMes . ' 
                             AND YEAR(datos.created_at) = ' . $year . ' 
                             GROUP BY lugares.tipolugares_id) AS total_lugar'),
                   'lugares.tipolugares_id', '=', 'total_lugar.tipolugares_id')
            ->select('propietarios.descripcion as propietario_nombre',
                     DB::raw('COUNT(*) as total'),
                     DB::raw('ROUND((COUNT(*) * 100.0 / total_lugar.total), 1) AS porcentaje'))
            ->where('lugares.estado', 1)
            ->where('lugares.tipolugares_id', $unidad)
            ->whereMonth('datos.created_at', $ultimoDiaMes)
            ->whereYear('datos.created_at', $year)
            ->groupBy('propietarios.descripcion', 'lugares.tipolugares_id', 'total_lugar.total')
            ->get();

        
        $datosTipo2 = DB::connection('mysql2')
            ->table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('propietarios', 'lugares.propietarios_id', '=', 'propietarios.id')
            ->join(DB::raw('(SELECT lugares.tipolugares_id, COUNT(*) AS total 
                             FROM datos 
                             JOIN lugares ON datos.lugares_id = lugares.id 
                             WHERE lugares.estado = 1
                             AND lugares.tipolugares_id = 2
                             AND MONTH(datos.created_at) = ' . $ultimoDiaMes . ' 
                             AND YEAR(datos.created_at) = ' . $year . ' 
                             GROUP BY lugares.tipolugares_id) AS total_lugar'),
                   'lugares.tipolugares_id', '=', 'total_lugar.tipolugares_id')
            ->select('propietarios.descripcion as propietario_nombre',
                     DB::raw('COUNT(*) as total'),
                     DB::raw('ROUND((COUNT(*) * 100.0 / total_lugar.total), 1) AS porcentaje'))
            ->where('lugares.estado', 1)
            ->where('lugares.tipolugares_id', $unidad)
            ->whereMonth('datos.created_at', $ultimoDiaMes)
            ->whereYear('datos.created_at', $year)
            ->groupBy('propietarios.descripcion', 'lugares.tipolugares_id', 'total_lugar.total')
            ->get();
        $propietarioNombre = $datosTipo1->pluck('propietario_nombre');
        
        $porcentajes = $datosTipo1->pluck('porcentaje')->map(function ($value) {
            return (float) $value;});
        
        return view('estudio-competencia.graficos.ocupacion', compact('datos', 'propietarioNombre', 'porcentajes'));
        
    }
}

