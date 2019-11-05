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
        $users = UsersController::insertUsers();
        //usuarios de la app
        $users_mobile = UsersController::insertMobileUsers();
        //usuarios de la web
        $users_web = UsersController::insertWebUsers();
        //actualizar estados de los usuarios
        $users_update = UsersController::updateUsers();
        //regiones
        $region = AreasController::regions();
        //zonas
        $zones = AreasController::zones();
        //sucursales
        $branch_offices = AreasController::branchOffices();
        //eliminar sucursales que no estan en maia
        $delete_branch = AreasController::deleteBranch();
        //Asignar zona a la region
        $assign_zone_to_region = AreasController::regionZones();
        //Asignar region a los coordinadores
        $user_region = UserAreaController::userRegion();
        //Asignar zonas a los supervisores
        $user_zones = UserAreaController::userZone();
        //Asignar sucursales a los administradores de drogueria
        $user_zones = UserAreaController::userBranch();
        //cumplimiento por sucursal
        $compliance_branchs = ComplianceController::complianceByBranch();
        //Cumplimiento por trabajadores
        $compliance_sellers = ComplianceController::sellerCompliance();
        //Empleados por sucursal
        $employed_branch = ComplianceController::employedOfBranch();
        //Eliminar Empleados inactivos por sucursal
        $delete_employed_branch = ComplianceController::deleteEmployedByBranch();
        //laboratorios
        $labs = OtherController::labs();
        //productos
        $product = OtherController::products();
        //Productos inactivos
        $inactive_product = OtherController::inactiveProducts();
    }
}
