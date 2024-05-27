<?php
/*
 * This is the starting point for a User model.
 */
namespace App\Models;

use App\Core\Database\Model;

class User extends Model
{
    protected static string $table = 'users';
    
    public int $id;
    
    public string $name;

    public string $password;

    public string $created;
    
}
