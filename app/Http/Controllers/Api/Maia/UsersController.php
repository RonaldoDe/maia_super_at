<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\RoleUser;
use App\Models\UserMaster;

class UsersController extends Controller
{
    public static function insertUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK as DK_empleado', 'e.estadoempleado_OID', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia', 'c.nombre')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', '!=', 115341)
        ->whereIn('c.codigo', ['003', '004', '005', '111', '071', '174', '178', '179', '180', '182', '204', '226', '274'])
        ->where('e.DK', '!=', 11788977)
        ->get();

        $password = '$2y$10$u4CCshmLkKB8Ij1S5p61ceI9f1RwtteyGAKSaI3J1mOcun4qwG81W';

        foreach ($m_users as $m_user) {
            $user_update = UserMaster::on('super_at_master')->where('email', $m_user->correoelectronico)->first();

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
        ->whereIn('c.codigo', ['003', '111', '178', '179', '180', '182', '204', '226', '074','004','171','274', '297'])
        ->whereNotIn('e.DK', [11788977, 11786426, 335694, 336188, 265828])
        ->get();

        foreach ($m_users as $m_user) {
            $user = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($user){
                $role_user = RoleUser::where('user_id', $user->id)->first();
                if($role_user){
                    if($m_user->nombre == 'ASISTENTE ADMINISTRATIVO'){
                        $role_user->role_id = 4;
                        $role_user->update();
                    }else if($m_user->codigo == '003' || $m_user->codigo == '111' || $m_user->codigo == '178' || $m_user->codigo == '179' || $m_user->codigo == '180' || $m_user->codigo == '182' || $m_user->codigo == '204' || $m_user->codigo == '226'){
                        $role_user->role_id = 5;
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

                    }else if($m_user->codigo == '003' || $m_user->codigo == '111' || $m_user->codigo == '178' || $m_user->codigo == '179' || $m_user->codigo == '180' || $m_user->codigo == '182' || $m_user->codigo == '204' || $m_user->codigo == '226'){
                        $role_user_assistant = RoleUser::create([
                            'user_id' => $user->id,
                            'role_id' => 5
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
        echo 'Roles asignados a los de la app<br>';

    }

    public static function insertWebUsers()
    {
        $m_users = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.DK as DK_empleado', 'e.idempleado_ID', 'e.codigoempleado_ID', 'e.correoelectronico', 'e.fotoempleado', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.telresidencia')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.DK')
        ->where('e.estadoempleado_OID', 115342)
        ->where('c.codigo', '005')
        ->get();
        foreach ($m_users as $m_user) {
            $user = User::where('user_dk', $m_user->DK_empleado)->where('user_state_id', 1)->first();
            if($user){
                $role_user = RoleUser::where('user_id', $user->id)->first();
                if($role_user){

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
}
