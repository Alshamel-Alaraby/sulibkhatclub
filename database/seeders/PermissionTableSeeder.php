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
        $permissions[] = ['name' => 'Dashboard', "title" => 'show', 'crud_name' => 'Dashboard', 'module' => 'General', 'table' => ''];
        $permissions[] = ['name' => 'Account Statement', "title" => 'show', 'crud_name' => 'Account Statement', 'module' => 'General', 'table' => ''];

        $permissions[] = ['name' => 'all Branch', "title" => 'show', 'crud_name' => 'Branch', 'module' => 'General', 'table' => 'general_branches'];
        $permissions[] = ['name' => 'create Branch', "title" => 'create', 'crud_name' => 'Branch', 'module' => 'General', 'table' => 'general_branches'];
        $permissions[] = ['name' => 'update Branch', "title" => 'update', 'crud_name' => 'Branch', 'module' => 'General', 'table' => 'general_branches'];
        $permissions[] = ['name' => 'delete Branch', "title" => 'delete', 'crud_name' => 'Branch', 'module' => 'General', 'table' => 'general_branches'];
        // end branch
        // start Store
        $permissions[] = ['name' => 'all Store', "title" => 'show', 'crud_name' => 'Store', 'module' => 'General', 'table' => 'general_stores'];
        $permissions[] = ['name' => 'create Store', "title" => 'create', 'crud_name' => 'Store', 'module' => 'General', 'table' => 'general_stores'];
        $permissions[] = ['name' => 'update Store', "title" => 'update', 'crud_name' => 'Store', 'module' => 'General', 'table' => 'general_stores'];
        $permissions[] = ['name' => 'delete Store', "title" => 'delete', 'crud_name' => 'Store', 'module' => 'General', 'table' => 'general_stores'];
        // end Store
        // start Serial
        $permissions[] = ['name' => 'all Serial', "title" => 'show', 'crud_name' => 'Serial', 'module' => 'General', 'table' => 'general_serials'];
        $permissions[] = ['name' => 'create Serial', "title" => 'create', 'crud_name' => 'Serial', 'module' => 'General', 'table' => 'general_serials'];
        $permissions[] = ['name' => 'update Serial', "title" => 'update', 'crud_name' => 'Serial', 'module' => 'General', 'table' => 'general_serials'];
        $permissions[] = ['name' => 'delete Serial', "title" => 'delete', 'crud_name' => 'Serial', 'module' => 'General', 'table' => 'general_serials'];
        // end Serial
        // start Screen Property
        $permissions[] = ['name' => 'all Screen Property', "title" => 'show', 'crud_name' => 'Screen Property', 'module' => 'General', 'table' => 'general_properties'];
        $permissions[] = ['name' => 'create Screen Property', "title" => 'create', 'crud_name' => 'Screen Property', 'module' => 'General', 'table' => 'general_properties'];
        $permissions[] = ['name' => 'update Screen Property', "title" => 'update', 'crud_name' => 'Screen Property', 'module' => 'General', 'table' => 'general_properties'];
        $permissions[] = ['name' => 'delete Screen Property', "title" => 'delete', 'crud_name' => 'Screen Property', 'module' => 'General', 'table' => 'general_properties'];
        // end Screen Property
        // start Tree Property
        $permissions[] = ['name' => 'all Tree Property', "title" => 'show', 'crud_name' => 'Property', 'module' => 'General', 'table' => 'general_tree_properties'];
        $permissions[] = ['name' => 'create Tree Property', "title" => 'create', 'crud_name' => 'Property', 'module' => 'General', 'table' => 'general_tree_properties'];
        $permissions[] = ['name' => 'update Tree Property', "title" => 'update', 'crud_name' => 'Property', 'module' => 'General', 'table' => 'general_tree_properties'];
        $permissions[] = ['name' => 'delete Tree Property', "title" => 'delete', 'crud_name' => 'Property', 'module' => 'General', 'table' => 'general_tree_properties'];
        // end Screen Property
        // start Sales Man Type
        $permissions[] = ['name' => 'all Sales Man Type', "title" => 'show', 'crud_name' => 'Salesman Type', 'module' => 'General', 'table' => 'general_salesmen_types'];
        $permissions[] = ['name' => 'create Sales Man Type', "title" => 'create', 'crud_name' => 'Salesman Type Type', 'module' => 'General', 'table' => 'general_salesmen_types'];
        $permissions[] = ['name' => 'update Sales Man Type', "title" => 'update', 'crud_name' => 'Salesman Type', 'module' => 'General', 'table' => 'general_salesmen_types'];
        $permissions[] = ['name' => 'delete Sales Man Type', "title" => 'delete', 'crud_name' => 'Salesman Type', 'module' => 'General', 'table' => 'general_salesmen_types'];
        // end Sales Man Type
        // start Sales Man
        $permissions[] = ['name' => 'all Sales Man', "title" => 'show', 'crud_name' => 'sales men', 'module' => 'General', 'table' => 'general_salesmen'];
        $permissions[] = ['name' => 'create Sales Man', "title" => 'create', 'crud_name' => 'sales men', 'module' => 'General', 'table' => 'general_salesmen'];
        $permissions[] = ['name' => 'update Sales Man', "title" => 'update', 'crud_name' => 'sales men', 'module' => 'General', 'table' => 'general_salesmen'];
        $permissions[] = ['name' => 'delete Sales Man', "title" => 'delete', 'crud_name' => 'sales men', 'module' => 'General', 'table' => 'general_salesmen'];
        // end Sales Man
        // start Internal Sales Man
        $permissions[] = ['name' => 'all Internal Sales Man', "title" => 'show', 'crud_name' => 'Internal sales men', 'module' => 'General', 'table' => 'general_internal_salesman'];
        $permissions[] = ['name' => 'create Internal Sales Man', "title" => 'create', 'crud_name' => 'Internal sales men', 'module' => 'General', 'table' => 'general_internal_salesman'];
        $permissions[] = ['name' => 'update Internal Sales Man', "title" => 'update', 'crud_name' => 'Internal sales men', 'module' => 'General', 'table' => 'general_internal_salesman'];
        $permissions[] = ['name' => 'delete Internal Sales Man', "title" => 'delete', 'crud_name' => 'Internal sales men', 'module' => 'General', 'table' => 'general_internal_salesman'];
        // end Internal Sales Man
        // start Payment Type
        $permissions[] = ['name' => 'all Payment Type', "title" => 'show', 'crud_name' => 'Payment Type', 'module' => 'General', 'table' => 'general_payment_types'];
        $permissions[] = ['name' => 'create Payment Type', "title" => 'create', 'crud_name' => 'Payment Type', 'module' => 'General', 'table' => 'general_payment_types'];
        $permissions[] = ['name' => 'update Payment Type', "title" => 'update', 'crud_name' => 'Payment Type', 'module' => 'General', 'table' => 'general_payment_types'];
        $permissions[] = ['name' => 'delete Payment Type', "title" => 'delete', 'crud_name' => 'Payment Type', 'module' => 'General', 'table' => 'general_payment_types'];
        // end Payment Type
        // start Payment methods
        $permissions[] = ['name' => 'all Payment Method', "title" => 'show', 'crud_name' => 'Payment Method', 'module' => 'General', 'table' => 'general_payment_methods'];
        $permissions[] = ['name' => 'create Payment Method', "title" => 'create', 'crud_name' => 'Payment Method', 'module' => 'General', 'table' => 'general_payment_methods'];
        $permissions[] = ['name' => 'update Payment Method', "title" => 'update', 'crud_name' => 'Payment Method', 'module' => 'General', 'table' => 'general_payment_methods'];
        $permissions[] = ['name' => 'delete Payment Method', "title" => 'delete', 'crud_name' => 'Payment Method', 'module' => 'General', 'table' => 'general_payment_methods'];
        // end Payment methods
        // start Bank
        $permissions[] = ['name' => 'all Bank', "title" => 'show', 'crud_name' => 'Bank', 'module' => 'General', 'table' => 'general_banks'];
        $permissions[] = ['name' => 'create Bank', "title" => 'create', 'crud_name' => 'Bank', 'module' => 'General', 'table' => 'general_banks'];
        $permissions[] = ['name' => 'update Bank', "title" => 'update', 'crud_name' => 'Bank', 'module' => 'General', 'table' => 'general_banks'];
        $permissions[] = ['name' => 'delete Bank', "title" => 'delete', 'crud_name' => 'Bank', 'module' => 'General', 'table' => 'general_banks'];
        // end Bank
        // start Bank Account
        $permissions[] = ['name' => 'all Bank Account', "title" => 'show', 'crud_name' => 'Bank Account', 'module' => 'General', 'table' => 'general_bank_accounts'];
        $permissions[] = ['name' => 'create Bank Account', "title" => 'create', 'crud_name' => 'Bank Account', 'module' => 'General', 'table' => 'general_bank_accounts'];
        $permissions[] = ['name' => 'update Bank Account', "title" => 'update', 'crud_name' => 'Bank Account', 'module' => 'General', 'table' => 'general_bank_accounts'];
        $permissions[] = ['name' => 'delete Bank Account', "title" => 'delete', 'crud_name' => 'Bank Account', 'module' => 'General', 'table' => 'general_bank_accounts'];
        // end Bank Account
        // start Country
        $permissions[] = ['name' => 'all Country', "title" => 'show', 'crud_name' => 'Country', 'module' => 'General', 'table' => 'general_countries'];
        $permissions[] = ['name' => 'create Country', "title" => 'create', 'crud_name' => 'Country', 'module' => 'General', 'table' => 'general_countries'];
        $permissions[] = ['name' => 'update Country', "title" => 'update', 'crud_name' => 'Country', 'module' => 'General', 'table' => 'general_countries'];
        $permissions[] = ['name' => 'delete Country', "title" => 'delete', 'crud_name' => 'Country', 'module' => 'General', 'table' => 'general_countries'];
        // end Country
        // start Governorate
        $permissions[] = ['name' => 'all Governorate', "title" => 'show', 'crud_name' => 'Governorate', 'module' => 'General', 'table' => 'general_governorates'];
        $permissions[] = ['name' => 'create Governorate', "title" => 'create', 'crud_name' => 'Governorate', 'module' => 'General', 'table' => 'general_governorates'];
        $permissions[] = ['name' => 'update Governorate', "title" => 'update', 'crud_name' => 'Governorate', 'module' => 'General', 'table' => 'general_governorates'];
        $permissions[] = ['name' => 'delete Governorate', "title" => 'delete', 'crud_name' => 'Governorate', 'module' => 'General', 'table' => 'general_governorates'];
        // end Governorate
        // start City
        $permissions[] = ['name' => 'all City', "title" => 'show', 'crud_name' => 'City', 'module' => 'General', 'table' => 'general_cities'];
        $permissions[] = ['name' => 'create City', "title" => 'create', 'crud_name' => 'City', 'module' => 'General', 'table' => 'general_cities'];
        $permissions[] = ['name' => 'update City', "title" => 'update', 'crud_name' => 'City', 'module' => 'General', 'table' => 'general_cities'];
        $permissions[] = ['name' => 'delete City', "title" => 'delete', 'crud_name' => 'City', 'module' => 'General', 'table' => 'general_cities'];
        // end City
        // start Avenue
        $permissions[] = ['name' => 'all Avenue', "title" => 'show', 'crud_name' => 'Avenue', 'module' => 'General', 'table' => 'general_avenues'];
        $permissions[] = ['name' => 'create Avenue', "title" => 'create', 'crud_name' => 'Avenue', 'module' => 'General', 'table' => 'general_avenues'];
        $permissions[] = ['name' => 'update Avenue', "title" => 'update', 'crud_name' => 'Avenue', 'module' => 'General', 'table' => 'general_avenues'];
        $permissions[] = ['name' => 'delete Avenue', "title" => 'delete', 'crud_name' => 'Avenue', 'module' => 'General', 'table' => 'general_avenues'];
        // end Avenue
        // start Street
        $permissions[] = ['name' => 'all Street', "title" => 'show', 'crud_name' => 'Street', 'module' => 'General', 'table' => 'general_streets'];
        $permissions[] = ['name' => 'create Street', "title" => 'create', 'crud_name' => 'Street', 'module' => 'General', 'table' => 'general_streets'];
        $permissions[] = ['name' => 'update Street', "title" => 'update', 'crud_name' => 'Street', 'module' => 'General', 'table' => 'general_streets'];
        $permissions[] = ['name' => 'delete Street', "title" => 'delete', 'crud_name' => 'Street', 'module' => 'General', 'table' => 'general_streets'];
        // end Street
        // start User
        $permissions[] = ['name' => 'all User', "title" => 'show', 'crud_name' => 'User', 'module' => 'General', 'table' => 'general_users'];
        $permissions[] = ['name' => 'create User', "title" => 'create', 'crud_name' => 'User', 'module' => 'General', 'table' => 'general_users'];
        $permissions[] = ['name' => 'update User', "title" => 'update', 'crud_name' => 'User', 'module' => 'General', 'table' => 'general_users'];
        $permissions[] = ['name' => 'delete User', "title" => 'delete', 'crud_name' => 'User', 'module' => 'General', 'table' => 'general_users'];
        // end User
        // start Role
        $permissions[] = ['name' => 'all Role', "title" => 'show', 'crud_name' => 'Role', 'module' => 'General', 'table' => 'general_roles'];
        $permissions[] = ['name' => 'create Role', "title" => 'create', 'crud_name' => 'Role', 'module' => 'General', 'table' => 'general_roles'];
        $permissions[] = ['name' => 'update Role', "title" => 'update', 'crud_name' => 'Role', 'module' => 'General', 'table' => 'general_roles'];
        $permissions[] = ['name' => 'delete Role', "title" => 'delete', 'crud_name' => 'Role', 'module' => 'General', 'table' => 'general_roles'];
        // end Role
        // start Department
        $permissions[] = ['name' => 'all Department', "title" => 'show', 'crud_name' => 'Department', 'module' => 'General', 'table' => 'general_departments'];
        $permissions[] = ['name' => 'create Department', "title" => 'create', 'crud_name' => 'Department', 'module' => 'General', 'table' => 'general_departments'];
        $permissions[] = ['name' => 'update Department', "title" => 'update', 'crud_name' => 'Department', 'module' => 'General', 'table' => 'general_departments'];
        $permissions[] = ['name' => 'delete Department', "title" => 'delete', 'crud_name' => 'Department', 'module' => 'General', 'table' => 'general_departments'];
        // end Department
        // start Priority
        $permissions[] = ['name' => 'all Priority', "title" => 'show', 'crud_name' => 'Priority', 'module' => 'General', 'table' => 'general_priorities'];
        $permissions[] = ['name' => 'create Priority', "title" => 'create', 'crud_name' => 'Priority', 'module' => 'General', 'table' => 'general_priorities'];
        $permissions[] = ['name' => 'update Priority', "title" => 'update', 'crud_name' => 'Priority', 'module' => 'General', 'table' => 'general_priorities'];
        $permissions[] = ['name' => 'delete Priority', "title" => 'delete', 'crud_name' => 'Priority', 'module' => 'General', 'table' => 'general_priorities'];
        // end Priority
        // start Periodic Maintenances
        $permissions[] = ['name' => 'all Periodic Maintenances', "title" => 'show', 'crud_name' => 'Periodic Maintenance', 'module' => 'General', 'table' => 'general_periodic_maintenances'];
        $permissions[] = ['name' => 'create Periodic Maintenances', "title" => 'create', 'crud_name' => 'Periodic Maintenance', 'module' => 'General', 'table' => 'general_periodic_maintenances'];
        $permissions[] = ['name' => 'update Periodic Maintenances', "title" => 'update', 'crud_name' => 'Periodic Maintenance', 'module' => 'General', 'table' => 'general_periodic_maintenances'];
        $permissions[] = ['name' => 'delete Periodic Maintenances', "title" => 'delete', 'crud_name' => 'Periodic Maintenance', 'module' => 'General', 'table' => 'general_periodic_maintenances'];
        // end Periodic Maintenances
        // start Location
        $permissions[] = ['name' => 'all Location', "title" => 'show', 'crud_name' => 'Location', 'module' => 'General', 'table' => 'general_locations'];
        $permissions[] = ['name' => 'create Location', "title" => 'create', 'crud_name' => 'Location', 'module' => 'General', 'table' => 'general_locations'];
        $permissions[] = ['name' => 'update Location', "title" => 'update', 'crud_name' => 'Location', 'module' => 'General', 'table' => 'general_locations'];
        $permissions[] = ['name' => 'delete Location', "title" => 'delete', 'crud_name' => 'Location', 'module' => 'General', 'table' => 'general_locations'];
        // end Location
        // start Category
        $permissions[] = ['name' => 'all Category', "title" => 'show', 'crud_name' => 'Category', 'module' => 'General', 'table' => 'general_categories'];
        $permissions[] = ['name' => 'create Category', "title" => 'create', 'crud_name' => 'Category', 'module' => 'General', 'table' => 'general_categories'];
        $permissions[] = ['name' => 'update Category', "title" => 'update', 'crud_name' => 'Category', 'module' => 'General', 'table' => 'general_categories'];
        $permissions[] = ['name' => 'delete Category', "title" => 'delete', 'crud_name' => 'Category', 'module' => 'General', 'table' => 'general_categories'];
        // end Category
        // start Customer
        $permissions[] = ['name' => 'all Customer', "title" => 'show', 'crud_name' => 'Customer', 'module' => 'General', 'table' => 'general_customers'];
        $permissions[] = ['name' => 'create Customer', "title" => 'create', 'crud_name' => 'Customer', 'module' => 'General', 'table' => 'general_customers'];
        $permissions[] = ['name' => 'update Customer', "title" => 'update', 'crud_name' => 'Customer', 'module' => 'General', 'table' => 'general_customers'];
        $permissions[] = ['name' => 'delete Customer', "title" => 'delete', 'crud_name' => 'Customer', 'module' => 'General', 'table' => 'general_customers'];
        // end Customer
        // start Equipment
        $permissions[] = ['name' => 'all Equipment', "title" => 'show', 'crud_name' => 'Equipment', 'module' => 'General', 'table' => 'general_equipments'];
        $permissions[] = ['name' => 'create Equipment', "title" => 'create', 'crud_name' => 'Equipment', 'module' => 'General', 'table' => 'general_equipments'];
        $permissions[] = ['name' => 'update Equipment', "title" => 'update', 'crud_name' => 'Equipment', 'module' => 'General', 'table' => 'general_equipments'];
        $permissions[] = ['name' => 'delete Equipment', "title" => 'delete', 'crud_name' => 'Equipment', 'module' => 'General', 'table' => 'general_equipments'];
        // end Equipment
        // start Currency
        $permissions[] = ['name' => 'all Currency', "title" => 'show', 'crud_name' => 'Currency', 'module' => 'General', 'table' => 'general_currencies'];
        $permissions[] = ['name' => 'create Currency', "title" => 'create', 'crud_name' => 'Currency', 'module' => 'General', 'table' => 'general_currencies'];
        $permissions[] = ['name' => 'update Currency', "title" => 'update', 'crud_name' => 'Currency', 'module' => 'General', 'table' => 'general_currencies'];
        $permissions[] = ['name' => 'delete Currency', "title" => 'delete', 'crud_name' => 'Currency', 'module' => 'General', 'table' => 'general_currencies'];
        // end Currency
        // start Financial Year
        $permissions[] = ['name' => 'all Financial Year', "title" => 'show', 'crud_name' => 'Financial Year', 'module' => 'General', 'table' => 'general_financial_years'];
        $permissions[] = ['name' => 'create Financial Year', "title" => 'create', 'crud_name' => 'Financial Year', 'module' => 'General', 'table' => 'general_financial_years'];
        $permissions[] = ['name' => 'update Financial Year', "title" => 'update', 'crud_name' => 'Financial Year', 'module' => 'General', 'table' => 'general_financial_years'];
        $permissions[] = ['name' => 'delete Financial Year', "title" => 'delete', 'crud_name' => 'Financial Year', 'module' => 'General', 'table' => 'general_financial_years'];
        // end Financial Year
        // start Unit
        $permissions[] = ['name' => 'all Unit', "title" => 'show', 'crud_name' => 'Unit', 'module' => 'General', 'table' => 'general_units'];
        $permissions[] = ['name' => 'create Unit', "title" => 'create', 'crud_name' => 'Unit', 'module' => 'General', 'table' => 'general_units'];
        $permissions[] = ['name' => 'update Unit', "title" => 'update', 'crud_name' => 'Unit', 'module' => 'General', 'table' => 'general_units'];
        $permissions[] = ['name' => 'delete Unit', "title" => 'delete', 'crud_name' => 'Unit', 'module' => 'General', 'table' => 'general_units'];
        // end  Unit
        // start Color
        $permissions[] = ['name' => 'all Color', "title" => 'show', 'crud_name' => 'Color', 'module' => 'General', 'table' => 'general_colors'];
        $permissions[] = ['name' => 'create Color', "title" => 'create', 'crud_name' => 'Color', 'module' => 'General', 'table' => 'general_colors'];
        $permissions[] = ['name' => 'update Color', "title" => 'update', 'crud_name' => 'Color', 'module' => 'General', 'table' => 'general_colors'];
        $permissions[] = ['name' => 'delete Color', "title" => 'delete', 'crud_name' => 'Color', 'module' => 'General', 'table' => 'general_colors'];
        // end  Color
        // start Status
        $permissions[] = ['name' => 'all Status', "title" => 'show', 'crud_name' => 'Status', 'module' => 'General', 'table' => 'general_statuses'];
        $permissions[] = ['name' => 'create Status', "title" => 'create', 'crud_name' => 'Status', 'module' => 'General', 'table' => 'general_statuses'];
        $permissions[] = ['name' => 'update Status', "title" => 'update', 'crud_name' => 'Status', 'module' => 'General', 'table' => 'general_statuses'];
        $permissions[] = ['name' => 'delete Status', "title" => 'delete', 'crud_name' => 'Status', 'module' => 'General', 'table' => 'general_statuses'];
        // end  Status
        // start Employee
        $permissions[] = ['name' => 'all Employee', "title" => 'show', 'crud_name' => 'Employee', 'module' => 'General', 'table' => 'general_employees'];
        $permissions[] = ['name' => 'create Employee', "title" => 'create', 'crud_name' => 'Employee', 'module' => 'General', 'table' => 'general_employees'];
        $permissions[] = ['name' => 'update Employee', "title" => 'update', 'crud_name' => 'Employee', 'module' => 'General', 'table' => 'general_employees'];
        $permissions[] = ['name' => 'delete Employee', "title" => 'delete', 'crud_name' => 'Employee', 'module' => 'General', 'table' => 'general_employees'];
        // end  Employee
        // start Plan
        $permissions[] = ['name' => 'all Plan', "title" => 'show', 'crud_name' => 'Plan', 'module' => 'General', 'table' => 'general_salesmen_plans'];
        $permissions[] = ['name' => 'create Plan', "title" => 'create', 'crud_name' => 'Plan', 'module' => 'General', 'table' => 'general_salesmen_plans'];
        $permissions[] = ['name' => 'update Plan', "title" => 'update', 'crud_name' => 'Plan', 'module' => 'General', 'table' => 'general_salesmen_plans'];
        $permissions[] = ['name' => 'delete Plan', "title" => 'delete', 'crud_name' => 'Plan', 'module' => 'General', 'table' => 'general_salesmen_plans'];
        // end  Plan
        // start Plan Detail
        $permissions[] = ['name' => 'all Plan Detail', "title" => 'show', 'crud_name' => 'Plan Detail', 'module' => 'General', 'table' => 'general_salesmen_plans_details'];
        $permissions[] = ['name' => 'create Plan Detail', "title" => 'create', 'crud_name' => 'Plan Detail', 'module' => 'General', 'table' => 'general_salesmen_plans_details'];
        $permissions[] = ['name' => 'update Plan Detail', "title" => 'update', 'crud_name' => 'Plan Detail', 'module' => 'General', 'table' => 'general_salesmen_plans_details'];
        $permissions[] = ['name' => 'delete Plan Detail', "title" => 'delete', 'crud_name' => 'Plan Detail', 'module' => 'General', 'table' => 'general_salesmen_plans_details'];
        // end  Plan Detail
        // start External Sales Men
        $permissions[] = ['name' => 'all External Sales Men', "title" => 'show', 'crud_name' => 'External Sales man', 'module' => 'General', 'table' => 'general_external_salesmen'];
        $permissions[] = ['name' => 'create External Sales Men', "title" => 'create', 'crud_name' => 'External Sales man', 'module' => 'General', 'table' => 'general_external_salesmen'];
        $permissions[] = ['name' => 'update External Sales Men', "title" => 'update', 'crud_name' => 'External Sales man', 'module' => 'General', 'table' => 'general_external_salesmen'];
        $permissions[] = ['name' => 'delete External Sales Men', "title" => 'delete', 'crud_name' => 'External Sales man', 'module' => 'General', 'table' => 'general_external_salesmen'];
        // end  External Sales Men
        // start document
        $permissions[] = ['name' => 'all document', "title" => 'show', 'crud_name' => 'Document', 'module' => 'General', 'table' => 'general_documents'];
        $permissions[] = ['name' => 'update document', "title" => 'update', 'crud_name' => 'Document', 'module' => 'General', 'table' => 'general_documents'];
        // end  document
        // start document
        $permissions[] = ['name' => 'all document status', "title" => 'show', 'crud_name' => 'Document Status', 'module' => 'General', 'table' => 'general_document_statuses'];
        $permissions[] = ['name' => 'create document status', "title" => 'create', 'crud_name' => 'Document Status', 'module' => 'General', 'table' => 'general_document_statuses'];
        $permissions[] = ['name' => 'update document status', "title" => 'update', 'crud_name' => 'Document Status', 'module' => 'General', 'table' => 'general_document_statuses'];
        $permissions[] = ['name' => 'delete document status', "title" => 'delete', 'crud_name' => 'Document Status', 'module' => 'General', 'table' => 'general_document_statuses'];
        // end  document
        // start Sector
        $permissions[] = ['name' => 'all Sector', "title" => 'show', 'crud_name' => 'Sector', 'module' => 'General', 'table' => 'general_sectors'];
        $permissions[] = ['name' => 'create Sector', "title" => 'create', 'crud_name' => 'Sector', 'module' => 'General', 'table' => 'general_sectors'];
        $permissions[] = ['name' => 'update Sector', "title" => 'update', 'crud_name' => 'Sector', 'module' => 'General', 'table' => 'general_sectors'];
        $permissions[] = ['name' => 'delete Sector', "title" => 'delete', 'crud_name' => 'Sector', 'module' => 'General', 'table' => 'general_sectors'];
        // end Sector
        // start customer resource
        $permissions[] = ['name' => 'all Customer Resource', "title" => 'show', 'crud_name' => 'Customer Resource', 'module' => 'General', 'table' => 'general_customer_sources'];
        $permissions[] = ['name' => 'create Customer Resource', "title" => 'create', 'crud_name' => 'Customer Resource', 'module' => 'General', 'table' => 'general_customer_sources'];
        $permissions[] = ['name' => 'update Customer Resource', "title" => 'update', 'crud_name' => 'Customer Resource', 'module' => 'General', 'table' => 'general_customer_sources'];
        $permissions[] = ['name' => 'delete Customer Resource', "title" => 'delete', 'crud_name' => 'Customer Resource', 'module' => 'General', 'table' => 'general_customer_sources'];
        // end customer resource
        //***********************************************************************\\
        //**************************** end General *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start Real State *****************************\\
        //*************************************************************************\\
        // start category Real State
