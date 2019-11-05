<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RoleUser;
use App\Models\UserZone;
use App\Models\Zone;
use App\Models\Region;
use App\Models\UserBranch;
use App\Models\UserRegion;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\BranchOffice;

class UserAreaController extends Controller
{
    public static function userZone()
    {
        $m_user_zones = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.dk AS dk_empleado', 'e.novedadtraslado_k', 'z.region_OID', 'z.nombresuper', 'z.DK as DK_zona')
        ->join('zonasupervision as z', 'z.DK', 'e.zonasupervision_link')
        ->join('cargoempleado as c', 'c.DK', 'e.cargoempleado_OID')
        ->where('estadoempleado_OID', '!=', 115341)
        ->get();

        foreach ($m_user_zones as $m_user_zone) {
            $user = User::where('user_dk', $m_user_zone->dk_empleado)->where('user_state_id', 1)->first();

            if($user){
                $zone = Zone::where('zone_dk', $m_user_zone->DK_zona)->first();
                if($zone){
                    $user_role = RoleUser::where('user_id', $user->id)->first();
                    if($user_role){
                        $user_zone = UserZone::where('role_user_id', $user_role->id)->first();
                        if($user_zone){
                            $user_zone->zone_id = $zone->id;
                            $user_zone->update();
                        }else{
                            $user_zone_create = UserZone::create([
                                'zone_id' => $zone->id,
                                'role_user_id' => $user_role->id,
                            ]);
                        }

                    }
                }

            }
        }
        echo 'Zona asiganada al usuario<br>';

    }

    public static function userRegion()
    {
        $m_user_regions = DB::connection('maiaDB')
        ->table('regionsucursal as r')
        ->select('r.nombre', 'e.primernombre', 'e.DK','r.DK as DK_region')
        ->join('empleado as e', 'r.empleado_OID', 'e.DK')
        ->get();

        foreach ($m_user_regions as $m_user_region) {
            $user = User::where('user_dk', $m_user_region->DK)->where('user_state_id', 1)->first();
            if($user){
                $region = Region::where('region_dk', $m_user_region->DK_region)->first();
                if($region){
                    $user_role = RoleUser::where('user_id', $user->id)->whereIn('role_id', [1, 3])->first();
                    if($user_role){
                        $user_region = UserRegion::where('role_user_id', $user_role->id)->first();
                        if($user_region){
                            $user_region->region_id = $region->id;
                            $user_region->update();
                        }else{
                            $user_region_create = UserRegion::create([
                                'region_id' => $region->id,
                                'role_user_id' => $user_role->id,
                            ]);
                        }
                    }
                }
            }
        }


        echo 'Relacion coordinador region creado y actualizado<br>';
    }
    public static function userBranch()
    {
        $m_user_branch = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.dk AS dk_empleado', 'e.sucursal_link')
        ->where('e.estadoempleado_OID', '!=', 115341)
        ->where('e.sucursal_link', '!=', 'null')
        ->whereIn('e.cargoempleado_OID', ['115403', '5299972', '5653194', '5653195', '5653242', '5653196', '5653220', '5653198'])
        ->get();

        error_log(count($m_user_branch));

        foreach ($m_user_branch as $m_user_branch) {
            $user = User::where('user_dk', $m_user_branch->dk_empleado)->where('user_state_id', 1)->first();

            if($user){
                $branch = BranchOffice::where('branch_office_dk', $m_user_branch->sucursal_link)->first();
                if($branch){
                    $user_role = RoleUser::where('user_id', $user->id)->first();
                    if($user_role){
                        $user_branch = UserBranch::where('user_id', $user_role->id)->first();
                        if($user_branch){
                            $user_branch->branch_office_id = $branch->id;
                            $user_branch->update();
                        }else{
                            $user_branch_create = UserBranch::create([
                                'branch_office_id' => $branch->id,
                                'user_id' => $user_role->id,
                            ]);
                        }

                    }
                }

            }
        }
        echo 'Sucursales asiganada al usuario<br>';

    }
}
