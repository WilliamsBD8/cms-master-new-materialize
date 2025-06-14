<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\API\ResponseTrait;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskSprint;
use App\Models\TaskState;
use App\Models\TaskFile;

class TaskController extends BaseController
{
    use ResponseTrait;

    private $users;
    private $companies;
    private $taskStates;
    private $taskActivities;
    private $taskSprints;
    private $tf_model;

    public function __construct(){
        
        $u_model = new User();
        $ta_model = new TaskActivity();
        $ts_model = new TaskState();
        $tsp_model = new TaskSprint();
        $this->tf_model = new TaskFile();
        

        $this->users = $u_model->where(['users.status' => 'Active'])->orderBy('users.name', 'ASC')->asObject()->findAll();

        $this->taskStates       = $ts_model->findAll();
        $this->taskActivities   = $ta_model->findAll();
        $this->taskSprints      = $tsp_model->findAll();
    }

    public function index()
    {
        
        return view('tasks/index', [
            'states'        => $this->taskStates,
            'activities'    => $this->taskActivities,
            'users'         => $this->users
        ]);
    }

    public function list(){
        return view('tasks/list', [
            'users'         => $this->users,
            'states'        => $this->taskStates,
            'activities'    => $this->taskActivities,
            'taskSprints'   => $this->taskSprints
        ]);
    }

    public function tablero(){
        return view('tasks/tablero', [
            'users'         => $this->users,
            'states'        => $this->taskStates,
            'activities'    => $this->taskActivities,
            'taskSprints'   => $this->taskSprints
        ]);
    }


    public function dataList(){

        $t_model = new Task();
        $filter = (object) $this->request->getGet();
        $t_model->setFilter($filter);
        $tasks = $t_model
            ->select([
                'tasks.*',

                'ts.name as task_state_name',

                'ta.name as task_activity_name',
                'ta.date_start as task_date_start',
                'ta.date_end as task_date_end',

                'ts.color_background as task_state_color_background',
                'ts.color_font as task_state_color_font',

                'tsp.name as sprint',

                'u.name as username',
            ])
            ->join('task_states as ts', 'ts.id = tasks.task_state_id', 'left')
            ->join('task_activities as ta', 'ta.id = tasks.task_activity_id', 'left')
            ->join('users as u', 'u.id = tasks.task_user_id', 'left')
            ->join('task_sprints as tsp', 'tsp.id = tasks.task_sprint_id', 'left')
            ->orderBy('orden', 'DESC')
            ->orderBy('id', 'DESC')
        ->findAll();
        return $this->respond([
            'tasks' => $tasks
        ]);
    }

