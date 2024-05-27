<?php

/*
 * This is the App controller.
 */

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\View\View;
use App\Core\Service\SessionManager;
use App\Core\Components\Controller;

class AppController extends Controller
{
    public mixed $auth_login;

    public Request $request;

    public Response $response;

    public function __construct(){
        parent::__construct();
        $this->renderUserLogin();
    }

    private function renderUserLogin(): void
    {
        $this->auth_login = SessionManager::get("login");
        View::render("login", $this->auth_login);
    }

}