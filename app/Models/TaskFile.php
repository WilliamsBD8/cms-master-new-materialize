<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskFile extends Model
{
    protected $table            = 'task_files';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['task_id', 'file'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ["deleteFile"];
    protected $afterDelete    = [];

    protected function deleteFile(array $data){
        $file = $this->find($data['id']);
        $file = reset($file);
        if($file){
            unlink(FCPATH . "uploads/task_{$file->task_id}/{$file->file}");
        }
        return $data;
    }
}
