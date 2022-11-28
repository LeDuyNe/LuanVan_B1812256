<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AbstractApiController;

class CreatorController extends AbstractApiController
{
    public function index()
    {
        $this->setStatus('200');
        $this->setMessage("This is dashboard of Creator");

        return $this->respond();
    }
}
