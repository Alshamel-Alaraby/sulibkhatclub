<?php

namespace App\Jobs;

use App\Models\DocumentHeader;
use App\Models\DocumentHeaderDetail;
use App\Models\ItemBreakDown;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Entities\Setting;
use Modules\RecievablePayable\Entities\RpBreakDown;

class YearlyContractInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $item  =  $this->data ;
        $data_header = DocumentHeader::find($item['header_id']);


        $create_header =  collect($data_header)->except(['document_id','id','date','total_invoice','invoice_discount','net_invoice','unrelaized_revenue','related_document_id','related_document_number','related_document_prefix'])->toArray();

        $documant_header = DocumentHeader::create(array_merge($create_header,[
            'document_id'              => 49,
            'date'                     => $item['due_day'],
            'total_invoice'            => $item['total_invoice'],
            'invoice_discount'         => $item['total_discount'],
            'net_invoice'              => $item['total_net'],
            'unrelaized_revenue'       => $item['total_net'],
            'related_document_id'      => $data_header->document_id,
            'related_document_number'  => $data_header->id,
            'related_document_prefix'  => $data_header->serial_number,

        ]));

        if ($data_header->documentHeaderDetails){
            foreach ($data_header->documentHeaderDetails as $haeder_detail){
                $create_detail =  collect($haeder_detail)->except(['id']);
                $detail        =  DocumentHeaderDetail::create(array_merge($create_detail->all(),[
                    'document_header_id' => $documant_header->id,
                ]));

                if($haeder_detail->itemBreakDowns){

                    foreach ($haeder_detail->itemBreakDowns as $itemDown){

                        $create_down = collect($itemDown)->except(['id','document_header_detail_id']);

                        ItemBreakDown::create(array_merge($create_down->all(),[
                            "document_header_detail_id" => $detail->id,
                        ]));
                    }

                }

            }

        }

        RpBreakDown::create([
            'instalment_date'       => $item['due_day'],
            'rate'                  => 1,
            'repate'                => 1,
            'currency_id'           => 1,
            'document_id'           => 49,
            'customer_id'           => $data_header['customer_id'],
            'break_id'              => $data_header['id'],
            'instalment_type_id'    => $data_header['installment_payment_type_id'],
            'break_type'            => 'documentHeader',
            'debit'                 => ($data_header->document->attributes && $data_header->document->attributes['customer'] == 1)  ? $item['total_net']:0,
            'credit'                => ($data_header->document->attributes && $data_header->document->attributes['customer'] == -1) ? $item['total_net']:0,
            'total'                 => $item['total_net'],
            'installment_statu_id'  => 1,
            'client_type_id'        => 1,
        ]);

    }
}
