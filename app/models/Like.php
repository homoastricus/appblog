<?php
/*
 * This is the starting point for a Like model.
 */
namespace App\Models;

use App\Core\Database\Model;

class Like extends Model
{
    protected static string $table = 'likes';
    
    public int $id;

    public int $article_id;

    public int $user_id;

    public string $created;
    
}