//        $permissions[] = ['name' => 'all category RealState',"title" => 'show','crud_name' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'general_real estate'];
//        $permissions[] = ['name' => 'create category RealState',"title" => 'create','crud_name' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update category RealState',"title" => 'update','crud_name' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete category RealState',"title" => 'delete','crud_name' => 'Category (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        // end category Real State
//        // start Items Real State
//        $permissions[] = ['name' => 'all items RealState',"title" => 'show','crud_name' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create items RealState',"title" => 'create','crud_name' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update items RealState',"title" => 'update','crud_name' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete items RealState',"title" => 'delete','crud_name' => 'Items (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        // end Items Real State
//        // start unit status Real State
//        $permissions[] = ['name' => 'all unit_status RealState',"title" => 'show','crud_name' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create unit_status RealState',"title" => 'create','crud_name' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update unit_status RealState',"title" => 'update','crud_name' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete unit_status RealState',"title" => 'delete','crud_name' => 'Unit Status (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        // end unit status Real State
//        // start wallets Real State
//        $permissions[] = ['name' => 'all wallet RealState',"title" => 'show','crud_name' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create wallet RealState',"title" => 'create','crud_name' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update wallet RealState',"title" => 'update','crud_name' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete wallet RealState',"title" => 'delete','crud_name' => 'Wallet (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        // end wallets Real State
//        // start owners Real State
//        $permissions[] = ['name' => 'all owner RealState',"title" => 'show','crud_name' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create owner RealState',"title" => 'create','crud_name' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update owner RealState',"title" => 'update','crud_name' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete owner RealState',"title" => 'delete','crud_name' => 'Owners (RealState)','module' => 'Real State','category' => ['lookup realEstate'],'table'=> 'real estate'];
//        // end owners Real State
//        // start building Real State
//        $permissions[] = ['name' => 'all building RealState',"title" => 'show','crud_name' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create building RealState',"title" => 'create','crud_name' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update building RealState',"title" => 'update','crud_name' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete building RealState',"title" => 'delete','crud_name' => 'Building (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        // end building Real State
//        // start units State
//        $permissions[] = ['name' => 'all building-wallet RealState',"title" => 'show','crud_name' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create building-wallet RealState',"title" => 'create','crud_name' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update building-wallet RealState',"title" => 'update','crud_name' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete building-wallet RealState',"title" => 'delete','crud_name' => 'Building Wallet (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        // end units State
//        // start units State
//        $permissions[] = ['name' => 'all units RealState',"title" => 'show','crud_name' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create units RealState',"title" => 'create','crud_name' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update units RealState',"title" => 'update','crud_name' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete units RealState',"title" => 'delete','crud_name' => 'Units (RealState)','module' => 'Real State','category' => ['master_file realEstate'],'table'=> 'real estate'];
//        // end units State
//        // start reservation Real State
//        $permissions[] = ['name' => 'all reservation RealState',"title" => 'show','crud_name' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create reservation RealState',"title" => 'create','crud_name' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update reservation RealState',"title" => 'update','crud_name' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete reservation RealState',"title" => 'delete','crud_name' => 'Reservation (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        // end reservation Real State
//        // start contract Real State
//        $permissions[] = ['name' => 'all contract RealState',"title" => 'show','crud_name' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create contract RealState',"title" => 'create','crud_name' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update contract RealState',"title" => 'update','crud_name' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete contract RealState',"title" => 'delete','crud_name' => 'Contract (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        // end contract Real State
//        // start invoice Real State
//        $permissions[] = ['name' => 'all invoice RealState',"title" => 'show','crud_name' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create invoice RealState',"title" => 'create','crud_name' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update invoice RealState',"title" => 'update','crud_name' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete invoice RealState',"title" => 'delete','crud_name' => 'Invoice (RealState)','module' => 'Real State','category' => ['transaction realEstate'],'table'=> 'real estate'];
//        // end invoice Real State
//        // start finance Real State
//        $permissions[] = ['name' => 'all finance RealState',"title" => 'show','crud_name' => 'Finance Report (RealState)','module' => 'Real State','category' => ['report realEstate'],'table'=> 'real estate'];
//        // end finance Real State
//        // start Sold Unit Real State
//        $permissions[] = ['name' => 'all sold_Unit RealState',"title" => 'show','crud_name' => 'Sold Unit (RealState)','module' => 'Real State','category' => ['admin_report realEstate','report realEstate'],'table'=> 'real estate'];
//        // end Sold Unit Real State
//        // start Sold Unit Real State
//        $permissions[] = ['name' => 'all unSold_Unit RealState',"title" => 'show','crud_name' => 'unsold Unit (RealState)','module' => 'Real State','category' => ['admin_report realEstate','report realEstate'],'table'=> 'real estate'];
//        // end Sold Unit Real State
//        // start customcrud_name Real State
//        $permissions[] = ['name' => 'all customcrud_name RealState',"title" => 'show','crud_name' => 'customcrud_name (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'update customcrud_name RealState',"title" => 'update','crud_name' => 'customcrud_name (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete customcrud_name RealState',"title" => 'delete','crud_name' => 'customcrud_name (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        // end customcrud_name Real State
//        // start DatabaseBackup Real State
//        $permissions[] = ['name' => 'all DatabaseBackup RealState',"title" => 'show','crud_name' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'create DatabaseBackup RealState',"title" => 'create','crud_name' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'download DatabaseBackup RealState',"title" => 'download','crud_name' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
//        $permissions[] = ['name' => 'delete DatabaseBackup RealState',"title" => 'delete','crud_name' => 'DatabaseBackup (RealState)','module' => 'Real State','category' => ['tools realEstate'],'table'=> 'real estate'];
        // end DatabaseBackup Real State
        //***********************************************************************\\
        //**************************** end Real State *****************************\\
        //*************************************************************************\\


        //***********************************************************************\\
        //**************************** start HR *****************************\\
        //*************************************************************************\\
        // start payrollHeads Hr
        $permissions[] = ['name' => 'all payrollHeads hr', "title" => 'show', 'crud_name' => 'Payroll Head', 'module' => 'HR', 'table' => 'hr_payroll_heads'];
        $permissions[] = ['name' => 'create payrollHeads hr', "title" => 'create', 'crud_name' => 'Payroll Head', 'module' => 'HR', 'table' => 'hr_payroll_heads'];
        $permissions[] = ['name' => 'update payrollHeads hr', "title" => 'update', 'crud_name' => 'Payroll Head', 'module' => 'HR', 'table' => 'hr_payroll_heads'];
        $permissions[] = ['name' => 'delete payrollHeads hr', "title" => 'delete', 'crud_name' => 'Payroll Head', 'module' => 'HR', 'table' => 'hr_payroll_heads'];
        // end payrollHeads Hr
        // start Job Title Hr
        $permissions[] = ['name' => 'all jobTitle hr', "title" => 'show', 'crud_name' => 'Job Title', 'module' => 'HR', 'table' => 'hr_job_title'];
        $permissions[] = ['name' => 'create jobTitle hr', "title" => 'create', 'crud_name' => 'Job Title', 'module' => 'HR', 'table' => 'hr_job_title'];
        $permissions[] = ['name' => 'update jobTitle hr', "title" => 'update', 'crud_name' => 'Job Title', 'module' => 'HR', 'table' => 'hr_job_title'];
        $permissions[] = ['name' => 'delete jobTitle hr', "title" => 'delete', 'crud_name' => 'Job Title', 'module' => 'HR', 'table' => 'hr_job_title'];
        // end job Title Hr
        // start request Types Hr
        $permissions[] = ['name' => 'all requestTypes hr', "title" => 'show', 'crud_name' => 'Request Type', 'module' => 'HR', 'table' => 'hr_requests_types'];
        $permissions[] = ['name' => 'create requestTypes hr', "title" => 'create', 'crud_name' => 'Request Type', 'module' => 'HR', 'table' => 'hr_requests_types'];
        $permissions[] = ['name' => 'update requestTypes hr', "title" => 'update', 'crud_name' => 'Request Type', 'module' => 'HR', 'table' => 'hr_requests_types'];
        $permissions[] = ['name' => 'delete requestTypes hr', "title" => 'delete', 'crud_name' => 'Request Type', 'module' => 'HR', 'table' => 'hr_requests_types'];
        // end request Types Hr
        // start Online Request Hr
        $permissions[] = ['name' => 'all onlineRequest hr', "title" => 'show', 'crud_name' => 'Online Request', 'module' => 'HR', 'table' => 'hr_requests'];
        $permissions[] = ['name' => 'create onlineRequest hr', "title" => 'create', 'crud_name' => 'Online Request', 'module' => 'HR', 'table' => 'hr_requests'];
        $permissions[] = ['name' => 'update onlineRequest hr', "title" => 'update', 'crud_name' => 'Online Request', 'module' => 'HR', 'table' => 'hr_requests'];
        $permissions[] = ['name' => 'delete onlineRequest hr', "title" => 'delete', 'crud_name' => 'Online Request', 'module' => 'HR', 'table' => 'hr_requests'];
        // end Online Request Hr
        // start time Attendance
        $permissions[] = ['name' => 'all timeAttendance hr', "title" => 'show', 'crud_name' => 'Time Attendance', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'create timeAttendance hr', "title" => 'create', 'crud_name' => 'Time Attendance', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'update timeAttendance hr', "title" => 'update', 'crud_name' => 'Time Attendance', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'delete timeAttendance hr', "title" => 'delete', 'crud_name' => 'Time Attendance', 'module' => 'HR', 'table' => ''];
        // end time Attendance
        // start Time Employee
        $permissions[] = ['name' => 'all timeEmployee hr', "title" => 'show', 'crud_name' => 'Time Employee', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'create timeEmployee hr', "title" => 'create', 'crud_name' => 'Time Employee', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'update timeEmployee hr', "title" => 'update', 'crud_name' => 'Time Employee', 'module' => 'HR', 'table' => ''];
        $permissions[] = ['name' => 'delete timeEmployee hr', "title" => 'delete', 'crud_name' => 'Time Employee', 'module' => 'HR', 'table' => ''];
        // end Time Employee
        // start attendance Setting Hr
        $permissions[] = ['name' => 'all attendanceSetting hr', "title" => 'show', 'crud_name' => 'Attendance Setting', 'module' => 'HR', 'table' => 'hr_attendance_settings'];
        $permissions[] = ['name' => 'update attendanceSetting hr', "title" => 'update', 'crud_name' => 'Attendance Setting', 'module' => 'HR', 'table' => 'hr_attendance_settings'];
        // end attendance Setting Hr
        // start payrolls Statement Hr
        $permissions[] = ['name' => 'all payrollsStatement hr', "title" => 'show', 'crud_name' => 'Payroll Statement', 'module' => 'HR', 'table' => ''];
        // end payrolls Statement Hr
        // start Vacations balances Hr
        $permissions[] = ['name' => 'all vacationsBalances hr', "title" => 'show', 'crud_name' => 'Vacation Balance', 'module' => 'HR', 'table' => ''];
        // end Vacations balances Hr
        // start End services Hr
        $permissions[] = ['name' => 'all endServices hr', "title" => 'show', 'crud_name' => 'End Service', 'module' => 'HR', 'table' => ''];
        // end End services Hr
        // start Emergency Balance Hr
        $permissions[] = ['name' => 'all emergienciesBalances hr', "title" => 'show', 'crud_name' => 'Emergency Balance', 'module' => 'HR', 'table' => ''];
        // end Emergency Balance Hr
        // start attendance
        $permissions[] = ['name' => 'all attendance hr', "title" => 'show', 'crud_name' => 'Attendance', 'module' => 'HR', 'table' => ''];
        // end attendance
        // start attendance And Departure
        $permissions[] = ['name' => 'all attendanceAndDeparture hr', "title" => 'show', 'crud_name' => 'Attendance And Departure', 'module' => 'HR', 'table' => ''];
        // end time Attendance
        //***********************************************************************\\
        //**************************** end HR *****************************\\
        //*************************************************************************\\


        //***********************************************************************\\
        //**************************** start Club *****************************\\
        //*************************************************************************\\
        // start Sponsor Club
