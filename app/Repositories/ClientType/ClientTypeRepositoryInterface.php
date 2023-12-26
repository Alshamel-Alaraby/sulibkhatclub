<?php


namespace App\Repositories\ClientType;


interface ClientTypeRepositoryInterface
{
    public function getAll($request);

    public function logs($id);
    public function getName($request);
    public function getDropDownByTable($request);

}
