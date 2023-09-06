<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmSponser;
use Modules\ClubMembers\Entities\CmSponsorGroup;

class CmStaticsController extends Controller
{

    public function getStatics()
    {
        $data = [];

        //     الأعضاء العادي
        $data['normalMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->count();
        // الأعضاء المؤسسين
        $data['foundingMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->count();
        // الأعضاء المفصولين
        $data['dismissedMemberCount'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->count();
        // عدد الرعاة
        $data['sponsorsCount'] = CmSponser::whereNotNull('parent_id')->count();
        // عدد أعضاء حضور الانتخابات
        $data['presenceMemberCount'] = CmMember::whereIn('auto_member_type_id', [1, 2, 3])->count();
        // عدد الأعضاء تصويت في الانتخابات
        $data['voteMemberCount'] = CmMember::where('auto_member_type_id', 2)->count();
        // عدد الأعضاء الترشح في الانتخابات
        $data['nominateMemberCount'] = CmMember::whereIn('auto_member_type_id', [2, 3])->count();
        ////////////////////////////////////// بيانات الاعضاء العاديين //////////////////////////////////////

        // // في لائحة الاعضاء
        $data['InTheListOfMembers'] = CmMember::where('member_type_id', 4)->count();

        // // موقوف
        $data['stopped'] = CmMember::where('member_type_id', 5)->count();

        // // له حق الترشح
        $data['HeHasTheRightToRun'] = CmMember::where('member_type_id', 11)->count();

        // // له حق التصويت
        $data['HeHasTheRightToVote'] = CmMember::where('member_type_id', 12)->count();

        // // ليس له حق التصويت
        $data['HeHasNoRightToVote'] = CmMember::where('member_type_id', 17)->count();

        // // مسدد ليس له حق الحضور
        $data['PaidPersonHasNoRightToAttend'] = CmMember::where('member_type_id', 19)->count();

        // //////////////////////////////////////////    المشطوبين    //////////////////////////////////////////////////////////

        // // شطب بناءا علي طلبه
        $data['DeletedAtHisRequest'] = CmMember::where('member_type_id', 6)->count();

        // // شطب للوفاة
        $data['CancellationDueToDeath'] = CmMember::where('member_type_id', 7)->count();

        // // شطب بموجب القرار الوزاري
        $data['CancellationPursuantToMinisterialDecision'] = CmMember::where('member_type_id', 8)->count();

        // // شطب بموجب كتاب الهيئة
        $data['CancellationAccordingToTheAuthoritySLetter'] = CmMember::where('member_type_id', 9)->count();

        // // شطب لعدم السداد
        $data['WriteOffForNonPayment'] = CmMember::where('member_type_id', 18)->count();

        // //////////////////////////////////////////    الأعضاء المؤسسين    //////////////////////////////////////////////////////////

        // // عضو مؤسس
        $data['FoundingMemberDetail'] = CmMember::where('member_type_id', 10)->count();

        // // له حق الترشح و التصويت
        $data['HeHasTheRightToRunAndVoteDetail'] = CmMember::where('member_type_id', 13)->count();


        ///////////////////////// الرعاه ////////////////////////////////////////////////////////////////////////////////
        $data['sponsors'] = CmSponser::whereNotNull('group_id')->count();
        $data['group'] = CmSponsorGroup::count();

        $groupIds = CmSponser::whereNotNull('group_id')->pluck('group_id')->unique();

        $groups = [];

        foreach ($groupIds as $groupId) {
            $count = CmSponser::where('group_id', $groupId)->count();
            $percentage = ($count / $data['sponsors']) * 100;
            $groups["group_id $groupId "] = [
                'count' => $count,
                'percentage' => round($percentage, 2),
            ];
        }


        // ///////////////////////////// طلبات العضوية ////////////////////////////////////////////////////////
        $data['allRequestMembersCount'] = CmMemberRequest::count();
        $pending_member_request = CmMemberRequest::where('member_type_id', 15)->count();
        $pending_member = CmMember::where('member_type_id', 15)->count();
        $data['RequestMembersPendingCount'] = $pending_member_request + $pending_member ;
        $data['RequestMembersRejectedCount'] = CmMemberRequest::where('member_type_id', 16)->count();

        $data['RequestMembersPendingPercentage'] =  ($data['allRequestMembersCount'] != 0) ? round($data['RequestMembersPendingCount'] / $data['allRequestMembersCount'] * 100, 2):0;
        $data['RequestMembersRejectedPercentage'] =  ($data['allRequestMembersCount'] != 0) ?  round($data['RequestMembersRejectedCount'] / $data['allRequestMembersCount'] * 100, 2) :0;

        // /////////////////////////// الاعضاء المسددين السنه القادمه ///////////////////////////////////////////////////////

        $data['allMembersCount'] = CmMember::whereHas('cmTransaction')->count();

        $data['allMembersWhereHasTransactionNextYearCount'] = CmMember::whereHas('cmTransaction', function ($q) {
            $q->where('year_to', date('Y') + 1);
        })->count();

        $data['allMembersWhereHasTransactionNextYearPercentage'] = round($data['allMembersWhereHasTransactionNextYearCount'] / $data['allMembersCount'] * 100, 2);

        $response = [
            'message' => 'success',
            'data' => $data,
            'groups' => $groups,
        ];

        return response()->json($response);
    }

    // public function getNormalMembersPercentage()
    // {
    //     //////////////////////////////////////////    اعداد    //////////////////////////////////////////////////////////

    //     $data = [];
    //     // //عدد الاعضاء كلهم
    //     $data['allMembers'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->count();

    //     // // عدد أعضاء حضور الانتخابات
    //     $data['presenceMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->whereIn('auto_member_type_id', [1, 2, 3])->count();

    //     // // عدد الأعضاء تصويت في الانتخابات
    //     $data['voteMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->where('auto_member_type_id', 2)->count();

    //     // // عدد الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 1)->whereIn('auto_member_type_id', [2, 3])->count();

    //     //////////////////////////////////////////    نسب    //////////////////////////////////////////////////////////

    //     // نسبة أعضاء حضور الانتخابات
    //     $data['presenceMemberPercentage'] = round($data['presenceMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء تصويت في الانتخابات
    //     $data['voteMemberPercentage'] = round($data['voteMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberPercentage'] = round($data['nominateMemberCount'] / $data['allMembers'] * 100, 2);

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];

    //     return response()->json($response);
    // }
    // public function getFoundingMembersPercentage()
    // {
    //     //////////////////////////////////////////    المؤسسين    //////////////////////////////////////////////////////////

    //     $data = [];
    //     // //عدد الاعضاء كلهم

    //     $data['allMembers'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->count();

    //     // // عدد أعضاء حضور الانتخابات
    //     $data['presenceMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->whereIn('auto_member_type_id', [1, 2, 3])->count();

    //     // // عدد الأعضاء تصويت في الانتخابات
    //     $data['voteMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->where('auto_member_type_id', 2)->count();

    //     // // عدد الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberCount'] = CmMember::WhereRelation('memberType', 'parent_id', 2)->whereIn('auto_member_type_id', [2, 3])->count();

    //     //////////////////////////////////////////    نسب    //////////////////////////////////////////////////////////

    //     // نسبة أعضاء حضور الانتخابات
    //     $data['presenceMemberPercentage'] = round($data['presenceMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء تصويت في الانتخابات
    //     $data['voteMemberPercentage'] = round($data['voteMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberPercentage'] = round($data['nominateMemberCount'] / $data['allMembers'] * 100, 2);

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];

    //     return response()->json($response);
    // }

    // public function getDismissedMembersPercentage()
    // {
    //     //////////////////////////////////////////    المشطوبين    //////////////////////////////////////////////////////////

    //     $data = [];
    //     // //عدد الاعضاء كلهم

    //     $data['allMembers'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->count();

    //     // // عدد أعضاء حضور الانتخابات
    //     $data['presenceMemberCount'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->whereIn('auto_member_type_id', [1, 2, 3])->count();

    //     // // عدد الأعضاء تصويت في الانتخابات
    //     $data['voteMemberCount'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->where('auto_member_type_id', 2)->count();

    //     // // عدد الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberCount'] = CmMember::whereIn('member_type_id', [6, 7, 8, 9, 18])->whereIn('auto_member_type_id', [2, 3])->count();

    //     //////////////////////////////////////////    نسب    //////////////////////////////////////////////////////////

    //     // نسبة أعضاء حضور الانتخابات
    //     $data['presenceMemberPercentage'] = round($data['presenceMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء تصويت في الانتخابات
    //     $data['voteMemberPercentage'] = round($data['voteMemberCount'] / $data['allMembers'] * 100, 2);

    //     // نسبة الأعضاء الترشح في الانتخابات
    //     $data['nominateMemberPercentage'] = round($data['nominateMemberCount'] / $data['allMembers'] * 100, 2);

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];

