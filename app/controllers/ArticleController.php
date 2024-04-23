<?php

/*
 * This is the Article controller. It adds returns the view for the article list portion of this framework.
 */

namespace App\Controllers;

use App\Core\App;
use App\Models\Article;
use App\Models\User;

class ArticleController
{

    /*
     * This function selects all the articles from the article database and then grabs the article view to display them.
     */
    public function index($vars = [])
    {
        $article = new Article();
        $count = $article->count();
        $paginationConfig = App::Config()['pagination'];
        $limit = $paginationConfig['per_page'] ?? 5;
        $page = $vars['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $articles = $article->where([['id', '>', '0']], $limit, $offset)->get();
        return view('articles', compact('articles', 'count', 'page', 'limit'));
    }

    /*
     * This function selects the user from the users database and then grabs the user view to display them.
     */
    public function show($vars)
    {
        //Here we use the Query Builder to get the user:
        /*$user = App::DB()->selectAllWhere('users', [
            ['user_id', '=', $vars['id']],
        ]);
        */

        //Here we use the ORM to get the user:
        $user = new User();
        $foundUser = $user->find($vars['id']);
        $user = $foundUser ? $foundUser->get() : [];

        if (empty($user)) {
            redirect('users');
        }
        return view('user', compact('user'));
    }

    /*
     * This function inserts a new user into our database using array notation.
     */
    public function store()
    {
        App::DB()->insert('users', [
            'name' => $_POST['name']
        ]);
        $paginationConfig = App::Config()['pagination'];
        if ($paginationConfig['show_latest_page_on_add']) {
            $totalRecords = App::DB()->count('users');
            $recordsPerPage = $paginationConfig['per_page'] ?? 5;
            $lastPage = ceil($totalRecords / $recordsPerPage);
            return redirect('users/' . $lastPage);
        } else {
            return redirect('users');
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
        return redirect('user/' . $vars['id']);
    }

    /*
     * This function deletes a user from our database.
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
}

?>
