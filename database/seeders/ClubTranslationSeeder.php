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
        //Branch
        Translation::insert([
            [
                "key" => "branch_create_form",
                "default_en" => "Add new branch",
                "default_ar" => "اضف فرع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_edit_form",
                "default_en" => "Edit branch form",
                "default_ar" => "تعديل نموذج الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_name_ar",
                "default_en" => "Branch name (arabic)",
                "default_ar" => "اسم الفرع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_name_en",
                "default_en" => "Branch name (english)",
                "default_ar" => "اسم الفرع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_status",
                "default_en" => "Branch status",
                "default_ar" => "حالة الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
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
                "default_ar" => "مدة الاشتراك تجاوزة (سنه)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial Status",
                "default_ar" => "حالة السداد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "allowed_subscription_date",
                "default_en" => "Allowed Subscription Date",
                "default_ar" => "فترة السداد لا تتجاوز تاريخ",
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
        //club subscription
        Translation::insert([
            [
                "key" => "branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club",
            ],
            [
                "key" => "statuses_name_ar",
                "default_en" => "status name (arabic)",
                "default_ar" => "اسم الحاله (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "statuses_name_en",
                "default_en" => "status name (english)",
                "default_ar" => "اسم الحاله (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "statuses_icon",
                "default_en" => "icon",
                "default_ar" => "أيقونة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "statuses_color",
                "default_en" => "color",
                "default_ar" => "اللون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "subscription_create_form",
                "default_en" => "Add new subscription",
                "default_ar" => "اضف اشتراك جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_edit_form",
                "default_en" => "Edit subscription form",
                "default_ar" => "تعديل الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member",
                "default_en" => "Member",
                "default_ar" => "العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "date_from",
                "default_en" => "From Date",
                "default_ar" => "تاريخ بداية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "date_to",
                "default_en" => "To Date",
                "default_ar" => "تاريخ نهاية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_type",
                "default_en" => "Type",
                "default_ar" => "نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_amount",
                "default_en" => "Subscription Amount",
                "default_ar" => "قيمة الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

        ]);
    }
}
