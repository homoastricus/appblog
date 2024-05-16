<?php

/*
 * This is the Article controller. It adds returns the view for the article list portion of this framework.
 */

namespace App\Controllers;

use App\Components\UserComponent;
use App\Core\App;
use App\Core\Request;
use App\Core\View\View;
use App\Models\Article;
use App\Models\User;
use App\Controllers\AppController;
use App\Components\ArticleComponent;
use JetBrains\PhpStorm\NoReturn;
use App\Core\Service\RedisManager;

class ArticleController extends AppController
{

    private ArticleComponent $Article;

    public Request $request;
    private RedisManager $redis;

    #[NoReturn] public function __construct(Request $request)
    {
        $this->request = $request;
        $this->Article = new ArticleComponent();
        $this->redis = new RedisManager();
        parent::__construct();
    }
    /*
     * This function selects all the articles from the article database and then grabs the article view to display them.
     */
    /**
     * @throws \Exception
     */
    public function index($vars = [])
    {
        $article = new Article();
        $count = $article->count();
        $paginationConfig = App::Config()['pagination'];
        $limit = $paginationConfig['per_page'] ?? 5;
        $page = $vars['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        if (!$this->redis->get("articles") OR $this->redis->ttl('articles')<=0) {
            $articles = $article->where([['id', '>', '0']], $limit, $offset)->get();
            $art_data = json_encode($articles);
            $this->redis->setex("articles", 11, $art_data);
            //$this->redis->expire('articles', 40);
            $source = "database";
        } else {
            $art_data = $this->redis->get("articles");
            $articles = json_decode($art_data);
            $source = "redis";
        }

        $user_model = new User();
        $user_by_ids = $user_model->order('id', 'desc')
            ->where([['id', '>', '0']], "", "", 'id', 'desc')->get();
        $user_ids = [];

        foreach ($user_by_ids as $user) {
            $user_ids[$user->id] = $user->name;
        }
        $currentUser = (new UserComponent())->getCurrentUser();
        $currentUserId = is_null($currentUser) ? null : $currentUser->id();


        return View::view('Articles', 'list',
            compact('articles', 'count', 'page', 'limit', 'user_ids', 'currentUserId', 'source'));
    }

    /*
     * This function selects the user from the users database and then grabs the user view to display them.
     */
    /**
     * @throws \Exception
     */
    public function show($vars)
    {
        $article = $this->Article->getArticleById($vars['id']);
        $current_user = (new UserComponent())->getCurrentUser();
        $isLiked = false;
        if ($current_user) {
            //print_r($current_user);
            $user_id = $current_user->id();
            $isLiked = $this->Article->isLiked($vars['id'], $user_id);
        }

        if (empty($article)) {
            return View::view(null, 'error', ['error' => 'данная статья не найдена в блоге']);
        }
        return View::view('Articles', 'view', compact('article', 'isLiked'));
    }

    // редактирование статьи блога
    public function edit($vars)
    {
        //Here we use the ORM to get the article:
        $article = $this->Article->getArticleById($vars['id']);

        if (empty($article)) {
            return View::view(null, 'error', ['error' => 'данная статья не найдена в блоге']);
        }
        return View::view('Articles', 'edit', compact('article'));
    }

    /*
     * This function inserts a new user into our database using array notation.
     */
    /**
     * @throws \Exception
     */
    public function create()
    {
        $name = $_POST['name'];
        if (!$this->Article->validate("name", $name)) {
            return View::view(null, 'error', ['error' => join(" ", $this->Article->validation_error)]);
        }
        $text = $_POST['text'];
        if (!$this->Article->validate("text", $text)) {
            return View::view(null, 'error', ['error' => join(" ", $this->Article->validation_error)]);
        }

//        $file = $_FILES['image'];
//        print_r($_FILES);
//        $public_path = ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
//        if (move_uploaded_file($file['tmp_name'], $public_path . $file['name'])) {
//            require_once ($public_path . $file['name']);
//        }
//        exit;

        $result = $this->Article->createArticle($name, $text);
        if ($result) {
            redirect('article/' . $result);
        } else {
            return View::view(null, 'error', ['error' => 'Ошибка создания статьи']);
        }

    }

    /*
     * This function updates a user from our database using array notation.
     */
    /**
     * @throws \Exception
     */
    public function update($vars)
    {
        $name = $_POST['name'];
        if (!$this->Article->validate("name", $name)) {
            return View::view(null, 'error', ['error' => join(" ", $this->Article->validation_error)]);
        }
        $text = $_POST['text'];
        if (!$this->Article->validate("text", $text)) {
            return View::view(null, 'error', ['error' => join(" ", $this->Article->validation_error)]);
        }

        $this->Article->updateArticle($name, $text, $vars['id']);
        redirect('article/' . $vars['id']);

        return View::view(null, 'error', ['error' => 'Ошибка создания статьи']);
    }

    /*
     * This function deletes a user from our database.
     */
    /**
     * @throws \Exception
     */
    public function delete($vars)
    {
        App::DB()->deleteWhere('articles', [
            ['id', '=', $vars['id']]
        ]);
        $paginationConfig = App::Config()['pagination'];
        if ($paginationConfig['show_latest_page_on_delete']) {
            $currentPage = $_GET['page'] ?? 1;
            $recordsPerPage = $paginationConfig['per_page'] ?? 5;
            $totalRecordsAfterDeletion = App::DB()->count('articles');
            $lastPageAfterDeletion = max(ceil($totalRecordsAfterDeletion / $recordsPerPage), 1);
            if ($currentPage > $lastPageAfterDeletion) {
                $redirectPage = $lastPageAfterDeletion;
            } else {
                $redirectPage = $currentPage;
            }
            return redirect('articles/' . $redirectPage);
        } else {
            return redirect('articles');
        }
    }

    public function like($vars)
    {
        //Here we use the ORM to get the article:
        $article = $this->Article->getArticleById($vars['id']);

        if (empty($article)) {
            return View::view(null, 'error', ['error' => 'данная статья не найдена в блоге']);
        }
        $current_user = (new UserComponent)->getCurrentUser();
        $result = $this->Article->likeArticle($vars['id'], $current_user->id());
        echo json_encode(['result' => $result]);
        exit;
    }
}
