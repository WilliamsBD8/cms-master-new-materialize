<?php


namespace App\Models;


use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'username', 'email', 'status', 'role_id', 'photo', 'id'];
    protected $returnType       = 'object';

    public function getPassword($id_user){
        $data = $this->builder('passwords')->where(['user_id' => $id_user, 'status' => 'active'])->get()->getResult();
        return $data[0];
    }

}