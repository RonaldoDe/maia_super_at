<?php

namespace App\Http\Controllers\Api\Maia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ComplianceByBranch;
use App\Models\SellerFulfillment;
use App\Models\BranchOffice;
use App\Models\EmployedOfBranchOffice;

class ComplianceController extends Controller
{
    public static function complianceByBranch()
    {
        $m_complaince_branchs = DB::connection('maiaDB')->select(DB::raw("SELECT s.Dk, s.nombresuc, CONCAT('$', FORMAT(sum(c.meta), 2)) as meta, CONCAT('$', FORMAT(f.ventas, 2)) as ventas, CONCAT(FORMAT((f.ventas/sum(c.meta))*100, 2),'%') as cumplimiento, MONTHNAME(CURDATE()) as mesactual from cuotacumplimiento c join sucursal s on c.sucursal_link=s.Dk join regionsucursal r on s.regionsucursal_OID=r.Dk join (select s.nombresuc, sum(n.ventascierre) as ventas from newdataminingsalessuc n, sucursal s, regionsucursal r where n.dksucursal=s.Dk and s.regionsucursal_OID=r.Dk and n.anomes=CONCAT(YEAR(CURDATE()),LPAD(MONTH(CURDATE()), 2, '0') ) and s.estado_OID=4481 group by s.Dk) as f on f.nombresuc=s.nombresuc where c.periodocuota_OID=392070 AND c.timefcuotainicio<=CURDATE() and c. timefcuotafin>=CURDATE() and s.estado_OID=4481 AND c.sucursal_link is NOT NULL group by s.Dk order by (f.ventas/sum(c.meta))*100 asc"));

        foreach ($m_complaince_branchs as $m_complaince_branch) {
            /*$last_month = date('F', strtotime("-1 month"));
            $current_month = date('F');*/
            $branch_office = BranchOffice::where('branch_office_dk', $m_complaince_branch->Dk)->first();
            if($branch_office){
                $compliance = ComplianceByBranch::where('branch_office_id', $branch_office->id)->first();
                if($compliance){
                    $compliance->branch_office_id = $branch_office->id;
                    $compliance->goal = $m_complaince_branch->meta;
                    $compliance->sales = $m_complaince_branch->ventas;
                    $compliance->compliance = $m_complaince_branch->cumplimiento;
                    $compliance->current_month = $m_complaince_branch->mesactual;
                    $compliance->update();
                }else{
                    $compliance_create = ComplianceByBranch::create([
                        'branch_office_id' => $branch_office->id,
                        'goal' => $m_complaince_branch->meta,
                        'sales' => $m_complaince_branch->ventas,
                        'compliance' => $m_complaince_branch->cumplimiento,
                        'current_month' => $m_complaince_branch->mesactual,
                    ]);
                }

            }

        }

        echo 'Cumplimiento por sucursal actualizada y creada<br>';
        
    }