//        $permissions[] = ['name' => 'all sponsor club',"title" => 'show','crud_name' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create sponsor club',"title" => 'create','crud_name' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update sponsor club',"title" => 'update','crud_name' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete sponsor club',"title" => 'delete','crud_name' => 'Sponsor (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        // end Sponsor Club
//        // start Setting Club
//        $permissions[] = ['name' => 'all setting club',"title" => 'show','crud_name' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create setting club',"title" => 'create','crud_name' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update setting club',"title" => 'update','crud_name' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete setting club',"title" => 'delete','crud_name' => 'Setting (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        // end Setting Club
//        // start Financial status Club
//        $permissions[] = ['name' => 'all financialStatus club',"title" => 'show','crud_name' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create financialStatus club',"title" => 'create','crud_name' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update financialStatus club',"title" => 'update','crud_name' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete financialStatus club',"title" => 'delete','crud_name' => 'Financial Status (Club)','module' => 'Club','category' => ['lookup club'],'table'=> 'club'];
//        // end Financial status Club
//        // start Members Club
//        $permissions[] = ['name' => 'all members club',"title" => 'show','crud_name' => 'Members (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        // end Members Club
//        // start memberRequest  Club
//        $permissions[] = ['name' => 'all memberRequest club',"title" => 'show','crud_name' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create memberRequest club',"title" => 'create','crud_name' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update memberRequest club',"title" => 'update','crud_name' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete memberRequest club',"title" => 'delete','crud_name' => 'Member Request (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        // end memberRequest Club
//        // start Accept or reject membersr Club
//        $permissions[] = ['name' => 'all acceptOrReject club',"title" => 'show','crud_name' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'accept acceptOrReject club',"title" => 'accept member','crud_name' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'reject acceptOrReject club',"title" => 'reject member','crud_name' => 'Accept Or Reject M(Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        // end Accepted members Club
//        // start Accept membersr Club
//        $permissions[] = ['name' => 'all acceptedMembers club',"title" => 'show','crud_name' => 'Accepted Members (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update acceptedMembers club',"title" => 'update','crud_name' => 'Accepted Members (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        // end Accepted Members  Club
//        // start Membership Renewal   Club
//        $permissions[] = ['name' => 'all membershipRenewal club',"title" => 'show','crud_name' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create membershipRenewal club',"title" => 'create','crud_name' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update membershipRenewal club',"title" => 'update','crud_name' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete membershipRenewal club',"title" => 'delete','crud_name' => 'Membership Renewal (Club)','module' => 'Club','category' => ['master_file club'],'table'=> 'club'];
//        // end Membership Renewal  Club
//        // start Subscription club
//        $permissions[] = ['name' => 'all subscription club',"title" => 'show','crud_name' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create subscription club',"title" => 'create','crud_name' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete subscription club',"title" => 'delete','crud_name' => 'Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        // end Subscription  club
//        // start Multi Subscription club
//        $permissions[] = ['name' => 'all multiSubscription club',"title" => 'show','crud_name' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create multiSubscription club',"title" => 'create','crud_name' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update multiSubscription club',"title" => 'update','crud_name' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete multiSubscription club',"title" => 'delete','crud_name' => 'Multi Subscription (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        // end Multi Subscription  club
//        // start Change Spenser club
//        $permissions[] = ['name' => 'all changeSpenser club',"title" => 'show','crud_name' => 'Change Spenser (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create changeSpenser club',"title" => 'create','crud_name' => 'Change Spenser (Club)','module' => 'Club','category' => ['transaction club'],'table'=> 'club'];
//        // end Change club
//        // start Payer member club
//        $permissions[] = ['name' => 'all payer member club',"title" => 'show','crud_name' => 'payer member (club)','module' => 'Club','category' => ['report club'],'table'=> 'club'];
//        // end Payer member club
//        // start Permission member
//        $permissions[] = ['name' => 'all Permission member club',"title" => 'show','crud_name' => 'Permission Member (club)','module' => 'Club','category' => ['report club'],'table'=> 'club'];
//        // end Permission member club
//        // start Uncover club
//        $permissions[] = ['name' => 'all uncover club',"title" => 'show','crud_name' => 'Uncover (club)','module' => 'Club','category' => ['report club'],'table'=> 'club'];
//        // end Uncover club
//        // start Election Disclosure club
//        $permissions[] = ['name' => 'all electionDisclosure club',"title" => 'show','crud_name' => 'Election Disclosure (club)','module' => 'Club','category' => ['report club'],'table'=> 'club'];
//        // end Election Disclosure club
//        // start Payments And Subscriptions club
//        $permissions[] = ['name' => 'all paymentsAndSubscriptions club',"title" => 'show','crud_name' => 'Payments And Subscriptions (club)','module' => 'Club','category' => ['report club'],'table'=> 'club'];
//        // end Payments And Subscriptions club
//        // start customcrud_name club
//        $permissions[] = ['name' => 'all customcrud_name club',"title" => 'show','crud_name' => 'customcrud_name (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'update customcrud_name club',"title" => 'update','crud_name' => 'customcrud_name (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete customcrud_name club',"title" => 'delete','crud_name' => 'customcrud_name (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        // end customcrud_name club
//        // start DatabaseBackup club
//        $permissions[] = ['name' => 'all DatabaseBackup club',"title" => 'show','crud_name' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'create DatabaseBackup club',"title" => 'create','crud_name' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'download DatabaseBackup club',"title" => 'download','crud_name' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
//        $permissions[] = ['name' => 'delete DatabaseBackup club',"title" => 'delete','crud_name' => 'DatabaseBackup (Club)','module' => 'Club','category' => ['tools club'],'table'=> 'club'];
        // end DatabaseBackup club
        //***********************************************************************\\
        //**************************** end club *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start board rent *****************************\\
        //*************************************************************************\\
        // start Panel board rent
