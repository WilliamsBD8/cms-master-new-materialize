<?php


namespace App\Models;


use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'username', 'email', 'status', 'role_id', 'photo', 'id'];
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = ["functionBeforeFind"];
    protected $afterFind      = ["functionAfterFind"];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function functionAfterFind(array $data)
    {
        if(isset($data['data']->id)){
            $data['data']->password = $this->builder('passwords')->where(['user_id' => $data['data']->id, 'status' => 'active'])->get()->getResult()[0];
        }

        return $data;
    }


    protected function functionBeforeFind(array $data)
    {
        $builder = $this->builder();
        $builder
        ->select(
            ['users.*', 'roles.name as role_name']
        )->join('roles', 'roles.id = users.role_id', 'left');

        return $data;
    }


}