<?php


namespace App\Repositories\Document;

use App\Http\Resources\Document\DocumentResource;
use App\Models\GeneralCustomTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
class DocumentIRepository implements DocumentInterface
{
    public function __construct(private \App\Models\Document$model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function find($id)
    {
        $data = $this->model->find($id);
        return $data;
    }
    public function findWhereIsAdmin($id)
    {
        $data = $this->model->where('is_admin',1)->find($id);
        return $data;
    }

    public function create($request){
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request);
            $model->documentRelateds()->sync($request['document_relateds']);

        });
    }

    public function createFromAdmin($request){
            $this->model->
            where([
                ['company_id',$request["documents"][0]['company_id']],
                ['is_admin',1]
            ])->delete();
            foreach ($request['documents'] as $document):
                $nullIsAdmin = $this->model->find($document['id']);
                if ($nullIsAdmin){
                    $nullIsAdmin->delete();
                }
                 $model = $this->model->create(array_merge($document,['is_admin'=>1]));
                 $model->documentRelateds()->sync($document['document_relateds']);
            endforeach;
    }

    public function update($request,$id){
        $data = $this->model->find($id);
        $data->update($request);
        $data->documentRelateds()->sync($request['document_relateds']);

        return $data;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id){
        $model = $this->model->find($id);
        $model->delete();
    }
}
