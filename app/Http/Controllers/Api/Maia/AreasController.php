<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Zone;
use App\Models\BranchOffice;
use App\Models\ZoneRegion;
use Illuminate\Support\Facades\DB;


class AreasController extends Controller
{
    public static function regions()
    {
        $m_regions = DB::connection('maiaDB')
        ->table('regionsucursal as r')
        ->select('r.nombre', 'e.primernombre', 'e.DK','r.DK as DK_region')
        ->join('empleado as e', 'r.empleado_OID', 'e.DK')
        ->get();

        foreach ($m_regions as $m_region) {
            $region = Region::where('region_dk', $m_region->DK_region)->first();
            if($region){
                $region->name = $m_region->nombre;
                $region->update();
            }else{
                $region_create = Region::create([
                    'name' => $m_region->nombre,
                    'region_dk' => $m_region->DK_region,
                    'state_id' => 1,
                ]);
            }
        }

        echo 'Regiones creadas y actualizadas<br>';
    }

    public static function zones()
    {
        $m_zones = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.dk AS dk_empleado', 'e.novedadtraslado_k', 'z.region_OID', 'z.nombresuper', 'z.DK as DK_zona')
        ->join('zonasupervision as z', 'z.DK', 'e.zonasupervision_link')
        ->join('cargoempleado as c', 'c.DK', 'e.cargoempleado_OID')
        ->where('e.estadoempleado_OID', '!=', 115341)
        ->get();

        foreach ($m_zones as $m_zone) {
            $zone = Zone::where('zone_dk', $m_zone->DK_zona)->first();
            if($zone){
                $zone->name = $m_zone->nombresuper;
                $zone->zone_dk = $m_zone->DK_zona;
                $zone->state_id = 1;
                $zone->update();
            }else{

                $zone_create = Zone::create([
                    'name' => $m_zone->nombresuper,
                    'zone_dk' => $m_zone->DK_zona,
                    'state_id' => 1,
                ]);
            }
        }

        echo 'Zonas creadas y actualizadas<br>';


    }

    public static function regionZones()
    {
        $m_zones_region = DB::connection('maiaDB')
        ->table('zonasupervision as z')
        ->select('z.region_OID', 'z.nombresuper', 'z.DK as DK_zona')
        ->where('z.region_OID', '!=', 'null')
        ->get();
        foreach ($m_zones_region as $m_zone_region) {
            $region = Region::where('region_dk', $m_zone_region->region_OID)->first();
            if($region){
                $zone = Zone::where('zone_dk', $m_zone_region->DK_zona)->first();
                if($zone){
                    $zone_region = ZoneRegion::where('zone_id', $zone->id)->first();
                    if($zone_region){
                        $zone_region->region_id = $region->id;
                        $zone_region->update();
                    }else{
                        $zone_region_create = ZoneRegion::create([
                            'region_id' => $region->id,
                            'zone_id' => $zone->id,
                        ]);
                    }
                }
            }

        }

        echo 'Zonas asignadas a su region<br>';


    }

    public static function branchOffices()
    {
        $m_branchs = DB::connection('maiaDB')
        ->table('sucursal as s')
        ->select('s.Dk', 's.codigosuc', 's.nombresuc', 's.celular', 'c.nombre AS ciudad', 's.direccionsuc', 's.latitud', 's.zonasupervision_LINK', 's.departamento_link', 's.ciudad_link')
        ->join('ciudad as c', 's.ciudad_link', 'c.DK')
        ->join('departamento as d', 'd.DK', 's.departamento_link')
        ->where('s.zonasupervision_LINK', '!=', 'null')
        ->where('s.estado_OID', 4481)
        ->where('s.Dk', '!=', 11778795)
        ->get();


        foreach ($m_branchs as $m_branch) {
            $branchs = BranchOffice::select('zone_id', 'branch_office_dk', 'id')->where('branch_office_dk', $m_branch->Dk)->first();
            $zone = Zone::select('id', 'zone_dk')->where('zone_dk', $m_branch->zonasupervision_LINK)->first();
            if($branchs){
                if($zone){
                    $branchs->zone_id = $zone->id;
                }
                $branchs->zone_id = '';
                $branchs->code = $m_branch->codigosuc;
                $branchs->name = $m_branch->nombresuc;
                $branchs->address = $m_branch->direccionsuc;
                $branchs->longitude = '';
                $branchs->latitude = $m_branch->latitud;
                $branchs->state = 1;
                $branchs->zone_dk = $m_branch->zonasupervision_LINK;
                $branchs->branch_office_dk = $m_branch->Dk;
            }else{
                $branch_create = BranchOffice::create([
                    'zone_id' => $zone->id,
                    'code' => $m_branch->codigosuc,
                    'name' => $m_branch->nombresuc,
                    'address' => $m_branch->direccionsuc,
                    'length' => '',
                    'latitude' => $m_branch->latitud,
                    'state' => 1,
                    'zone_dk' => $m_branch->zonasupervision_LINK,
                    'branch_office_dk' => $m_branch->Dk,
                ]);
            }
        }
        echo 'Sucursales creadas y actualizadas<br>';
    }

    public static function deleteBranch()
    {

        $m_branchs = DB::connection('maiaDB')
        ->table('sucursal as s')
        ->select('s.Dk')
        ->get();

        $m_branch_array = array();
        foreach ($m_branchs as $m_branch) {
            array_push($m_branch_array, $m_branch->Dk);
        }


        $branchs = BranchOffice::whereNotIn('branch_office_dk', $m_branch_array)->update(['state' => 2]);

        /*foreach ($branchs as $branch) {
            $m_branchs = DB::connection('maiaDB')
            ->table('sucursal as s')
            ->select('s.Dk')
            ->where('s.Dk', $branch->branch_office_dk)
            ->first();
            if(!$m_branchs){
                $branch_delete = BranchOffice::where('id', $branch->id)->first();
                $branch_delete->state = 2;
                $branch_delete->update();
            }
        }*/
        echo 'Sucursal desactivadas<br>';
    }

}