    public static function sellerCompliance()
    {
        $m_sellers_complaince = DB::connection('maiaDB')->select(DB::raw("SELECT e.codigoempleado_ID, e.sucursal_link, CONCAT(e.primernombre,' ',e.segundonombre,' ',e.primerapellido,' ',e.segundoapellido) as nombreempleado, CONCAT('$', FORMAT(sum(c.meta), 2)) as meta, CONCAT('$', FORMAT(f.ventas, 2)) as ventas, CONCAT(FORMAT((f.ventas/sum(c.meta))*100, 2),'%') as cumplimiento, MONTHNAME(CURDATE()) as mesactual from cuotacumplimiento c join empleado e on c.empleado_OID=e.Dk join (select e.codigoempleado_ID, CONCAT(e.primernombre,' ',e.segundonombre,' ',e.primerapellido,' ',e.segundoapellido) as nombreempleado, sum(n.ventascierre) as ventas from newdataminingsales n, empleado e where n.dkempleado=e.Dk and n.anomes=CONCAT(YEAR(CURDATE()),LPAD(MONTH(CURDATE()), 2, '0') ) and e.estadoempleado_OID<>115341 group by e.Dk) as f on f.codigoempleado_ID=e.codigoempleado_ID where c.periodocuota_OID=392070 AND c.timefcuotainicio<=CURDATE() and c. timefcuotafin>=CURDATE() AND c.empleado_OID is NOT NULL and e.estadoempleado_OID<>115341 group by e.Dk order by (f.ventas/sum(c.meta))*100 desc"));
        foreach ($m_sellers_complaince as $m_seller_complaince) {
            $branch_office = BranchOffice::where('branch_office_dk', $m_seller_complaince->sucursal_link)->first();
            if($branch_office){
                $seller_compliance = SellerFulfillment::where('branch_offices_id', $branch_office->id)->first();
                if($seller_compliance){
                    $seller_compliance->branch_offices_id = $branch_office->id;
                    $seller_compliance->goal = $m_seller_complaince->meta;
                    $seller_compliance->sales = $m_seller_complaince->ventas;
                    $seller_compliance->fulfillment = $m_seller_complaince->cumplimiento;
                    $seller_compliance->current_month = $m_seller_complaince->mesactual;
                    $seller_compliance->employed_code = $m_seller_complaince->codigoempleado_ID;
                    $seller_compliance->user_state_id = 1;
                    $seller_compliance->name = $m_seller_complaince->nombreempleado;
                    $seller_compliance->update();
                }else{
                    $compliance_create = SellerFulfillment::create([
                        'branch_offices_id' => $branch_office->id,
                        'goal' => $m_seller_complaince->meta,
                        'sales' => $m_seller_complaince->ventas,
                        'fulfillment' => $m_seller_complaince->cumplimiento,
                        'current_month' => $m_seller_complaince->mesactual,
                        'employed_code' => $m_seller_complaince->codigoempleado_ID,
                        'user_state_id' => 1,
                        'name' => $m_seller_complaince->nombreempleado,
                    ]);
                }

            }

        }

       echo 'Cumplimiento por vendedor actualizado y creado<br>';
        
    }

    public static function employedOfBranch()
    {
        $m_employeds_branch = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.Dk as empleado_dk', 'e.primernombre', 'e.segundonombre', 'e.primerapellido', 'e.segundoapellido', 'e.sucursal_link', 'c.Dk as dk_cargo', 'c.nombre')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.Dk')
        ->where('e.sucursal_link', '!=', 'null')
        ->where('e.estadoempleado_OID', 115342)
        ->get();

        foreach ($m_employeds_branch as $m_employed_branch) {
            $branch_office = BranchOffice::where('branch_office_dk', $m_employed_branch->sucursal_link)->first();
            if($branch_office){
                $employed_branch = EmployedOfBranchOffice::where('employed_dk', $m_employed_branch->empleado_dk)->first();
                if($employed_branch){
                    $employed_branch->branch_offices_id = $branch_office->id;
                    $employed_branch->name = $m_employed_branch->primernombre. ' ' .$m_employed_branch->segundonombre;
                    $employed_branch->last_name = $m_employed_branch->primerapellido.' '.$m_employed_branch->segundoapellido;
                    $employed_branch->dk_position = $m_employed_branch->dk_cargo;
                    $employed_branch->position = $m_employed_branch->nombre;
                    $employed_branch->employed_dk = $m_employed_branch->empleado_dk;
                    $employed_branch->user_state_id = 1;
                    $employed_branch->update();
                }else{
                    $employed_branch_create = EmployedOfBranchOffice::create([
                        'branch_offices_id' => $branch_office->id,
                        'name' => $m_employed_branch->primernombre. ' ' .$m_employed_branch->segundonombre,
                        'last_name' => $m_employed_branch->primerapellido.' '.$m_employed_branch->segundoapellido,
                        'dk_position' => $m_employed_branch->dk_cargo,
                        'position' => $m_employed_branch->nombre,
                        'employed_dk' => $m_employed_branch->empleado_dk,
                        'user_state_id' => 1,
                    ]);
                }

            }

        }

        echo 'Empleado por sucursal actualizados y creados<br>';
        
    }

    public static function deleteEmployedByBranch()
    {
        $m_employeds_branch = DB::connection('maiaDB')
        ->table('empleado as e')
        ->select('e.Dk as empleado_dk')
        ->join('cargoempleado as c', 'e.cargoempleado_OID', 'c.Dk')
        ->where('e.sucursal_link', '!=', 'null')
        ->where('e.estadoempleado_OID', 115341)
        ->get();

        foreach ($m_employeds_branch as $m_employed_branch) {
            $employed_branch = EmployedOfBranchOffice::where('employed_dk', $m_employed_branch->empleado_dk)->where('user_state_id', 1)->first();
            if($employed_branch){
                $employed_branch->user_state_id = 2;
                $employed_branch->update();
            }
        }

        echo 'Empleados actulizados<br>';
    }
}
