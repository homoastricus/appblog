<?php
/*
 * This is the starting point for a Comment model.
 */
namespace App\Models;

use App\Core\Database\Model;

class Comment extends Model
{
    protected static string $table = 'comments';
    
    public int $id;

    public int $article_id;

    public int $user_id;
    
    public string $comment;

    public string $created;
}
