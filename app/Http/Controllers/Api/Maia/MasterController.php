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
        error_log('Insertando usuarios');
        $users = UsersController::insertUsers();
        //usuarios de la app
        $users_mobile = UsersController::insertMobileUsers();
        //usuarios de la web
        $users_web = UsersController::insertWebUsers();
        //actualizar estados de los usuarios
        error_log('Actualizando estados');
        $users_update = UsersController::updateUsers();
        //regiones
        error_log('Insertando regiones');
        $region = AreasController::regions();
        //zonas
        error_log('Insertando zonas');
        $zones = AreasController::zones();
        //sucursales
        error_log('Insertando sucursales');
        $branch_offices = AreasController::branchOffices();
        //eliminar sucursales que no estan en maia
        error_log('Cambiando estados a las sucursales');
        $delete_branch = AreasController::deleteBranch();
        //Asignar zona a la region
        error_log('Insertando zonas a las regiones');
        $assign_zone_to_region = AreasController::regionZones();
        //Asignar region a los coordinadores
        error_log('Asignando regiones a los usuarios');
        $user_region = UserAreaController::userRegion();
        //Asignar zonas a los supervisores
        error_log('Asignando zonas a los usuarios');
        $user_zones = UserAreaController::userZone();
        //Asignar sucursales a los administradores de drogueria
        error_log('Asignando sucursales a los usuarios');
        $user_zones = UserAreaController::userBranch();
        //cumplimiento por sucursal
        error_log('Cumplimiento por sucursal');
        $compliance_branchs = ComplianceController::complianceByBranch();
        //Cumplimiento por trabajadores
        error_log('Cumplimiento por vendedores');
        $compliance_sellers = ComplianceController::sellerCompliance();
        //Empleados por sucursal
        error_log('Empleados por sucursal');
        $employed_branch = ComplianceController::employedOfBranch();
        //Eliminar Empleados inactivos por sucursal
        error_log('Eliminando empleados por sucursal');
        $delete_employed_branch = ComplianceController::deleteEmployedByBranch();
        //laboratorios
        error_log('Insertando laboratorios');
        $labs = OtherController::labs();
        //productos
        error_log('Insertando productos');
        $product = OtherController::products();
        //Productos inactivos
        error_log('Insertando descativando productos');
        $inactive_product = OtherController::inactiveProducts();
    }
}
