<?php

use App\Core\App;

//If the array notation routing config option in config.php is set to true, let's use array notation routing.
try {
    if (App::get('config')['options']['array_routing']) {
        $router->getArray([
            '' => 'PagesController@home',
            'about' => 'PagesController@about',
            'contact' => 'PagesController@contact',
            'users' => 'UsersController@index',
            'users/{page}' => 'UsersController@index',
            'user/{id}' => 'UsersController@show',
            'user/delete/{id}' => 'UsersController@delete',
            'articles', 'ArticleController@index',
            'article/{id}' => 'ArticleController@show',
            'article/edit/{id}' => 'ArticleController@edit',
            'article/delete/{id}' => 'ArticleController@delete',
            'user/logout' => 'UsersController@logout',
            'articles/{page}' => 'ArticleController@index',
        ]);
        $router->postArray([
            'users' => 'UsersController@store',
            'user/update/{id}' => 'UsersController@update',
            'article/create' => 'ArticleController@create',
            'article/update/{id}' => 'ArticleController@update',
            'user/login' => 'UsersController@login',
            'user/create' => 'UsersController@create',

        ]);
    } else {
        $router->get('', 'ArticleController@index');
        $router->get('about', 'PagesController@about');
        $router->get('contact', 'PagesController@contact');
        $router->get('articles', 'ArticleController@index');
        $router->get('article/{id}', 'ArticleController@show');
        $router->get('article/edit/{id}', 'ArticleController@edit');
        $router->get('user/logout', 'UsersController@logout');
        $router->get('register', 'UsersController@register');

        $router->get('users', 'UsersController@index');
        $router->get('users/{page}', 'UsersController@index');
        $router->get('user/{id}', 'UsersController@show');
        $router->get('user/delete/{id}', 'UsersController@delete');
        $router->get('articles/{page}', 'ArticleController@index');
        $router->get('article/delete/{id}', 'ArticleController@delete');
        $router->get('article/like/{id}', 'ArticleController@like');

        $router->post('users', 'UsersController@store');
        $router->post('user/update/{id}', 'UsersController@update');
        $router->post('article/create', 'ArticleController@create');
        $router->post('article/update/{id}', 'ArticleController@update');
        $router->post('user/login', 'UsersController@login');
        $router->post('user/create', 'UsersController@create');

    }
} catch (Exception $e) {
    echo $e->getMessage();
}
