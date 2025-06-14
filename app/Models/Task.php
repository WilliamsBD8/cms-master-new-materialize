<?php

namespace App\Models;

use CodeIgniter\Model;

class Task extends Model
{
    protected $table            = 'tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'task_state_id',
        'task_sprint_id',
        'task_activity_id',
        'task_user_id',
        'title',
        'description',
        'orden',
        'date_task',
        'date_state',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
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
    protected $beforeUpdate   = ["functionBeforeUpdate"];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ["functionAfterFind"];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function setFilter($data){
        // $data = (object) $data;

        if(isset($data->filter_task_activity)){
            $this->where("tasks.task_activity_id", $data->filter_task_activity == 0 ? null : $data->filter_task_activity);
        }

        if(isset($data->filter_task_company)){
            $this->where("tasks.task_company_id", $data->filter_task_company == 0 ? null : $data->filter_task_company);
        }

        if(isset($data->filter_task_user)){
            $this->where("tasks.task_user_id", $data->filter_task_user == 0 ? null : $data->filter_task_user);
        }

        if(isset($data->filter_task_sprint)){
            $this->where("tasks.task_sprint_id", $data->filter_task_sprint == 0 ? null : $data->filter_task_sprint);
        }

        if(isset($data->filter_task_date_init)){
            $this->where("tasks.date_task >=", $data->filter_task_date_init);
        }

        if(isset($data->filter_task_date_end)){
            $this->where("tasks.date_task <=", $data->filter_task_date_end);
        }
    }

    protected function functionBeforeUpdate(array $data){

        
        $task = $this->find($data['id'][0]);
        
        if($data['data']['task_user_id'] == null){
            $data['data']['task_state_id'] = 1;
        }else if($data['data']['task_user_id'] != $task->task_user_id)
            $data['data']['task_state_id'] = 2;
        
        if(isset($data['data']['task_state_id'])){
            if($task->task_state_id != $data['data']['task_state_id'])
                $data['data']['date_state'] = date('Y-m-d');
        }

        
        // echo json_encode($data);
        return $data;
    }

    protected function functionAfterFind(array $data){
        
        log_message("info", json_encode($data));
        if(isset($data['id'])){
            $data['data']->files = $this->builder('task_files')
                ->where([
                    'task_id' => $data['id']
                ])->get()->getResult();
        }else{
            foreach($data['data'] as $task){
                $task->files = $this->builder('task_files')
                    ->select('task_files.*, task_files.file as name')
                    ->where([
                        'task_id' => $task->id
                    ])->get()->getResult();
    
                foreach($task->files as $file){
                    $path = FCPATH . "uploads/task_{$task->id}/{$file->file}";
                    $file->base64 = base64_encode(file_get_contents($path));
                    $file->size = filesize($path);
                    $file->onDelete = false;
                }
            }
        }
        return $data;
    }
}
