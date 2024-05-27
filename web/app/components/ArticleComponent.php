<?php

namespace App\Components;

use App\Core\Components\Component;

use App\Core\App;
use App\Core\Database\Model;
use App\Models\Article;
use App\Core\Service\SecurityManager;
use App\Core\Service\SessionManager;
use App\Core\Service\ValidationManager;
use App\Components\UserComponent as User;
use App\Models\Like;
use Exception;

class ArticleComponent extends Component
{

    public array $validation_error = [];

    private array $validate_rules = [
        'name' => ['trim' => true, 'min_length' => 3, 'max_length' => 255],
        'text' => ['trim' => true, 'min_length' => 6, 'max_length' => 255],
        'owner_id' => ['trim' => true, 'min_length' => 1, 'max_length' => 255]
    ];

    public function validate(string $field, string $value): bool
    {
        $validated = ValidationManager::validate($this->validate_rules[$field], $value);
        if (!$validated['result']) {
            $this->validation_error = $validated['errors'];
            return false;
        }
        return true;
    }


    /**
     * @param $id
     * @return array
     */
    public function getArticleById($id): array
    {
        //Here we use the Query Builder to get the user:
        /*$user = App::DB()->selectAllWhere('users', [
            ['user_id', '=', $vars['id']],
        ]);
        */
        $article = new Article();
        $foundArticle = $article->find($id);
        return $foundArticle ? $foundArticle->get() : [];
    }

    /**
     * @param string $name
     * @param string $text
     * @return mixed
     * @throws Exception
     */
    public function createArticle(string $name, string $text): mixed
    {
        $current_user = (new User)->getCurrentUser();
        return App::DB()->insert('articles', [
            'name' => $name,
            'text' => $text,
            'owner_id' => $current_user->id(),
            'created' => now_date(),
        ]);
    }

    /**
     * @param string $name
     * @param string $text
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function updateArticle(string $name, string $text, int $id): void
    {
        App::DB()->updateWhere('articles', [
            'name' => $name,
            'text' => $text,
        ], [
            ['id', '=', $id]
        ]);
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return string
     * @throws Exception
     */
    public function likeArticle(int $article_id, int $user_id): string
    {
        $likes = $this->getLikeByUserAndArticle($article_id, $user_id);
        if ($likes->first() == null) {
            App::DB()->insert('likes', [
                'article_id' => $article_id,
                'user_id' => $user_id,
                'created' => now_date()
            ]);
            return "like";
        } else {
            App::DB()->deleteWhere('likes', [
                ['id', '=', $likes->first()->id()]
            ]);
            return "unlike";
        }
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return bool
     * @throws Exception
     */
    public function isLiked(int $article_id, int $user_id): bool
    {
        $likes = $this->getLikeByUserAndArticle($article_id, $user_id);//$article_id
        //echo $likes->first()->count();
        if ($likes->first() == null) {
            return false;
        }
        return true;
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return Like|Model
     * @throws Exception
     */
    private function getLikeByUserAndArticle(int $article_id, int $user_id): Like|\App\Core\Database\Model
    {
        return  (new Like())->where([['article_id', '=', $article_id], ['user_id', '=', $user_id]], "", "");
    }
}