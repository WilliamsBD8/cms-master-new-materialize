<?php


namespace App\Models;


use CodeIgniter\Model;

class Menu extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'menus';

    protected $returnType       = 'object';
}