<?php

namespace Modules\ClubMembers\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\ElectoralCommittee;
use Modules\ClubMembers\Transformers\CmMemberPermissionReportResource;

class CmReportController extends Controller
{
    public function __construct(private CmMember $model, private ElectoralCommittee $electoralCommitteeModel)
    {
        $this->model = $model;
        $this->electoralCommitteeModel = $electoralCommitteeModel;
    }

    public function membersByPermissions(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->members_permissions_id) {
            $year = Carbon::createFromFormat('d-m-Y', $request->dateOfYear)->format('Y') + 1;

            $models->whereIn('members_permissions_id', $request->members_permissions_id)->where('member_status_id', 1)->where('last_transaction_year', $year)->with('lastCmTransaction');
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', CmMemberPermissionReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    // public function memberForCommittee(Request $request)
    // {
    //     $data = [];
    //     $data['electoral_committees'] = $this->electoralCommitteeModel->orderBy('number_of_individuals', 'DESC')->get();

    //     $models = $this->model->filter($request)
    //         ->orderByRaw("CONVERT(SUBSTRING(full_name COLLATE utf8mb4_unicode_ci, 1) USING utf8mb4) ASC");

    //     if ($request->members_permissions_id) {
    //         $year = Carbon::createFromFormat('d-m-Y', $request->dateOfYear)->format('Y') + 1;
    //         $models->whereIn('members_permissions_id', $request->members_permissions_id)
    //             ->where('member_status_id', 1)
    //             ->where('last_transaction_year', $year)
    //             ->with('lastCmTransaction');
    //     }

    //     $allMembers = $models->get()->toArray();

    //     $membersByCommittee = [];
    //     foreach ($data['electoral_committees'] as $committee) {
    //         $numberOfIndividuals = $committee['number_of_individuals'];

    //         $members = [];
    //         for ($i = 0; $i < $numberOfIndividuals && count($allMembers) > 0; $i++) {
    //             $members[] = array_shift($allMembers);
    //         }

    //         $membersPaginated = [];
    //         if ($request->per_page && $request->per_page == 1) {
    //             $page = $request->page ?? 1;
    //             $perPage = 1;
    //             $offset = ($page - 1) * $perPage;
    //             $membersPaginated = array_slice($members, $offset, $perPage);
    //         } else {
    //             $membersPaginated = $members;
    //         }

    //         $committeeMembers = [
    //             'id' => $committee['id'],
    //             'name' => $committee['name'],
    //             'name_e' => $committee['name_e'],
    //             'number_of_individuals' => $numberOfIndividuals,
    //             'members_data' => $membersPaginated,
    //         ];

    //         $membersByCommittee[] = $committeeMembers;
    //     }

    //     $pagination = null;
    //     if ($request->per_page && $request->per_page == 1) {
    //         $totalMembers = count($allMembers);
    //         $pagination = [
    //             'per_page' => 1,
    //             'path' => $request->url(),
    //             'total' => $totalMembers,
    //             'current_page' => $request->page ?? 1,
    //             'next_page_url' => $totalMembers > 1 ? $request->fullUrlWithQuery(['page' => $request->page + 1]) : null,
    //             'previous_page_url' => $request->page > 1 ? $request->fullUrlWithQuery(['page' => $request->page - 1]) : null,
    //             'last_page' => ceil($totalMembers / 1),
    //             'has_more_pages' => $totalMembers > 1 ? true : false,
    //             'from' => 1,
    //             'to' => 1,
    //         ];
    //     }

    //     $response = [
    //         'committees_with_members' => $membersByCommittee,
    //         'pagination' => $pagination,
    //     ];

    //     return responseJson(200, 'success', $response);
    // }

    public function memberForCommittee(Request $request)
    {
        $data = [];
        $data['electoral_committees'] = $this->electoralCommitteeModel->orderBy('number_of_individuals', 'DESC')->get();

        $models = $this->model->filter($request)
            ->orderByRaw("CONVERT(SUBSTRING(full_name COLLATE utf8mb4_unicode_ci, 1) USING utf8mb4) ASC");

        if ($request->members_permissions_id) {
            $year = Carbon::createFromFormat('d-m-Y', $request->dateOfYear)->format('Y') + 1;
            $models->whereIn('members_permissions_id', $request->members_permissions_id)
                ->where('member_status_id', 1)
                ->where('last_transaction_year', $year)
                ->with('lastCmTransaction');
        }

        $allMembers = $models->get()->toArray();

        $membersByCommittee = [];
        foreach ($data['electoral_committees'] as $committee) {
            $numberOfIndividuals = $committee['number_of_individuals'];

            $members = [];
            for ($i = 0; $i < $numberOfIndividuals && count($allMembers) > 0; $i++) {
                $members[] = array_shift($allMembers);
            }

            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? count($members);
            $offset = ($page - 1) * $perPage;

            $membersPaginated = array_slice($members, $offset, $perPage);

            $firstMemberName = count($members) > 0 ? $members[0]['full_name'] : null;
            $lastMemberName = count($members) > 0 ? $members[count($members) - 1]['full_name'] : null;

            
            $committeeMembers = [
                'id' => $committee['id'],
                'name' => $committee['name'],
                'name_e' => $committee['name_e'],
                'number_of_individuals' => $numberOfIndividuals,
                'first_member' => $firstMemberName,
                'last_member' => $lastMemberName,
                'members_data' => $membersPaginated,
            ];

            $membersByCommittee[] = $committeeMembers;
        }

        $pagination = [
            'per_page' => $request->per_page ?? 1,
            'path' => $request->url(),
            'total' => count($allMembers),
            'current_page' => $page ?? 1,
            'next_page_url' => $page < count($allMembers) / $perPage ? $request->fullUrlWithQuery(['page' => $page + 1]) : null,
            'previous_page_url' => $page > 1 ? $request->fullUrlWithQuery(['page' => $page - 1]) : null,
            'last_page' => ceil(count($allMembers) / $perPage),
            'has_more_pages' => $page < count($allMembers) / $perPage,
            'from' => $offset + 1,
            'to' => $offset + count($membersPaginated),
        ];

        $response = [
            'committees_with_members' => $membersByCommittee,
            'pagination' => $pagination,
        ];

        return responseJson(200, 'success', $response);
    }

}
