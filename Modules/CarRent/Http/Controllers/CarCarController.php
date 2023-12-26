<?php

namespace Modules\CarRent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CarRent\Entities\CarCar;
use Modules\CarRent\Entities\CarPricing;
use Modules\CarRent\Http\Requests\CarCarRequest;
use Modules\CarRent\Transformers\CarCarResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CarCarController extends Controller
{
    public function __construct(private CarCar $model, private Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->generalfilter($request)->data()->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', CarCarResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CarCarResource($model));
    }

    public function create(CarCarRequest $request)
    {

        return DB::transaction(function () use ($request) {
            $requestData = $request->validated();
            $model = $this->model->create($requestData);
            $model->refresh();

            if ($request->media) {
                foreach ($request->media as $media) {
                    $this->media::where('id', $media)->update([
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }
            return responseJson(200, 'created', new CarCarResource($model));

        });

    }

    public function update($id, CarCarRequest $request)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return DB::transaction(function () use ($request, $model) {
            $model->update($request->validated());
            $model->refresh();

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
            $model->refresh();
            return responseJson(200, 'updated', new CarCarResource($model));
        });

    }
    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $relationsWithChildren = $model->hasChildren();

        if (!empty($relationsWithChildren)) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = [
                    "message" => "This item has {$childCount} {$relationName} (Names: {$childIds}) and can't be deleted. Remove its {$relationName} first."
                ];
            }
            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null
            ], 400);
        }

        $model->delete();
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->model->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $model->delete();
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = [
                        'message' => "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first."
                    ];
                }

                $errorMessages = array_merge($errorMessages, $relationErrorMessages);
            }

            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null
            ], 400);
        }

        return responseJson(200, 'success');
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
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

    public function updatePrices(Request $request)
    {

        $carIds = $request->input('car_ids');
        $dailyPrice = $request->input('daily_price');
        $weekPrice = $request->input('weekly_price');
        $monthPrice = $request->input('monthly_price');

        try {
            foreach ($carIds as $carId) {
                $car = CarCar::findOrFail($carId);

                $car->daily_price = $dailyPrice;
                $car->weekly_price = $weekPrice;
                $car->monthly_price = $monthPrice;

                $car->save();

                CarPricing::create([
                    'car_id' => $carId,
                    'daily_price' => $dailyPrice,
                    'weekly_price' => $weekPrice,
                    'monthly_price' => $monthPrice,

                ]);
            }

            return responseJson(200, 'updated');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update prices'], 500);
        }
    }
}
