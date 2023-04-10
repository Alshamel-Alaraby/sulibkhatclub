<?php

namespace App\Repositories\GeneralCustomer;

use App\Models\GeneralCustomer;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GeneralCustomerRepository implements GeneralCustomerRepositoryInterface
{

    public function __construct(private GeneralCustomer $model, private Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where(function ($q) use ($request) {
                $q->when($request->opening_balance, function ($q) use ($request) {
                    $q->doesntHave('opening_balances');
                });
            });

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            $model = $this->model->create($data);
            return $model;
        });
    }

    public function update($data, $id)
    {

        return DB::transaction(function () use ($id, $data) {
            $model = $this->model->where("id", $id)->first();
            $model->update($data->all());

            if (request()->media && !request()->old_media) { // if there is new media and no old media
                $model->clearMediaCollection('media');
                foreach (request()->media as $media) {
                    uploadImage($media, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if (request()->old_media && !request()->media) { // if there is old media and no new media
                $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                    $media->delete();
                });
            }

            if (request()->old_media && request()->media) { // if there is old media and new media
                $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                    $media->delete();
                });
                foreach (request()->media as $image) {
                    uploadImage($image, [
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            if (!request()->old_media && !request()->media) { // if this is no old media and new media
                $model->clearMediaCollection('media');
            }
            return $this->model->find($id);
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    private function forget($id)
    {
        $keys = [
            "GeneralCustomer",
            "GeneralCustomer_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

}
