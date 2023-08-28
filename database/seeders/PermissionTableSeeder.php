<?php

namespace Database\Seeders;

use App\Models\Notify;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the databas seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [];

        //***********************************************************************\\
        //**************************** start General *****************************\\
        //*************************************************************************\\
        // start branch
        $permissions[] = ['name' => 'all Branch',"title" => 'show','table' => 'Branch','module' => 'General','category' => [''],'workflow'=> 'branch'];
        $permissions[] = ['name' => 'create Branch',"title" => 'create','table' => 'Branch','module' => 'General','category' => [''],'workflow'=> 'branch'];
        $permissions[] = ['name' => 'update Branch',"title" => 'update','table' => 'Branch','module' => 'General','category' => [''],'workflow'=> 'branch'];
        $permissions[] = ['name' => 'delete Branch',"title" => 'delete','table' => 'Branch','module' => 'General','category' => [''],'workflow'=> 'branch'];
        // end branch
        // start Store
        $permissions[] = ['name' => 'all Store',"title" => 'show','table' => 'Store','module' => 'General','category' => [''],'workflow'=> 'store'];
        $permissions[] = ['name' => 'create Store',"title" => 'create','table' => 'Store','module' => 'General','category' => [''],'workflow'=> 'store'];
        $permissions[] = ['name' => 'update Store',"title" => 'update','table' => 'Store','module' => 'General','category' => [''],'workflow'=> 'store'];
        $permissions[] = ['name' => 'delete Store',"title" => 'delete','table' => 'Store','module' => 'General','category' => [''],'workflow'=> 'store'];
        // end Store
        // start Serial
        $permissions[] = ['name' => 'all Serial',"title" => 'show','table' => 'Serial','module' => 'General','category' => [''],'workflow'=> 'serial'];
        $permissions[] = ['name' => 'create Serial',"title" => 'create','table' => 'Serial','module' => 'General','category' => [''],'workflow'=> 'serial'];
        $permissions[] = ['name' => 'update Serial',"title" => 'update','table' => 'Serial','module' => 'General','category' => [''],'workflow'=> 'serial'];
        $permissions[] = ['name' => 'delete Serial',"title" => 'delete','table' => 'Serial','module' => 'General','category' => [''],'workflow'=> 'serial'];
        // end Serial
        // start Screen Property
        $permissions[] = ['name' => 'all Screen Property',"title" => 'show','table' => 'Screen Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'create Screen Property',"title" => 'create','table' => 'Screen Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'update Screen Property',"title" => 'update','table' => 'Screen Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'delete Screen Property',"title" => 'delete','table' => 'Screen Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        // end Screen Property
        // start Tree Property
        $permissions[] = ['name' => 'all Tree Property',"title" => 'show','table' => 'Screen Tree Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'create Tree Property',"title" => 'create','table' => 'Screen Tree Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'update Tree Property',"title" => 'update','table' => 'Screen Tree Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        $permissions[] = ['name' => 'delete Tree Property',"title" => 'delete','table' => 'Screen Tree Property','module' => 'General','category' => [''],'workflow'=> 'properties'];
        // end Screen Property
        // start Sales Man Type
        $permissions[] = ['name' => 'all Sales Man Type',"title" => 'show','table' => 'Sales Man Type','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'create Sales Man Type',"title" => 'create','table' => 'Sales Man Type','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'update Sales Man Type',"title" => 'update','table' => 'Sales Man Type','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'delete Sales Man Type',"title" => 'delete','table' => 'Sales Man Type','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        // end Sales Man Type
        // start Sales Man
        $permissions[] = ['name' => 'all Sales Man',"title" => 'show','table' => 'Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'create Sales Man',"title" => 'create','table' => 'Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'update Sales Man',"title" => 'update','table' => 'Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'delete Sales Man',"title" => 'delete','table' => 'Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        // end Sales Man
        // start Internal Sales Man
        $permissions[] = ['name' => 'all Internal Sales Man',"title" => 'show','table' => 'Internal Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'create Internal Sales Man',"title" => 'create','table' => 'Internal Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'update Internal Sales Man',"title" => 'update','table' => 'Internal Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        $permissions[] = ['name' => 'delete Internal Sales Man',"title" => 'delete','table' => 'Internal Sales Man','module' => 'General','category' => [''],'workflow'=> 'sales men'];
        // end Internal Sales Man
        // start Payment Type
        $permissions[] = ['name' => 'all Payment Type',"title" => 'show','table' => 'Payment Type','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'create Payment Type',"title" => 'create','table' => 'Payment Type','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'update Payment Type',"title" => 'update','table' => 'Payment Type','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'delete Payment Type',"title" => 'delete','table' => 'Payment Type','module' => 'General','category' => [''],'workflow'=> 'bank'];
        // end Payment Type
        // start Bank
        $permissions[] = ['name' => 'all Bank',"title" => 'show','table' => 'Bank','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'create Bank',"title" => 'create','table' => 'Bank','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'update Bank',"title" => 'update','table' => 'Bank','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'delete Bank',"title" => 'delete','table' => 'Bank','module' => 'General','category' => [''],'workflow'=> 'bank'];
        // end Bank
        // start Bank Account
        $permissions[] = ['name' => 'all Bank Account',"title" => 'show','table' => 'Bank Account','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'create Bank Account',"title" => 'create','table' => 'Bank Account','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'update Bank Account',"title" => 'update','table' => 'Bank Account','module' => 'General','category' => [''],'workflow'=> 'bank'];
        $permissions[] = ['name' => 'delete Bank Account',"title" => 'delete','table' => 'Bank Account','module' => 'General','category' => [''],'workflow'=> 'bank'];
        // end Bank Account
        // start Country
        $permissions[] = ['name' => 'all Country',"title" => 'show','table' => 'Country','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'create Country',"title" => 'create','table' => 'Country','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'update Country',"title" => 'update','table' => 'Country','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'delete Country',"title" => 'delete','table' => 'Country','module' => 'General','category' => [''],'workflow'=> 'area'];
        // end Country
        // start Governorate
        $permissions[] = ['name' => 'all Governorate',"title" => 'show','table' => 'Governorate','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'create Governorate',"title" => 'create','table' => 'Governorate','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'update Governorate',"title" => 'update','table' => 'Governorate','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'delete Governorate',"title" => 'delete','table' => 'Governorate','module' => 'General','category' => [''],'workflow'=> 'area'];
        // end Governorate
        // start City
        $permissions[] = ['name' => 'all City',"title" => 'show','table' => 'City','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'create City',"title" => 'create','table' => 'City','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'update City',"title" => 'update','table' => 'City','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'delete City',"title" => 'delete','table' => 'City','module' => 'General','category' => [''],'workflow'=> 'area'];
        // end City
        // start Avenue
        $permissions[] = ['name' => 'all Avenue',"title" => 'show','table' => 'Avenue','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'create Avenue',"title" => 'create','table' => 'Avenue','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'update Avenue',"title" => 'update','table' => 'Avenue','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'delete Avenue',"title" => 'delete','table' => 'Avenue','module' => 'General','category' => [''],'workflow'=> 'area'];
        // end Avenue
        // start Street
        $permissions[] = ['name' => 'all Street',"title" => 'show','table' => 'Street','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'create Street',"title" => 'create','table' => 'Street','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'update Street',"title" => 'update','table' => 'Street','module' => 'General','category' => [''],'workflow'=> 'area'];
        $permissions[] = ['name' => 'delete Street',"title" => 'delete','table' => 'Street','module' => 'General','category' => [''],'workflow'=> 'area'];
        // end Street
        // start User
        $permissions[] = ['name' => 'all User',"title" => 'show','table' => 'User','module' => 'General','category' => [''],'workflow'=> 'users'];
        $permissions[] = ['name' => 'create User',"title" => 'create','table' => 'User','module' => 'General','category' => [''],'workflow'=> 'users'];
        $permissions[] = ['name' => 'update User',"title" => 'update','table' => 'User','module' => 'General','category' => [''],'workflow'=> 'users'];
        $permissions[] = ['name' => 'delete User',"title" => 'delete','table' => 'User','module' => 'General','category' => [''],'workflow'=> 'users'];
        // end User
        // start Role
        $permissions[] = ['name' => 'all Role',"title" => 'show','table' => 'Role','module' => 'General','category' => [''],'workflow'=> 'role'];
        $permissions[] = ['name' => 'create Role',"title" => 'create','table' => 'Role','module' => 'General','category' => [''],'workflow'=> 'role'];
        $permissions[] = ['name' => 'update Role',"title" => 'update','table' => 'Role','module' => 'General','category' => [''],'workflow'=> 'role'];
        $permissions[] = ['name' => 'delete Role',"title" => 'delete','table' => 'Role','module' => 'General','category' => [''],'workflow'=> 'role'];
        // end Role
        // start Department
        $permissions[] = ['name' => 'all Department',"title" => 'show','table' => 'Department','module' => 'General','category' => [''],'workflow'=> 'department'];
        $permissions[] = ['name' => 'create Department',"title" => 'create','table' => 'Department','module' => 'General','category' => [''],'workflow'=> 'department'];
        $permissions[] = ['name' => 'update Department',"title" => 'update','table' => 'Department','module' => 'General','category' => [''],'workflow'=> 'department'];
        $permissions[] = ['name' => 'delete Department',"title" => 'delete','table' => 'Department','module' => 'General','category' => [''],'workflow'=> 'department'];
        // end Department
        // start Priority
        $permissions[] = ['name' => 'all Priority',"title" => 'show','table' => 'Priority','module' => 'General','category' => [''],'workflow'=> 'priority'];
        $permissions[] = ['name' => 'create Priority',"title" => 'create','table' => 'Priority','module' => 'General','category' => [''],'workflow'=> 'priority'];
        $permissions[] = ['name' => 'update Priority',"title" => 'update','table' => 'Priority','module' => 'General','category' => [''],'workflow'=> 'priority'];
        $permissions[] = ['name' => 'delete Priority',"title" => 'delete','table' => 'Priority','module' => 'General','category' => [''],'workflow'=> 'priority'];
        // end Priority
        // start Periodic Maintenances
        $permissions[] = ['name' => 'all Periodic Maintenances',"title" => 'show','table' => 'Periodic Maintenances','module' => 'General','category' => [''],'workflow'=> 'periodic maintenance'];
        $permissions[] = ['name' => 'create Periodic Maintenances',"title" => 'create','table' => 'Periodic Maintenances','module' => 'General','category' => [''],'workflow'=> 'periodic maintenance'];
        $permissions[] = ['name' => 'update Periodic Maintenances',"title" => 'update','table' => 'Periodic Maintenances','module' => 'General','category' => [''],'workflow'=> 'periodic maintenance'];
        $permissions[] = ['name' => 'delete Periodic Maintenances',"title" => 'delete','table' => 'Periodic Maintenances','module' => 'General','category' => [''],'workflow'=> 'periodic maintenance'];
        // end Periodic Maintenances
        // start Location
        $permissions[] = ['name' => 'all Location',"title" => 'show','table' => 'Location','module' => 'General','category' => [''],'workflow'=> 'location'];
        $permissions[] = ['name' => 'create Location',"title" => 'create','table' => 'Location','module' => 'General','category' => [''],'workflow'=> 'location'];
        $permissions[] = ['name' => 'update Location',"title" => 'update','table' => 'Location','module' => 'General','category' => [''],'workflow'=> 'location'];
        $permissions[] = ['name' => 'delete Location',"title" => 'delete','table' => 'Location','module' => 'General','category' => [''],'workflow'=> 'location'];
        // end Location
        // start Category
        $permissions[] = ['name' => 'all Category',"title" => 'show','table' => 'Category','module' => 'General','category' => [''],'workflow'=> 'category'];
        $permissions[] = ['name' => 'create Category',"title" => 'create','table' => 'Category','module' => 'General','category' => [''],'workflow'=> 'category'];
        $permissions[] = ['name' => 'update Category',"title" => 'update','table' => 'Category','module' => 'General','category' => [''],'workflow'=> 'category'];
        $permissions[] = ['name' => 'delete Category',"title" => 'delete','table' => 'Category','module' => 'General','category' => [''],'workflow'=> 'category'];
        // end Category
        // start Customer
        $permissions[] = ['name' => 'all Customer',"title" => 'show','table' => 'Customer','module' => 'General','category' => [''],'workflow'=> 'general customer'];
        $permissions[] = ['name' => 'create Customer',"title" => 'create','table' => 'Customer','module' => 'General','category' => [''],'workflow'=> 'general customer'];
        $permissions[] = ['name' => 'update Customer',"title" => 'update','table' => 'Customer','module' => 'General','category' => [''],'workflow'=> 'general customer'];
        $permissions[] = ['name' => 'delete Customer',"title" => 'delete','table' => 'Customer','module' => 'General','category' => [''],'workflow'=> 'general customer'];
        // end Customer
        // start Equipment
        $permissions[] = ['name' => 'all Equipment',"title" => 'show','table' => 'Equipment','module' => 'General','category' => [''],'workflow'=> 'equipment'];
        $permissions[] = ['name' => 'create Equipment',"title" => 'create','table' => 'Equipment','module' => 'General','category' => [''],'workflow'=> 'equipment'];
        $permissions[] = ['name' => 'update Equipment',"title" => 'update','table' => 'Equipment','module' => 'General','category' => [''],'workflow'=> 'equipment'];
        $permissions[] = ['name' => 'delete Equipment',"title" => 'delete','table' => 'Equipment','module' => 'General','category' => [''],'workflow'=> 'equipment'];
        // end Equipment
        // start Currency
        $permissions[] = ['name' => 'all Currency',"title" => 'show','table' => 'Currency','module' => 'General','category' => [''],'workflow'=> 'currencies'];
        $permissions[] = ['name' => 'create Currency',"title" => 'create','table' => 'Currency','module' => 'General','category' => [''],'workflow'=> 'currencies'];
        $permissions[] = ['name' => 'update Currency',"title" => 'update','table' => 'Currency','module' => 'General','category' => [''],'workflow'=> 'currencies'];
        $permissions[] = ['name' => 'delete Currency',"title" => 'delete','table' => 'Currency','module' => 'General','category' => [''],'workflow'=> 'currencies'];
        // end Currency
        // start Financial Year
        $permissions[] = ['name' => 'all Financial Year',"title" => 'show','table' => 'Financial Year','module' => 'General','category' => [''],'workflow'=> 'financial Year'];
        $permissions[] = ['name' => 'create Financial Year',"title" => 'create','table' => 'Financial Year','module' => 'General','category' => [''],'workflow'=> 'financial Year'];
        $permissions[] = ['name' => 'update Financial Year',"title" => 'update','table' => 'Financial Year','module' => 'General','category' => [''],'workflow'=> 'financial Year'];
        $permissions[] = ['name' => 'delete Financial Year',"title" => 'delete','table' => 'Financial Year','module' => 'General','category' => [''],'workflow'=> 'financial Year'];
        // end Financial Year
        // start Unit
        $permissions[] = ['name' => 'all Unit',"title" => 'show','table' => 'Unit','module' => 'General','category' => [''],'workflow'=> 'units'];
        $permissions[] = ['name' => 'create Unit',"title" => 'create','table' => 'Unit','module' => 'General','category' => [''],'workflow'=> 'units'];
        $permissions[] = ['name' => 'update Unit',"title" => 'update','table' => 'Unit','module' => 'General','category' => [''],'workflow'=> 'units'];
        $permissions[] = ['name' => 'delete Unit',"title" => 'delete','table' => 'Unit','module' => 'General','category' => [''],'workflow'=> 'units'];
        // end  Unit
        // start Color
        $permissions[] = ['name' => 'all Color',"title" => 'show','table' => 'Color','module' => 'General','category' => [''],'workflow'=> 'color'];
        $permissions[] = ['name' => 'create Color',"title" => 'create','table' => 'Color','module' => 'General','category' => [''],'workflow'=> 'color'];
        $permissions[] = ['name' => 'update Color',"title" => 'update','table' => 'Color','module' => 'General','category' => [''],'workflow'=> 'color'];
        $permissions[] = ['name' => 'delete Color',"title" => 'delete','table' => 'Color','module' => 'General','category' => [''],'workflow'=> 'color'];
        // end  Color
        // start Status
        $permissions[] = ['name' => 'all Status',"title" => 'show','table' => 'Status','module' => 'General','category' => [''],'workflow'=> 'statuses'];
        $permissions[] = ['name' => 'create Status',"title" => 'create','table' => 'Status','module' => 'General','category' => [''],'workflow'=> 'statuses'];
        $permissions[] = ['name' => 'update Status',"title" => 'update','table' => 'Status','module' => 'General','category' => [''],'workflow'=> 'statuses'];
        $permissions[] = ['name' => 'delete Status',"title" => 'delete','table' => 'Status','module' => 'General','category' => [''],'workflow'=> 'statuses'];
        // end  Status
        // start Sell Method
        $permissions[] = ['name' => 'all Sell Method',"title" => 'show','table' => 'Sell Method','module' => 'General','category' => [''],'workflow'=> 'sell method'];
        $permissions[] = ['name' => 'create Sell Method',"title" => 'create','table' => 'Sell Method','module' => 'General','category' => [''],'workflow'=> 'sell method'];
        $permissions[] = ['name' => 'update Sell Method',"title" => 'update','table' => 'Sell Method','module' => 'General','category' => [''],'workflow'=> 'sell method'];
        $permissions[] = ['name' => 'delete Sell Method',"title" => 'delete','table' => 'Sell Method','module' => 'General','category' => [''],'workflow'=> 'sell method'];
        // end  Sell Method
        // start Employee
        $permissions[] = ['name' => 'all Employee',"title" => 'show','table' => 'Employee','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'create Employee',"title" => 'create','table' => 'Employee','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'update Employee',"title" => 'update','table' => 'Employee','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'delete Employee',"title" => 'delete','table' => 'Employee','module' => 'General','category' => [''],'workflow'=> 'employees'];
        // end  Employee
        // start Plan
        $permissions[] = ['name' => 'all Plan',"title" => 'show','table' => 'Plan','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'create Plan',"title" => 'create','table' => 'Plan','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'update Plan',"title" => 'update','table' => 'Plan','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'delete Plan',"title" => 'delete','table' => 'Plan','module' => 'General','category' => [''],'workflow'=> 'employees'];
        // end  Plan
        // start Plan Detail
        $permissions[] = ['name' => 'all Plan Detail',"title" => 'show','table' => 'Plan Detail','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'create Plan Detail',"title" => 'create','table' => 'Plan Detail','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'update Plan Detail',"title" => 'update','table' => 'Plan Detail','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'delete Plan Detail',"title" => 'delete','table' => 'Plan Detail','module' => 'General','category' => [''],'workflow'=> 'employees'];
        // end  Plan Detail
        // start External Sales Men
        $permissions[] = ['name' => 'all External Sales Men',"title" => 'show','table' => 'External Sales Men','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'create External Sales Men',"title" => 'create','table' => 'External Sales Men','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'update External Sales Men',"title" => 'update','table' => 'External Sales Men','module' => 'General','category' => [''],'workflow'=> 'employees'];
        $permissions[] = ['name' => 'delete External Sales Men',"title" => 'delete','table' => 'External Sales Men','module' => 'General','category' => [''],'workflow'=> 'employees'];
        // end  External Sales Men
        // start document
        $permissions[] = ['name' => 'all document',"title" => 'show','table' => 'Document','module' => 'General','category' => [''],'workflow'=> 'document'];
        $permissions[] = ['name' => 'update document',"title" => 'update','table' => 'Document','module' => 'General','category' => [''],'workflow'=> 'document'];
        // end  document
        // start document
        $permissions[] = ['name' => 'all document status',"title" => 'show','table' => 'Document Status','module' => 'General','category' => [''],'workflow'=> 'document'];
        $permissions[] = ['name' => 'create document status',"title" => 'create','table' => 'Document Status','module' => 'General','category' => [''],'workflow'=> 'document'];
        $permissions[] = ['name' => 'update document status',"title" => 'update','table' => 'Document Status','module' => 'General','category' => [''],'workflow'=> 'document'];
        $permissions[] = ['name' => 'delete document status',"title" => 'delete','table' => 'Document Status','module' => 'General','category' => [''],'workflow'=> 'document'];
        // end  document
        // start Sector
        $permissions[] = ['name' => 'all Sector',"title" => 'show','table' => 'Sector','module' => 'General','category' => [''],'workflow'=> 'sector'];
        $permissions[] = ['name' => 'create Sector',"title" => 'create','table' => 'Sector','module' => 'General','category' => [''],'workflow'=> 'sector'];
        $permissions[] = ['name' => 'update Sector',"title" => 'update','table' => 'Sector','module' => 'General','category' => [''],'workflow'=> 'sector'];
        $permissions[] = ['name' => 'delete Sector',"title" => 'delete','table' => 'Sector','module' => 'General','category' => [''],'workflow'=> 'sector'];
        // end Sector
        // start customer resource
        $permissions[] = ['name' => 'all Customer Resource',"title" => 'show','table' => 'Customer Resource','module' => 'General','category' => [''],'workflow'=> 'customer resource'];
        $permissions[] = ['name' => 'create Customer Resource',"title" => 'create','table' => 'Customer Resource','module' => 'General','category' => [''],'workflow'=> 'customer resource'];
        $permissions[] = ['name' => 'update Customer Resource',"title" => 'update','table' => 'Customer Resource','module' => 'General','category' => [''],'workflow'=> 'customer resource'];
        $permissions[] = ['name' => 'delete Customer Resource',"title" => 'delete','table' => 'Customer Resource','module' => 'General','category' => [''],'workflow'=> 'customer resource'];
        // end customer resource
        //***********************************************************************\\
        //**************************** end General *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start Real State *****************************\\
        //*************************************************************************\\
        // start category Real State
        $permissions[] = ['name' => 'all category RealState',"title" => 'show','table' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create category RealState',"title" => 'create','table' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update category RealState',"title" => 'update','table' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete category RealState',"title" => 'delete','table' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        // end category Real State
        // start Items Real State
        $permissions[] = ['name' => 'all items RealState',"title" => 'show','table' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create items RealState',"title" => 'create','table' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update items RealState',"title" => 'update','table' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete items RealState',"title" => 'delete','table' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        // end Items Real State
        // start unit status Real State
        $permissions[] = ['name' => 'all unit_status RealState',"title" => 'show','table' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create unit_status RealState',"title" => 'create','table' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update unit_status RealState',"title" => 'update','table' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete unit_status RealState',"title" => 'delete','table' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        // end unit status Real State
        // start wallets Real State
        $permissions[] = ['name' => 'all wallet RealState',"title" => 'show','table' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create wallet RealState',"title" => 'create','table' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update wallet RealState',"title" => 'update','table' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete wallet RealState',"title" => 'delete','table' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        // end wallets Real State
        // start owners Real State
        $permissions[] = ['name' => 'all owner RealState',"title" => 'show','table' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create owner RealState',"title" => 'create','table' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update owner RealState',"title" => 'update','table' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete owner RealState',"title" => 'delete','table' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'workflow'=> 'real estate'];
        // end owners Real State
        // start building Real State
        $permissions[] = ['name' => 'all building RealState',"title" => 'show','table' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create building RealState',"title" => 'create','table' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update building RealState',"title" => 'update','table' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete building RealState',"title" => 'delete','table' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        // end building Real State
        // start units State
        $permissions[] = ['name' => 'all building-wallet RealState',"title" => 'show','table' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create building-wallet RealState',"title" => 'create','table' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update building-wallet RealState',"title" => 'update','table' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete building-wallet RealState',"title" => 'delete','table' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        // end units State
        // start units State
        $permissions[] = ['name' => 'all units RealState',"title" => 'show','table' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create units RealState',"title" => 'create','table' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update units RealState',"title" => 'update','table' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete units RealState',"title" => 'delete','table' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'workflow'=> 'real estate'];
        // end units State
        // start reservation Real State
        $permissions[] = ['name' => 'all reservation RealState',"title" => 'show','table' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create reservation RealState',"title" => 'create','table' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update reservation RealState',"title" => 'update','table' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete reservation RealState',"title" => 'delete','table' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        // end reservation Real State
        // start contract Real State
        $permissions[] = ['name' => 'all contract RealState',"title" => 'show','table' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create contract RealState',"title" => 'create','table' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update contract RealState',"title" => 'update','table' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete contract RealState',"title" => 'delete','table' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        // end contract Real State
        // start invoice Real State
        $permissions[] = ['name' => 'all invoice RealState',"title" => 'show','table' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create invoice RealState',"title" => 'create','table' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update invoice RealState',"title" => 'update','table' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete invoice RealState',"title" => 'delete','table' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'workflow'=> 'real estate'];
        // end invoice Real State
        // start finance Real State
        $permissions[] = ['name' => 'all finance RealState',"title" => 'show','table' => 'Finance Report (RealState)','module' => 'Real State','category' => ['report realEstate'],'workflow'=> 'real estate'];
        // end finance Real State
        // start Sold Unit Real State
        $permissions[] = ['name' => 'all sold_Unit RealState',"title" => 'show','table' => 'Sold Unit (RealState)','module' => 'Real State','category' => ['admin_report realEstate','report realEstate'],'workflow'=> 'real estate'];
        // end Sold Unit Real State
        // start Sold Unit Real State
        $permissions[] = ['name' => 'all unSold_Unit RealState',"title" => 'show','table' => 'unsold Unit (RealState)','module' => 'Real State','category' => ['admin_report realEstate','report realEstate'],'workflow'=> 'real estate'];
        // end Sold Unit Real State
        // start customTable Real State
        $permissions[] = ['name' => 'all customTable RealState',"title" => 'show','table' => 'customTable (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'update customTable RealState',"title" => 'update','table' => 'customTable (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete customTable RealState',"title" => 'delete','table' => 'customTable (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        // end customTable Real State
        // start DatabaseBackup Real State
        $permissions[] = ['name' => 'all DatabaseBackup RealState',"title" => 'show','table' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'create DatabaseBackup RealState',"title" => 'create','table' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'download DatabaseBackup RealState',"title" => 'download','table' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        $permissions[] = ['name' => 'delete DatabaseBackup RealState',"title" => 'delete','table' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'workflow'=> 'real estate'];
        // end DatabaseBackup Real State
        //***********************************************************************\\
        //**************************** end Real State *****************************\\
        //*************************************************************************\\


        //***********************************************************************\\
        //**************************** start HR *****************************\\
        //*************************************************************************\\
        // start payrollHeads Hr
        $permissions[] = ['name' => 'all payrollHeads hr',"title" => 'show','table' => 'Payroll Heads (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'create payrollHeads hr',"title" => 'create','table' => 'Payroll Heads (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'update payrollHeads hr',"title" => 'update','table' => 'Payroll Heads (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'delete payrollHeads hr',"title" => 'delete','table' => 'Payroll Heads (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end payrollHeads Hr
        // start Job Title Hr
        $permissions[] = ['name' => 'all jobTitle hr',"title" => 'show','table' => 'Job Title (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'create jobTitle hr',"title" => 'create','table' => 'Job Title (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'update jobTitle hr',"title" => 'update','table' => 'Job Title (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'delete jobTitle hr',"title" => 'delete','table' => 'Job Title (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end job Title Hr
        // start request Types Hr
        $permissions[] = ['name' => 'all requestTypes hr',"title" => 'show','table' => 'Request Types (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'create requestTypes hr',"title" => 'create','table' => 'Request Types (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'update requestTypes hr',"title" => 'update','table' => 'Request Types (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'delete requestTypes hr',"title" => 'delete','table' => 'Request Types (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end request Types Hr
        // start Online Request Hr
        $permissions[] = ['name' => 'all onlineRequest hr',"title" => 'show','table' => 'Online Request (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'create onlineRequest hr',"title" => 'create','table' => 'Online Request (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'update onlineRequest hr',"title" => 'update','table' => 'Online Request (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        $permissions[] = ['name' => 'delete onlineRequest hr',"title" => 'delete','table' => 'Online Request (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end Online Request Hr
        // start payrolls Statement Hr
        $permissions[] = ['name' => 'all payrollsStatement hr',"title" => 'show','table' => 'Payrolls Statement (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end payrolls Statement Hr
        // start Vacations balances Hr
        $permissions[] = ['name' => 'all vacationsBalances hr',"title" => 'show','table' => 'Vacations Balances (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end Vacations balances Hr
        // start End services Hr
        $permissions[] = ['name' => 'all endServices hr',"title" => 'show','table' => 'End Services (HR)','module' => 'HR','category' => [''],'workflow'=> 'hr'];
        // end End services Hr
        //***********************************************************************\\
        //**************************** end HR *****************************\\
        //*************************************************************************\\


        //***********************************************************************\\
        //**************************** start Club *****************************\\
        //*************************************************************************\\
        // start Sponsor Club
        $permissions[] = ['name' => 'all sponsor club',"title" => 'show','table' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create sponsor club',"title" => 'create','table' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update sponsor club',"title" => 'update','table' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete sponsor club',"title" => 'delete','table' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        // end Sponsor Club
        // start Setting Club
        $permissions[] = ['name' => 'all setting club',"title" => 'show','table' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create setting club',"title" => 'create','table' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update setting club',"title" => 'update','table' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete setting club',"title" => 'delete','table' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        // end Setting Club
        // start Financial status Club
        $permissions[] = ['name' => 'all financialStatus club',"title" => 'show','table' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create financialStatus club',"title" => 'create','table' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update financialStatus club',"title" => 'update','table' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete financialStatus club',"title" => 'delete','table' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'workflow'=> 'club'];
        // end Financial status Club
        // start Members Club
        $permissions[] = ['name' => 'all members club',"title" => 'show','table' => 'Members (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        // end Members Club
        // start memberRequest  Club
        $permissions[] = ['name' => 'all memberRequest club',"title" => 'show','table' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create memberRequest club',"title" => 'create','table' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update memberRequest club',"title" => 'update','table' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete memberRequest club',"title" => 'delete','table' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        // end memberRequest Club
        // start Accept or reject membersr Club
        $permissions[] = ['name' => 'all acceptOrReject club',"title" => 'show','table' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'accept acceptOrReject club',"title" => 'accept member','table' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'reject acceptOrReject club',"title" => 'reject member','table' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        // end Accepted members Club
        // start Accept membersr Club
        $permissions[] = ['name' => 'all acceptedMembers club',"title" => 'show','table' => 'Accepted Members (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update acceptedMembers club',"title" => 'update','table' => 'Accepted Members (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        // end Accepted Members  Club
        // start Membership Renewal   Club
        $permissions[] = ['name' => 'all membershipRenewal club',"title" => 'show','table' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create membershipRenewal club',"title" => 'create','table' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update membershipRenewal club',"title" => 'update','table' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete membershipRenewal club',"title" => 'delete','table' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'workflow'=> 'club'];
        // end Membership Renewal  Club
        // start Subscription club
        $permissions[] = ['name' => 'all subscription club',"title" => 'show','table' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create subscription club',"title" => 'create','table' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete subscription club',"title" => 'delete','table' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        // end Subscription  club
        // start Multi Subscription club
        $permissions[] = ['name' => 'all multiSubscription club',"title" => 'show','table' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create multiSubscription club',"title" => 'create','table' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update multiSubscription club',"title" => 'update','table' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete multiSubscription club',"title" => 'delete','table' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        // end Multi Subscription  club
        // start Change Spenser club
        $permissions[] = ['name' => 'all changeSpenser club',"title" => 'show','table' => 'Change Spenser (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create changeSpenser club',"title" => 'create','table' => 'Change Spenser (Club)','module' => 'Club','category' => ['transaction club'],'workflow'=> 'club'];
        // end Change club
        // start Payer member club
        $permissions[] = ['name' => 'all payer member club',"title" => 'show','table' => 'payer member (club)','module' => 'Club','category' => ['report club'],'workflow'=> 'club'];
        // end Payer member club
        // start Permission member
        $permissions[] = ['name' => 'all Permission member club',"title" => 'show','table' => 'Permission Member (club)','module' => 'Club','category' => ['report club'],'workflow'=> 'club'];
        // end Permission member club
        // start Uncover club
        $permissions[] = ['name' => 'all uncover club',"title" => 'show','table' => 'Uncover (club)','module' => 'Club','category' => ['report club'],'workflow'=> 'club'];
        // end Uncover club
        // start Election Disclosure club
        $permissions[] = ['name' => 'all electionDisclosure club',"title" => 'show','table' => 'Election Disclosure (club)','module' => 'Club','category' => ['report club'],'workflow'=> 'club'];
        // end Election Disclosure club
        // start Payments And Subscriptions club
        $permissions[] = ['name' => 'all paymentsAndSubscriptions club',"title" => 'show','table' => 'Payments And Subscriptions (club)','module' => 'Club','category' => ['report club'],'workflow'=> 'club'];
        // end Payments And Subscriptions club
        // start customTable club
        $permissions[] = ['name' => 'all customTable club',"title" => 'show','table' => 'customTable (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'update customTable club',"title" => 'update','table' => 'customTable (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete customTable club',"title" => 'delete','table' => 'customTable (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        // end customTable club
        // start DatabaseBackup club
        $permissions[] = ['name' => 'all DatabaseBackup club',"title" => 'show','table' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'create DatabaseBackup club',"title" => 'create','table' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'download DatabaseBackup club',"title" => 'download','table' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        $permissions[] = ['name' => 'delete DatabaseBackup club',"title" => 'delete','table' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'workflow'=> 'club'];
        // end DatabaseBackup club
        //***********************************************************************\\
        //**************************** end club *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start board rent *****************************\\
        //*************************************************************************\\
        // start Panel board rent
        $permissions[] = ['name' => 'all panel boardRent',"title" => 'show','table' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create panel boardRent',"title" => 'create','table' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update panel boardRent',"title" => 'update','table' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete panel boardRent',"title" => 'delete','table' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        // end Panel board rent
        // start package board rent
        $permissions[] = ['name' => 'all package boardRent',"title" => 'show','table' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create package boardRent',"title" => 'create','table' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update package boardRent',"title" => 'update','table' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete package boardRent',"title" => 'delete','table' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'workflow'=> 'board rent'];
        // end package board rent
        // start Quotations board rent
        $permissions[] = ['name' => 'all quotation boardRent',"title" => 'show','table' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create quotation boardRent',"title" => 'create','table' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update quotation boardRent',"title" => 'update','table' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete quotation boardRent',"title" => 'delete','table' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        // end Quotations  board rent
        // start Order board rent
        $permissions[] = ['name' => 'all order boardRent',"title" => 'show','table' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create order boardRent',"title" => 'create','table' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update order boardRent',"title" => 'update','table' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete order boardRent',"title" => 'delete','table' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        // end Order  board rent
        // start invoice board rent
        $permissions[] = ['name' => 'all invoice boardRent',"title" => 'show','table' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create invoice boardRent',"title" => 'create','table' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update invoice boardRent',"title" => 'update','table' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete invoice boardRent',"title" => 'delete','table' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'workflow'=> 'board rent'];
        // end invoice  board rent
        // start finance boardRent
        $permissions[] = ['name' => 'all finance boardRent',"title" => 'show','table' => 'Finance Report (boardRent)','module' => 'Board Rent','category' => ['report boardRent'],'workflow'=> 'board rent'];
        // end finance boardRent
        // start customTable boardRent
        $permissions[] = ['name' => 'all customTable boardRent',"title" => 'show','table' => 'customTable (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update customTable boardRent',"title" => 'update','table' => 'customTable (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete customTable boardRent',"title" => 'delete','table' => 'customTable (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        // end customTable boardRent
        // start DatabaseBackup boardRent
        $permissions[] = ['name' => 'all DatabaseBackup boardRent',"title" => 'show','table' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create DatabaseBackup boardRent',"title" => 'create','table' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'download DatabaseBackup boardRent',"title" => 'download','table' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete DatabaseBackup boardRent',"title" => 'delete','table' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'workflow'=> 'board rent'];
        // end DatabaseBackup boardRent
        // start unitStatus board rent
        $permissions[] = ['name' => 'all unitStatus boardRent',"title" => 'show','table' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create unitStatus boardRent',"title" => 'create','table' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update unitStatus boardRent',"title" => 'update','table' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete unitStatus boardRent',"title" => 'delete','table' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        // end unitStatus  board rent
        // start lookup board rent
        $permissions[] = ['name' => 'all lookup boardRent',"title" => 'show','table' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'create lookup boardRent',"title" => 'create','table' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'update lookup boardRent',"title" => 'update','table' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        $permissions[] = ['name' => 'delete lookup boardRent',"title" => 'delete','table' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'workflow'=> 'board rent'];
        // end lookup  board rent
        //***********************************************************************\\
        //**************************** end board rent *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start receivable payable *****************************\\
        //*************************************************************************\\
        // start installmentStatus
        $permissions[] = ['name' => 'all status RP',"title" => 'show','table' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create status RP',"title" => 'create','table' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update status RP',"title" => 'update','table' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete status RP',"title" => 'delete','table' => 'status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        // end installmentStatus
        // start payment type
        $permissions[] = ['name' => 'all paymentType RP',"title" => 'show','table' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create paymentType RP',"title" => 'create','table' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update paymentType RP',"title" => 'update','table' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete paymentType RP',"title" => 'delete','table' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'workflow'=> 'receivable payable'];
        // end payment type
        // start payment Plan
        $permissions[] = ['name' => 'all paymentPlan RP',"title" => 'show','table' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create paymentPlan RP',"title" => 'create','table' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update paymentPlan RP',"title" => 'update','table' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete paymentPlan RP',"title" => 'delete','table' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        // end payment Plan
        // start Document Plan
        $permissions[] = ['name' => 'all documentPlan RP',"title" => 'show','table' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create documentPlan RP',"title" => 'create','table' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update documentPlan RP',"title" => 'update','table' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete documentPlan RP',"title" => 'delete','table' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        // end Document Plan
        // start condition
        $permissions[] = ['name' => 'all condition RP',"title" => 'show','table' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create condition RP',"title" => 'create','table' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update condition RP',"title" => 'update','table' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete condition RP',"title" => 'delete','table' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        // end condition
        // start payment plan installment
        $permissions[] = ['name' => 'all paymentPlanInstallment RP',"title" => 'show','table' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create paymentPlanInstallment RP',"title" => 'create','table' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update paymentPlanInstallment RP',"title" => 'update','table' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete paymentPlanInstallment RP',"title" => 'delete','table' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'workflow'=> 'receivable payable'];
        // end payment plan installment
        // start customTable
        $permissions[] = ['name' => 'all customTable RP',"title" => 'show','table' => 'customTable (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update customTable RP',"title" => 'update','table' => 'customTable (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete customTable RP',"title" => 'delete','table' => 'customTable (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        // end customTable
        // start DatabaseBackup
        $permissions[] = ['name' => 'all DatabaseBackup RP',"title" => 'show','table' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create DatabaseBackup RP',"title" => 'create','table' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'download DatabaseBackup RP',"title" => 'download','table' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete DatabaseBackup RP',"title" => 'delete','table' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'workflow'=> 'receivable payable'];
        // end DatabaseBackup
        // start Receivable Payable
        $permissions[] = ['name' => 'all admin RP',"title" => 'show','table' => 'Admin Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable'],'workflow'=> 'receivable payable'];
        // end Receivable Payable
        // start payment Report
        $permissions[] = ['name' => 'all paymentReport RP',"title" => 'show','table' => 'Payment Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable','finance receivablePayable'],'workflow'=> 'receivable payable'];
        // end payment Report
        // start payment Report
        $permissions[] = ['name' => 'all voucher RP',"title" => 'show','table' => 'Voucher Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable','finance receivablePayable'],'workflow'=> 'receivable payable'];
        // end payment Report
        // start openinig balance
        $permissions[] = ['name' => 'all openingBalance RP',"title" => 'show','table' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create openingBalance RP',"title" => 'create','table' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update openingBalance RP',"title" => 'update','table' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end openinig balance
        // start reservation
        $permissions[] = ['name' => 'all reservation RP',"title" => 'show','table' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create reservation RP',"title" => 'create','table' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update reservation RP',"title" => 'update','table' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete reservation RP',"title" => 'delete','table' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end reservation
        // start contract
        $permissions[] = ['name' => 'all contract RP',"title" => 'show','table' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create contract RP',"title" => 'create','table' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update contract RP',"title" => 'update','table' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete contract RP',"title" => 'delete','table' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end contract
        // start receipt Voucher
        $permissions[] = ['name' => 'all receiptVoucher RP',"title" => 'show','table' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create receiptVoucher RP',"title" => 'create','table' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update receiptVoucher RP',"title" => 'update','table' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end receipt Voucher
        // start credit note
        $permissions[] = ['name' => 'all creditNote RP',"title" => 'show','table' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create creditNote RP',"title" => 'create','table' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update creditNote RP',"title" => 'update','table' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end credit note
        // start Debit note
        $permissions[] = ['name' => 'all debitNote RP',"title" => 'show','table' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create debitNote RP',"title" => 'create','table' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update debitNote RP',"title" => 'update','table' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete debitNote RP',"title" => 'delete','table' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end Debit note
        // start issue Vourcher
        $permissions[] = ['name' => 'all issueVourcher RP',"title" => 'show','table' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'create issueVourcher RP',"title" => 'create','table' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'update issueVourcher RP',"title" => 'update','table' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        $permissions[] = ['name' => 'delete issueVourcher RP',"title" => 'delete','table' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'workflow'=> 'receivable payable'];
        // end issue Vourcher
        //***********************************************************************\\
        //**************************** end receivable payable *****************************\\
        //*************************************************************************\\


        //***********************************************************************\\
        //**************************** start Ticket Manager *****************************\\
        //*************************************************************************\\
        // start Task
        $permissions[] = ['name' => 'all Task',"title" => 'show','table' => 'Task','module' => 'Ticket Manager','category' => [''],'workflow'=> 'ticket manager'];
        $permissions[] = ['name' => 'create Task',"title" => 'create','table' => 'Task','module' => 'Ticket Manager','category' => [''],'workflow'=> 'ticket manager'];
        $permissions[] = ['name' => 'update Task',"title" => 'update','table' => 'Task','module' => 'Ticket Manager','category' => [''],'workflow'=> 'ticket manager'];
        $permissions[] = ['name' => 'delete Task',"title" => 'delete','table' => 'Task','module' => 'Ticket Manager','category' => [''],'workflow'=> 'ticket manager'];
        $permissions[] = ['name' => 'all Calender',"title" => 'show','table' => 'Calender','module' => 'Ticket Manager','category' => [''],'workflow'=> 'ticket manager'];
        // end Task
        //***********************************************************************\\
        //**************************** end Ticket Manager *****************************\\
        //*************************************************************************\\



//        $notifies = [];
//
//        // start Verify
//        $notifies[] =['name' =>'Trust Designer','name_e' =>'Trust Designer','module' => 'Verify'];
//
//
//        foreach ($notifies as $notify) {
//            Notify::create([
//                'name' => $notify['name'],
//                'name_e' => $notify['name_e'],
//                'module' => $notify['module']
//            ]);
//        }

        foreach ($permissions as $permission) {
            Permission::create([
                "name" => $permission["name"],
                "title" => $permission["title"],
                "table" => $permission["table"],
                "module" => $permission["module"],
                "category" => $permission["category"],
                "workflow" => $permission["workflow"]
            ]);
        }
    }
}
