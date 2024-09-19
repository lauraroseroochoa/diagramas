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

        // Obtener los anunciantes
        $anunciantes = DB::connection('mysql2')->table('anunciantes')->get()->keyBy('id')->all();

        // Obtener comercializadores
        $empresas = DB::connection('mysql2')->table('propietarios')->get()->keyBy('id')->all();

        // Tipo de publicidad
        $tipoPublicidades = DB::connection('mysql2')->table('tipopublicidades')->where('tipolugares_id', $unidad)->get();
        $tipoPublicidadesId = $tipoPublicidades->pluck('id');
        $tipoPublicidadesLedId = $tipoPublicidades->where('tecnologia', 1)->pluck('id')->toArray();
        $tipoPublicidadesTradicionalId = $tipoPublicidades->where('tecnologia', 0)->pluck('id')->toArray();

        // Obtener los datos
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
        // dd($comercializadoresXanunciante);
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
            ->pluck('anunciante_id','id')
            ->all();


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
                $data2->where('datos.comercializador2_id', $empresa);
            }    
        }

        if($uso==1){
            $data2->where('datos.tipopautas_id', '=', 1);
        }
        if($uso==2){
            $data2->where('datos.tipopautas_id', '!=', 1);
        }
        

        $data2->groupBy('lugares_id','pantallaNumero');
        $datos = $data2->get();


        // Procesamiento de datos
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
                        //$datos2[$comercializadorId]['ventas']++;
                    }
                    
                    if (!in_array($anunciantesProductos[$producto2Id], $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                        $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                        $datos2[$comercializadorId]['ventas']++;
                    }

            }
        }

        $totalPautaComercial = collect($datos2)->sum(function($item) {
            return $item['total'];
        });

        $datos2 = collect($datos2)->sortByDesc(function($item) use ($totalPautaComercial) {
            return ($item['ventas'] * 100 / ($totalPautaComercial * 6));
        })->all();


        switch ($unidad) {
            case 1: // vallas
        
               
                $data2 = DB::connection('mysql2')->table('datos')
                    ->select(
                        'datos.lugares_id',
                        DB::raw("GROUP_CONCAT(DISTINCT
                            CASE 
                                WHEN {$unidad} = 1 THEN lugares.propietarios_id 
                                ELSE datos.comercializador2_id 
                            END SEPARATOR ',') as comercializadorId"
                        ),
                        DB::raw("GROUP_CONCAT(DISTINCT datos.producto2 SEPARATOR ',') as producto2"),
                        'datos.comercializador',
                        'datos.pantallaNumero',
                        'datos.tipopublicidades_id' ,
                        'lugares.tipovalla_id'

                    )
                    ->join('lugares', 'datos.lugares_id', '=', 'lugares.id')
                    // ->whereIn('datos.tipopublicidades_id', [1, 2]) // Usar IDs 1 y 2
                    ->whereIn('lugares.tipovalla_id', [1, 2])
                    ->whereDate('datos.created_at', '<=', $ultimoDiaMes)
                    ->whereDate('datos.created_at', '>=', $primerDiaMes);
        
                if($empresa != 0){
                    $data2->where('lugares.propietarios_id', $empresa);
                }
        
                if($uso == 1){
                    $data2->where('datos.tipopautas_id', '=', 1);
                }
                if($uso == 2){
                    $data2->where('datos.tipopautas_id', '!=', 1);
                }
        
                $data2->groupBy('lugares_id','pantallaNumero');
                $datos = $data2->get();
        
                // Procesamiento de datos
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
        
                    // Multiplicar por 6 si el tipopublicidades_id es 2 (LED)
                    $multiplicar = ($value->tipovalla_id == 2) ? 6 : 1;
        
                    $datos2[$comercializadorId]['total'] += $multiplicar;
        
                    $producto2Array = explode(",", $value->producto2);
        
                    foreach ($producto2Array as $producto2Id) {
                        if (!isset($datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                            $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero] = [];
                        }
        
                        if (!in_array($anunciantesProductos[$producto2Id], $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero])) {
                            $datos2[$comercializadorId][$value->lugares_id][$value->pantallaNumero][] = $anunciantesProductos[$producto2Id];
                            $datos2[$comercializadorId]['ventas'] += $multiplicar;
                        }
                    }
                }
        
                $totalPautaComercial = collect($datos2)->sum(function($item) {
                    return $item['total'];
                });
                // dd($datos2);
        
                
                $datos2 = collect($datos2)->sortByDesc(function($item) use ($totalPautaComercial) {
                    return ($item['ventas'] * 100 / $totalPautaComercial); 
                })->all();
                
        
                return view('estudio-competencia.graficos.participacionVallas', compact('empresas', 'datos2', 'totalPautaComercial'));
            break;
        
            default:
                return view('estudio-competencia.graficos.participacionOtros', compact('empresas', 'datos2', 'totalPautaComercial'));
            break;
        }
    }        
}