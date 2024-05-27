<?php

/*
 * This is the users controller. It adds returns the view for the users list portion of this framework.
 */

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Core\View\View;
use App\Controllers\AppController;
use App\Core\Service\SecurityManager;
use App\Core\Service\SessionManager;
use App\Components\UserComponent;
use App\Components\EmailComponent;

class UsersController extends AppController
{

    private UserComponent $User;

    public Request $request;

    public Response $response;

    public function __construct(Request $request)
    {   $this->request = $request;
        $this->response = new Response();
        $this->User = new UserComponent();
        parent::__construct();
    }

    /*
     * This function selects all the users from the users database and then grabs the users view to display them.
     */
    /**
     * @throws \Exception
     */
    public function index($vars = [])
    {
        //print_r($this->request->query());
        //print_r($this->request->headers());
        $user = new User();
        $count = $user->count();
        $paginationConfig = App::Config()['pagination'];
        $limit = $paginationConfig['per_page'] ?? 5;
        $page = $vars['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $users = $user->where([['id', '>', '0']], $limit, $offset)->get();
        //$this->response->statusCode(502);
        //$this->response->send();
        return View::view('Users', 'users', compact('users', 'count', 'page', 'limit'));
    }

    /**
     * @throws \Exception
     */
    public function login($vars)
    {
        //пароль
        $password = $_POST['password'];
        if(!$this->User->validate("password", $password)){
            return View::view(null, 'error', ['error' => join(" ",$this->User->validation_error)]);
        }
        $login = $_POST['name'];
        if(!$this->User->validate("login", $login)){
            return View::view(null, 'error', ['error' => join(" ", $this->User->validation_error)]);
        }

        if ($this->User->checkUserByLoginAndPass($login, $password)) {
            //удачная авторизация
            $this->User->login($login);
            redirect('users');
        } else {
            return View::view(null, 'error', ['error' => 'имя или пароль не нейдены или заданы некорректно']);
        }
    }

    public function register()
    {
        return View::view('Users', 'register', []);

    }

    public function logout(): void
    {
        //выход из системы
        $this->User->logout();
        redirect('articles');
    }

    /*
     * This function selects a the user from the users database and then grabs the user view to display them.
     */
    public function show($vars)
    {
        //Here we use the Query Builder to get the user:
        /*$user = App::DB()->selectAllWhere('users', [
            ['user_id', '=', $vars['id']],
        ]);
        */

        //Here we use the ORM to get the user:
        $user = $this->User->getUserById($vars['id']);

        if (empty($user)) {
            redirect('users');
        }
        return View::view('Users', 'view', compact('user'));
    }

    /*
     * This function inserts a new user into our database using array notation.
     */
    public function store(): void
    {
        App::DB()->insert('users', [
            'name' => $_POST['name']
        ]);
        $paginationConfig = App::Config()['pagination'];
        if ($paginationConfig['show_latest_page_on_add']) {
            $totalRecords = App::DB()->count('users');
            $recordsPerPage = $paginationConfig['per_page'] ?? 5;
            $lastPage = ceil($totalRecords / $recordsPerPage);
            redirect('users/' . $lastPage);
        } else {
            redirect('users');
        }
    }

    /*
     * This function updates a user from our database using array notation.
     */
    public function update($vars)
    {
        App::DB()->updateWhere('users', [
            'name' => $_POST['name']
        ], [
            ['user_id', '=', $vars['id']]
        ]);
        redirect('user/' . $vars['id']);
    }

    /*
     * This function deletes a user from our database.
     */
    /**
     * @throws \Exception
     */
    public function delete($vars)
    {
        App::DB()->deleteWhere('users', [
            ['user_id', '=', $vars['id']]
        ]);
        $paginationConfig = App::Config()['pagination'];
        if ($paginationConfig['show_latest_page_on_delete']) {
            $currentPage = $_GET['page'] ?? 1;
            $recordsPerPage = $paginationConfig['per_page'] ?? 5;
            $totalRecordsAfterDeletion = App::DB()->count('users');
            $lastPageAfterDeletion = max(ceil($totalRecordsAfterDeletion / $recordsPerPage), 1);
            if ($currentPage > $lastPageAfterDeletion) {
                $redirectPage = $lastPageAfterDeletion;
            } else {
                $redirectPage = $currentPage;
            }
            return redirect('users/' . $redirectPage);
        } else {
            return redirect('users');
        }
    }

    /**
     * @throws \Exception
     */
    public function create()
    {
        $password = $_POST['password'];
        if(!$this->User->validate("password", $password)){
            return View::view(null, 'error', ['error' => join(" ", $this->User->validation_error)]);
        }
        $login = $_POST['name'];
        if(!$this->User->validate("login", $login)){
            return View::view(null, 'error', ['error' => join(" ", $this->User->validation_error)]);
        }

        $check_user_exists = $this->User->checkUserExists($login);
        if ($check_user_exists) {
            return View::view(null, 'error', ['error' => 'пользователь с таким именем уже есть в базе']);
        }

        $this->User->createUser($login, $password);
        EmailComponent::sendEmail("backendspb@gmail.com", "регистрация", "привет");
        redirect('users');
    }
}