//        $permissions[] = ['name' => 'all panel boardRent',"title" => 'show','crud_name' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create panel boardRent',"title" => 'create','crud_name' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update panel boardRent',"title" => 'update','crud_name' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete panel boardRent',"title" => 'delete','crud_name' => 'Panel (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        // end Panel board rent
//        // start package board rent
//        $permissions[] = ['name' => 'all package boardRent',"title" => 'show','crud_name' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create package boardRent',"title" => 'create','crud_name' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update package boardRent',"title" => 'update','crud_name' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete package boardRent',"title" => 'delete','crud_name' => 'Package (boardRent)','module' => 'Board Rent','category' => ['master_file boardRent'],'table'=> 'board rent'];
//        // end package board rent
//        // start Quotations board rent
//        $permissions[] = ['name' => 'all quotation boardRent',"title" => 'show','crud_name' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create quotation boardRent',"title" => 'create','crud_name' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update quotation boardRent',"title" => 'update','crud_name' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete quotation boardRent',"title" => 'delete','crud_name' => 'Quotation (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        // end Quotations  board rent
//        // start Order board rent
//        $permissions[] = ['name' => 'all order boardRent',"title" => 'show','crud_name' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create order boardRent',"title" => 'create','crud_name' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update order boardRent',"title" => 'update','crud_name' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete order boardRent',"title" => 'delete','crud_name' => 'Order (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        // end Order  board rent
//        // start invoice board rent
//        $permissions[] = ['name' => 'all invoice boardRent',"title" => 'show','crud_name' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create invoice boardRent',"title" => 'create','crud_name' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update invoice boardRent',"title" => 'update','crud_name' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete invoice boardRent',"title" => 'delete','crud_name' => 'Invoice (boardRent)','module' => 'Board Rent','category' => ['transaction boardRent'],'table'=> 'board rent'];
//        // end invoice  board rent
//        // start finance boardRent
//        $permissions[] = ['name' => 'all finance boardRent',"title" => 'show','crud_name' => 'Finance Report (boardRent)','module' => 'Board Rent','category' => ['report boardRent'],'table'=> 'board rent'];
//        // end finance boardRent
//        // start customcrud_name boardRent
//        $permissions[] = ['name' => 'all customcrud_name boardRent',"title" => 'show','crud_name' => 'customcrud_name (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update customcrud_name boardRent',"title" => 'update','crud_name' => 'customcrud_name (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete customcrud_name boardRent',"title" => 'delete','crud_name' => 'customcrud_name (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        // end customcrud_name boardRent
//        // start DatabaseBackup boardRent
//        $permissions[] = ['name' => 'all DatabaseBackup boardRent',"title" => 'show','crud_name' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create DatabaseBackup boardRent',"title" => 'create','crud_name' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'download DatabaseBackup boardRent',"title" => 'download','crud_name' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete DatabaseBackup boardRent',"title" => 'delete','crud_name' => 'DatabaseBackup (boardRent)','module' => 'Board Rent','category' => ['tools boardRent'],'table'=> 'board rent'];
//        // end DatabaseBackup boardRent
//        // start unitStatus board rent
//        $permissions[] = ['name' => 'all unitStatus boardRent',"title" => 'show','crud_name' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create unitStatus boardRent',"title" => 'create','crud_name' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update unitStatus boardRent',"title" => 'update','crud_name' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete unitStatus boardRent',"title" => 'delete','crud_name' => 'Unit Status (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        // end unitStatus  board rent
//        // start lookup board rent
//        $permissions[] = ['name' => 'all lookup boardRent',"title" => 'show','crud_name' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'create lookup boardRent',"title" => 'create','crud_name' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'update lookup boardRent',"title" => 'update','crud_name' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
//        $permissions[] = ['name' => 'delete lookup boardRent',"title" => 'delete','crud_name' => 'Lookup (boardRent)','module' => 'Board Rent','category' => ['system boardRent'],'table'=> 'board rent'];
        // end lookup  board rent
        //***********************************************************************\\
        //**************************** end board rent *****************************\\
        //*************************************************************************\\

        //***********************************************************************\\
        //**************************** start receivable payable *****************************\\
        //*************************************************************************\\
        // start installmentStatus
