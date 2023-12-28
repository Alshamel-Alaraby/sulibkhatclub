<?php

namespace App\Repositories\DocumentHeader;



interface DocumentHeaderInterface
{
    public function all($request);
    public function find($id);
    public function create($request);
    public function update($request,$id);
    public function logs($id);
    public function delete($id);

    public function getDateRelatedDocumentId($request);
    public function allDocumentHeader($request);
    public function customerRoom($request);
    public function checkOutPrint($id);
    public function checkBooking();
    public function checkInCustomer();

    public function createDailyCheckInCustomer();

    public function updateCheckInCustomer($request);

    public function createDailyInvoiceOnline($request);

    public function getDocumentsCustomer($id,$request);

    public function documentRealEstateData($id);

    public function updateContractHeader($request,$id);
    public function all_renew_contract_header($request);
    public function renew_contract($request);
}
