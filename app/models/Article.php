<?php
/*
 * This is the starting point for a Article model.
 */
namespace App\Models;

use App\Core\Database\Model;

class Article extends Model
{
    protected static string $table = 'articles';
    
    public int $id;

    public int $owner_id;
    
    public string $name;

    public string $text;

    public string $created;
}