//        $permissions[] = ['name' => 'all status RP',"title" => 'show','crud_name' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create status RP',"title" => 'create','crud_name' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update status RP',"title" => 'update','crud_name' => 'Status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete status RP',"title" => 'delete','crud_name' => 'status (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        // end installmentStatus
//        // start payment type
//        $permissions[] = ['name' => 'all paymentType RP',"title" => 'show','crud_name' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create paymentType RP',"title" => 'create','crud_name' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update paymentType RP',"title" => 'update','crud_name' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete paymentType RP',"title" => 'delete','crud_name' => 'Payment Type (RP)','module' => 'Receivable Payable','category' => ['lookup receivablePayable'],'table'=> 'receivable payable'];
//        // end payment type
//        // start payment Plan
//        $permissions[] = ['name' => 'all paymentPlan RP',"title" => 'show','crud_name' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create paymentPlan RP',"title" => 'create','crud_name' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update paymentPlan RP',"title" => 'update','crud_name' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete paymentPlan RP',"title" => 'delete','crud_name' => 'Payment Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        // end payment Plan
//        // start Document Plan
//        $permissions[] = ['name' => 'all documentPlan RP',"title" => 'show','crud_name' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create documentPlan RP',"title" => 'create','crud_name' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update documentPlan RP',"title" => 'update','crud_name' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete documentPlan RP',"title" => 'delete','crud_name' => 'Document Plan (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        // end Document Plan
//        // start condition
//        $permissions[] = ['name' => 'all condition RP',"title" => 'show','crud_name' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create condition RP',"title" => 'create','crud_name' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update condition RP',"title" => 'update','crud_name' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete condition RP',"title" => 'delete','crud_name' => 'Condition (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        // end condition
//        // start payment plan installment
//        $permissions[] = ['name' => 'all paymentPlanInstallment RP',"title" => 'show','crud_name' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create paymentPlanInstallment RP',"title" => 'create','crud_name' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update paymentPlanInstallment RP',"title" => 'update','crud_name' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete paymentPlanInstallment RP',"title" => 'delete','crud_name' => 'Payment Plan Installment (RP)','module' => 'Receivable Payable','category' => ['master_file receivablePayable'],'table'=> 'receivable payable'];
//        // end payment plan installment
//        // start customcrud_name
//        $permissions[] = ['name' => 'all customcrud_name RP',"title" => 'show','crud_name' => 'customcrud_name (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update customcrud_name RP',"title" => 'update','crud_name' => 'customcrud_name (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete customcrud_name RP',"title" => 'delete','crud_name' => 'customcrud_name (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        // end customcrud_name
//        // start DatabaseBackup
//        $permissions[] = ['name' => 'all DatabaseBackup RP',"title" => 'show','crud_name' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create DatabaseBackup RP',"title" => 'create','crud_name' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'download DatabaseBackup RP',"title" => 'download','crud_name' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete DatabaseBackup RP',"title" => 'delete','crud_name' => 'DatabaseBackup (RP)','module' => 'Receivable Payable','category' => ['tools receivablePayable'],'table'=> 'receivable payable'];
//        // end DatabaseBackup
//        // start Receivable Payable
//        $permissions[] = ['name' => 'all admin RP',"title" => 'show','crud_name' => 'Admin Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable'],'table'=> 'receivable payable'];
//        // end Receivable Payable
//        // start payment Report
//        $permissions[] = ['name' => 'all paymentReport RP',"title" => 'show','crud_name' => 'Payment Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable','finance receivablePayable'],'table'=> 'receivable payable'];
//        // end payment Report
//        // start payment Report
//        $permissions[] = ['name' => 'all voucher RP',"title" => 'show','crud_name' => 'Voucher Report (RP)','module' => 'Receivable Payable','category' => ['report receivablePayable','finance receivablePayable'],'table'=> 'receivable payable'];
//        // end payment Report
//        // start openinig balance
//        $permissions[] = ['name' => 'all openingBalance RP',"title" => 'show','crud_name' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create openingBalance RP',"title" => 'create','crud_name' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update openingBalance RP',"title" => 'update','crud_name' => 'Openinig Balance (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end openinig balance
//        // start reservation
//        $permissions[] = ['name' => 'all reservation RP',"title" => 'show','crud_name' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create reservation RP',"title" => 'create','crud_name' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update reservation RP',"title" => 'update','crud_name' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete reservation RP',"title" => 'delete','crud_name' => 'Reservation (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end reservation
//        // start contract
//        $permissions[] = ['name' => 'all contract RP',"title" => 'show','crud_name' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create contract RP',"title" => 'create','crud_name' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update contract RP',"title" => 'update','crud_name' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete contract RP',"title" => 'delete','crud_name' => 'Contract (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end contract
//        // start receipt Voucher
//        $permissions[] = ['name' => 'all receiptVoucher RP',"title" => 'show','crud_name' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create receiptVoucher RP',"title" => 'create','crud_name' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update receiptVoucher RP',"title" => 'update','crud_name' => 'Receipt Voucher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end receipt Voucher
//        // start credit note
//        $permissions[] = ['name' => 'all creditNote RP',"title" => 'show','crud_name' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create creditNote RP',"title" => 'create','crud_name' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update creditNote RP',"title" => 'update','crud_name' => 'Credit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end credit note
//        // start Debit note
//        $permissions[] = ['name' => 'all debitNote RP',"title" => 'show','crud_name' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create debitNote RP',"title" => 'create','crud_name' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update debitNote RP',"title" => 'update','crud_name' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete debitNote RP',"title" => 'delete','crud_name' => 'Debit Note (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end Debit note
//        // start issue Vourcher
//        $permissions[] = ['name' => 'all issueVourcher RP',"title" => 'show','crud_name' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'create issueVourcher RP',"title" => 'create','crud_name' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'update issueVourcher RP',"title" => 'update','crud_name' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        $permissions[] = ['name' => 'delete issueVourcher RP',"title" => 'delete','crud_name' => 'Issue Vourcher (RP)','module' => 'Receivable Payable','category' => ['transaction receivablePayable'],'table'=> 'receivable payable'];
//        // end issue Vourcher
//        //***********************************************************************\\
//        //**************************** end receivable payable *****************************\\
//        //*************************************************************************\\
//
//
//        //***********************************************************************\\
//        //**************************** start Ticket Manager *****************************\\
//        //*************************************************************************\\
//        // start Task
        $permissions[] = ['name' => 'all Task', "title" => 'show', 'crud_name' => 'Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'create Task', "title" => 'create', 'crud_name' => 'Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'update Task', "title" => 'update', 'crud_name' => 'Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'delete Task', "title" => 'delete', 'crud_name' => 'Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'all Calender', "title" => 'show calender', 'crud_name' => 'Tasks Report', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'all Tasks Report', "title" => 'show task report', 'crud_name' => 'Tasks Report', 'module' => 'Follow Up', 'table' => 'ticket manager'];

        $permissions[] = ['name' => 'all External Task', "title" => 'show', 'crud_name' => 'External Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'create External Task', "title" => 'create', 'crud_name' => 'External Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'update External Task', "title" => 'update', 'crud_name' => 'External Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        $permissions[] = ['name' => 'delete External Task', "title" => 'delete', 'crud_name' => 'External Tasks', 'module' => 'Follow Up', 'table' => 'ticket manager'];
        //    $permissions[] = ['name' => 'all External Calender',"title" => 'show calender','crud_name' => 'External Tasks','module' => 'Follow Up','table'=> 'ticket manager'];
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


        $permissions[] = ['name' => 'all RoomsCategory', "title" => 'show', 'crud_name' => 'RoomsCategories', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms_categories'];
        $permissions[] = ['name' => 'create RoomsCategory', "title" => 'create', 'crud_name' => 'RoomsCategories', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms_categories'];
        $permissions[] = ['name' => 'update RoomsCategory', "title" => 'update', 'crud_name' => 'RoomsCategories', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms_categories'];
        $permissions[] = ['name' => 'delete RoomsCategory', "title" => 'delete', 'crud_name' => 'RoomsCategories', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms_categories'];

        $permissions[] = ['name' => 'all ServiceType', "title" => 'show', 'crud_name' => 'ServicesTypes', 'module' => 'Hospital Management System', 'table' => 'h_m_s_service_types'];
        $permissions[] = ['name' => 'create ServiceType', "title" => 'create', 'crud_name' => 'ServicesTypes', 'module' => 'Hospital Management System', 'table' => 'h_m_s_service_types'];
        $permissions[] = ['name' => 'update ServiceType', "title" => 'update', 'crud_name' => 'ServicesTypes', 'module' => 'Hospital Management System', 'table' => 'h_m_s_service_types'];
        $permissions[] = ['name' => 'delete ServiceType', "title" => 'delete', 'crud_name' => 'ServicesTypes', 'module' => 'Hospital Management System', 'table' => 'h_m_s_service_types'];

        $permissions[] = ['name' => 'all Specialty', "title" => 'show', 'crud_name' => 'Specialties', 'module' => 'Hospital Management System', 'table' => 'h_m_s_specialties'];
        $permissions[] = ['name' => 'create Specialty', "title" => 'create', 'crud_name' => 'Specialties', 'module' => 'Hospital Management System', 'table' => 'h_m_s_specialties'];
        $permissions[] = ['name' => 'update Specialty', "title" => 'update', 'crud_name' => 'Specialties', 'module' => 'Hospital Management System', 'table' => 'h_m_s_specialties'];
        $permissions[] = ['name' => 'delete Specialty', "title" => 'delete', 'crud_name' => 'Specialties', 'module' => 'Hospital Management System', 'table' => 'h_m_s_specialties'];

        $permissions[] = ['name' => 'all Room', "title" => 'show', 'crud_name' => 'Rooms', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms'];
        $permissions[] = ['name' => 'create Room', "title" => 'create', 'crud_name' => 'Rooms', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms'];
        $permissions[] = ['name' => 'update Room', "title" => 'update', 'crud_name' => 'Rooms', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms'];
        $permissions[] = ['name' => 'delete Room', "title" => 'delete', 'crud_name' => 'Rooms', 'module' => 'Hospital Management System', 'table' => 'h_m_s_rooms'];

        $permissions[] = ['name' => 'all Prescription', "title" => 'show', 'crud_name' => 'Prescriptions', 'module' => 'Hospital Management System', 'table' => 'h_m_s_prescriptions'];
        $permissions[] = ['name' => 'create Prescription', "title" => 'create', 'crud_name' => 'Prescriptions', 'module' => 'Hospital Management System', 'table' => 'h_m_s_prescriptions'];
        $permissions[] = ['name' => 'update Prescription', "title" => 'update', 'crud_name' => 'Prescriptions', 'module' => 'Hospital Management System', 'table' => 'h_m_s_prescriptions'];
        $permissions[] = ['name' => 'delete Prescription', "title" => 'delete', 'crud_name' => 'Prescriptions', 'module' => 'Hospital Management System', 'table' => 'h_m_s_prescriptions'];

        $permissions[] = ['name' => 'all Patient', "title" => 'Show All Patients', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patients'];
        $permissions[] = ['name' => 'create Patient', "title" => 'Create New Patient', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patients'];
        $permissions[] = ['name' => 'update Patient', "title" => 'Update Patient', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patients'];
        $permissions[] = ['name' => 'delete Patient', "title" => 'Delete Patient', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patients'];

        $permissions[] = ['name' => 'show Patient Details', "title" => 'Show Patient Details', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patients'];
        $permissions[] = ['name' => 'show Health History', "title" => 'Show Health History', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_health_histories'];
        $permissions[] = ['name' => 'create Health History', "title" => 'Create Health History', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_health_histories'];
        $permissions[] = ['name' => 'delete Health History', "title" => 'Delete Health History', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_health_histories'];
        $permissions[] = ['name' => 'show Medical Files', "title" => 'Show Medical Files', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_medical_files'];
        $permissions[] = ['name' => 'create Medical Files', "title" => 'Create Medical Files', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_medical_files'];
        $permissions[] = ['name' => 'delete Medical Files', "title" => 'Delete Medical Files', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_medical_files'];
        $permissions[] = ['name' => 'show Patient Appointments', "title" => 'Show Patient Appointments', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'create Patient Appointments', "title" => 'Create Patient Appointments', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'show Patient Prescriptions', "title" => 'Show Patient Prescriptions', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'h_m_s_patient_medical_files'];
        $permissions[] = ['name' => 'show Patient Invoices', "title" => 'Show Patient Invoices', 'crud_name' => 'Patients', 'module' => 'Hospital Management System', 'table' => 'general_document_headers'];

        $permissions[] = ['name' => 'all InsuranceCompany', "title" => 'show', 'crud_name' => 'Insurance Companies', 'module' => 'General', 'table' => 'general_insurance_companies'];
        $permissions[] = ['name' => 'create InsuranceCompany', "title" => 'create', 'crud_name' => 'Insurance Companies', 'module' => 'General', 'table' => 'general_insurance_companies'];
        $permissions[] = ['name' => 'update InsuranceCompany', "title" => 'update', 'crud_name' => 'Insurance Companies', 'module' => 'General', 'table' => 'general_insurance_companies'];
        $permissions[] = ['name' => 'delete InsuranceCompany', "title" => 'delete', 'crud_name' => 'Insurance Companies', 'module' => 'General', 'table' => 'general_insurance_companies'];

        $permissions[] = ['name' => 'all WorkNature', "title" => 'show', 'crud_name' => 'Work Nature', 'module' => 'General', 'table' => 'general_work_natures'];
        $permissions[] = ['name' => 'create WorkNature', "title" => 'create', 'crud_name' => 'Work Nature', 'module' => 'General', 'table' => 'general_work_natures'];
        $permissions[] = ['name' => 'update WorkNature', "title" => 'update', 'crud_name' => 'Work Nature', 'module' => 'General', 'table' => 'general_work_natures'];
        $permissions[] = ['name' => 'delete WorkNature', "title" => 'delete', 'crud_name' => 'Work Nature', 'module' => 'General', 'table' => 'general_work_natures'];

        $permissions[] = ['name' => 'all Drug', "title" => 'show', 'crud_name' => 'Drugs', 'module' => 'Hospital Management System', 'table' => 'h_m_s_drugs'];
        $permissions[] = ['name' => 'create Drug', "title" => 'create', 'crud_name' => 'Drugs', 'module' => 'Hospital Management System', 'table' => 'h_m_s_drugs'];
        $permissions[] = ['name' => 'update Drug', "title" => 'update', 'crud_name' => 'Drugs', 'module' => 'Hospital Management System', 'table' => 'h_m_s_drugs'];
        $permissions[] = ['name' => 'delete Drug', "title" => 'delete', 'crud_name' => 'Drugs', 'module' => 'Hospital Management System', 'table' => 'h_m_s_drugs'];

        $permissions[] = ['name' => 'all Doctor', "title" => 'show', 'crud_name' => 'Doctors', 'module' => 'Hospital Management System', 'table' => 'h_m_s_doctors'];
        $permissions[] = ['name' => 'create Doctor', "title" => 'create', 'crud_name' => 'Doctors', 'module' => 'Hospital Management System', 'table' => 'h_m_s_doctors'];
        $permissions[] = ['name' => 'update Doctor', "title" => 'update', 'crud_name' => 'Doctors', 'module' => 'Hospital Management System', 'table' => 'h_m_s_doctors'];
        $permissions[] = ['name' => 'delete Doctor', "title" => 'delete', 'crud_name' => 'Doctors', 'module' => 'Hospital Management System', 'table' => 'h_m_s_doctors'];

        $permissions[] = ['name' => 'all DiagnosisTest', "title" => 'show', 'crud_name' => 'DiagnosisTests', 'module' => 'Hospital Management System', 'table' => 'h_m_s_diagnosis_tests'];
        $permissions[] = ['name' => 'create DiagnosisTest', "title" => 'create', 'crud_name' => 'DiagnosisTests', 'module' => 'Hospital Management System', 'table' => 'h_m_s_diagnosis_tests'];
        $permissions[] = ['name' => 'update DiagnosisTest', "title" => 'update', 'crud_name' => 'DiagnosisTests', 'module' => 'Hospital Management System', 'table' => 'h_m_s_diagnosis_tests'];
        $permissions[] = ['name' => 'delete DiagnosisTest', "title" => 'delete', 'crud_name' => 'DiagnosisTests', 'module' => 'Hospital Management System', 'table' => 'h_m_s_diagnosis_tests'];

        $permissions[] = ['name' => 'all HMS Invoice', "title" => 'show', 'crud_name' => 'HMS Invoices', 'module' => 'Hospital Management System', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create HMS Invoice', "title" => 'create', 'crud_name' => 'HMS Invoices', 'module' => 'Hospital Management System', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update HMS Invoice', "title" => 'update', 'crud_name' => 'HMS Invoices', 'module' => 'Hospital Management System', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete HMS Invoice', "title" => 'delete', 'crud_name' => 'HMS Invoices', 'module' => 'Hospital Management System', 'table' => 'general_document_headers'];

        $permissions[] = ['name' => 'all Appointment', "title" => 'show', 'crud_name' => 'All Appointments', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'create Appointment', "title" => 'create', 'crud_name' => 'All Appointments', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'update Appointment', "title" => 'update', 'crud_name' => 'All Appointments', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'delete Appointment', "title" => 'delete', 'crud_name' => 'All Appointments', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];

        $permissions[] = ['name' => 'all Rooms Reservation', "title" => 'show', 'crud_name' => 'Rooms Reservation', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'create Rooms Reservation', "title" => 'create', 'crud_name' => 'Rooms Reservation', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'update Rooms Reservation', "title" => 'update', 'crud_name' => 'Rooms Reservation', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'delete Rooms Reservation', "title" => 'delete', 'crud_name' => 'Rooms Reservation', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];

        $permissions[] = ['name' => 'Upcoming Appointment', "title" => 'show', 'crud_name' => 'Upcoming Appointments', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'Waiting Room', "title" => 'show', 'crud_name' => 'Waiting Rooms', 'module' => 'Hospital Management System', 'table' => 'h_m_s_appointments'];
        $permissions[] = ['name' => 'show HMS Settings', "title" => 'Show HMS Settings', 'crud_name' => 'HMSSettings', 'module' => 'Hospital Management System', 'table' => 'h_m_s_settings'];
        $permissions[] = ['name' => 'update HMS Settings', "title" => 'Update HMS Settings', 'crud_name' => 'HMSSettings', 'module' => 'Hospital Management System', 'table' => 'h_m_s_settings'];


        // ============================= Bord Rent ==========================
        // Start Request Quotation BoardRent
        $permissions[] = ['name' => 'all Request Quotation BoardRent', "title" => 'show', 'crud_name' => 'Request Quotation BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Request Quotation BoardRent', "title" => 'create', 'crud_name' => 'Request Quotation BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Request Quotation BoardRent', "title" => 'update', 'crud_name' => 'Request Quotation BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Request Quotation BoardRent', "title" => 'delete', 'crud_name' => 'Request Quotation BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Request Quotation BoardRent

        // Start panel
        $permissions[] = ['name' => 'all panel boardRent', "title" => 'show', 'crud_name' => 'Board rent Panel', 'module' => 'Board Rent', 'table' => 'boards_rent_panels'];
        $permissions[] = ['name' => 'create panel boardRent', "title" => 'create', 'crud_name' => 'Board rent Panel', 'module' => 'Board Rent', 'table' => 'boards_rent_panels'];
        $permissions[] = ['name' => 'update panel boardRent', "title" => 'update', 'crud_name' => 'Board rent Panel', 'module' => 'Board Rent', 'table' => 'boards_rent_panels'];
        $permissions[] = ['name' => 'delete panel boardRent', "title" => 'delete', 'crud_name' => 'Board rent Panel', 'module' => 'Board Rent', 'table' => 'boards_rent_panels'];
        // End panel

        // Start package
        $permissions[] = ['name' => 'all package boardRent', "title" => 'show', 'crud_name' => 'Board rent Package', 'module' => 'Board Rent', 'table' => 'boards_rent_packages'];
        $permissions[] = ['name' => 'create package boardRent', "title" => 'create', 'crud_name' => 'Board rent Package', 'module' => 'Board Rent', 'table' => 'boards_rent_packages'];
        $permissions[] = ['name' => 'update package boardRent', "title" => 'update', 'crud_name' => 'Board rent Package', 'module' => 'Board Rent', 'table' => 'boards_rent_packages'];
        $permissions[] = ['name' => 'delete package boardRent', "title" => 'delete', 'crud_name' => 'Board rent Package', 'module' => 'Board Rent', 'table' => 'boards_rent_packages'];
        // End package

        // Start Quotation BoardRent
        $permissions[] = ['name' => 'all Quotation BoardRent Basic', "title" => 'show', 'crud_name' => 'Quotation BoardRent Basic', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Quotation BoardRent Basic', "title" => 'create', 'crud_name' => 'Quotation BoardRent Basic', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Quotation BoardRent Basic', "title" => 'update', 'crud_name' => 'Quotation BoardRent Basic', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Quotation BoardRent Basic', "title" => 'delete', 'crud_name' => 'Quotation BoardRent Basic', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Quotation BoardRent

        // Start Publication Contract
        $permissions[] = ['name' => 'all Publication Contract', "title" => 'show', 'crud_name' => 'Publication Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Publication Contract', "title" => 'create', 'crud_name' => 'Publication Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Publication Contract', "title" => 'update', 'crud_name' => 'Publication Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Publication Contract', "title" => 'delete', 'crud_name' => 'Publication Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Publication Contract

        // Start Job Order
        $permissions[] = ['name' => 'all Job Order BoardRent', "title" => 'show', 'crud_name' => 'Job Order BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Job Order BoardRent', "title" => 'create', 'crud_name' => 'Job Order BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Job Order BoardRent', "title" => 'update', 'crud_name' => 'Job Order BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Job Order BoardRent', "title" => 'delete', 'crud_name' => 'Job Order BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Job Order

        // Start Yearly Contract Invoice BoardRent
        $permissions[] = ['name' => 'all Yearly Contract Invoice BoardRent', "title" => 'show', 'crud_name' => 'Yearly Contract Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Yearly Contract Invoice BoardRent', "title" => 'create', 'crud_name' => 'Yearly Contract Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Yearly Contract Invoice BoardRent', "title" => 'update', 'crud_name' => 'Yearly Contract Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Yearly Contract Invoice BoardRent', "title" => 'delete', 'crud_name' => 'Yearly Contract Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Yearly Contract Invoice BoardRent

        // Start Invoice BoardRent
        $permissions[] = ['name' => 'all Invoice BoardRent', "title" => 'show', 'crud_name' => 'Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Invoice BoardRent', "title" => 'create', 'crud_name' => 'Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Invoice BoardRent', "title" => 'update', 'crud_name' => 'Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Invoice BoardRent', "title" => 'delete', 'crud_name' => 'Invoice BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Invoice BoardRent

        // Start Yearly Publishing Contract
        $permissions[] = ['name' => 'all Yearly Publishing Contract', "title" => 'show', 'crud_name' => 'Yearly Publishing Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'create Yearly Publishing Contract', "title" => 'create', 'crud_name' => 'Yearly Publishing Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'update Yearly Publishing Contract', "title" => 'update', 'crud_name' => 'Yearly Publishing Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        $permissions[] = ['name' => 'delete Yearly Publishing Contract', "title" => 'delete', 'crud_name' => 'Yearly Publishing Contract', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Yearly Publishing Contract

        // Start board rent unit status
        $permissions[] = ['name' => 'all unitStatus boardRent', "title" => 'show', 'crud_name' => 'board rent unit status', 'module' => 'Board Rent', 'table' => 'boards_rent_unit_statuses'];
        $permissions[] = ['name' => 'create unitStatus boardRent', "title" => 'create', 'crud_name' => 'board rent unit status', 'module' => 'Board Rent', 'table' => 'boards_rent_unit_statuses'];
        $permissions[] = ['name' => 'update unitStatus boardRent', "title" => 'update', 'crud_name' => 'board rent unit status', 'module' => 'Board Rent', 'table' => 'boards_rent_unit_statuses'];
        $permissions[] = ['name' => 'delete unitStatus boardRent', "title" => 'delete', 'crud_name' => 'board rent unit status', 'module' => 'Board Rent', 'table' => 'boards_rent_unit_statuses'];
        // End board rent unit status

        // Start Finance Report BoardRent
        $permissions[] = ['name' => 'all Finance Report BoardRent', "title" => 'show', 'crud_name' => 'Finance Report BoardRent', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Finance Report BoardRent

        // Start Annual Finance Report
        $permissions[] = ['name' => 'all Annual Finance Report', "title" => 'show', 'crud_name' => 'Annual Finance Report', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Annual Finance Report

        // Start Customer Statement Of Account
        $permissions[] = ['name' => 'all Customer Statement Of Account', "title" => 'show', 'crud_name' => 'Customer Statement Of Account', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Customer Statement Of Account

        // Start Panel Usage Rate
        $permissions[] = ['name' => 'all Panel Usage Rate', "title" => 'show', 'crud_name' => 'Panel Usage Rate', 'module' => 'Board Rent', 'table' => 'general_document_headers'];
        // End Panel Usage Rate


        foreach ($permissions as $permission) {
            Permission::create([
                "name" => $permission["name"],
                "title" => $permission["title"],
                "crud_name" => $permission["crud_name"],
                "module" => $permission["module"],
                "table" => $permission["table"]
            ]);
        }
    }
}
