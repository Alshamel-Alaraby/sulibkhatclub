<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Models\Serial;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstReservation;
use Modules\RealEstate\Http\Requests\RlstReservationRequest;
use Modules\RealEstate\Transformers\RlstReservationResource;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RlstReservationController extends Controller
{

    public function __construct(private RlstReservation $model, private Media $media ,private RpBreakDown $modelBreakDown)
    {
        $this->model = $model;
        $this->modelBreakDown = $modelBreakDown;

    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstReservationResource($model));
    }

    public function all(AllRequest $request)
    {

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstReservationResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstReservationRequest $request)
    {
        $model = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $serial = Serial::where([['branch_id',$request->branch_id],['document_id',$request->document_id]])->first();
            $data['serial_id'] = $serial ? $serial->id:null;
            $model = $this->model->create($data);
            foreach ($request->details as $detail) {
                $model->details()->create($detail);
            }
            if ($request->media) {
                foreach ($request->media as $media) {
                    $this->media::where('id', $media)->update([
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }
            $model->refresh();
            $serials = generalSerial($model);
            $model->update([
                "serial_number" => $serials['serial_number'],
                "prefix" => $serials['prefix'],
            ]);
            return $model;

        });

        return responseJson(200, 'created', new RlstReservationResource($model));

    }

    public function update($id, RlstReservationRequest $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            if (!$model) {
                return responseJson(404, 'not found');
            }

            $model->update($request->validated());
            if ($request->media && !$request->old_media) { // if there is new media and no old media
                $model->clearMediaCollection('media');
                foreach ($request->media as $media) {
                    uploadImage($media, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if ($request->old_media && !$request->media) { // if there is old media and no new media
                $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                    $media->delete();
                });
            }

            if ($request->old_media && $request->media) { // if there is old media and new media
                $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                    $media->delete();
                });
                foreach ($request->media as $image) {
                    uploadImage($image, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if (!$request->old_media && !$request->media) { // if this is no old media and new media
                $model->clearMediaCollection('media');
            }

            foreach ($request->details as $detail) {
                $model->details()->updateOrCreate(['id' => $detail['id']], $detail);
            }
            $serials = generalSerialUpdate($model);
            $model->update([
                "serial_number" => $serials['serial_number'],
                "prefix" => $serials['prefix'],
            ]);

            $model->refresh();
            return responseJson(200, 'updated', new RlstReservationResource($model));
        });

    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
//        if ($model->hasChildren()){
//            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
//
//        }
        if ($model->breakDowns){
            $this->modelBreakDown->where([['break_id',$model->id],['break_type','reservation']])->delete();
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function getSerial($branch_id)
    {
        return gen_get_serial($branch_id);
    }
}
