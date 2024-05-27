<?php

namespace App\Core\Components;

use App\Core\Request;
use App\Core\Response;

class Controller
{
    public Request $request;

    public Response $response;

    public function __construct(){}
}