<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Translation::insert([
            [
                "key" => "parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "year_from",
                "default_en" => "From Year",
                "default_ar" => "تاريخ بداية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "year_to",
                "default_en" => "To Year",
                "default_ar" => "تاريخ نهاية الاشتراك",
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
                "key" => "member_request_create_form",
                "default_en" => "Add new member request",
                "default_ar" => "اضف  طلب لعضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_create_accept",
                "default_en" => "Acceptance of members",
                "default_ar" => "قبول الاعضاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_create_reject",
                "default_en" => "Reject member",
                "default_ar" => "رفض عضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_edit_form",
                "default_en" => "Edit member request",
                "default_ar" => "تعديل الطلب",
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
                "default_ar" => "الرقم المدنى",
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
                "key" => "apply_membership_date",
                "default_en" => "date of request",
                "default_ar" => "تاريخ الطلب",
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
                "key" => "apply_membership_number",
                "default_en" => "number of request",
                "default_ar" => "رقم الطلب ",
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
                "key" => "from-sponsor",
                "default_en" => "Sponsor name (from)",
                "default_ar" => "اسم الراعي (من)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "to-sponsor",
                "default_en" => "Sponsor name (to)",
                "default_ar" => "اسم الراعي (الي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_is_sponsor",
                "default_en" => "Is Sponsor",
                "default_ar" => " هل  راعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);
        //Financial status
        Translation::insert([
            [
                "key" => "financial_status_create_form",
                "default_en" => "Add new financial status",
                "default_ar" => "اضف حالة مالية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "financial_status_edit_form",
                "default_en" => "Edit financial status form",
                "default_ar" => "تعديل الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "financial_status_name_ar",
                "default_en" => "Financial status name (arabic)",
                "default_ar" => "اسم الحالة المالية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "financial_status_name_en",
                "default_en" => "Financial status name (english)",
                "default_ar" => "اسم الحالة المالية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
    }
}
