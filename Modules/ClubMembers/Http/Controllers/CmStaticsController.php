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

        $data['normalMemberCount'] = CmMember::where(function ($query) {
            $query->where('member_type_id', 1);
        })->orWhereRelation('memberType', 'parent_id', 1)->count();

        $data['foundingMemberCount'] = CmMember::where(function ($query) {
            $query->where('member_type_id', 2);
        })->orWhereRelation('memberType', 'parent_id', 2)->count();

        $data['dismissedMemberCount'] = CmMember::where(function ($query) {
            $query->where('member_type_id', 3);
        })->orWhereRelation('memberType', 'parent_id', 3)->count();

        $data['sponsorsCount'] = CmSponser::count();

        $data['presenceMemberCount'] = CmMember::where('auto_member_type_id',1)->count();
        $data['voteMemberCount'] = CmMember::where('auto_member_type_id',2)->count();
        $data['nominateMemberCount'] = CmMember::where('auto_member_type_id',3)->count();


        $response = [
            'message' => 'success',
            'data' => $data,
        ];

        return response()->json($response);
    }

}
