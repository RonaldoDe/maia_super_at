<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BranchOfficeAdministrator;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\RoleUser;
use App\Models\UserMaster;
use App\Models\UserRegion;
use App\Models\UserZone;

class UsersController extends Controller
{
    public static function insertUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK as DK_empleado', 'e.estadoempleado_OID', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia', 'c.nombre')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', '!=', 115341)
        ->whereIn('c.codigo', ['003', '004', '332', '111', '071', '074', '171', '174', '178', '179', '180', '182', '204', '226', '274', '297'])
        ->where('e.DK', '!=', 11788977)
	->where('e.DK', '!=', 335694)
        ->get();

        $password = '$2y$10$u4CCshmLkKB8Ij1S5p61ceI9f1RwtteyGAKSaI3J1mOcun4qwG81W';

        foreach ($m_users as $m_user) {
            $user_update = UserMaster::on('super_at_master')->where('email', $m_user->correoelectronico)->whereNotIn('id', [664, 663, 41, 42, 72, 661, 81])->first();

            if($user_update){
                $user_update->name = $m_user->primernombre.' '.$m_user->segundonombre;
                $user_update->last_name = $m_user->primerapellido.' '.$m_user->segundoapellido;
                $user_update->dni = $m_user->idempleado_ID;
                $user_update->company_id = 17;
                $user_update->state_id = 1;
                $user_update->update();
                if($user_update){
                    $company_user = User::where('user_id', $user_update->id)->first();
                    if($company_user){
                        $company_user->name = $m_user->primernombre.' '.$m_user->segundonombre;
                        $company_user->last_name = $m_user->primerapellido.' '.$m_user->segundoapellido;
                        $company_user->cc_dni = $m_user->idempleado_ID;
                        $company_user->user_state_id = 1;
                        $company_user->update();
                    }
                }

            }else{
                $user_create = UserMaster::on('super_at_master')->create([
                    'name' => $m_user->primernombre.' '.$m_user->segundonombre,
                    'last_name' => $m_user->primerapellido.' '.$m_user->segundoapellido,
                    'dni' => $m_user->idempleado_ID,
                    'user_dk' => $m_user->DK_empleado,
                    'email' => $m_user->correoelectronico,
                    'password' => $password,
                    'company_id' => 17,
                    'state_id' => 1,
                ]);

                if($user_create){
                    $user_company = User::create([
                        'name' => $m_user->primernombre.' '.$m_user->segundonombre,
                        'last_name' => $m_user->primerapellido.' '.$m_user->segundoapellido,
                        'phone' => $m_user->telresidencia,
                        'cc_dni' => $m_user->idempleado_ID,
                        'email' => $m_user->correoelectronico,
                        'user_state_id' => 1,
                        'user_dk' => $m_user->DK_empleado,
                        'user_id' => $user_create->id,
                    ]);
                }
            }
        }

        echo 'Usuario actualizados<br>';
    }

    public static function insertMobileUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.estadoempleado_OID', 'c.nombre', 'c.codigo', 'e.DK as DK_empleado', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', 115342)
        ->where('e.zonasupervision_link', '!=', 'null')
        ->whereIn('c.codigo', ['074','004','171','274', '297'])
        ->whereNotIn('e.DK', [11788977, 11786426, 335694, 336188, 265828])
        ->get();

        foreach ($m_users as $m_user) {
            $user = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($user){
                $role_user = RoleUser::where('user_id', $user->id)->first();
                if($role_user){
                    if($m_user->nombre == 'ASISTENTE ADMINISTRATIVO'){
                        #validar el rol que tenia antes para eliminar las (sucursales, zonas, regiones)

                        if($role_user->role_id == 3 || $role_user->role_id == 1){
                            $delete_regions = UserRegion::where('role_user_id', $role_user->id)->delete();
                        }else if($role_user->role_id == 5){
                            $delete_zones = BranchOfficeAdministrator::where('user_id', $role_user->id)->delete();
                        }

                        $role_user->role_id = 4;
                        $role_user->update();
                    }else{
                        $role_user->role_id = 2;
                        $role_user->update();
                    }
                }else{
                    if($m_user->nombre == 'ASISTENTE ADMINISTRATIVO'){
                        $role_user_assistant = RoleUser::create([
                            'user_id' => $user->id,
                            'role_id' => 4
                            ]);

                    }else{
                        $role_user_supervisor = RoleUser::create([
                            'user_id' => $user->id,
                            'role_id' => 2
                        ]);
                    }
                }
            }

        }
        $m_users_admins = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.estadoempleado_OID', 'c.nombre', 'c.codigo', 'e.DK as DK_empleado', 'e.codigoempleado_ID', 'e.sucursal_link')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.sucursal_link', '!=', 'null')
        ->where('e.estadoempleado_OID', 115342)
        ->whereIn('c.codigo', ['003', '111', '178','179', '180', '182', '204', '226'])
        ->get();

        foreach ($m_users_admins as $m_user) {
            $user = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($user){
                $role_user = RoleUser::where('user_id', $user->id)->first();
                if($role_user){
                    #validar el rol que tenia antes para eliminar las (sucursales, zonas, regiones)

                    if($role_user->role_id == 3 || $role_user->role_id == 1){
                        $delete_regions = UserRegion::where('role_user_id', $role_user->id)->delete();
                    }else if($role_user->role_id == 2 || $role_user->role_id == 4){
                        $delete_zones = UserZone::where('role_user_id', $role_user->id)->delete();
                    }
                    $role_user->role_id = 5;
                    $role_user->update();
                }else{

                    $role_user_supervisor = RoleUser::create([
                        'user_id' => $user->id,
                        'role_id' => 5
                    ]);

                }
            }

        }

        echo 'Roles asignados a los de la app<br>';

    }

    public static function insertWebUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK as DK_empleado', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', 115342)
        ->where('c.codigo', '332')
        ->get();
        foreach ($m_users as $m_user) {
            $user = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($user){
                $role_user = RoleUser::where('user_id', $user->id)->first();
                if($role_user){
                    #validar el rol que tenia antes para eliminar las (sucursales, zonas, regiones)

                    if($role_user->role_id == 2 || $role_user->role_id == 4){
                        $delete_regions = UserZone::where('role_user_id', $role_user->id)->delete();
                    }else if($role_user->role_id == 5){
                        $delete_zones = BranchOfficeAdministrator::where('user_id', $role_user->id)->delete();
                    }
                    $role_user->role_id = 3;
                    $role_user->update();

                }else{

                    $role_user_assistant = RoleUser::create([
                        'user_id' => $user->id,
                        'role_id' => 3
                    ]);
                }
            }

        }
        echo 'Roles asignados a los de la web<br>';
    }

    public static function updateUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK AS DK_empleado')
        ->where('e.estadoempleado_OID', 115341)
        ->get();

        foreach ($m_users as $m_user) {
            $users = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($users){
                $users->user_state_id = 2;
                $users->update();
            }
        }

        echo 'Estado de usuarios actualizados<br>';
    }

    public static function validateNewRole()
    {

        # $user_update = UserMaster::on('super_at_master')->whereNotIn('id', [664, 663, 41, 42, 72, 661, 81])->first();
        $users = User::where('user_state_id', 1)
        ->whereNotIn('user_dk', [0])
        ->get();

        $maia_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK as DK_empleado', 'e.estadoempleado_OID', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia', 'c.nombre')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', '!=', 115341)
        ->whereIn('c.codigo', ['003', '004', '332', '111', '071', '074', '171', '174', '178', '179', '180', '182', '204', '226', '274', '297'])
        ->get();

        foreach ($users as $user) {

            #$users = User::where('user_dk', $user->DK_empleado)->where('user_state_id', 1)->first();
            $maia_user = collect($maia_users)->where('DK_empleado', $user->user_dk)->first();
            if(!$maia_user){
                $user_update = UserMaster::on('super_at_master')->where('id', $user->user_id)->first();
                $user_update->state_id = 2;
                $user_update->update();

                $user->user_state_id = 2;
                $user->update();
            }
        }

        echo 'Estado de usuarios actualizados<br>';
    }
}
