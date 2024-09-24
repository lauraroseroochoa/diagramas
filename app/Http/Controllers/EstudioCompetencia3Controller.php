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

    public function anunciantes($unidad, $periodo, $empresa, $uso)
    {
        
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $anunciantes = DB::connection('mysql2')->table('anunciantes')->get()->keyBy('id')->all();

        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $data2 = DB::connection('mysql2')->table('datos')
            ->select(
                'anunciantes.anunciante',
                'anunciantes.id as anunciantes_id',
                'anunciantes_productos.producto',
                'anunciantes_productos.anunciante_id as anunciante_id',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.pantallaNumero',
                'datos.lugares_id'
            )
            ->leftJoin('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->leftJoin('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->leftJoin('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data2->where('lugares.propietarios_id', $empresa);
            } else {
                $data2->where('datos.comercializador2_id', $empresa);
            }
        }
        if ($uso == 1) {
            $data2->where('datos.tipopautas_id', '=', 1);
        }
        if ($uso == 2) {
            $data2->where('datos.tipopautas_id', '!=', 1);
        }
        $datos2 = $data2->get();
        $anunciantes = $datos2->unique('anunciante')
            ->pluck('anunciante','anunciantes_id')
            ->sort()
            ->all();
        $productosXanunciante = [];
        $comercializadoresXanunciante = [];
        $lugaresXanunciante = [];
        


        foreach ($datos2 as $key => $value) {

            if (isset($productosXanunciante[$value->anunciantes_id][$value->producto])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    if (isset($productosXanunciante[$value->anunciantes_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $productosXanunciante[$value->anunciantes_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $productosXanunciante[$value->anunciantes_id][$value->producto]['total']++;
                            $productosXanunciante[$value->anunciantes_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $productosXanunciante[$value->anunciantes_id][$value->producto]['total']++;
                        $productosXanunciante[$value->anunciantes_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $productosXanunciante[$value->anunciantes_id][$value->producto]['total']++;
                }
            } else {
                $productosXanunciante[$value->anunciantes_id][$value->producto]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    $productosXanunciante[$value->anunciantes_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }

            if (isset($comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    if (isset($comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                            $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                        $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                }
            } else {
                $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    $comercializadoresXanunciante[$value->anunciantes_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }

            $lugaresXanunciante[$value->anunciantes_id][$value->lugares_id]['direccion'] = $value->direccion;
            $lugaresXanunciante[$value->anunciantes_id][$value->lugares_id]['coordenadas'] = $value->coordenadas;
        }
         //dd($comercializadoresXanunciante);
        // dd($productosXanunciante);
        // dd($lugaresXanunciante);
        return view('estudio-competencia.graficos.anunciantes', compact('anunciantes', 'datos2', 'productosXanunciante', 'comercializadoresXanunciante','lugaresXanunciante'));
    }

    public function clientes($unidad, $periodo, $empresa, $uso)
    {
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $clientes = DB::connection('mysql2')->table('clientes')->get()->keyBy('id')->all();

        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();
       
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $data = DB::connection('mysql2')->table('datos')
            ->select(
                'clientes.cliente',
                'clientes.id as cliente_id',
                'anunciantes.anunciante',
                'anunciantes.id as anunciantes_id',
                'anunciantes_productos.producto',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.pantallaNumero',
                'datos.lugares_id'
            )
            ->leftJoin('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->leftJoin('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->leftJoin('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->leftJoin('clientes', 'clientes.id', '=', 'datos.clientes_id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data->where('lugares.propietarios_id', $empresa);
            } else {
                $data->where('datos.comercializador2_id', $empresa);
            }
        }
        if ($uso == 1) {
            $data->where('datos.tipopautas_id', '=', 1);
        }
        if ($uso == 2) {
            $data->where('datos.tipopautas_id', '!=', 1);
        }

        $datos = $data->get();
        $clientes = $datos->unique('cliente')
            ->pluck('cliente','cliente_id')
            ->sort()
            ->all();

        $anunciantesXcliente = [];
        $productosXcliente = [];
        $comercializadoresXcliente = [];
        $lugaresXcliente = [];

        foreach ($datos as $key => $value) {
    
            // Anunciantes por cliente
            if (isset($anunciantesXcliente[$value->cliente_id][$value->anunciante])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    if (isset($anunciantesXcliente[$value->cliente_id][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $anunciantesXcliente[$value->cliente_id][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $anunciantesXcliente[$value->cliente_id][$value->anunciante]['total']++;
                            $anunciantesXcliente[$value->cliente_id][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $anunciantesXcliente[$value->cliente_id][$value->anunciante]['total']++;
                        $anunciantesXcliente[$value->cliente_id][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $anunciantesXcliente[$value->cliente_id][$value->anunciante]['total']++;
                }
            } else {
                $anunciantesXcliente[$value->cliente_id][$value->anunciante]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    $anunciantesXcliente[$value->cliente_id][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }
    
            // Productos por cliente
            if (isset($productoXcliente[$value->cliente_id][$value->producto])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    if (isset($productoXcliente[$value->cliente_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $productoXcliente[$value->cliente_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $productoXcliente[$value->cliente_id][$value->producto]['total']++;
                            $productoXcliente[$value->cliente_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $productoXcliente[$value->cliente_id][$value->producto]['total']++;
                        $productoXcliente[$value->cliente_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $productoXcliente[$value->cliente_id][$value->producto]['total']++;
                }
            } else {
                $productoXcliente[$value->cliente_id][$value->producto]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    $productoXcliente[$value->cliente_id][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }
            // Comercializadores por cliente
            if (isset($comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    if (isset($comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                            $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                        $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion]['total']++;
                }
            } else {
                $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    // Pantalla LED
                    $comercializadorXcliente[$value->cliente_id][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }
            $lugaresXcliente[$value->cliente_id][$value->lugares_id]['direccion'] = $value->direccion;
            $lugaresXcliente[$value->cliente_id][$value->lugares_id]['coordenadas'] = $value->coordenadas;
        }
    
        return view('estudio-competencia.graficos.clientes', compact('clientes', 'datos', 'anunciantesXcliente', 'productoXcliente', 'comercializadorXcliente', 'lugaresXcliente'));
    }

    public function ocupacion($unidad, $periodo, $empresa, $uso)
    {
        
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();
        
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
       
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $anunciantesProductos = DB::connection('mysql2')
            ->table('anunciantes_productos')
            ->pluck('anunciante_id','id')
            ->all();

        $data2 = DB::connection('mysql2')->table('datos')
            ->select(
                'datos.lugares_id',
                'datos.tipopautas_id',
                DB::raw("
                    GROUP_CONCAT(DISTINCT
                        CASE 
                            WHEN {$unidad} = 1 THEN lugares.propietarios_id 
                            ELSE datos.comercializador
                        END
                        SEPARATOR ','
                    ) as comercializadorId
                "),
                DB::raw("
                    GROUP_CONCAT(DISTINCT
                        datos.producto2
                        SEPARATOR ','
                    ) as producto2
                "),
                'datos.comercializador',
                'datos.pantallaNumero'
            )
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesLedId)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if($empresa!=0){
            if ($unidad == 1) {
                $data2->where('lugares.propietarios_id', $empresa);    
            } else {
                $data2->where('datos.comercializador', $empresa);
            }    
        }

        //$data2->where('datos.tipopautas_id', '=', 1);
        /*
        if($uso==1){
            $data2->where('datos.tipopautas_id', '=', 1);
        }
        if($uso==2){
            $data2->where('datos.tipopautas_id', '!=', 1);
        }
        */

        $data2->groupBy('lugares_id','pantallaNumero');
        $datos = $data2->get();

        $datos2 = [];
        foreach ($datos as $value) {
            $comercializadorId = $value->comercializadorId;
            $comercializadorIdsArray = explode(',', $comercializadorId);

            if (count($comercializadorIdsArray) > 1) {
                $comercializadorId = $value->comercializador;
            }

            if (!isset($datos2[$comercializadorId])) {
                $datos2[$comercializadorId] = [
                    'total' => 0,
                    'ventas' => 0
                ];
            }

            $datos2[$comercializadorId]['total']++;
            

            $producto2Array = explode(",", $value->producto2);

            foreach ($producto2Array as $producto2Id) {

                    if (!isset($datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                        $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero] = [];
                       
                    }
                    
                    if (!in_array($anunciantesProductos[$producto2Id], $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                        $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                        $datos2[$comercializadorId]['ventas']++;
                    }
            }
        }

        $datos2 = collect($datos2)->sortByDesc(function($item) {
            return ($item['ventas'] * 100 / ($item['total'] * 6));
        })->all();

        $aplicarMultiplicacion = ($unidad != 1);


        switch ($unidad) {
            case 1: //vallas


                $data = DB::connection('mysql2')->table('lugares')
                    ->select('id','propietarios_id','tipovalla_id')
                    ->where('tipolugares_id', $unidad)
                    ->whereDate('created_at', '<=', $ultimoDiaMes)
                    ->where(function($query) use($ultimoDiaMes) {
                        $query->where('fecha_desactivacion','>=',$ultimoDiaMes)
                            ->orWhereNull('fecha_desactivacion');
                    });
                if($empresa!=0 && $unidad == 1){
                    $data->where('propietarios_id',$empresa);
                }
                /*
                if($uso!=0){
                    $data->where('usos_id',$uso);
                }*/
                $data->where('usos_id',1);
                $data = $data->get();

                $dataLugares = $data->pluck('id');

                
                $data2 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        'datos.tipopautas_id',
                        DB::raw("
                            GROUP_CONCAT(DISTINCT
                                CASE
                                    WHEN {$unidad} = 1 THEN lugares.propietarios_id
                                    ELSE datos.comercializador
                                END
                                SEPARATOR ','
                            ) as comercializadorId
                        "),
                        DB::raw("
                            GROUP_CONCAT(DISTINCT
                                datos.producto2
                                SEPARATOR ','
                            ) as producto2
                        "),
                        'datos.comercializador',
                        'datos.pantallaNumero',
                        'datos.tipopublicidades_id',
                    )
                    ->where('datos.tipopautas_id', '=', 1)
                    ->whereIn('datos.lugares_id', $dataLugares)
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes)
                    ->leftJoin('lugares','lugares.id','=','datos.lugares_id');
                //filtro solo comercial porque es para ocupacion
                //$data2->where('datos.tipopautas_id', '=', 1);
                if($empresa!=0){
                    if ($unidad == 1) {
                        $data2->where('lugares.propietarios_id', $empresa);
                    } else {
                        $data2->where('datos.comercializador', $empresa);
                    }
                }

                /*
                if($uso==1){
                    $data2->where('datos.tipopautas_id', '=', 1);
                }
                if($uso==2){
                    $data2->where('datos.tipopautas_id', '!=', 1);
                }
                */
                $data2->groupBy('lugares_id','pantallaNumero','tipopublicidades_id');
                $data2 = $data2->get()->keyBy('lugares_id');



                // Procesamiento de datos
                $ocupacionLed = [];
                $ocupacionTradicional = [];
                $ocupacionGeneral = [];
                foreach ($data as $value)
                {
                    $lugareId = $value->id;

                    if(isset($data2[$lugareId]) && in_array($data2[$lugareId]->tipopublicidades_id,$tipoPublicidadesLedId)){
                        if (!isset($ocupacionLed[$value->propietarios_id])) {
                            $ocupacionLed[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionLed[$value->propietarios_id]['total'] += 5;
                    }else if(in_array($value->tipovalla_id,$tipoPublicidadesLedId)){
                        if (!isset($ocupacionLed[$value->propietarios_id])) {
                            $ocupacionLed[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionLed[$value->propietarios_id]['total']+=5;
                    }

                    if(isset($data2[$lugareId]) && in_array($data2[$lugareId]->tipopublicidades_id,$tipoPublicidadesTradicionalId)){
                        if (!isset($ocupacionTradicional[$value->propietarios_id])) {
                            $ocupacionTradicional[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionTradicional[$value->propietarios_id]['total']++;
                    }else if(in_array($value->tipovalla_id,$tipoPublicidadesTradicionalId)){
                        if (!isset($ocupacionTradicional[$value->propietarios_id])) {
                            $ocupacionTradicional[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionTradicional[$value->propietarios_id]['total']++;
                    }

                    if (!isset($ocupacionGeneral[$value->propietarios_id])) {
                        $ocupacionGeneral[$value->propietarios_id] = [
                            'total' => 0,
                            'ventas' => 0
                        ];
                    }
                    if(isset($data2[$lugareId])){
                        if(in_array($data2[$lugareId]->tipopublicidades_id,$tipoPublicidadesLedId)){
                            $ocupacionGeneral[$value->propietarios_id]['total'] += 5;
                        }else{
                            $ocupacionGeneral[$value->propietarios_id]['total']++;
                        }
                    }else{
                        if(in_array($value->tipovalla_id,$tipoPublicidadesLedId)){
                            $ocupacionGeneral[$value->propietarios_id]['total'] += 5;
                        }else{
                            $ocupacionGeneral[$value->propietarios_id]['total']++;
                        }
                    }



                    if(isset($data2[$lugareId]))
                    {
                        $comercializadorId = $data2[$lugareId]->comercializadorId;
                        $comercializadorIdsArray = explode(',', $comercializadorId);
                        if (count($comercializadorIdsArray) > 1) {
                            $comercializadorId = $data2[$lugareId]->comercializador;
                        }

                        $producto2Array = explode(",", $data2[$lugareId]->producto2);

                        foreach ($producto2Array as $producto2Id) {


                            if(in_array($data2[$lugareId]->tipopublicidades_id,$tipoPublicidadesLedId)){
                                if (!isset($ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero] = [];
                                }
                                if (!in_array($anunciantesProductos[$producto2Id], $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                                    $ocupacionLed[$comercializadorId]['ventas']++;
                                }
                            }


                            if(in_array($data2[$lugareId]->tipopublicidades_id,$tipoPublicidadesTradicionalId)){
                                if (!isset($ocupacionTradicional[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionTradicional[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero] = [];
                                }
                                if (!in_array($anunciantesProductos[$producto2Id], $ocupacionTradicional[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionTradicional[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                                    $ocupacionTradicional[$comercializadorId]['ventas']++;
                                }
                            }

                            if (!isset($ocupacionGeneral[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                $ocupacionGeneral[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero] = [];
                            }
                            if (!in_array($anunciantesProductos[$producto2Id], $ocupacionGeneral[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                $ocupacionGeneral[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero][] = $anunciantesProductos[$producto2Id];


                                $ocupacionGeneral[$comercializadorId]['ventas']++;
                            }

                        }


                    }
                }

                $ocupacionLed = collect($ocupacionLed)->sortByDesc(function($item) {
                     return ($item['ventas'] * 100 / ($item['total']));
                })->all();

                $ocupacionTradicional = collect($ocupacionTradicional)->sortByDesc(function($item) {
                     return ($item['ventas'] * 100 / ($item['total']));
                })->all();

                $ocupacionGeneral = collect($ocupacionGeneral)->sortByDesc(function($item) {
                     return ($item['ventas'] * 100 / ($item['total']));
                })->all();




                // $datos2 = collect($datos2)->sortByDesc(function($item) {
                //     return ($item['ventas'] * 100 /($item['total'])); 
                // })->all();


dd($ocupacionTradicional);
                return view('estudio-competencia.graficos.ocupacionVallas', compact('empresas', 'ocupacionLed', 'ocupacionTradicional','ocupacionGeneral'));
            break;

            default:
                return view('estudio-competencia.graficos.ocupacionOtros',compact('empresas','datos2', 'aplicarMultiplicacion'));
            break;
        }
    }

    public function rankingAnunciantes($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

       
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray(); 
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray(); 

        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id') 
            ->select(
                'anunciantes_productos.anunciante_id', 
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesTradicionalId).') THEN 1 ELSE 0 END) as totalTradicional'), // Conteo de tradicionales
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesLedId).') THEN 1 ELSE 0 END) as totalLed') // Conteo de LED
            )
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->groupBy('anunciantes_productos.anunciante_id');

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data->where('lugares.propietarios_id', $empresa);    
            } else {
                $data->where('datos.comercializador', $empresa);
            }    
        }

        if ($uso == 1) {
            $data->where('datos.tipopautas_id', '=', 1);
        } elseif ($uso == 2) {
            $data->where('datos.tipopautas_id', '!=', 1);
        }

        $data = $data->get();

        $ranking = [];

        foreach ($data as $value) {
            $anuncianteId = $value->anunciante_id;
            $totalTradicional = $value->totalTradicional;
            $totalLed = $value->totalLed;
            $total = $totalTradicional + $totalLed;

            if (!isset($ranking[$anuncianteId])) {
                $ranking[$anuncianteId] = [
                    'nombre' => DB::connection('mysql2')->table('anunciantes')->where('id', $anuncianteId)->value('anunciante'),
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            $ranking[$anuncianteId]['tradicional'] += $totalTradicional;
            $ranking[$anuncianteId]['led'] += $totalLed;
            $ranking[$anuncianteId]['total'] += $total;
        }

        $ranking = collect($ranking)->sortByDesc('total')->values()->all();

        return view('estudio-competencia.graficos.rankingAnunciantes', compact('ranking'));
    }


    public function rankingClientes($unidad, $periodo, $empresa, $uso)
    {
        
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $anunciantesProductos = DB::connection('mysql2')
            ->table('anunciantes_productos')
            ->pluck('anunciante_id', 'id')
            ->all();

        $data2 = DB::connection('mysql2')->table('datos')
            ->select(
                'datos.clientes_id',
                DB::raw("
                    GROUP_CONCAT(DISTINCT
                        datos.producto2
                        SEPARATOR ','
                    ) as producto2
                "),
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesTradicionalId).') THEN 1 ELSE 0 END) as totalTradicional'), // Conteo de tradicionales
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesLedId).') THEN 1 ELSE 0 END) as totalLed') // Conteo de LED
            )
            ->join('tipopublicidades', 'datos.tipopublicidades_id', '=', 'tipopublicidades.id')
            // ->whereIn('datos.tipopublicidades_id', array_merge($tipoPublicidadesTradicionalId, $tipoPublicidadesLedId))
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if($empresa!=0){
            if ($unidad == 1) {
                $data2->where('lugares.propietarios_id', $empresa);    
            } else {
                $data2->where('datos.comercializador', $empresa);
            }    
        }

        // $data2->where('datos.tipopautas_id', '=', 1);
        if ($uso == 1) {
            $data2->where('datos.tipopautas_id', '=', 1);
        } elseif ($uso == 2) {
            $data2->where('datos.tipopautas_id', '!=', 1);
        }

        $data2->groupBy('datos.clientes_id');
        $datos = $data2->get();

        $rankingClientes = [];
        foreach ($datos as $value) {
            $clienteId = $value->clientes_id;
            $totalTradicional = $value->totalTradicional;
            $totalLed = $value->totalLed;
            $total = $totalTradicional + $totalLed;

            if (!isset($rankingClientes[$clienteId])) {
                $rankingClientes[$clienteId] = [
                    'nombre' => DB::connection('mysql2')->table('clientes')->where('id', $clienteId)->value('cliente'),
                    'totalTradicional' => 0,
                    'totalLed' => 0,
                    'total' => 0
                ];
            }

            $rankingClientes[$clienteId]['totalTradicional'] += $totalTradicional;
            $rankingClientes[$clienteId]['totalLed'] += $totalLed;
            $rankingClientes[$clienteId]['total'] += $total;
        }
        $rankingClientes = collect($rankingClientes)->sortByDesc('total')->values()->all();

        return view('estudio-competencia.graficos.rankingClientes', compact('rankingClientes'));
    }

    public function rankingProductos($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray(); 
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray(); 

        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->select(
                'anunciantes_productos.id as producto_id', 
                'anunciantes_productos.producto as nombre_producto', 
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesTradicionalId).') THEN 1 ELSE 0 END) as totalTradicional'), 
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesLedId).') THEN 1 ELSE 0 END) as totalLed') 
            )
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->groupBy('anunciantes_productos.id', 'anunciantes_productos.producto');

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data->where('lugares.propietarios_id', $empresa);
            } else {
                $data->where('datos.comercializador', $empresa);
            }    
        }

        if ($uso == 1) {
            $data->where('datos.tipopautas_id', '=', 1);
        } elseif ($uso == 2) {
            $data->where('datos.tipopautas_id', '!=', 1);
        }

        $data = $data->get();

        $ranking = [];

        foreach ($data as $value) {
            $productoId = $value->producto_id;
            $totalTradicional = $value->totalTradicional;
            $totalLed = $value->totalLed;
            $total = $totalTradicional + $totalLed;

            if (!isset($ranking[$productoId])) {
                $ranking[$productoId] = [
                    'nombre_producto' => $value->nombre_producto,
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            $ranking[$productoId]['tradicional'] += $totalTradicional;
            $ranking[$productoId]['led'] += $totalLed;
            $ranking[$productoId]['total'] += $total;
        }

        $ranking = collect($ranking)->sortByDesc('total')->values()->all();

        return view('estudio-competencia.graficos.rankingProductos', compact('ranking'));
    }

    public function rankingGrupos($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        
        $data = DB::connection('mysql2')->table('datos')
            ->join('clientes', 'datos.clientes_id', '=', 'clientes.id')
            ->join('grupos', 'clientes.grupo_id', '=', 'grupos.id') 
            ->select(
                'grupos.id as grupo_id',
                'grupos.descr as grupo_desc',
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesTradicionalId).') THEN 1 ELSE 0 END) as totalTradicional'),
                DB::raw('SUM(CASE WHEN datos.tipopublicidades_id IN ('.implode(',', $tipoPublicidadesLedId).') THEN 1 ELSE 0 END) as totalLed')
            )
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->groupBy('grupos.id', 'grupos.descr');

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data->where('lugares.propietarios_id', $empresa);
            } else {
                $data->where('datos.comercializador', $empresa);
            }
        }

        if ($uso == 1) {
            $data->where('datos.tipopautas_id', '=', 1);
        } elseif ($uso == 2) {
            $data->where('datos.tipopautas_id', '!=', 1);
        }

        $data = $data->get();

        $ranking = [];

        foreach ($data as $value) {
            $grupoId = $value->grupo_id;
            $grupoDesc = $value->grupo_desc;
            $totalTradicional = $value->totalTradicional;
            $totalLed = $value->totalLed;
            $total = $totalTradicional + $totalLed;

            if (!isset($ranking[$grupoId])) {
                $ranking[$grupoId] = [
                    'nombre' => $grupoDesc,
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            $ranking[$grupoId]['tradicional'] += $totalTradicional;
            $ranking[$grupoId]['led'] += $totalLed;
            $ranking[$grupoId]['total'] += $total;
        }

        $ranking = collect($ranking)->sortByDesc('nombre')->values()->all();

        return view('estudio-competencia.graficos.rankingGrupos', compact('ranking'));
    }
}