    public function store(){
        try{
            $data = $this->request->getJson();

            $t_model = new Task();
            $task_orden = $t_model->selectMax('orden')->first();

            $task = [
                'task_state_id'     => !empty($data->add_task_user) ? 2 : 1,
                'task_sprint_id'    => !empty($data->add_task_sprint) ? $data->add_task_sprint : null,
                'task_activity_id'  => !empty($data->add_task_activity) ? $data->add_task_activity : null,
                'task_user_id'      => !empty($data->add_task_user) ? $data->add_task_user : null,
                'task_company_id'   => !empty($data->add_task_company) ? $data->add_task_company : null,
                'title'             => $data->add_task_title,
                'description'       => !empty($data->add_task_description) ? $data->add_task_description : null ,
                'date_task'         => !empty($data->add_task_date) ? $data->add_task_date : null,
                'date_state'        => null,
                'orden'             => empty($task_orden) ? 1 : (int) $task_orden->orden + 1
            ];

            $t_model = new Task();
            $t_model->save($task);

            $task_id = $t_model->insertID();

            if(!empty($data->files)){
                foreach($data->files as $file){
                    
                    $name = uniqid()."_".$file->name;

                    $base64 = $file->base64 ?? '';

                    if (strpos($base64, 'base64,') !== false) {
                        $base64 = explode('base64,', $base64)[1];
                    }

                    $carpeta = FCPATH . "uploads/task_{$task_id}";
                    $rutaArchivo = $carpeta . "/" . $name;

                    // Si la carpeta no existe, crearla
                    if (!is_dir($carpeta)) {
                        if (!mkdir($carpeta, 0755, true)) {
                            return $this->respond(['title' => 'Error en el servidor', 'error' => 'No se pudo crear la carpeta.'], 500);
                        }
                    }

                    // Decodificar
                    $contenido = base64_decode($base64);

                    if (file_put_contents($rutaArchivo, $contenido)) {
                        $this->tf_model->save([
                            'task_id'   => $task_id,
                            'file'      => $name
                        ]);
                    } else {
                        return $this->respond(['title' => 'Error en el servidor', 'error' => 'No se pudo guardar el archivo.'], 500);
                    }

                }
            }

            return $this->respond([
                'data' => $data
            ]);
        }catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
		}
    }

    public function updated(){
        try{
            $data = $this->request->getJson();

            $task = [
                'id'                => $data->task_id,
                'task_sprint_id'    => !empty($data->edit_task_sprint) ? $data->edit_task_sprint : null,
                'task_activity_id'  => !empty($data->edit_task_activity) ? $data->edit_task_activity : null,
                'task_user_id'      => !empty($data->edit_task_user) ? $data->edit_task_user : null,
                'title'             => $data->edit_task_title,
                'description'       => !empty($data->edit_task_description) ? $data->edit_task_description : NULL,
                'date_task'         => !empty($data->edit_task_date) ? $data->edit_task_date : null,
            ];

            if(!empty($data->edit_task_orden))
                $task["orden"] = $data->edit_task_orden;
            if(!empty($data->edit_task_state))
                $task["task_state_id"] = $data->edit_task_state;
            

            $t_model = new Task();
            if($t_model->save($task)){
                if(!empty($data->files)){
                    foreach($data->files as $file){
                        if(isset($file->id) && $file->onDelete){
                            $this->tf_model->delete($file->id);
                        }else if(!isset($file->id)){
                            $name = uniqid()."_".$file->name;
                            $base64 = $file->base64 ?? '';

                            if (strpos($base64, 'base64,') !== false) {
                                $base64 = explode('base64,', $base64)[1];
                            }

                            $carpeta = FCPATH . "uploads/task_{$data->task_id}";
                            $rutaArchivo = $carpeta . "/" . $name;

                            // Si la carpeta no existe, crearla
                            if (!is_dir($carpeta)) {
                                if (!mkdir($carpeta, 0755, true)) {
                                    return $this->respond(['title' => 'Error en el servidor', 'error' => 'No se pudo crear la carpeta.'], 500);
                                }
                            }

                            // Decodificar
                            $contenido = base64_decode($base64);

                            if (file_put_contents($rutaArchivo, $contenido)) {
                                $this->tf_model->save([
                                    'task_id'   => $data->task_id,
                                    'file'      => $name
                                ]);
                            }
                        }
                    }
                }
                // $task = $t_model->find($data->task_id);
                
                // $task = $t_model
                //     ->select([
                //         'tasks.*',
    
                //         'ts.name as task_state_name',
    
                //         'ta.name as task_activity_name',
                //         'ta.date_start as task_activity_date_start',
                //         'ta.date_end as task_activity_date_end',
    
                //         'ts.color as task_state_color',
    
                //         'tsp.name as sprint',
    
                //         'u.name as username',
                //         'c.company'
                //     ])
                //     ->join('tasks_states as ts', 'ts.id = tasks.task_state_id', 'left')
                //     ->join('tasks_activities as ta', 'ta.id = tasks.task_activity_id', 'left')
                //     ->join('users as u', 'u.id = tasks.task_user_id', 'left')
                //     ->join('companies as c', 'c.id = tasks.task_company_id', 'left')
                //     ->join('tasks_sprints as tsp', 'tsp.id = tasks.task_sprint_id', 'left')
                //     ->orderBy('orden', 'DESC')
                //     ->orderBy('id', 'DESC')
                //     ->where(['tasks.id' => $data->task_id])
                // ->first();
                return $this->respond([
                    'data'  => $data,
                    // 'task'  => $task
                ]);
            }

            return $this->respond(['title' => 'Error en el servidor', 'error' => "No se pudo actualizar la tarea"], 500);
            

        }catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
		}
    }

}
