<?php

namespace App\Models;

use App\Models\Depertment;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Task;
use Modules\CarRent\Entities\CarDriver;
use Modules\HMS\Entities\HMSDoctor;
use Modules\HR\Entities\EmergencyBalance;
use Modules\HR\Entities\EmployeeFingerprintDetail;
use Modules\HR\Entities\EmployeeLocationDate;
use Modules\HR\Entities\EmployeePublicHolidayDetail;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\EndService;
use Modules\HR\Entities\Fingerprint;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Entities\OnlineRequest;
use Modules\HR\Entities\Payroll;
use Modules\HR\Entities\Request;
use Modules\HR\Entities\RequestType;
use Modules\HR\Entities\VacationBalance;
use Modules\RealEstate\Entities\RlstContractHeader;

class Employee extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_employees';
    protected $casts = ["manage_others" => "integer"];
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'manager_id',
                'name',
                'name_e',
                'is_salesman',
                'customer_handel',
                'department_id',
                'code_country',
                'salesman_type_id',
                'for_all_customer',
                'mobile',
                'email',
                'whatsapp',
                'sms',
                'att_code',
                // 'company_id',
                'job_id',
                'manage_others',
                'branch_id',
                'mobile_id',
            )
            ->with(
                'jobTitle:id,name,name_e',
                'branch:id,name,name_e',
                'manager:id,name,name_e',
                'salesmanType:id,name,name_e',
                'department:id,name,name_e',
                'plans:id,name,name_e',
                'managersData'
            );
    }

    public function scopeEmployeesTimeTablesDetailsData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'att_code',
                'department_id',
                'branch_id',
            )
            ->with(
                'branch:id,name,name_e',
                'department:id,name,name_e',
            );
    }

    public function scopeEmployeesAttendanceDepartureReportData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'att_code',
                'department_id',
                'branch_id',
            )
            ->with(
                'fingerprints:id,att_code,vdate,att_type,machine_id',
            );
    }

    public function users()
    {
        return $this->hasMany(User::class, 'employee_id');
    }


    public function messageReceiverContacts()
    {
        return $this->hasMany(MessageReceiverContact::class, 'manager_employee_id');
    }

    public function GeneralMessageRequests()
    {
        return $this->hasMany(GeneralMessageRequest::class, 'employee_id');
    }

    public function DocumentApprovalDetails()
    {
        return $this->hasMany(DocumentApprovalDetail::class, 'employee_id');
    }
    public function BankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'emp_id');
    }
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function doctor()
    {
        return $this->hasOne(HMSDoctor::class,'employee_id');
    }

    public function internalSalesman()
    {
        return $this->hasOne(InternalSalesman::class,'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }



//    public function tasksOwner()
//    {
//        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'owner_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
//    }
//
//    public function tasksSupervisor()
//    {
//        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'supervisor_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
//    }

    public function tasksnNotification()
    {
        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'notification_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    public function salesmanType()
    {
        return $this->belongsTo(SalesmenType::class, 'salesman_type_id');
    }

    public function plans()
    {

        return $this->belongsToMany(SalesmenPlan::class, 'general_salesmen_plans_details_assignment', 'employee_id', 'plan_id');
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'general_documents_approve_personal', 'employee_id', 'document_id', 'id', 'id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'employee_id');

    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function children()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function request_types()
    {

        return $this->belongsToMany(RequestType::class, 'hr_request_types_employees', 'employee_id', 'request_type_id');
    }

    public function managers()
    {
        return $this->belongsToMany(Employee::class, 'general_employee_managers', 'employee_id', 'manager_id');
    }

    public function EmployeesTimeTablesDetails()
    {
        return $this->hasMany(EmployeesTimeTablesDetail::class, 'employee_id');
    }

    public function fingerprints()
    {
        return $this->hasMany(Fingerprint::class, 'att_code');
    }

    public function managersData()
    {
        return $this->hasMany(GeneralEmployeeManager::class, 'employee_id')
            ->select('employee_id', 'manager_id')
            ->leftJoin('general_employees', 'general_employee_managers.manager_id', '=', 'general_employees.id')
            ->select('general_employee_managers.employee_id', 'general_employee_managers.manager_id', 'general_employees.name AS manager_name', 'general_employees.name_e AS manager_name_e');
    }


    public function customers()
    {
        return $this->hasMany(GeneralCustomer::class, 'employee_id');
    }

    public function employeeManagers()
    {
        return $this->hasMany(GeneralEmployeeManager::class, 'employee_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'employee_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }

    public function carDrivers()
    {
        return $this->hasMany(CarDriver::class, 'employee_id');
    }
    public function emergencyBalances()
    {
        return $this->hasMany(EmergencyBalance::class, 'employee_id');
    }

    public function employeeFingerprintDetails()
    {
        return $this->hasMany(EmployeeFingerprintDetail::class, 'employee_id');
    }
    public function employeeLocationDates()
    {
        return $this->hasMany(EmployeeLocationDate::class, 'employee_id');
    }
    public function employeePublicHolidayDetails()
    {
        return $this->hasMany(EmployeePublicHolidayDetail::class, 'employee_id');
    }
    public function endServices()
    {
        return $this->hasMany(EndService::class, 'employee_id');
    }

    public function onlineRequests()
    {
        return $this->hasMany(OnlineRequest::class, 'employee_id');
    }
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }
    public function requests()
    {
        return $this->hasMany(Request::class, 'employee_id');
    }
    public function vacationBalances()
    {
        return $this->hasMany(VacationBalance::class, 'employee_id');
    }

    public function contractHeaders()
    {
        return $this->hasMany(RlstContractHeader::class, 'employee_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->contractHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractHeaders',
                'count' => $this->contractHeaders()->count(),
                'ids' => $this->contractHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->vacationBalances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'vacationBalances',
                'count' => $this->vacationBalances()->count(),
                'ids' => $this->vacationBalances()->pluck('date')->toArray(),
            ];
        }
        if ($this->requests()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'requests',
                'count' => $this->requests()->count(),
                'ids' => $this->requests()->pluck('id')->toArray(),
            ];
        }
        if ($this->payrolls()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payrolls',
                'count' => $this->payrolls()->count(),
                'ids' => $this->payrolls()->pluck('id')->toArray(),
            ];
        }
        if ($this->onlineRequests()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'onlineRequests',
                'count' => $this->onlineRequests()->count(),
                'ids' => $this->onlineRequests()->pluck('id')->toArray(),
            ];
        }
        if ($this->endServices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'endServices',
                'count' => $this->endServices()->count(),
                'ids' => $this->endServices()->pluck('date')->toArray(),
            ];
        }
        if ($this->employeePublicHolidayDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeePublicHolidayDetails',
                'count' => $this->employeePublicHolidayDetails()->count(),
                'ids' => $this->employeePublicHolidayDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->employeeLocationDates()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeeLocationDates',
                'count' => $this->employeeLocationDates()->count(),
                'ids' => $this->employeeLocationDates()->pluck('id')->toArray(),
            ];
        }
        if ($this->employeeFingerprintDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeeFingerprintDetails',
                'count' => $this->employeeFingerprintDetails()->count(),
                'ids' => $this->employeeFingerprintDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->emergencyBalances()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'emergencyBalances',
                'count' => $this->emergencyBalances()->count(),
                'ids' => $this->emergencyBalances()->pluck('date')->toArray(),
            ];
        }
        if ($this->carDrivers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'carDrivers',
                'count' => $this->carDrivers()->count(),
                'ids' => $this->carDrivers()->pluck('driving_license')->toArray(),
            ];
        }
        if ($this->user()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'user',
                'count' => $this->user()->count(),
                'ids' => $this->user()->pluck('name')->toArray(),
            ];
        }
        if ($this->doctor()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'doctor',
                'count' => $this->doctor()->count(),
                'ids' => $this->doctor()->pluck('name')->toArray(),
            ];
        }
        if ($this->suppliers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'suppliers',
                'count' => $this->suppliers()->count(),
                'ids' => $this->suppliers()->pluck('name')->toArray(),
            ];
        }
        if ($this->internalSalesman()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'internalSalesman',
                'count' => $this->internalSalesman()->count(),
                'ids' => $this->internalSalesman()->pluck('id')->toArray(),
            ];
        }
        if ($this->documentHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeader',
                'count' => $this->documentHeader()->count(),
                'ids' => $this->documentHeader()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->users()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'users',
                'count' => $this->users()->count(),
                'ids' => $this->users()->pluck('name')->toArray(),
            ];
        }
        if ($this->EmployeesTimeTablesDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'EmployeesTimeTablesDetails',
                'count' => $this->EmployeesTimeTablesDetails()->count(),
                'ids' => $this->EmployeesTimeTablesDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->fingerprints()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'fingerprints',
                'count' => $this->fingerprints()->count(),
                'ids' => $this->fingerprints()->pluck('att_code')->toArray(),
            ];
        }
        if ($this->BankAccounts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Bank Accounts',
                'count' => $this->BankAccounts()->count(),
                'ids' => $this->BankAccounts()->pluck('account_number')->toArray(),
            ];
        }
        if ($this->DocumentApprovalDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Document Approval Details',
                'count' => $this->DocumentApprovalDetails()->count(),
                'ids' => $this->DocumentApprovalDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->customers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customers',
                'count' => $this->customers()->count(),
                'ids' => $this->customers()->pluck('name')->toArray(),
            ];
        }
        if ($this->employeeManagers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeeManagers',
                'count' => $this->employeeManagers()->count(),
                'ids' => $this->employeeManagers()->pluck('starting_from')->toArray(),
            ];
        }
        if ($this->messageReceiverContacts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'message-Receiver-Contacts',
                'count' => $this->messageReceiverContacts()->count(),
                'ids' => $this->messageReceiverContacts()->pluck('id')->toArray(),
            ];
        }
        if ($this->tasks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tasks',
                'count' => $this->tasks()->count(),
                'ids' => $this->tasks()->pluck('contact_person')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

}