    //     return response()->json($response);
    // }

    // public function getSponsorGroupPercentage()
    // {
    //     $data = [];

    //     $data['sponsors'] = CmSponser::whereNotNull('group_id')->count();
    //     $data['group'] = CmSponsorGroup::count();

    //     $groupIds = CmSponser::whereNotNull('group_id')->pluck('group_id')->unique();

    //     foreach ($groupIds as $groupId) {
    //         $count = CmSponser::where('group_id', $groupId)->count();
    //         $percentage = ($count / $data['sponsors']) * 100;
    //         $data["group_id $groupId "] = [
    //             'count' => $count,
    //             'percentage' => round($percentage, 2),
    //         ];
    //     }

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];
    //     return response()->json($response);
    // }

    // public function getMemberRequestPercentage()
    // {
    //     $data = [];
    //     ///////////////// اعداد /////////////////////////////////
    //     $data['allRequestMembersCount'] = CmMemberRequest::count();
    //     $data['RequestMembersPendingCount'] = CmMember::WhereRelation('memberType', 'id', 15)->count();
    //     $data['RequestMembersRejectedCount'] = CmMember::WhereRelation('memberType', 'id', 16)->count();
    //     ///////////////// نسب /////////////////////////////////
    //     $data['RequestMembersPendingPercentage'] = round($data['RequestMembersPendingCount'] / $data['allRequestMembersCount'] * 100, 2);
    //     $data['RequestMembersRejectedPercentage'] = round($data['RequestMembersRejectedCount'] / $data['allRequestMembersCount'] * 100, 2);

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];
    //     return response()->json($response);
    // }

    // public function getMembersWhereHasTransactionNextYearCount()
    // {
    //     $data = [];
    //     ///////////////// اعداد /////////////////////////////////
    //     $data['allMembersCount'] = CmMember::count();

    //     $data['allMembersWhereHasTransactionNextYearCount'] = CmMember::whereHas('cmTransaction', function ($q) {
    //         $q->where('year_to', date('Y') + 1);
    //     })->count();

    //     ///////////////// نسب /////////////////////////////////
    //     $data['allMembersWhereHasTransactionNextYearPercentage'] = round($data['allMembersWhereHasTransactionNextYearCount'] / $data['allMembersCount'] * 100, 2);

    //     $response = [
    //         'message' => 'success',
    //         'data' => $data,
    //     ];
    //     return response()->json($response);
    // }

}
