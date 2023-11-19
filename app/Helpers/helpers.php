<?php

use Illuminate\Support\Facades\Cache;

function uploadImage($id, array $value)
{
    \Spatie\MediaLibrary\MediaCollections\Models\Media::where('id', $id)->update($value);
}

/**
 * @param message which return with of response json
 * @param data $object
 * @param response_status like 200,500,400
 * @param pagination $pagination have default value null
 * @return \Illuminate\Http\JsonResponse
 */
function responseJson($response_status, $massage, $object = null, $pagination = null)
{
    $response = [
        'message' => $massage,
        'data' => $object,
        'pagination' => $pagination,
    ];
    if (request()->get('get_translation') == true) {
        $response['translations'] = get_trans();
    }
    return response()->json($response, $response_status);
}

/**
 * @param collection of data resource
 * @return array of properties for pagination
 */
function getPaginates($collection)
{
    return [
        'per_page' => $collection->perPage(),
        'path' => $collection->path(),
        'total' => $collection->total(),
        'current_page' => $collection->currentPage(),
        'next_page_url' => $collection->nextPageUrl(),
        'previous_page_url' => $collection->previousPageUrl(),
        'last_page' => $collection->lastPage(),
        'has_more_pages' => $collection->hasMorePages(),
        'from' => $collection->firstItem(),
        'to' => $collection->lastItem(),
    ];
}

// Cache methods
function cachePut($key, $value, $minutes = null)
{

    if ($minutes) {
        return Cache::put($key, $value, $minutes);
    } else {
        return Cache::put($key, $value);
    }

}

function cacheGet($key)
{
    return Cache::get($key);
}

function cacheForget($key)
{
    return Cache::forget($key);
}

function hasChildren($model)
{
    if ($model->hasChildren()) {
        return true;
    }
    return false;
}

function get_trans()
{
    $translations = \App\Models\Translation::query();
    if (request()->has('company_id')) {
        $translations = $translations->where('company_id', request()->get('company_id'));
    } else {
        $translations = $translations->where('company_id', 0);
    }
    $translations = $translations->get();
//    return \App\Http\Resources\TranslationResource::collection ($translations);
    $arr = [];
    foreach ($translations as $d) {
        $arr[$d->key] = [
            'id' => $d->id,
            'default_en' => $d->default_en,
            'default_ar' => $d->default_ar,
            'new_en' => $d->new_en,
            'new_ar' => $d->new_ar,
            "screen" => $d->screen,
        ];
    }
    return $arr;

}

function checkIsDefaultGeneral($default, $model)
{
    if ($default == 1) {
        return collect($model->all())->each(function ($item) {
            $item->update(["is_default" => 0]);
        });

    }
    return "fales";

}

function excelFilter($filterId, $collection, $columnName, $value1, $value2)
{
    return $collection->filter(function ($item) use ($filterId, $columnName, $value1, $value2) {
        switch($filterId){
            case 1: // equals(numeric/text)
                return $item->$columnName == $value1;
                break;
            case 2: // not equals(numeric/text)
                return $item->$columnName != $value1;
                break;
            case 3: // greater than(numeric)
                return $item->$columnName > $value1;
                break;
            case 4: // greater than or equal to(numeric)
                return $item->$columnName >= $value1;
                break; 
            case 5: // less than(numeric)
                return $item->$columnName < $value1;
                break;
            case 6: // less than or equal to(numeric)
                return $item->$columnName <= $value1;
                break;
            case 7: // between(numeric)
                return $item->$columnName >= $value1 && $item->$columnName <= $value2;
                break;
            case 10: // begins with(text)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    return \Illuminate\Support\Str::startsWith($item->$columnName, $value1);
                });
                break;
            case 11: // ends with(text)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    return \Illuminate\Support\Str::endsWith($item->$columnName, $value1);
                });
                break;
            case 12: // contains(text)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    return \Illuminate\Support\Str::contains($item->$columnName, $value1);
                });
                break;
            case 13: // does not contain(text)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    return !\Illuminate\Support\Str::contains($item->$columnName, $value1);
                });
                break;
            case 14: // on(date)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    $date = \Carbon\Carbon::parse($item->$columnName);
                    $filterDate = \Carbon\Carbon::parse($value1);
                    return $date->eq($filterDate);
                });
                break;
            case 15: // from(date)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    $date = \Carbon\Carbon::parse($item->$columnName);
                    $filterDate = \Carbon\Carbon::parse($value1);
                    return $date->gt($filterDate);
                });
                break;   
            case 16: // to(date)
                return $collection->filter(function ($item) use ($columnName, $value1) {
                    $date = \Carbon\Carbon::parse($item->$columnName);
                    $filterDate = \Carbon\Carbon::parse($value1);
                    return $date->lt($filterDate);
                });
                break;
            case 17: // from - to(date)
                return $collection->filter(function ($item) use ($columnName, $value1, $value2) {
                    $date = \Carbon\Carbon::parse($item->$columnName);
                    $filterDate1 = \Carbon\Carbon::parse($value1);
                    $filterDate2 = \Carbon\Carbon::parse($value2);
                    return $date->between($filterDate1, $filterDate2);
                });
                break;
                     

        }

        
    });
    
}
