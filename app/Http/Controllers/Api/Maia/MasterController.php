<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Maia\AreasController;
use App\Http\Controllers\Api\Maia\UsersController;
use App\Http\Controllers\Api\Maia\UserAreaController;
use App\Http\Controllers\Api\Maia\ComplianceController;
use App\Http\Controllers\Api\Maia\OtherController;

class MasterController extends Controller
{
    public function masterAll()
    {

        //usuarios de login
        echo 'Insertando usuarios<br>';
        $users = UsersController::insertUsers();
        //usuarios de la app
        $users_mobile = UsersController::insertMobileUsers();
        //usuarios de la web
        $users_web = UsersController::insertWebUsers();
        //actualizar estados de los usuarios
        echo 'Actualizando estados<br>';
        $users_update = UsersController::updateUsers();
        //regiones
        echo 'Insertando regiones<br>';
        $region = AreasController::regions();
        //zonas
        echo 'Insertando zonas<br>';
        $zones = AreasController::zones();
        //sucursales
        echo 'Insertando sucursales<br>';
        $branch_offices = AreasController::branchOffices();
        //eliminar sucursales que no estan en maia
        echo 'Cambiando estados a las sucursales<br>';
        $delete_branch = AreasController::deleteBranch();
        //Asignar zona a la region
        echo 'Insertando zonas a las regiones<br>';
        $assign_zone_to_region = AreasController::regionZones();
        //Asignar region a los coordinadores
        echo 'Asignando regiones a los usuarios<br>';
        $user_region = UserAreaController::userRegion();
        //Asignar zonas a los supervisores
        echo 'Asignando zonas a los usuarios<br>';
        $user_zones = UserAreaController::userZone();
        //Asignar sucursales a los administradores de drogueria
        echo 'Asignando sucursales a los usuarios<br>';
        $user_zones = UserAreaController::userBranch();
        //cumplimiento por sucursal
        echo 'Cumplimiento por sucursal<br>';
        $compliance_branchs = ComplianceController::complianceByBranch();
        //Cumplimiento por trabajadores
        echo 'Cumplimiento por vendedores<br>';
        $compliance_sellers = ComplianceController::sellerCompliance();
        //Empleados por sucursal
        echo 'Empleados por sucursal<br>';
        $employed_branch = ComplianceController::employedOfBranch();
        //Eliminar Empleados inactivos por sucursal
        echo 'Eliminando empleados por sucursal<br>';
        $delete_employed_branch = ComplianceController::deleteEmployedByBranch();
        //laboratorios
        echo 'Insertando laboratorios<br>';
        $labs = OtherController::labs();
        //productos
        echo 'Insertando productos<br>';
        $product = OtherController::products();
        //Productos inactivos
        echo 'Insertando descativando productos<br>';
        $inactive_product = OtherController::inactiveProducts();
    }
}
