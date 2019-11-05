<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Lab;
class OtherController extends Controller
{
    public static function products()
    {
        $m_products = DB::connection('maiaDB')
        ->table('productos')
        ->select('codigo', 'dk', 'categoria_OID', 'laboratorio_OID', 'nombrecomercial')
        ->where('estado', 'ACTIVO')
        ->get();

        foreach ($m_products as $m_product) {
            $products = Product::where('code', $m_product->codigo)->first();
            $lab = Lab::where('dk', $m_product->laboratorio_OID)->first();
            if($products){
                $products->code = $m_product->codigo;
                $products->dk = $m_product->dk;
                $products->name = $m_product->nombrecomercial;
                $products->category_id = $m_product->categoria_OID;
                $products->state_id = 1;
                if($lab){
                    $products->labs_id = $lab->id;
                }
                $products->update();
            }else{
                if($lab){
                    $products_create = Product::create([
                        'code' => $m_product->codigo,
                        'dk' => $m_product->dk,
                        'name' => $m_product->nombrecomercial,
                        'category_id' => $m_product->categoria_OID,
                        'state_id' => 1,
                        'labs_id' => $lab->id,
                    ]);
                }else{
                    $products_create = Product::create([
                        'code' => $m_product->codigo,
                        'dk' => $m_product->dk,
                        'name' => $m_product->nombrecomercial,
                        'category_id' => $m_product->categoria_OID,
                        'state_id' => 1,
                    ]);
                }
            }
        }

        echo 'Productos insertados y actualizados con exito<br>';
    }

    public static function inactiveProducts()
    {
        $m_products = DB::connection('maiaDB')
        ->table('productos')
        ->select('dk')
        ->where('estado', 'INACTIVO')
        ->get();

        foreach ($m_products as $m_product) {
            $product = Product::select('dk', 'state_id')
            ->where('state_id', 1)
            ->where('dk', $m_product->dk)
            ->first();
            if($product){
                $product->state_id = 2;
                $product->update();
            }
        }
        echo 'Estado de Productos actualizados con exito<br>';

    }

    public static function labs()
    {
        $m_labs = DB::connection('maiaDB')
        ->table('laboratorio')
        ->select('Dk', 'nombre', 'laboratorio_ID')
        ->get();

        foreach ($m_labs as $m_lab) {
            $labs = Lab::where('dk', $m_lab->Dk)->first();
            if($labs){
                $labs->dk = $m_lab->Dk;
                $labs->name = $m_lab->nombre;
                $labs->state_id = 1;
            }else{
                $lab_create = Lab::create([
                    'dk' => $m_lab->Dk,
                    'name' => $m_lab->nombre,
                    'state_id' => 1,
                ]);
            }
        }

        echo 'Laboratorios creados y actualizados<br>';
    }

}
