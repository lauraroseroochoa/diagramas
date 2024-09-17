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
}
