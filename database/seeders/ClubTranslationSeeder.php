<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Payment plan installment
        Translation::insert([
            [
                "key" => "payment_plan_installment_create_form",
                "default_en" => "Add new payment plan installment",
                "default_ar" => "إضافة خطة دفع جديدة بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "payment_plan_installment_edit_form",
                "default_en" => "Edit payment plan installment form",
                "default_ar" => "نموذج تعديل التقسيط لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan",
                "default_en" => "installment payment plan name",
                "default_ar" => "اسم خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_detail",
                "default_en" => "Installment payment plan detail",
                "default_ar" => "تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_v_date",
                "default_en" => "payment plan v-date",
                "default_ar" => "التاريخ الخامس لتفاصيل خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "installment_payment_plan_due_date",
                "default_en" => "payment plan due date",
                "default_ar" => "تاريخ استحقاق خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_total_amount",
                "default_en" => "total amount",
                "default_ar" => "المبلغ الاجمالي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_paid_amount",
                "default_en" => "paid amount",
                "default_ar" => "المبلغ المدفوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status",
                "default_en" => "Installment status name",
                "default_ar" => "اسم حالة التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "doc_type",
                "default_en" => "Document type name",
                "default_ar" => "اسم نوع الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "module",
                "default_en" => "Module name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen",
                "default_en" => "Screen name",
                "default_ar" => "اسم الشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "ref_id",
                "default_en" => "Ref ID",
                "default_ar" => "رقم الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "rp_code",
                "default_en" => "rp code",
                "default_ar" => "ترميز rp",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Status
        Translation::insert([
            [
                "key" => "installment_status_create_form",
                "default_en" => "Add new Installment Status",
                "default_ar" => "اضافة حاله تقسيط جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_edit_form",
                "default_en" => "Edit  Installment Status",
                "default_ar" => "نموذج تعديل حاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_name",
                "default_en" => "Installment Status name (arabic)",
                "default_ar" => "اسم حاله التقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_name_e",
                "default_en" => "installment_status name (english)",
                "default_ar" => "اسم حاله التقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_default",
                "default_en" => "Installment Status Default",
                "default_ar" => "الحالة الافتراضية لحاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Payment Type
        Translation::insert([
            [
                "key" => "installment_payment_type_create_form",
                "default_en" => "Add new Installment Payment Type",
                "default_ar" => "اضافة نوع دفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_edit_form",
                "default_en" => "Edit  Installment Payment Type",
                "default_ar" => "نموذج تعديل نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_name",
                "default_en" => "Installment Payment Type name (arabic)",
                "default_ar" => "اسم نوع دفع بالتقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_name_e",
                "default_en" => "Installment Payment Type name (english)",
                "default_ar" => "اسم نوع دفع بالتقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_auto_freq",
                "default_en" => "Installment Status Auto Freq",
                "default_ar" => "حالة تقسيط التكرار التلقائي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_is_partially",
                "default_en" => "Installment Payment Partially",
                "default_ar" => "تقسيط السداد جزئيا",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed",
                "default_en" => "Is passed",
                "default_ar" => "تم تمريره",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed_all",
                "default_en" => "Is passed all",
                "default_ar" => "تم تمريره بالكامل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "freq_period",
                "default_en" => "Freq period",
                "default_ar" => "فترة التكرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installmentPaymentType_per",
                "default_en" => "Percent",
                "default_ar" => "النسبة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installmentPaymentType_freq",
                "default_en" => "Frequency",
                "default_ar" => "التكرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_per",
                "default_en" => "Interest percent",
                "default_ar" => "نسبة الفائدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed_contract_plan",
                "default_en" => "Is passed contract plan",
                "default_ar" => "هل خطة العقد ناجحة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "is_conditional",
                "default_en" => "Is conditional",
                "default_ar" => "شرطي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "condition",
                "default_en" => "Condition name",
                "default_ar" => "اسم الشرط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ]
        ]);
        //Main Contact Groups
        Translation::insert([
            [
                "key" => "MainContactGroupsCreate",
                "default_en" => "Add new Main Contact Group",
                "default_ar" => "اضافة مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "MainContactGroupsEdit",
                "default_en" => "Edit Main Contact Group",
                "default_ar" => "نموذج تعديل مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "Group_name",
                "default_en" => "name Group (arabic)",
                "default_ar" => "اسم المجموعة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "Group_name_e",
                "default_en" => "name Group (english)",
                "default_ar" => "اسم المجموعة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Payment Plan Detail
        Translation::insert([
            [
                "key" => "InstallmentPaymentPlanDetailCreate",
                "default_en" => "Add new Installment Payment Plan Detail",
                "default_ar" => "اضافة تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "InstallmentPaymentPlanDetailEdit",
                "default_en" => "Edit Installment Payment Plan Detail",
                "default_ar" => "نموذج تعديل تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_id",
                "default_en" => "installment payment plan",
                "default_ar" => "خطه الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_id",
                "default_en" => "installment payment type",
                "default_ar" => "نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "ln_no",
                "default_en" => "Line No",
                "default_ar" => "رقم الخط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_fixed",
                "default_en" => "is fixed day",
                "default_ar" => "تم إصلاحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_per",
                "default_en" => "payment type per",
                "default_ar" => "لكل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_freq",
                "default_en" => "payment type freq",
                "default_ar" => "عدد الاقساط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_per",
                "default_en" => "interest per",
                "default_ar" => "نسبة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_value",
                "default_en" => "interest value",
                "default_ar" => "قيمة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment payment plan
        Translation::insert([
            [
                "key" => "installment_payment_plan_create",
                "default_en" => "Add new install payment plan",
                "default_ar" => "اضف خطة دفع جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_edit_form",
                "default_en" => "Edit install payment plan",
                "default_ar" => "تعديل خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_name_ar",
                "default_en" => "Installment payment name (arabic)",
                "default_ar" => "اسم خطة الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_name_en",
                "default_en" => "Install payment plan name (english)",
                "default_ar" => "اسم خطة الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_default",
                "default_en" => "Is default",
                "default_ar" => "افتراضي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_active",
                "default_en" => "Is active",
                "default_ar" => "فعال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_description_ar",
                "default_en" => "Installment payment description (arabic)",
                "default_ar" => "وصف خطة الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_description_en",
                "default_en" => "Install payment plan description (english)",
                "default_ar" => "وصف خطة الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_start_date",
                "default_en" => "Install payment plan start date",
                "default_ar" => "تاريخ بداية خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type",
                "default_en" => "Install payment type name",
                "default_ar" => "اسم نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //installment document plan
        Translation::insert([
            [
                "key" => "installment_document_plan_create_form",
                "default_en" => "Add document plan",
                "default_ar" => "اضف خطة وثيقة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_document_plan_edit_form",
                "default_en" => "Edit installment document plan",
                "default_ar" => "تعديل خطة وثيقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_plan",
                "default_en" => "Installment plan",
                "default_ar" => "خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //screen sub contact group
        Translation::insert([
            [
                "key" => "screen_subcontact_group_create_form",
                "default_en" => "Add new screen subcontact group",
                "default_ar" => "اضف جروب اتصال فرعي للشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen_subcontact_group_edit_form",
                "default_en" => "Edit screen subcontact group",
                "default_ar" => "تعديل جروب الاتصال الفرعي للشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "subcontact_group",
                "default_en" => "Subcontact group name",
                "default_ar" => "اسم جروب التواصل الفرعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen",
                "default_en" => "Screen name",
                "default_ar" => "اسم الشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Status
        Translation::insert([
            [
                "key" => "installment_condition_create_form",
                "default_en" => "Add new Installment Condition",
                "default_ar" => "اضافة شرط للتقسيط جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_edit_form",
                "default_en" => "Edit  Installment Condition",
                "default_ar" => "نموذج تعديل شرط التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_name",
                "default_en" => "Installment Condition name (arabic)",
                "default_ar" => "اسم شرط تقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_name_e",
                "default_en" => "Installment Condition name (english)",
                "default_ar" => "اسم شرط تقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_default",
                "default_en" => "Installment Condition Default",
                "default_ar" => "الحالة الافتراضية لشرط تقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        // installment opening balance
        Translation::insert([
            [
                "key" => "installment_opening_balance_create_form",
                "default_en" => "Add customer opening balance",
                "default_ar" => "اضافة الرصيد الافتتاحى للعملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_opening_balance_edit_form",
                "default_en" => "Edit customer opening balance",
                "default_ar" => "نموذج تعديل الرصيد الافتتاحى للعملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_date",
                "default_en" => "date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_currency",
                "default_en" => "Currency",
                "default_ar" => "العملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_rate",
                "default_en" => "Rate",
                "default_ar" => "المعدل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_debit",
                "default_en" => "Debit Foreign",
                "default_ar" => "مدين اجنبية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_credit",
                "default_en" => "Credit Foreign",
                "default_ar" => "دائن اجنبية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_local_debit",
                "default_en" => "Local Debit",
                "default_ar" => "مدين محلية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_local_credit",
                "default_en" => "Local Credit",
                "default_ar" => "دائن محلية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_count_opening_balance",
                "default_en" => "Count Opening Balance",
                "default_ar" => "عدد الارصدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_total_local_debit",
                "default_en" => "Total Local Debit",
                "default_ar" => "اجمالى مدين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_total_local_credit",
                "default_en" => "Total Local Credit",
                "default_ar" => "اجمالى دائن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_net",
                "default_en" => "Net",
                "default_ar" => "الرصيد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
        ]);

        //Customers
        Translation::insert([
            [
                "key" => "customer_create_form",
                "default_en" => "Add new customer",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "customer_edit_form",
                "default_en" => "Edit customer form",
                "default_ar" => "نموذج تعديل الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم الزبون (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم الزبون (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد الزبون الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات الزبون الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
        ]);
        //Invoice
        Translation::insert([
            [
                "key" => "invoice_create_form",
                "default_en" => "Add new invoice",
                "default_ar" => "اضف فاتورة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "invoice_edit_form",
                "default_en" => "Edit invoice form",
                "default_ar" => "تعديل الفاتورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "serial",
                "default_en" => "Serial",
                "default_ar" => "التسلسل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
        ]);

        Translation::insert([
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account",
                "default_en" => "Bank account name",
                "default_ar" => "اسم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_name_ar",
                "default_en" => "Branch name (arabic)",
                "default_ar" => "اسم الفرع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_name_en",
                "default_en" => "Branch name (english)",
                "default_ar" => "اسم الفرع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_status",
                "default_en" => "Branch status",
                "default_ar" => "حالة الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم الزبون (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم الزبون (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد الزبون الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات الزبون الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "general_customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_name_ar",
                "default_en" => "Bank name (arabic)",
                "default_ar" => "اسم البنك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_name_en",
                "default_en" => "Bank name (english)",
                "default_ar" => "اسم البنك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country",
                "default_en" => "Coutnry name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_swiftcode",
                "default_en" => "Bank swiftcode",
                "default_ar" => "سويفت كود البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank",
                "default_en" => "Bank name",
                "default_ar" => "اسم البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account_number",
                "default_en" => "Bank account number",
                "default_ar" => "رقم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account_phone",
                "default_en" => "Bank account phone",
                "default_ar" => "هاتف الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account_address",
                "default_en" => "Bank account address",
                "default_ar" => "عنوان الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account_email",
                "default_en" => "Bank account email",
                "default_ar" => "البريد الالكتروني للحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "bank_account_rpcode",
                "default_en" => "Bank account rpcode",
                "default_ar" => "ار بي كود الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "sale_man_type_name_ar",
                "default_en" => "Sale man type name (arabic)",
                "default_ar" => "اسم نوع رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "sale_man_type_name_en",
                "default_en" => "Sale man type name (english)",
                "default_ar" => "اسم نوع رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_employee",
                "default_en" => "Is employee",
                "default_ar" => "هل موظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "sale_man_name_ar",
                "default_en" => "Sale man name (arabic)",
                "default_ar" => "اسم رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "sale_man_name_en",
                "default_en" => "Sale man name (english)",
                "default_ar" => "اسم رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "sale_man_type",
                "default_en" => "Sale man type",
                "default_ar" => "نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_name_ar",
                "default_en" => "Avenue name (arabic)",
                "default_ar" => "اسم المنطقة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_name_en",
                "default_en" => "Avenue name (english)",
                "default_ar" => "اسم المنطقة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_status",
                "default_en" => "Avenue status",
                "default_ar" => "حالة المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);

        //country
        Translation::insert([
            [
                "key" => "country_create_form",
                "default_en" => "Add new country",
                "default_ar" => "اضف دولة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_edit_form",
                "default_en" => "Edit country form",
                "default_ar" => "نموذج تعديل الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_name_ar",
                "default_en" => "Country name (arabic)",
                "default_ar" => "اسم الدولة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_name_en",
                "default_en" => "Country name (english)",
                "default_ar" => "اسم الدولة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_long_name_ar",
                "default_en" => "Country long name (arabic)",
                "default_ar" => "اسم الدولة طويل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_long_name_en",
                "default_en" => "Country long name (english)",
                "default_ar" => "اسم الدولة طويل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_phone_key",
                "default_en" => "Country phone key",
                "default_ar" => "كود هاتف الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_short_code",
                "default_en" => "Country web code",
                "default_ar" => "الكود الويب المختصر للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_default",
                "default_en" => "Country default",
                "default_ar" => "الحالة الافتراضية للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_status",
                "default_en" => "Country status",
                "default_ar" => "حالة الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country_national_id",
                "default_en" => "Country national id",
                "default_ar" => "الرقم القومي للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //governorate
        Translation::insert([
            [
                "key" => "governorate_create_form",
                "default_en" => "Add new governorate",
                "default_ar" => "اضف محافظة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_edit_form",
                "default_en" => "Edit governorate form",
                "default_ar" => "نموذج تعديل المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_name_ar",
                "default_en" => "Governorate name (arabic)",
                "default_ar" => "اسم المحافظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_name_en",
                "default_en" => "Governorate name (english)",
                "default_ar" => "اسم المحافظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_phone_key",
                "default_en" => "Governorate phone key",
                "default_ar" => "كود هاتف المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_default",
                "default_en" => "Governorate default",
                "default_ar" => "الحالة الافتراضية للمحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate_status",
                "default_en" => "Governorate status",
                "default_ar" => "حالة المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //city
        Translation::insert([
            [
                "key" => "city_create_form",
                "default_en" => "Add new city",
                "default_ar" => "اضف مدينة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city_edit_form",
                "default_en" => "Edit city form",
                "default_ar" => "نموذج تعديل المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city_name_ar",
                "default_en" => "City name (arabic)",
                "default_ar" => "اسم المدينة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city_name_en",
                "default_en" => "City name (english)",
                "default_ar" => "اسم المدينة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city_status",
                "default_en" => "City status",
                "default_ar" => "حالة المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //avenue
        Translation::insert([
            [
                "key" => "avenue_create_form",
                "default_en" => "Add new avenue",
                "default_ar" => "اضف منطقة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_edit_form",
                "default_en" => "Edit avenue form",
                "default_ar" => "نموذج تعديل المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_name_ar",
                "default_en" => "Avenue name (arabic)",
                "default_ar" => "اسم المنطقة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_name_en",
                "default_en" => "Avenue name (english)",
                "default_ar" => "اسم المنطقة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "avenue_status",
                "default_en" => "Avenue status",
                "default_ar" => "حالة المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Branch
        Translation::insert([
            [
                "key" => "branch_create_form",
                "default_en" => "Add new branch",
                "default_ar" => "اضف فرع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_edit_form",
                "default_en" => "Edit branch form",
                "default_ar" => "تعديل نموذج الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_name_ar",
                "default_en" => "Branch name (arabic)",
                "default_ar" => "اسم الفرع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_name_en",
                "default_en" => "Branch name (english)",
                "default_ar" => "اسم الفرع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "branch_status",
                "default_en" => "Branch status",
                "default_ar" => "حالة الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);

        ///////// club ///////////////////////////

        //Financial status
        Translation::insert([
            [
                "key" => "financial_status_create_form",
                "default_en" => "Add new financial status",
                "default_ar" => "اضف حالة مالية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status_edit_form",
                "default_en" => "Edit financial status form",
                "default_ar" => "تعديل الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "financial_status_name_ar",
                "default_en" => "Financial status name (arabic)",
                "default_ar" => "اسم الحالة المالية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status_name_en",
                "default_en" => "Financial status name (english)",
                "default_ar" => "اسم الحالة المالية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);

        //Member
        Translation::insert([
            [
                "key" => "member_create_form",
                "default_en" => "Add new member",
                "default_ar" => "اضف عضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_edit_form",
                "default_en" => "Edit member",
                "default_ar" => "تعديل العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "member_first_name",
                "default_en" => "First name",
                "default_ar" => "الاسم الاول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_second_name",
                "default_en" => "Second name",
                "default_ar" => "الاسم الثاني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_third_name",
                "default_en" => "Third name",
                "default_ar" => "الاسم الثالث",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial status name",
                "default_ar" => "اسم الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type_id",
                "default_en" => "Member type name",
                "default_ar" => "اسم نوع العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type",
                "default_en" => "Type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_last_name",
                "default_en" => "Last name",
                "default_ar" => "الاسم الرابع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_family_name",
                "default_en" => "Family name",
                "default_ar" => "اسم العائلة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "status",
                "default_en" => "Status name",
                "default_ar" => "اسم الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_birth_date",
                "default_en" => "Birth date",
                "default_ar" => "تاريخ الميلاد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_national_id",
                "default_en" => "National id",
                "default_ar" => "الرقم القومي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_nationality_number",
                "default_en" => "Nationality number",
                "default_ar" => "رقم الهوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_home_phone",
                "default_en" => "Home phone",
                "default_ar" => "تليفون المنزل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_work_phone",
                "default_en" => "Work phone",
                "default_ar" => "تليفون العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

            [
                "key" => "member_home_address",
                "default_en" => "Home address",
                "default_ar" => "عنوان المنزل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_work_address",
                "default_en" => "Work address",
                "default_ar" => "عنوان العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

            [
                "key" => "member_membership_date",
                "default_en" => "Membership date",
                "default_ar" => "تاريخ العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_membership_number",
                "default_en" => "Membership number",
                "default_ar" => "رقم العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_membership_request_number",
                "default_en" => "Membership request number",
                "default_ar" => "رقم طلب العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_job",
                "default_en" => "Job",
                "default_ar" => "الوظيفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_degree",
                "default_en" => "Degree",
                "default_ar" => "الدرجة العلمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance",
                "default_en" => "Acceptance",
                "default_ar" => "القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_session_date",
                "default_en" => "Session date",
                "default_ar" => "تاريخ الجلسة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance_date",
                "default_en" => "Acceptance date",
                "default_ar" => "تاريخ القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance_number",
                "default_en" => "Acceptance number",
                "default_ar" => "رقم القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_session_number",
                "default_en" => "Session number",
                "default_ar" => "رقم الجلسة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "sponsor",
                "default_en" => "Sponsor name",
                "default_ar" => "اسم الراعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_is_sponsor",
                "default_en" => "Sponsor",
                "default_ar" => "راعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);

        //Pending member
        Translation::insert([
            [
                "key" => "pending_member_create_form",
                "default_en" => "Add new pending member",
                "default_ar" => "اضف عضو معلق جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "pending_member_edit_form",
                "default_en" => "Edit pending member",
                "default_ar" => "تعديل عضو معلق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "pending_member_name_ar",
                "default_en" => "Pending member name (arabic)",
                "default_ar" => "اسم العضو المعلق (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "pending_member_name_en",
                "default_en" => "Pending member name (english)",
                "default_ar" => "اسم العضو المعلق (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "pending_member_status",
                "default_en" => "Pending member status",
                "default_ar" => "حالة العضو المعلق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);

        //Sponsor
        Translation::insert([
            [
                "key" => "sponsor_create_form",
                "default_en" => "Add new sponsor",
                "default_ar" => "اضف راعي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "sponsor_edit_form",
                "default_en" => "Edit sponsor form",
                "default_ar" => "تعديل الراعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "sponsor_name_ar",
                "default_en" => "Sponsor name (arabic)",
                "default_ar" => "اسم الراعي (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "sponsor_name_en",
                "default_en" => "Sponsor name (english)",
                "default_ar" => "اسم الراعي (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ]
        ]);

        //club settings
        Translation::insert([
            [
                "key" => "settings_create_form",
                "default_en" => "Add new setting",
                "default_ar" => "اضف اعدادات جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "settings_edit_form",
                "default_en" => "Edit setting form",
                "default_ar" => "تعديل الاعدادات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type",
                "default_en" => "Member Type",
                "default_ar" => "نواع العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_permission",
                "default_en" => "Member Permission",
                "default_ar" => "صلاحيات العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_period",
                "default_en" => "Membership Period",
                "default_ar" => "مدة الاشتراك تجاوزة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial Status",
                "default_ar" => "حالة العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "allowed_subscription_date",
                "default_en" => "Allowed Subscription Date",
                "default_ar" => "فترة السداد لا تتجاوز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);
        //membership_renewal
        Translation::insert([
            [
                "key" => "membership_renewal_create_form",
                "default_en" => "Add new membership renewal",
                "default_ar" => "اضف تجديد عضويه جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_edit_form",
                "default_en" => "Edit membership renewal",
                "default_ar" => "تعديل تجديد عضويه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "membership_renewal_from",
                "default_en" => "from",
                "default_ar" => "من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_to",
                "default_en" => "to",
                "default_ar" => "الي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_fromDay",
                "default_en" => "from (D)",
                "default_ar" => "من (اليوم)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_toDay",
                "default_en" => "to (D)",
                "default_ar" => "الي (اليوم)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_fromMonth",
                "default_en" => "from (M)",
                "default_ar" => "من (الشهر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_toMonth",
                "default_en" => "to (M)",
                "default_ar" => "الي (الشهر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_membership_availability",
                "default_en" => "membership availability",
                "default_ar" => "عضويه متاحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_availability",
                "default_en" => "renewal availability",
                "default_ar" => "متوفر التجديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_membership_cost",
                "default_en" => "membership cost",
                "default_ar" => "تكلفة العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_renewal_cost",
                "default_en" => "renewal cost",
                "default_ar" => "تكلفة التجديد ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ]
        ]);
    }
}
