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
    public function checkBooking();

    public function getDocumentsCustomer($id);
}
