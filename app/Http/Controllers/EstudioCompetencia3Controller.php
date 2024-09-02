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

    public function ocupacion($unidad, $periodo, $empresa, $uso)
    {

        $periodo = explode("-", $periodo);
        $month = (int)$periodo[0];
        $year = (int)$periodo[1];

        $ultimoDiaMes = Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');

        $datosTipo1 = DB::connection('mysql2')
            ->table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('propietarios', 'lugares.propietarios_id', '=', 'propietarios.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->join(
                DB::raw('(SELECT lugares.tipolugares_id, COUNT(*) AS total
                             FROM datos
                             JOIN lugares ON datos.lugares_id = lugares.id
                             JOIN anunciantes_productos ON datos.producto2 = anunciantes_productos.id
                             WHERE lugares.estado = 1
                             AND lugares.usos_id = ' . $uso . '
                             ' . ($uso == 1 ? 'AND anunciantes_productos.tipoPauta = "comercial"' : '') . '
                             AND lugares.tipolugares_id = ' . $unidad . '
                             AND datos.created_at <= "' . $ultimoDiaMes . '"
                             
                             GROUP BY lugares.tipolugares_id) AS total_lugar'),
                'lugares.tipolugares_id',
                '=',
                'total_lugar.tipolugares_id'
            )
            ->select(
                'propietarios.descripcion as propietario_nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND((COUNT(*) * 100 / total_lugar.total), 1) AS porcentaje')
            )
            ->where('lugares.estado', 1)
            ->where('lugares.usos_id', $uso)
            ->when($uso == 1, function ($query) {
                return $query->where('anunciantes_productos.tipoPauta', 'comercial');
            })
            ->where('lugares.tipolugares_id', $unidad)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            
            ->when($empresa != 0, function($query) use ($empresa) {
                return $query->where('propietarios.id', $empresa);
            })
            ->groupBy('propietarios.descripcion', 'lugares.tipolugares_id', 'total_lugar.total')
            ->get();
        $propietarioNombre = $datosTipo1->pluck('propietario_nombre');
        $total = $datosTipo1->pluck('total');

        $porcentajes = $datosTipo1->pluck('porcentaje')->map(function ($value) {
            return (float) $value;
        });


        $datosTipo2 = DB::connection('mysql2')
            ->table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('propietarios', 'lugares.propietarios_id', '=', 'propietarios.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->join(
                DB::raw('(SELECT lugares.tipolugares_id, COUNT(*) AS total
                             FROM datos
                             JOIN lugares ON datos.lugares_id = lugares.id
                             JOIN anunciantes_productos ON datos.producto2 = anunciantes_productos.id
                             WHERE lugares.estado = 1
                             AND lugares.usos_id = ' . $uso . '
                             ' . ($uso == 1 ? 'AND anunciantes_productos.tipoPauta = "comercial"' : '') . '
                             AND lugares.tipolugares_id =' . $unidad . '
                             AND lugares.tipovalla_id = 2
                             AND datos.created_at <= "' . $ultimoDiaMes . '"
                             GROUP BY lugares.tipolugares_id) AS total_lugar'),
                'lugares.tipolugares_id',
                '=',
                'total_lugar.tipolugares_id'
            )
            ->select(
                'propietarios.descripcion as propietario_nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND((COUNT(*) * 100 / total_lugar.total), 1) AS porcentaje')
            )
            ->where('lugares.estado', 1)
            ->where('lugares.usos_id', $uso)
            ->when($uso == 1, function ($query) {
                return $query->where('anunciantes_productos.tipoPauta', 'comercial');
            })
            ->where('lugares.tipolugares_id', $unidad)
            ->where('lugares.tipovalla_id', 2)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->when($empresa != 0, function($query) use ($empresa) {
                return $query->where('propietarios.id', $empresa);
            })
            ->groupBy('propietarios.descripcion', 'lugares.tipolugares_id', 'total_lugar.total')
            ->get();

        $propietarioNombre2 = $datosTipo2->pluck('propietario_nombre');

        $porcentajes2 = $datosTipo2->pluck('porcentaje')->map(function ($value) {
            return (float) $value;
        });

        $datosTipo3 = DB::connection('mysql2')
            ->table('datos')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('propietarios', 'lugares.propietarios_id', '=', 'propietarios.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->join(
                DB::raw('(SELECT lugares.tipolugares_id, COUNT(*) AS total
                             FROM datos
                             JOIN lugares ON datos.lugares_id = lugares.id
                             JOIN anunciantes_productos ON datos.producto2 = anunciantes_productos.id
                             WHERE lugares.estado = 1
                             AND lugares.usos_id = ' . $uso . '
                             ' . ($uso == 1 ? 'AND anunciantes_productos.tipoPauta = "comercial"' : '') . '
                             AND lugares.tipolugares_id =' . $unidad . '
                             AND lugares.tipovalla_id = 1
                             AND datos.created_at <= "' . $ultimoDiaMes . '"
                             GROUP BY lugares.tipolugares_id) AS total_lugar'),
                'lugares.tipolugares_id',
                '=',
                'total_lugar.tipolugares_id'
            )
            ->select(
                'propietarios.descripcion as propietario_nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND((COUNT(*) * 100.0 / total_lugar.total), 1) AS porcentaje')
            )
            ->where('lugares.estado', 1)
            ->where('lugares.usos_id', $uso)
            ->when($uso == 1, function ($query) {
                return $query->where('anunciantes_productos.tipoPauta', 'comercial');
            })
            ->where('lugares.tipolugares_id', $unidad)
            ->where('lugares.tipovalla_id', 1)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->when($empresa != 0, function($query) use ($empresa) {
                return $query->where('propietarios.id', $empresa);
            })
            ->groupBy('propietarios.descripcion', 'lugares.tipolugares_id', 'total_lugar.total')
            ->get();
        $propietarioNombre3 = $datosTipo3->pluck('propietario_nombre');

        $porcentajes3 = $datosTipo3->pluck('porcentaje')->map(function ($value) {
            return (float) $value;
        });

        return view('estudio-competencia.graficos.ocupacion', compact('total','datosTipo1', 'datosTipo2', 'datosTipo3', 'propietarioNombre', 'porcentajes', 'propietarioNombre2', 'porcentajes2', 'propietarioNombre3', 'porcentajes3'));
    }
}
