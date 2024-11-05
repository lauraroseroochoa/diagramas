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
            $data2->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data2->where('datos.tipopautas_id', '=', 7);
        }
        
        $datos2 = $data2->get();
        $anunciantes = $datos2->unique('anunciante')
            ->pluck('anunciante', 'anunciantes_id')
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

            $lugaresXanunciante[$value->anunciantes_id][$value->lugares_id]['empresa'] = $empresas[$value->comercializadorId]->descripcion;
            $lugaresXanunciante[$value->anunciantes_id][$value->lugares_id]['direccion'] = $value->direccion;
            $lugaresXanunciante[$value->anunciantes_id][$value->lugares_id]['coordenadas'] = $value->coordenadas;
        }
        //dd($comercializadoresXanunciante);
        // dd($productosXanunciante);
        // dd($lugaresXanunciante);
        return view('estudio-competencia.graficos.anunciantes', compact('anunciantes', 'datos2', 'productosXanunciante', 'comercializadoresXanunciante', 'lugaresXanunciante'));
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
            $data->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data->where('datos.tipopautas_id', '=', 7);
        }
        
        $datos = $data->get();
        $clientes = $datos->unique('cliente')
            ->pluck('cliente', 'cliente_id')
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
            $lugaresXcliente[$value->cliente_id][$value->lugares_id]['empresa'] = $empresas[$value->comercializadorId]->descripcion;
            $lugaresXcliente[$value->cliente_id][$value->lugares_id]['direccion'] = $value->direccion;
            $lugaresXcliente[$value->cliente_id][$value->lugares_id]['coordenadas'] = $value->coordenadas;
        }

        return view('estudio-competencia.graficos.clientes', compact('clientes', 'datos', 'anunciantesXcliente', 'productoXcliente', 'comercializadorXcliente', 'lugaresXcliente'));
    }

    public function aforo($unidad, $periodo, $empresa, $uso)
    {
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $propietarios =  DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        $data = DB::connection('mysql2')->table('lugares')
            ->where('tipolugares_id', $unidad)
            ->where('created_at', '<=', $ultimoDiaMes)
            ->where(function ($query) use ($primerDiaMes) {
                $query->where('fecha_desactivacion', '>=', $primerDiaMes)
                    ->orWhereNull('fecha_desactivacion');
            });
        //validamos si el uso es diferente a todos, de ser asi filtramos por usos_id
        if ($uso != 0 && $unidad == 1) {
            $data->where('usos_id', $uso);
        }
        //validamos si el propietario es diferente a todos, de ser asi filtramos por propietario_id
        if ($empresa != 0 && $unidad == 1) {
            $data->where('propietarios_id', $empresa);
        }
        $data = $data->get();

        $totalLugares = count($data);
        switch ($unidad) {
            case 1: //vallas
                $tipovallas = DB::connection('mysql2')->table('tipovallas')->pluck('descripcion', 'id')->all();
                //obtenemos el total de caras desactivadas y el listado en el periorodo indicado
                $desactivados = $data->where('estado', 0)->where('fecha_desactivacion', '<=', $ultimoDiaMes);
                $totalDesactivados = $desactivados->count();
                //obtenemos el total de caras creadas (actidads) y el listado en el periorodo indicado
                $activadoNuevo = $data->where('created_at', '>=', $primerDiaMes)->where('created_at', '<=', $ultimoDiaMes);
                $totalActivados = $activadoNuevo->count();
                //total de caras activas en el periodo inidicado
                $totalLugaresActivos = $totalLugares - $totalDesactivados;
                //cara tipo led
                $totalCarasLed1 = $data->where('tipovalla_id', 2)->whereNull('fecha_desactivacion')->count();
                $totalCarasLed2 = $data->where('tipovalla_id', 2)->where('fecha_desactivacion', '>=', $ultimoDiaMes)->count();
                $totalCarasLed = $totalCarasLed1 + $totalCarasLed2;
                //cara tipo tradicional
                $totalCarasTradicional1 = $data->where('tipovalla_id', 1)->where('fecha_desactivacion', '>=', $ultimoDiaMes)->count();
                $totalCarasTradicional2 = $data->where('tipovalla_id', 1)->whereNull('fecha_desactivacion')->count();
                $totalCarasTradicional = $totalCarasTradicional1 + $totalCarasTradicional2;

                return view('estudio-competencia.graficos.aforoVallas', compact('desactivados', 'totalDesactivados', 'activadoNuevo', 'totalActivados', 'totalLugaresActivos', 'totalCarasTradicional', 'totalCarasLed', 'propietarios', 'tipovallas'));
                break;
            default:

                $varTotalLugares = 'estaciones y portales';
                if ($unidad == 2) {
                    $varTotalLugares = 'centros comerciales';
                }
                $lugares = DB::connection('mysql2')->table('lugares')->pluck('direccion', 'id')->all();
                $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
                //filtro los id
                $tipoPublicidadesId = $tipoPublicidades->where('tecnologia', 1)->pluck('id');

                $data2 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw("
                            GROUP_CONCAT(DISTINCT datos.comercializador2_id SEPARATOR ',') as comercializadorId
                        "),
                        'datos.pantallaNumero',
                    )
                    ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
                    ->when($uso == 1, function ($query) {
                        return $query->where('tipopautas_id', 1);
                    })
                    ->when($uso == 2, function ($query) {
                        return $query->where('tipopautas_id', '!=', 1)
                                     ->where('tipopautas_id', '!=', 7);
                    })
                    ->when($uso == 7, function ($query) {
                        return $query->where('tipopautas_id', 7);
                    })                    
                    ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes);
                if ($empresa != 0) {
                    $data2->where('datos.comercializador2_id', $empresa);
                }
                $data2->groupBy('lugares_id', 'pantallaNumero');
                $data2 = $data2->get();


                $data3 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw("
                            GROUP_CONCAT(DISTINCT datos.comercializador2_id SEPARATOR ',') as comercializadorId
                        "),
                        'datos.pantallaNumero',
                    )
                    ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
                    ->when($uso == 1, function ($query) {
                        return $query->where('tipopautas_id', 1);
                    })
                    ->when($uso == 2, function ($query) {
                        return $query->where('tipopautas_id', '!=', 1)
                                     ->where('tipopautas_id', '!=', 7);
                    })
                    ->when($uso == 7, function ($query) {
                        return $query->where('tipopautas_id', 7);
                    })                    

                    ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes);
                if ($empresa != 0) {
                    $data3->where('datos.comercializador2_id', $empresa);
                }
                $data3->groupBy('lugares_id', 'pantallaNumero');
                $data3 = $data3->get();

                $resultadosP = [];
                $resultadosPL = [];
                $resultadosL = [];
                foreach ($data2 as $key => $value) {
                    if (isset($resultadosPL[$value->lugares_id][$value->comercializadorId])) {
                        $resultadosPL[$value->lugares_id][$value->comercializadorId]++;
                    } else {
                        $resultadosPL[$value->lugares_id][$value->comercializadorId] = 1;
                    }

                    if (isset($resultadosL[$value->lugares_id])) {
                        $resultadosL[$value->lugares_id]++;
                    } else {
                        $resultadosL[$value->lugares_id] = 1;
                    }

                    //$comercializadores = explode(",",$value->comercializadorId);
                    /*$propietariosComercializadores = '';
                    foreach ($comercializadores as $key2 => $value2) {
                        $propietariosComercializadores .=  $propietarios[$value2].' - ';
                    }*/
                    /*
                    if(count($comercializadores)==1){
                        if(isset($resultadosP[$value->comercializadorId])){
                            $resultadosP[$value->comercializadorId]++;
                        }else{
                            $resultadosP[$value->comercializadorId]=1;
                        }
                    }
                    */
                }


                foreach ($data3 as $key => $value) {


                    $comercializadores = explode(",", $value->comercializadorId);
                    if (count($comercializadores) == 1) {
                        if (isset($resultadosP[$value->comercializadorId])) {
                            $resultadosP[$value->comercializadorId]++;
                        } else {
                            $resultadosP[$value->comercializadorId] = 1;
                        }
                    }
                }





                arsort($resultadosP);


                return view('estudio-competencia.graficos.aforoOtros', compact('totalLugares', 'propietarios', 'resultadosPL', 'lugares', 'resultadosL', 'resultadosP', 'varTotalLugares'));
                break;
        }
    }


    public function cantidad($unidad, $periodo, $empresa, $uso)
    {

        //convertir periodo a mes y año
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth(); //->endOfDay()
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $empresas = DB::connection('mysql2')->table('propietarios')->pluck('descripcion', 'id')->all();

        switch ($unidad) {
            case 1: //vallas
                $descr = 'caras por empresa';
                if ($empresa != 0) {
                    $descr = 'vallas ' . $empresas[$empresa];
                }
                $sentidos = DB::connection('mysql2')->table('sentidos')->pluck('descripcion', 'id')->all();
                $data = DB::connection('mysql2')->table('lugares')
                    ->select(
                        'propietario',
                        'sentidos_id',
                        'coordenadas',
                        'registro',
                        'via_principal',
                        'direccion',
                        'tipovalla_id',
                        'propietarios_id'
                    )
                    ->where('tipolugares_id', $unidad)
                    ->whereDate('created_at', '<=', $ultimoDiaMes)
                    ->where(function ($query) use ($ultimoDiaMes) {
                        $query->where('fecha_desactivacion', '>=', $ultimoDiaMes)
                            ->orWhereNull('fecha_desactivacion');
                    });

                if ($empresa != 0 && $unidad == 1) {
                    $data->where('propietarios_id', $empresa);
                }

                if ($uso != 0) {
                    $data->where('usos_id', $uso);
                }
                $data = $data->get();


                $datos = $data->groupBy('propietarios_id')->map(function ($items, $propietario) {
                    // Contar el total de registros para cada propietario
                    $total = $items->count();
                    // Contar cuántos registros son "tradicional" (tipovalla_id = 1)
                    $datosTradicional = $items->where('tipovalla_id', 1);
                    $tradicional = $datosTradicional->count();

                    // Contar cuántos registros son "led" (tipovalla_id = 2)
                    $datosLed = $items->where('tipovalla_id', 2);
                    $led = $datosLed->count();

                    return [
                        'total' => $total,
                        'tradicional' => $tradicional,
                        'led' => $led,
                        'datosTradicional' => $datosTradicional->all(),
                        'datosLed' => $datosLed->all(),
                        'datos' => $items->all(),
                    ];
                });

                // Ordenar los resultados en orden decreciente basado en el total
                $datos = $datos->sortByDesc('total');

                //dd($datos);
                return view('estudio-competencia.graficos.cantidadVallas', compact('datos', 'sentidos', 'descr', 'empresas'));
                break;
            default:
                if ($unidad == 2) {
                    $descr = 'centro comercial';
                    if ($empresa != 0) $descr = $empresas[$empresa] . ' y centro comercial';
                } else {
                    $descr = 'estacion/portal';
                    if ($empresa != 0) $descr =  $empresas[$empresa] . ' y estacion/portal';
                }


                $lugares = DB::connection('mysql2')->table('lugares')->get()->keyBy('id')->all();
                $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
                //filtro los id
                $tipoPublicidadesId = $tipoPublicidades->pluck('id');
                $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
                $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();


                $subQuery = DB::connection('mysql2')->table('datos')
                    ->select([
                        'datos.lugares_id',
                        'datos.pantallaNumero',
                        DB::raw('COUNT(DISTINCT anunciantes_productos.anunciante_id) AS uniqueAnunciante')
                    ])
                    ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
                    ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesLedId)

                    ->when($uso == 1, function ($query) {
                        return $query->where('tipopautas_id', 1);
                    })
                    ->when($uso == 2, function ($query) {
                        return $query->where('tipopautas_id', '!=', 1)
                                     ->where('tipopautas_id', '!=', 7);
                    })
                    ->when($uso == 7, function ($query) {
                        return $query->where('tipopautas_id', 7);
                    })                    

                    ->where('datos.created_at', '>=', $primerDiaMes)
                    ->where('datos.created_at', '<=', $ultimoDiaMes)
                    ->when($empresa != 0, function ($query) use ($empresa) {
                        return $query->where('datos.comercializador2_id', $empresa);
                    })
                    ->groupBy('datos.lugares_id', 'datos.pantallaNumero');




                // Convert subquery to SQL string
                $subQuerySql = $subQuery->toSql();
                $bindings = $subQuery->getBindings(); // Get the bindings from the query

                $dataLed = DB::connection('mysql2')->table(DB::raw("({$subQuerySql}) as sub"))
                    ->select([
                        'lugares_id',
                        DB::raw('SUM(uniqueAnunciante) AS totalLed')
                    ])
                    ->addBinding($bindings) // Add the bindings to the outer query
                    ->groupBy('lugares_id')
                    ->get();

                $dataTradicional = DB::connection('mysql2')->table('datos')
                    ->select([
                        'lugares_id',
                        DB::raw("SUM(CASE WHEN tipopublicidades_id IN (" . implode(',', $tipoPublicidadesTradicionalId) . ") THEN 1 ELSE 0 END) AS totalTradicional")
                    ])

                    ->when($uso == 1, function ($query) {
                        return $query->where('tipopautas_id', 1);
                    })
                    ->when($uso == 2, function ($query) {
                        return $query->where('tipopautas_id', '!=', 1)
                                     ->where('tipopautas_id', '!=', 7);
                    })
                    ->when($uso == 7, function ($query) {
                        return $query->where('tipopautas_id', 7);
                    })                    
                    ->where('created_at', '>=', $primerDiaMes)
                    ->where('created_at', '<=', $ultimoDiaMes)
                    ->when($empresa != 0, function ($query) use ($empresa) {
                        return $query->where('comercializador2_id', $empresa);
                    })
                    ->whereIn('tipopublicidades_id', $tipoPublicidadesTradicionalId)
                    ->groupBy('lugares_id')
                    ->get();

                // Convertir los resultados en arrays para la combinación
                $dataLedArray = $dataLed->keyBy('lugares_id')->toArray();
                $dataTradicionalArray = $dataTradicional->keyBy('lugares_id')->toArray();

                // Combinar los resultados
                $combinedData = [];

                // Obtener todas las claves (lugares_id) de ambos arrays
                $allKeys = array_merge(array_keys($dataLedArray), array_keys($dataTradicionalArray));

                foreach ($allKeys as $lugaresId) {
                    $combinedData[$lugaresId] = [
                        'lugares_id' => $lugaresId,
                        'totalLed' => isset($dataLedArray[$lugaresId]) ? $dataLedArray[$lugaresId]->totalLed : 0,
                        'totalTradicional' => isset($dataTradicionalArray[$lugaresId]) ? $dataTradicionalArray[$lugaresId]->totalTradicional : 0,
                    ];
                }

                return view('estudio-competencia.graficos.cantidadOtros', compact('combinedData', 'descr', 'lugares', 'empresas'));
                break;
        }
    }



    public function productos($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $data2 =  DB::connection('mysql2')->table('datos')
            ->select('anunciantes_productos.*', 'datos.foto2', 'datos.referencia2', 'datos.clientes_id', 'lugares.propietarios_id', 'datos.comercializador2_id', 'lugares.direccion', 'datos.tipopublicidades_id', 'datos.lugares_id', 'lugares.coordenadas', 'datos.tipopautas_id', 'datos.created_at')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })
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
        if ($uso != 0 && $unidad == 1) {
            $data2->where('lugares.usos_id', $uso);
        }
        $datos = $data2->get();


        // Apply the whereIn filter on the collection
        $filteredDatosLed = $datos->whereIn('tipopublicidades_id', $tipoPublicidadesLedId);
        $groupedDatosLed = $filteredDatosLed->groupBy(['lugares_id', 'anunciante_id']);
        $totalLed = count($groupedDatosLed);

        $filteredDatosTradicional = $datos->whereIn('tipopublicidades_id', $tipoPublicidadesTradicionalId);
        $groupedDatosTradicional = $filteredDatosTradicional->groupBy(['lugares_id']);
        $totalTradicional = count($groupedDatosTradicional);

        $datos2 = [];
        $datos3 = [];

        foreach ($datos as $key => $value) {
            if ($unidad == 1) {
                $propietarioIds = $value->propietarios_id;
            } else {
                $propietarioIds = $value->comercializador2_id;
            }

            if (isset($empresas[$propietarioIds])) {

                if (isset($datos3[$value->id])) {
                    if (!isset($datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion])) {
                        $datos3[$value->id]++;
                    }
                } else {
                    $datos3[$value->id] = 1;
                }

                if (isset($datos2[$value->id][$empresas[$propietarioIds]->descripcion]['total'])) {

                    if (!isset($datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion])) {
                        $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['total']++;
                        $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['ubicaciones'][$value->coordenadas] = 1;
                    }

                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion][$value->referencia2]['foto'] = $value->foto2;
                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion][$value->referencia2]['fecha'] = $value->created_at;
                } else {
                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['total'] = 1;
                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['color'] = $empresas[$propietarioIds]->color;

                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion][$value->referencia2]['foto']  = $value->foto2;
                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['referencias'][$value->direccion][$value->referencia2]['fecha'] = $value->created_at;

                    $datos2[$value->id][$empresas[$propietarioIds]->descripcion]['ubicaciones'][$value->coordenadas] = 1;
                }
            }
        }

        //dd($datos2);
        // dejar solo los unicos id
        if ($uso == 1) {
            $datos = $datos->where('tipopautas_id', 1);
        }
        $productos = $datos->unique('id')
            ->pluck('producto', 'id')
            ->sort()
            ->all();

        return view('estudio-competencia.graficos.productos', compact('datos', 'productos', 'datos2', 'empresas', 'totalLed', 'totalTradicional', 'datos3'));
    }


    public function grupos($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $data2 =  DB::connection('mysql2')->table('datos')
            ->select(
                'grupos.descr as grupo',
                'grupos.id as grupo_id',
                'clientes.cliente',
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
            ->leftJoin('grupos', 'grupos.id', '=', 'clientes.grupo_id')
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
            $data2->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data2->where('datos.tipopautas_id', '=', 7);
        }
        //$data2->groupBy('datos.lugares_id',DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END"),'datos.pantallaNumero');

        $datos = $data2->get();

        $grupos = $datos->unique('grupo')
            ->pluck('grupo', 'grupo_id')
            ->sort()
            ->all();

        $clienteXgrupo = [];
        $anunciantesXgrupo = [];
        $productoXgrupo = [];
        $comercializadorXgrupo = [];
        $lugaresXgrupo = [];
        foreach ($datos as $key => $value) {

            if (isset($clienteXgrupo[$value->grupo][$value->cliente])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    if (isset($clienteXgrupo[$value->grupo][$value->cliente][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $clienteXgrupo[$value->grupo][$value->cliente][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $clienteXgrupo[$value->grupo][$value->cliente]['total']++;
                            $clienteXgrupo[$value->grupo][$value->cliente][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $clienteXgrupo[$value->grupo][$value->cliente]['total']++;
                        $clienteXgrupo[$value->grupo][$value->cliente][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $clienteXgrupo[$value->grupo][$value->cliente]['total']++;
                }
            } else {
                $clienteXgrupo[$value->grupo][$value->cliente]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    $clienteXgrupo[$value->grupo][$value->cliente][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }


            if (isset($anunciantesXgrupo[$value->grupo][$value->anunciante])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    if (isset($anunciantesXgrupo[$value->grupo][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $anunciantesXgrupo[$value->grupo][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $anunciantesXgrupo[$value->grupo][$value->anunciante]['total']++;
                            $anunciantesXgrupo[$value->grupo][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $anunciantesXgrupo[$value->grupo][$value->anunciante]['total']++;
                        $anunciantesXgrupo[$value->grupo][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $anunciantesXgrupo[$value->grupo][$value->anunciante]['total']++;
                }
            } else {
                $anunciantesXgrupo[$value->grupo][$value->anunciante]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    $anunciantesXgrupo[$value->grupo][$value->anunciante][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }



            if (isset($productoXgrupo[$value->grupo][$value->producto])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    if (isset($productoXgrupo[$value->grupo][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $productoXgrupo[$value->grupo][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $productoXgrupo[$value->grupo][$value->producto]['total']++;
                            $productoXgrupo[$value->grupo][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $productoXgrupo[$value->grupo][$value->producto]['total']++;
                        $productoXgrupo[$value->grupo][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $productoXgrupo[$value->grupo][$value->producto]['total']++;
                }
            } else {
                $productoXgrupo[$value->grupo][$value->producto]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    $productoXgrupo[$value->grupo][$value->producto][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }


            if (isset($comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion])) {
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    if (isset($comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                        if (!in_array($value->anunciantes_id, $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'])) {
                            $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion]['total']++;
                            $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                        }
                    } else {
                        $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion]['total']++;
                        $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                    }
                } else {
                    $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion]['total']++;
                }
            } else {
                $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion]['total'] = 1;
                if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                    //es una pantalla led
                    $comercializadorXgrupo[$value->grupo][$empresas[$value->comercializadorId]->descripcion][$value->lugares_id][$value->pantallaNumero]['anunciantes'][] = $value->anunciantes_id;
                }
            }


            $lugaresXgrupo[$value->grupo][$value->lugares_id]['direccion'] = $value->direccion;
            $lugaresXgrupo[$value->grupo][$value->lugares_id]['coordenadas'] = $value->coordenadas;
        }

        return view('estudio-competencia.graficos.grupos', compact('grupos', 'datos', 'clienteXgrupo', 'anunciantesXgrupo', 'productoXgrupo', 'comercializadorXgrupo', 'lugaresXgrupo'));
    }


    public function pauta($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();


        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();


        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidades = $tipoPublicidades->pluck('descripcion', 'id')->all();


        $data2 =  DB::connection('mysql2')->table('datos')
            ->select(
                'grupos.descr as grupo',
                'clientes.cliente',
                'anunciantes.anunciante',
                'anunciantes_productos.producto',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.created_at',
                'datos.lugares_id'
            )
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->join('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id')
            ->join('grupos', 'grupos.id', '=', 'clientes.grupo_id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })
            
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data2->where('lugares.propietarios_id', $empresa);
            } else {
                $data2->where('datos.comercializador2_id', $empresa);
            }
        }
        if ($uso != 0 && $unidad == 1) {
            $data2->where('lugares.usos_id', $uso);
        }
        $datos = $data2->get();
        return view('estudio-competencia.graficos.pauta', compact('datos', 'empresas', 'tipoPublicidades'));
    }


    public function nomarketAnunciante($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();

        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidades = $tipoPublicidades->pluck('descripcion', 'id')->all();

        $data =  DB::connection('mysql2')->table('datos')
            ->select('anunciantes_productos.anunciante_id as anunciantes')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')

            ->when($unidad == 1, function ($query) {
                return $query->where('lugares.propietarios_id', 2);
            })
            ->when($unidad == 2, function ($query) {
                return $query->where('datos.comercializador2_id', 2);
            })
            ->when($unidad == 3, function ($query) {
                return $query->where('datos.comercializador2_id', 2);
            })


            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->groupBy('anunciantes_productos.anunciante_id')
            ->pluck('anunciantes_productos.anunciantes');



        $data2 =  DB::connection('mysql2')->table('datos')
            ->select(
                'grupos.descr as grupo',
                'clientes.cliente',
                'anunciantes.anunciante',
                'anunciantes_productos.producto',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.created_at',
                'datos.lugares_id'
            )
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->join('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id')
            ->join('grupos', 'grupos.id', '=', 'clientes.grupo_id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereNotIn('anunciantes_productos.anunciante_id', $data)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($unidad == 1) {
            $data2->where('lugares.propietarios_id', '!=', 2);
        } else {
            $data2->where('datos.comercializador2_id', '!=', 2);
        }

        if ($uso != 0 && $unidad == 1) {
            $data2->where('lugares.usos_id', $uso);
        }

        $data2->groupBy('anunciante');
        $datos = $data2->get();

        return view('estudio-competencia.graficos.nomarket', compact('datos', 'empresas', 'tipoPublicidades'));
    }


    public function nomarketProducto($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();

        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidades = $tipoPublicidades->pluck('descripcion', 'id')->all();

        $data =  DB::connection('mysql2')->table('datos')
            ->select('datos.producto2')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->when($unidad == 1, function ($query) {
                return $query->where('lugares.propietarios_id', 2);
            })
            ->when($unidad == 2, function ($query) {
                return $query->where('datos.comercializador2_id', 2);
            })
            ->when($unidad == 3, function ($query) {
                return $query->where('datos.comercializador2_id', 2);
            })
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->groupBy('datos.producto2')
            ->pluck('datos.producto2');


        $data2 =  DB::connection('mysql2')->table('datos')
            ->select(
                'grupos.descr as grupo',
                'clientes.cliente',
                'anunciantes.anunciante',
                'anunciantes_productos.producto',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.created_at',
                'datos.lugares_id'
            )
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->join('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id')
            ->join('grupos', 'grupos.id', '=', 'clientes.grupo_id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereNotIn('datos.producto2', $data)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);


        if ($unidad == 1) {
            $data2->where('lugares.propietarios_id', '!=', 2);
        } else {
            $data2->where('datos.comercializador2_id', '!=', 2);
        }

        if ($uso != 0 && $unidad == 1) {
            $data2->where('lugares.usos_id', $uso);
        }

        $data2->groupBy('producto');
        $datos = $data2->get();

        return view('estudio-competencia.graficos.nomarket', compact('datos', 'empresas', 'tipoPublicidades'));
    }


    public function noOtrosAnunciante($unidad, $periodo, $empresa, $uso)
    {

        // Crear el primer y último día del mes
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();

        // Filtrar IDs
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidades = $tipoPublicidades->pluck('descripcion', 'id')->all();

        $data =  DB::connection('mysql2')->table('datos')
            ->select('anunciantes_productos.anunciante_id as anunciantes')
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->when($unidad == 1, function ($query) {
                return $query->where('lugares.propietarios_id','!=', 2);
            })
            ->when($unidad == 2, function ($query) {
                return $query->where('datos.comercializador2_id', '!=',2);
            })
            ->when($unidad == 3, function ($query) {
                return $query->where('datos.comercializador2_id','!=', 2);
            })
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->groupBy('anunciantes_productos.anunciante_id')
            ->pluck('anunciantes_productos.anunciantes');


        $data2 =  DB::connection('mysql2')->table('datos')
            ->select(
                'grupos.descr as grupo',
                'clientes.cliente',
                'anunciantes.anunciante',
                'anunciantes_productos.producto',
                'lugares.direccion',
                'lugares.coordenadas',
                'lugares.propietarios_id',
                'datos.comercializador2_id',
                DB::raw("CASE WHEN $unidad = 1 THEN lugares.propietarios_id ELSE datos.comercializador2_id END as comercializadorId"),
                'datos.tipopublicidades_id',
                'datos.foto2',
                'datos.created_at',
                'datos.lugares_id'
            )
            ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
            ->join('anunciantes_productos', 'anunciantes_productos.id', '=', 'datos.producto2')
            ->join('anunciantes', 'anunciantes_productos.anunciante_id', '=', 'anunciantes.id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id')
            ->join('grupos', 'grupos.id', '=', 'clientes.grupo_id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesId)
            ->whereNotIn('anunciantes_productos.anunciante_id', $data)
            ->when($uso == 1, function ($query) {
                return $query->where('tipopautas_id', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('tipopautas_id', '!=', 1)
                             ->where('tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('tipopautas_id', 7);
            })            
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($unidad == 1) {
            $data2->where('lugares.propietarios_id', '=', 2);
        } else {
            $data2->where('datos.comercializador2_id', '=', 2);
        }

        if ($uso != 0 && $unidad == 1) {
            $data2->where('lugares.usos_id', $uso);
        }

        $data2->groupBy('producto');
        $datos = $data2->get();


        return view('estudio-competencia.graficos.nomarket', compact('datos', 'empresas', 'tipoPublicidades'));
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
            ->pluck('anunciante_id', 'id')
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

        if ($empresa != 0) {
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

        $data2->groupBy('lugares_id', 'pantallaNumero');
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

        $datos2 = collect($datos2)->sortByDesc(function ($item) {
            return ($item['ventas'] * 100 / ($item['total'] * 6));
        })->all();
        // $datos2 = collect($datos2)->sortByDesc(function($item) {
        //     return ($item['ventas'] * 100 /($item['total']));
        // })->all();


        switch ($unidad) {
            case 1: //vallas


                $data = DB::connection('mysql2')->table('lugares')
                    ->select('id', 'propietarios_id', 'tipovalla_id', 'exclusiva')
                    ->where('tipolugares_id', $unidad)
                    ->whereDate('created_at', '<=', $ultimoDiaMes)
                    ->where(function ($query) use ($ultimoDiaMes) {
                        $query->where('fecha_desactivacion', '>=', $ultimoDiaMes)
                            ->orWhereNull('fecha_desactivacion');
                    });
                if ($empresa != 0 && $unidad == 1) {
                    $data->where('propietarios_id', $empresa);
                }
                /*
                if($uso!=0){
                    $data->where('usos_id',$uso);
                }*/
                $data->where('usos_id', 1);
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
                                    ELSE datos.comercializador2_id
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
                        'datos.comercializador2_id',
                        'datos.pantallaNumero',
                        'datos.tipopublicidades_id',
                    )
                    ->where('datos.tipopautas_id', '=', 1)
                    ->whereIn('datos.lugares_id', $dataLugares)
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes)
                    ->join('lugares', 'lugares.id', '=', 'datos.lugares_id');
                //filtro solo comercial porque es para ocupacion
                //$data2->where('datos.tipopautas_id', '=', 1);
                if ($empresa != 0) {
                    if ($unidad == 1) {
                        $data2->where('lugares.propietarios_id', $empresa);
                    } else {
                        $data2->where('datos.comercializador2_id', $empresa);
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

                $data2->groupBy('lugares_id', 'pantallaNumero', 'tipopublicidades_id');
                $data2 = $data2->get()->keyBy('lugares_id');
                // Procesamiento de datos
                $ocupacionLed = [];
                $ocupacionTradicional = [];
                $ocupacionGeneral = [];
                foreach ($data as $value) {
                    $lugareId = $value->id;


                    if (isset($data2[$lugareId]) && in_array($data2[$lugareId]->tipopublicidades_id, $tipoPublicidadesLedId)) {
                        if (!isset($ocupacionLed[$value->propietarios_id])) {
                            $ocupacionLed[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionLed[$value->propietarios_id]['total'] += 6;
                    } else if (in_array($value->tipovalla_id, $tipoPublicidadesLedId)) {
                        if (!isset($ocupacionLed[$value->propietarios_id])) {
                            $ocupacionLed[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionLed[$value->propietarios_id]['total'] += 6;
                    }

                    if (isset($data2[$lugareId]) && in_array($value->tipovalla_id, $tipoPublicidadesTradicionalId)) {
                        if (!isset($ocupacionTradicional[$value->propietarios_id])) {
                            $ocupacionTradicional[$value->propietarios_id] = [
                                'total' => 0,
                                'ventas' => 0
                            ];
                        }
                        $ocupacionTradicional[$value->propietarios_id]['total']++;
                    } else if (in_array($value->tipovalla_id, $tipoPublicidadesTradicionalId)) {
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
                    if (isset($data2[$lugareId])) {
                        if (in_array($data2[$lugareId]->tipopublicidades_id, $tipoPublicidadesLedId)) {
                            $ocupacionGeneral[$value->propietarios_id]['total'] += 6;
                        } else {
                            $ocupacionGeneral[$value->propietarios_id]['total']++;
                        }
                    } else {
                        if (in_array($value->tipovalla_id, $tipoPublicidadesLedId)) {
                            $ocupacionGeneral[$value->propietarios_id]['total'] += 6;
                        } else {
                            $ocupacionGeneral[$value->propietarios_id]['total']++;
                        }
                    }



                    if (isset($data2[$lugareId])) {
                        $comercializadorId = $data2[$lugareId]->comercializadorId;
                        $comercializadorIdsArray = explode(',', $comercializadorId);
                        if (count($comercializadorIdsArray) > 1) {
                            $comercializadorId = $data2[$lugareId]->comercializador2_id;
                        }

                        $producto2Array = explode(",", $data2[$lugareId]->producto2);

                        foreach ($producto2Array as $producto2Id) {


                            if (in_array($data2[$lugareId]->tipopublicidades_id, $tipoPublicidadesLedId)) {
                                if (!isset($ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero] = [];
                                }
                                if (!in_array($anunciantesProductos[$producto2Id], $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero])) {
                                    $ocupacionLed[$comercializadorId][$data2[$lugareId]->lugares_id][$data2[$lugareId]->pantallaNumero][] = $anunciantesProductos[$producto2Id];

                                    if ($value->exclusiva == 1) {
                                        $ocupacionLed[$comercializadorId]['ventas'] += 6;
                                    } else {
                                        $ocupacionLed[$comercializadorId]['ventas']++;
                                    }
                                }
                            }


                            if (in_array($data2[$lugareId]->tipopublicidades_id, $tipoPublicidadesTradicionalId)) {
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

                $ocupacionLed = collect($ocupacionLed)->sortByDesc(function ($item) {
                    return ($item['ventas'] * 100 / ($item['total']));
                })->all();

                $ocupacionTradicional = collect($ocupacionTradicional)->sortByDesc(function ($item) {
                    return ($item['ventas'] * 100 / ($item['total']));
                })->all();

                $ocupacionGeneral = collect($ocupacionGeneral)->sortByDesc(function ($item) {
                    return ($item['ventas'] * 100 / ($item['total']));
                })->all();


                $participacion = $ocupacionGeneral;

                $totalPautaComercial = collect($ocupacionGeneral)->sum(function ($item) {
                    return $item['ventas'];
                });

                $participacion = collect($participacion)->sortByDesc(function ($item) use ($totalPautaComercial) {
                    return ($item['ventas'] * 100 / ($totalPautaComercial));
                })->all();


                // $datos2 = collect($datos2)->sortByDesc(function($item) {
                //     return ($item['ventas'] * 100 /($item['total']));
                // })->all();


                return view('estudio-competencia.graficos.ocupacionVallas', compact('empresas', 'ocupacionLed', 'ocupacionTradicional', 'ocupacionGeneral', 'totalPautaComercial', 'participacion'));
                break;

            default:
                return view('estudio-competencia.graficos.ocupacionOtros', compact('empresas', 'datos2'));
                break;
        }
    }


    public function participacion($unidad, $periodo, $empresa, $uso)
    {
        // Crear el primer y último día del mes
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();
        // Obtener las empresas
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();
        // Obtener tipo de publicidades
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        // Filtrar IDs
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        $anunciantesProductos = DB::connection('mysql2')
            ->table('anunciantes_productos')
            ->pluck('anunciante_id', 'id')
            ->all();





        switch ($unidad) {
            case 1: // vallas


                $data2 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw(
                            "GROUP_CONCAT(DISTINCT
                            CASE
                                WHEN {$unidad} = 1 THEN lugares.propietarios_id
                                ELSE datos.comercializador2_id
                            END SEPARATOR ',') as comercializadorId"
                        ),
                        DB::raw("GROUP_CONCAT(DISTINCT datos.producto2 SEPARATOR ',') as producto2"),
                        'datos.comercializador2_id',
                        'datos.pantallaNumero',
                        'datos.tipopublicidades_id',
                        'lugares.tipovalla_id'

                    )
                    ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
                    // ->whereIn('datos.tipopublicidades_id', [1, 2]) // Usar IDs 1 y 2
                    ->whereIn('lugares.tipovalla_id', [1, 2])
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes);

                if ($empresa != 0) {
                    $data2->where('lugares.propietarios_id', $empresa);
                }

                if ($uso == 1) {
                    $data2->where('lugares.usos_id', '=', 1);
                }
                if ($uso == 2) {
                    $data2->where('lugares.usos_id', '!=', 1);
                }

                $data2->groupBy('lugares_id', 'pantallaNumero', 'tipopublicidades_id');
                $datos = $data2->get();

                //dd($datos);


                // Procesamiento de datos
                $datos2 = [];
                foreach ($datos as $value) {
                    $comercializadorId = $value->comercializadorId;
                    $comercializadorIdsArray = explode(',', $comercializadorId);

                    if (count($comercializadorIdsArray) > 1) {
                        $comercializadorId = $value->comercializador2_id;
                    }

                    if (!isset($datos2[$comercializadorId])) {
                        $datos2[$comercializadorId] = [
                            'total' => 0,
                            'ventas' => 0
                        ];
                    }



                    // Multiplicar por 6 si el tipopublicidades_id es 2 (LED)
                    $multiplicar = ($value->tipopublicidades_id == 2) ? 6 : 1;

                    $datos2[$comercializadorId]['total'] += $multiplicar;

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

                $totalPautaComercial = collect($datos2)->sum(function ($item) {
                    return $item['total'];
                });


                $datos2 = collect($datos2)->sortByDesc(function ($item) use ($totalPautaComercial) {
                    return ($item['ventas'] * 100 / $totalPautaComercial);
                })->all();



                return view('estudio-competencia.graficos.participacionVallas', compact('empresas', 'datos2', 'totalPautaComercial'));
                break;

            default:

                $data2 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw("
                            GROUP_CONCAT(DISTINCT
                                CASE
                                    WHEN {$unidad} = 1 THEN lugares.propietarios_id
                                    ELSE datos.comercializador2_id
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
                        'datos.comercializador2_id',
                        'datos.pantallaNumero'
                    )
                    ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
                    ->whereIn('datos.tipopublicidades_id', $tipoPublicidadesLedId)
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
                    $data2->where('datos.tipopautas_id', '!=', 1)
                          ->where('datos.tipopautas_id', '!=', 7);
                }
                if ($uso == 7) {
                    $data2->where('datos.tipopautas_id', '=', 7);
                }


                $data2->groupBy('lugares_id', 'pantallaNumero');
                $datos = $data2->get();


                // Procesamiento de datos
                $datos2 = [];
                foreach ($datos as $value) {
                    $comercializadorId = $value->comercializadorId;
                    $comercializadorIdsArray = explode(',', $comercializadorId);

                    if (count($comercializadorIdsArray) > 1) {
                        $comercializadorId = $value->comercializador2_id;
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
                            //$datos2[$comercializadorId]['ventas']++;
                        }

                        if (!in_array($anunciantesProductos[$producto2Id], $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                            $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                            $datos2[$comercializadorId]['ventas']++;
                        }
                    }
                }

                $totalPautaComercial = collect($datos2)->sum(function ($item) {
                    return $item['total'];
                });

                $datos2 = collect($datos2)->sortByDesc(function ($item) use ($totalPautaComercial) {
                    return ($item['ventas'] * 100 / ($totalPautaComercial * 6));
                })->all();


                //de ajustar

                $data3 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw("
                            GROUP_CONCAT(DISTINCT
                                CASE
                                    WHEN {$unidad} = 1 THEN lugares.propietarios_id
                                    ELSE datos.comercializador2_id
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

                if ($empresa != 0) {
                    if ($unidad == 1) {
                        $data3->where('lugares.propietarios_id', $empresa);
                    } else {
                        $data3->where('datos.comercializador2_id', $empresa);
                    }
                }

                if ($uso == 1) {
                    $data3->where('datos.tipopautas_id', '=', 1);
                }
                if ($uso == 2) {
                    $data3->where('datos.tipopautas_id', '!=', 1)
                          ->where('datos.tipopautas_id', '!=', 7);
                }
                if ($uso == 7) {
                    $data3->where('datos.tipopautas_id', '=', 7);
                }


                $data3->groupBy('lugares_id', 'pantallaNumero');
                $datos = $data3->get();


                // Procesamiento de datos
                $datos3 = [];
                foreach ($datos as $value) {
                    $comercializadorId = $value->comercializadorId;
                    $comercializadorIdsArray = explode(',', $comercializadorId);

                    if (count($comercializadorIdsArray) > 1) {
                        $comercializadorId = $value->comercializador2_id;
                    }

                    if (!isset($datos3[$comercializadorId])) {
                        $datos3[$comercializadorId] = [
                            'total' => 0,
                            'ventas' => 0
                        ];
                    }

                    $datos3[$comercializadorId]['total']++;


                    $producto2Array = explode(",", $value->producto2);

                    foreach ($producto2Array as $producto2Id) {

                        if (!isset($datos3[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                            $datos3[$comercializadorId][$value->lugares_id][$value->pantallaNumero] = [];
                            //$datos3[$comercializadorId]['ventas']++;
                        }

                        if (!in_array($anunciantesProductos[$producto2Id], $datos3[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                            $datos3[$comercializadorId][$value->lugares_id][$value->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                            $datos3[$comercializadorId]['ventas']++;
                        }
                    }
                }

                $totalPautaComercial3 = collect($datos3)->sum(function ($item) {
                    return $item['total'];
                });

                $datos3 = collect($datos3)->sortByDesc(function ($item) use ($totalPautaComercial3) {
                    return ($item['ventas'] * 100 / ($totalPautaComercial3 * 6));
                })->all();


                return view('estudio-competencia.graficos.participacionOtros', compact('empresas', 'datos2', 'datos3', 'totalPautaComercial', 'totalPautaComercial3'));
                break;
        }
    }

    public function ranking($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidades2 = $tipoPublicidades->pluck('id')->toArray();

        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->select(
                'datos.tipopublicidades_id',
                'anunciantes_productos.anunciante_id'
            )
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidades2)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->groupBy('datos.lugares_id', 'datos.tipopublicidades_id', 'datos.pantallaNumero', 'anunciantes_productos.anunciante_id');

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
            $data->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data->where('datos.tipopautas_id', '=', 7);
        }

        $data = $data->get();

        $ranking = [];

        foreach ($data as $value) {
            $anuncianteId = $value->anunciante_id;

            if (!isset($ranking[$anuncianteId])) {
                $ranking[$anuncianteId] = [
                    'id' => $anuncianteId,
                    'nombre' => DB::connection('mysql2')->table('anunciantes')->where('id', $anuncianteId)->value('anunciante'),
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                if (isset($ranking[$anuncianteId]['led'])) {
                    $ranking[$anuncianteId]['led']++;
                } else {
                    $ranking[$anuncianteId]['led'] = 1;
                }
            }


            if (in_array($value->tipopublicidades_id, $tipoPublicidadesTradicionalId)) {
                if (isset($ranking[$anuncianteId]['tradicional'])) {
                    $ranking[$anuncianteId]['tradicional']++;
                } else {
                    $ranking[$anuncianteId]['tradicional'] = 1;
                }
            }

            if (isset($ranking[$anuncianteId]['total'])) {
                $ranking[$anuncianteId]['total']++;
            } else {
                $ranking[$anuncianteId]['total'] = 1;
            }
        }

        $rankingAnunciantes = collect($ranking)->sortByDesc('total')->values()->all();


        //----
        $data2 = DB::connection('mysql2')->table('datos')
            ->select(
                'datos.tipopublicidades_id',
                'datos.clientes_id'
            )
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidades2)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes);

        if ($empresa != 0) {
            if ($unidad == 1) {
                $data2->where('lugares.propietarios_id', $empresa);
            } else {
                $data2->where('datos.comercializador2_id', $empresa);
            }
        }

        // $data2->where('datos.tipopautas_id', '=', 1);
        if ($uso == 1) {
            $data2->where('datos.tipopautas_id', '=', 1);
        }
        if ($uso == 2) {
            $data2->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data2->where('datos.tipopautas_id', '=', 7);
        }

        //$data2->groupBy('datos.clientes_id');
        $data2->groupBy('datos.lugares_id', 'datos.tipopublicidades_id', 'datos.pantallaNumero', 'anunciantes_productos.anunciante_id');
        $datos = $data2->get();


        //dd($datos);

        $ranking = [];
        foreach ($datos as $value) {
            $clienteId = $value->clientes_id;


            if (!isset($ranking[$clienteId])) {
                $ranking[$clienteId] = [
                    'id' => $clienteId,
                    'nombre' => DB::connection('mysql2')->table('clientes')->where('id', $clienteId)->value('cliente'),
                    'totalTradicional' => 0,
                    'totalLed' => 0,
                    'total' => 0
                ];
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                if (isset($ranking[$clienteId]['totalLed'])) {
                    $ranking[$clienteId]['totalLed']++;
                } else {
                    $ranking[$clienteId]['totalLed'] = 1;
                }
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesTradicionalId)) {
                if (isset($ranking[$clienteId]['totalTradicional'])) {
                    $ranking[$clienteId]['totalTradicional']++;
                } else {
                    $ranking[$clienteId]['totalTradicional'] = 1;
                }
            }

            if (isset($ranking[$clienteId]['total'])) {
                $ranking[$clienteId]['total']++;
            } else {
                $ranking[$clienteId]['total'] = 1;
            }
        }
        $rankingClientes = collect($ranking)->sortByDesc('total')->values()->all();


        ///-------
        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->select(
                'datos.tipopublicidades_id',
                'anunciantes_productos.id as producto_id',
                'anunciantes_productos.producto as nombre_producto'
            )
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidades2)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->groupBy('datos.lugares_id', 'datos.tipopublicidades_id', 'datos.pantallaNumero', 'anunciantes_productos.id');

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
            $data->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data->where('datos.tipopautas_id', '=', 7);
        }

        $data = $data->get();

        $ranking = [];

        foreach ($data as $value) {
            $productoId = $value->producto_id;

            if (!isset($ranking[$productoId])) {
                $ranking[$productoId] = [
                    'id' => $productoId,
                    'nombre_producto' => $value->nombre_producto,
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                if (isset($ranking[$productoId]['led'])) {
                    $ranking[$productoId]['led']++;
                } else {
                    $ranking[$productoId]['led'] = 1;
                }
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesTradicionalId)) {
                if (isset($ranking[$productoId]['tradicional'])) {
                    $ranking[$productoId]['tradicional']++;
                } else {
                    $ranking[$productoId]['tradicional'] = 1;
                }
            }

            if (isset($ranking[$productoId]['total'])) {
                $ranking[$productoId]['total']++;
            } else {
                $ranking[$productoId]['total'] = 1;
            }
        }

        $rankingProductos = collect($ranking)->sortByDesc('total')->values()->all();


        //----

        $data = DB::connection('mysql2')->table('datos')
            ->join('clientes', 'datos.clientes_id', '=', 'clientes.id')
            ->join('grupos', 'clientes.grupo_id', '=', 'grupos.id')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->select(
                'datos.tipopublicidades_id',
                'grupos.id as grupo_id',
                'grupos.descr as grupo_desc'

            )
            //->where('grupos.id',9)
            ->whereIn('datos.tipopublicidades_id', $tipoPublicidades2)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            //->groupBy('grupos.id', 'grupos.descr');
            ->groupBy('datos.lugares_id', 'datos.tipopublicidades_id', 'datos.pantallaNumero', 'anunciantes_productos.anunciante_id');

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
            $data->where('datos.tipopautas_id', '!=', 1)
                  ->where('datos.tipopautas_id', '!=', 7);
        }
        if ($uso == 7) {
            $data->where('datos.tipopautas_id', '=', 7);
        }
        $data = $data->get();

        $ranking = [];
        foreach ($data as $value) {
            $grupoId = $value->grupo_id;
            $grupoDesc = $value->grupo_desc;

            if (!isset($ranking[$grupoId])) {
                $ranking[$grupoId] = [
                    'id' => $grupoId,
                    'nombre' => $grupoDesc,
                    'tradicional' => 0,
                    'led' => 0,
                    'total' => 0
                ];
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesLedId)) {
                if (isset($ranking[$grupoId]['led'])) {
                    $ranking[$grupoId]['led']++;
                } else {
                    $ranking[$grupoId]['led'] = 1;
                }
            }

            if (in_array($value->tipopublicidades_id, $tipoPublicidadesTradicionalId)) {
                if (isset($ranking[$grupoId]['tradicional'])) {
                    $ranking[$grupoId]['tradicional']++;
                } else {
                    $ranking[$grupoId]['tradicional'] = 1;
                }
            }

            if (isset($ranking[$grupoId]['total'])) {
                $ranking[$grupoId]['total']++;
            } else {
                $ranking[$grupoId]['total'] = 1;
            }
        }


        $rankingGrupos = collect($ranking)->sortByDesc('nombre')->values()->all();

        return view('estudio-competencia.graficos.ranking', compact('rankingAnunciantes', 'rankingClientes', 'rankingProductos', 'rankingGrupos'));
    }


    public function rankingCentroComercial($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();

        $tradicionalIds = implode(',', $tipoPublicidadesTradicionalId);
        $ledIds = implode(',', $tipoPublicidadesLedId);
        $allIds = array_merge($tipoPublicidadesTradicionalId, $tipoPublicidadesLedId);

        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->join('lugares', 'lugares.id', '=', 'datos.lugares_id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id');

        // Cambiar la unión según la unidad
        if ($unidad == 3) {
            $data->join('propietarios', 'propietarios.id', '=', 'datos.comercializador2_id');
        } else {
            $data->join('propietarios', 'propietarios.id', '=', 'lugares.propietarios_id');
        }

        $data = $data->select(
            
            'propietarios.descripcion as empresa',
            'propietarios.id as empresa_id',
            DB::raw('COUNT(DISTINCT datos.clientes_id) as clientes'),
            DB::raw('COUNT(DISTINCT CASE WHEN clientes.grupo_id = 2 THEN datos.clientes_id END) as clientes_directo'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $tradicionalIds . ') THEN datos.clientes_id END) as clientes_tradicional'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $ledIds . ') THEN datos.clientes_id END) as clientes_led'),
            DB::raw('COUNT(DISTINCT anunciantes_productos.anunciante_id) as anunciantes'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $tradicionalIds . ') THEN anunciantes_productos.anunciante_id END) as anunciantes_tradicional'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $ledIds . ') THEN anunciantes_productos.anunciante_id END) as anunciantes_led')
        
            )
        
            ->whereIn('datos.tipopublicidades_id', $allIds)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->when($empresa != 0, function ($query) use ($empresa, $unidad) {
                if ($unidad == 1) {
                    return $query->where('lugares.propietarios_id', $empresa);
                }
                return $query->where('datos.comercializador2_id', $empresa);
            })
            ->when($uso == 1, function ($query) {
                return $query->where('datos.tipopautas_id', '=', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('datos.tipopautas_id', '!=', 1)
                             ->where('datos.tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('datos.tipopautas_id','=', 7);
            })
            ->groupBy('propietarios.id') 
            ->get();

        return view('estudio-competencia.graficos.rankingEmpresa', compact('data'));
    }


    public function rankingEmpresa($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();

        $tradicionalIds = implode(',', $tipoPublicidadesTradicionalId);
        $ledIds = implode(',', $tipoPublicidadesLedId);
        $allIds = array_merge($tipoPublicidadesTradicionalId, $tipoPublicidadesLedId);

        $data = DB::connection('mysql2')->table('datos')
            ->join('anunciantes_productos', 'datos.producto2', '=', 'anunciantes_productos.id')
            ->join('lugares', 'lugares.id', '=', 'datos.lugares_id')
            ->join('clientes', 'clientes.id', '=', 'datos.clientes_id');

        if ($unidad == 3 || $unidad==2) {
            $data->join('propietarios', 'propietarios.id', '=', 'datos.comercializador2_id');
        } else {
            $data->join('propietarios', 'propietarios.id', '=', 'lugares.propietarios_id');
        }

        $data = $data->select(
            'propietarios.descripcion as empresa',
            'propietarios.id as empresa_id',
            DB::raw('COUNT(DISTINCT datos.clientes_id) as clientes'),
            DB::raw('COUNT(DISTINCT CASE WHEN clientes.grupo_id = 2 THEN datos.clientes_id END) as clientes_directo'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $tradicionalIds . ') THEN datos.clientes_id END) as clientes_tradicional'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $ledIds . ') THEN datos.clientes_id END) as clientes_led'),
            DB::raw('COUNT(DISTINCT anunciantes_productos.anunciante_id) as anunciantes'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $tradicionalIds . ') THEN anunciantes_productos.anunciante_id END) as anunciantes_tradicional'),
            DB::raw('COUNT(DISTINCT CASE WHEN datos.tipopublicidades_id IN (' . $ledIds . ') THEN anunciantes_productos.anunciante_id END) as anunciantes_led')
        
            )
            ->whereIn('datos.tipopublicidades_id', $allIds)
            ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
            ->whereDate('datos.created_at', '>=', $primerDiaMes)
            ->when($empresa != 0, function ($query) use ($empresa, $unidad) {
                if ($unidad == 1) {
                    return $query->where('lugares.propietarios_id', $empresa);
                }
                return $query->where('datos.comercializador2_id', $empresa);
            })
            ->when($uso == 1, function ($query) {
                return $query->where('datos.tipopautas_id', '=', 1);
            })
            ->when($uso == 2, function ($query) {
                return $query->where('datos.tipopautas_id', '!=', 1)
                             ->where('datos.tipopautas_id', '!=', 7);
            })
            ->when($uso == 7, function ($query) {
                return $query->where('datos.tipopautas_id','=', 7);
            })
            ->groupBy('propietarios.id') 
            ->get();

        return view('estudio-competencia.graficos.rankingEmpresa', compact('data'));
    }



    public function comparacionAforo($unidad, $periodo, $empresa, $uso)
    {
        $periodo = explode("-", $periodo);
        $mes = (int)$periodo[0];
        $anno = (int)$periodo[1];
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $propietarios =  DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Define el rango de años y meses (por ejemplo, desde el inicio hasta el final del año actual)
        $inicio = Carbon::create(2024, 1, 1); // Cambia annoInicio por el año de inicio deseado
        $fin = Carbon::create(2024, 12, 31); // Cambia annoFin por el año de fin deseado

        // Array para almacenar resultados por mes
        $resultadosPorMes = [];

        while ($inicio->lte($fin)) {
            $mes = $inicio->month;
            $anno = $inicio->year;

            $ultimoDiaMes = $inicio->endOfMonth();
            $primerDiaMes = $inicio->startOfMonth();

            // Consulta los lugares para el mes actual
            $data = DB::connection('mysql2')->table('lugares')
                ->where('tipolugares_id', $unidad)
                ->where('created_at', '<=', $ultimoDiaMes)
                ->where(function ($query) use ($primerDiaMes) {
                    $query->where('fecha_desactivacion', '>=', $primerDiaMes)
                        ->orWhereNull('fecha_desactivacion');
                });

            // Filtrado por usos_id si es necesario
            if ($uso != 0 && $unidad == 1) {
                $data->where('usos_id', $uso);
            }

            // Filtrado por propietario_id si es necesario
            if ($empresa != 0 && $unidad == 1) {
                $data->where('propietarios_id', $empresa);
            }

            $data = $data->get();

            $totalLugares = count($data);

            switch ($unidad) {
                case 1: // vallas
                    $desactivados = $data->where('estado', 0)->where('fecha_desactivacion', '<=', $ultimoDiaMes);
                    $totalDesactivados = $desactivados->count();

                    $activadoNuevo = $data->where('created_at', '>=', $primerDiaMes)->where('created_at', '<=', $ultimoDiaMes);
                    $totalActivados = $activadoNuevo->count();

                    $totalLugaresActivos = $totalLugares - $totalDesactivados;

                    $totalCarasLed1 = $data->where('tipovalla_id', 2)->whereNull('fecha_desactivacion')->count();
                    $totalCarasLed2 = $data->where('tipovalla_id', 2)->where('fecha_desactivacion', '>=', $ultimoDiaMes)->count();
                    $totalCarasLed = $totalCarasLed1 + $totalCarasLed2;

                    $totalCarasTradicional1 = $data->where('tipovalla_id', 1)->where('fecha_desactivacion', '>=', $ultimoDiaMes)->count();
                    $totalCarasTradicional2 = $data->where('tipovalla_id', 1)->whereNull('fecha_desactivacion')->count();
                    $totalCarasTradicional = $totalCarasTradicional1 + $totalCarasTradicional2;

                    // Almacena los resultados en el array
                    $resultadosPorMes[] = [
                        'mes' => $mes - 1,
                        'anno' => $anno,
                        'desactivados' => $totalDesactivados,
                        'activados' => $totalActivados,
                        'lugaresActivos' => $totalLugaresActivos,
                        'carasTradicional' => $totalCarasTradicional,
                        'carasLed' => $totalCarasLed,
                    ];
                    break;
            }

            // Avanza al siguiente mes
            $inicio->addMonth();
        }


        // Finalmente, puedes pasar los resultados a la vista
        return view('estudio-competencia.graficos.comparacionAforo', compact('resultadosPorMes', 'propietarios'));
    }

    public function validacionReporte($unidad, $periodo, $empresa, $uso)
    {
        list($mes, $anno) = explode("-", $periodo);
        $mes = (int)$mes;
        $anno = (int)$anno;
        $ultimoDiaMes = Carbon::create($anno, $mes)->endOfMonth()->endOfDay();
        $primerDiaMes = Carbon::create($anno, $mes)->startOfMonth();

        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $propietarios = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        $lugaresQuery = DB::connection('mysql2')->table('lugares')
            ->select('propietarios_id', 'id', 'direccion')
            ->where('tipolugares_id', $unidad)
            ->where(function ($query) use ($ultimoDiaMes) {
                $query->where('fecha_desactivacion', '>=', $ultimoDiaMes)
                    ->orWhereNull('fecha_desactivacion');
            });

        $lugares = $lugaresQuery->get();
        $lugaresId = $lugares->pluck('id')->toArray();
        $tipopublicidadesFilter = [];

        $dataQuery = DB::connection('mysql2')->table('datos')
            ->whereIn('lugares_id', $lugaresId)
            ->whereDate('created_at', '<=', $ultimoDiaMes)
            ->whereDate('created_at', '>=', $primerDiaMes);

        switch ($unidad) {
            case 1:

                break;
            case 2:
                $tipopublicidadesFilter = [103, 111];
                break;
            case 3:

                $tipopublicidadesFilter = [84, 85, 88];
                break;
            default:

                break;
        }

        if (!empty($tipopublicidadesFilter)) {
            $dataQuery->whereIn('tipopublicidades_id', $tipopublicidadesFilter);
        }

        $data = $dataQuery->groupBy('lugares_id')->get()->keyBy('lugares_id');

        $resultado = [];


        switch ($unidad) {
            case 1:
            case 2:

                foreach ($lugares as $value) {
                    $propietarioId = $value->propietarios_id;

                    if (!isset($resultado[$propietarioId])) {
                        $resultado[$propietarioId] = [
                            'inventario' => 0,
                            'reporte' => 0,
                            'ubicaciones' => []
                        ];
                    }

                    $resultado[$propietarioId]['inventario']++;

                    if (isset($data[$value->id])) {
                        $resultado[$propietarioId]['reporte']++;
                    } else {
                        $resultado[$propietarioId]['ubicaciones'][] = $value->direccion . ' [' . $value->id . ']';
                    }
                }
                break;

            case 3:

                foreach ($lugares as $value) {
                    $comercializadorId = DB::connection('mysql2')->table('datos')
                        ->where('lugares_id', $value->id)
                        ->value('comercializador2_id');

                    if (!isset($resultado[$comercializadorId])) {
                        $resultado[$comercializadorId] = [
                            'inventario' => 0,
                            'reporte' => 0,
                            'ubicaciones' => []
                        ];
                    }

                    $resultado[$comercializadorId]['inventario']++;

                    if (isset($data[$value->id])) {
                        $resultado[$comercializadorId]['reporte']++;
                    } else {
                        $resultado[$comercializadorId]['ubicaciones'][] = $value->direccion . ' [' . $value->id . ']';
                    }
                }
                break;
        }

        return view('estudio-competencia.graficos.validacionReporte', compact('resultado', 'propietarios'));
    }
}
