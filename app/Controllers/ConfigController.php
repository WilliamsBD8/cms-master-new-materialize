<?php


namespace App\Controllers;


use App\Models\Password;

use App\Models\Configuration;
use App\Traits\Grocery;

class ConfigController extends BaseController
{
    use Grocery;

    private $crud;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
    }

    public function index($data)
    {

        $this->crud->setTable($data);
        switch ($data) {
            case 'users':
                $title = 'Usuarios';
                $subtitle = 'Listado de usuarios.';
                $this->crud->setActionButton('Contraseñas', 'fa fa-lock', function ($row) {
                    return base_url(['config', 'users', $row->id]);
                }, false);
                
                // $this->crud->unsetColumns(['password']);
                // $this->crud->fieldType('password', 'password');
                // $this->crud->callbackBeforeInsert(function ($stateParameters) {
                //     $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                //     return $stateParameters;
                // });
                // $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                //     if(strlen($stateParameters->data['password']) < 20) {
                //         $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                //     }
                //     return $stateParameters;
                // });
                $this->crud->setRelation('role_id', 'roles', 'name');
                $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');

                break;
            case 'permissions':
                $title = 'Permisos';
                $subtitle = 'Listado de permisos.';
                $this->crud->setRelation('role_id', 'roles', 'name');
                $this->crud->setRelation('menu_id', 'menus', '{option} - {type}');
                break;
            case 'menus':
                $title = 'Opciones del Menu';
                $subtitle = 'Listado de opciones de menu.';
                // $this->crud->setTexteditor(['description']);
                $this->crud->setRelation('references', 'menus', 'option');
                break;
            case 'roles':
                $title = 'Roles';
                $subtitle = 'Listado de roles.';
                break;
            case 'notifications':
                $title = 'Notificaciones';
                $subtitle = 'Listado de Notificaciones.';
                $id = session()->get('user');
                $this->crud->fieldType('user_id', 'hidden', $id->id );
                break;
            case 'configurations':
                $title = 'Configuraciones';
                $subtitle = 'Listado de configuraciones.';
                $config = new Configuration();
                $data = $config->findAll();
                $this->crud->setTexteditor(['footer', 'intro']);
                $this->crud->setFieldUpload('background_image', 'assets/img', base_url().'/assets/img');
                $this->crud->setFieldUpload('favicon', 'assets/img', base_url().'/assets/img');
                $this->crud->setFieldUpload('background_img_vertical', 'assets/img', base_url().'/assets/img');

                if (count($data)  > 0) {
                    $this->crud->unsetAdd();
                    $this->crud->unsetDelete();
                }

                if(session('user')->role_id == 2){
                    $this->curd->columns(['register', 'captcha', 'primary_color', 'secundary_color']);
                    $this->crud->displayAs([
                        'register'          => 'Registro',
                        'primary_color'     => 'Color Primario',
                        'secundary_color'   => 'Color secundario'
                    ]);
                }

                $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                    $stateParameters->data['primary_color'] = trim($stateParameters->data['primary_color']);
                    $stateParameters->data['secundary_color'] = trim($stateParameters->data['secundary_color']);
                    return $stateParameters;
                });
                break;
        }
        $output = $this->crud->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

        $this->viewTable($output, $title, $subtitle);
    }
    
    public function detail($data, $id)
    {
        $title = '';
        $description = '';
        $this->id = $id;
        if($data) {
            
            switch ($data) {
                case 'users':
                    $this->crud->setTable('passwords');
                    $this->crud->where(['user_id' => $this->id]);
                    $this->crud->unsetDelete();
                    $this->crud->unsetEdit();
                    $this->crud->unsetColumns(['password', 'user_id', 'updated_at']);
                    $this->crud->fieldType('password', 'password');
                    $this->crud->addFields(['password']);
                    $this->crud->callbackBeforeInsert(function ($info){
                        $info->data['created_at']   = date('Y-m-d H:i:s');
                        $info->data['updated_at']   = date('Y-m-d H:i:s');
                        $info->data['user_id']      = $this->id;
                        $info->data['temporary']    = 'Si';
                        $info->data['password']     = password_hash($info->data['password'], PASSWORD_DEFAULT);
                        $p_model = new Password();
                        $passwords = $p_model->where(['user_id' => $this->id, 'status' => 'active'])->findAll();
                        foreach ($passwords as $key => $password) {
                            $p_model->save([
                                'id'        => $password->id,
                                'status'    => 'inactive'
                            ]);
                        }
                        return $info;
                    });

                    $this->crud->displayAs([
                        'attempts'      => 'N° Intentos',
                        'status'        => 'Estado',
                        'created_at'    => 'Fecha de creación',
                        'password'      => 'Contraseña',
                        'temporary'     => 'Temporal'
                    ]);
                    break;
                default:
                    break;   
            }
            

            $output = $this->crud->render();
            if (isset($output->isJSONResponse) && $output->isJSONResponse) {
                header('Content-Type: application/json; charset=utf-8');
                echo $output->output;
                exit;
            }

            $this->viewTable($output, $title, $description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }


}