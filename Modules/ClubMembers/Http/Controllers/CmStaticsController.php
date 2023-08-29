<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmSponser;

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
        $data['dismissedMemberCount'] = CmMember::whereIn('member_type_id', [6,7,8,9,18])->count();
        // عدد الرعاة
        $data['sponsorsCount'] = CmSponser::whereNotNull('parent_id')->count();
        // عدد أعضاء حضور الانتخابات
        $data['presenceMemberCount'] = CmMember::whereIn('auto_member_type_id',[1,2,3])->count();
        // عدد الأعضاء تصويت في الانتخابات
        $data['voteMemberCount'] = CmMember::where('auto_member_type_id',2)->count();
        // عدد الأعضاء الترشح في الانتخابات
        $data['nominateMemberCount'] = CmMember::whereIn('auto_member_type_id',[2,3])->count();


        $response = [
            'message' => 'success',
            'data' => $data,
        ];

        return response()->json($response);
    }

}
