<?php

namespace App\Http\Controllers\InsuranceCompany;

use App\Http\Requests\InsuranceCompany\InsuranceCompanyRequest;
use App\Http\Resources\InsuranceCompany\InsuranceCompanyResource;
use App\Models\InsuranceCompany;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InsuranceCompanyController extends Controller
{
    public function __construct(private InsuranceCompany $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        if($request->drop_down)
            return $this->model->select('id','name','name_e')->latest()->get();

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', InsuranceCompanyResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(InsuranceCompanyRequest $request)
    {
        $this->model->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(InsuranceCompanyRequest $request, InsuranceCompany $insurance_company)
    {
        $insurance_company->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(InsuranceCompany $insurance_company)
    {
        $insurance_company->delete();
    }
}
