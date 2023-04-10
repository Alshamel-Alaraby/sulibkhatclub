<?php

use App\Models\Serial;
use Modules\RealEstate\Entities\RlstInvoice;

// function generalSerial($model, $type)
// {
//     if (isset($model->serial)) {
//         if ($model->serial->branch) {
//             return $type . $model->document_id . $model->serial->branch->id . $model->id;
//         } else {
//             return $type . $model->document_id . $model->id;
//         }
//     } else {
//         if ($model->branch) {
//             $serial = Serial::where("branch_id", $model->branch->id)->first();
//             if ($serial) {
//                 return $type . $model->document_id . $serial->id . $model->id;
//             } else {
//                 return $type . $model->document_id . $model->id;
//             }


//         }
//     }

// }
function generalSerial($model, $type = "")
{
    $data = [];
    if ($model->branch) {
        $serial = Serial::where(["branch_id"=> $model->branch->id,"document_id"=> $model->document_id])->first();


        $count = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial->id])->get()->count();

        if ($count == 1) {

            $start_number = (int)$serial->start_no;
            $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $start_number;
            $data['serial_number'] = $start_number;

        } else {

            $last_serial_number = $model->where(['document_id' => $model->document_id, 'serial_id' => $serial->id])->latest()->skip(1)->first();

            $new_serial_number = (int)$last_serial_number->serial_number + 1;

            $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $new_serial_number;
            $data['serial_number'] = $new_serial_number;

        }
        return $data;

    }
}


function generalSerialUpdate($model)
{
    $data = [];
    if ($model->branch) {
        $serial = Serial::where(["branch_id"=> $model->branch->id,"document_id"=> $model->document_id])->first();
        $start_number = $model->serial_number;
        $data['prefix'] = $serial->perfix . '-' . $model->document_id . '-' . $start_number;
        $data['serial_number'] = $start_number;
    }
    return $data;

}
