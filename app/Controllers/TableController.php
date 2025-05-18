<?php


namespace App\Controllers;


use App\Traits\Grocery;
use App\Models\Menu;
use CodeIgniter\Exceptions\PageNotFoundException;

class TableController extends BaseController
{
    use Grocery;

    private $crud;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        // $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
    }

    public function index($data)
    {
        $menu = new Menu();
        $component = $menu->where(['url' => $data, 'component' => 'table'])->get()->getResult();



        if($component) {
            $this->crud->setTable($component[0]->table);
            switch ($component[0]->url) {
                case 'usuarios':
                    $this->crud->setActionButton('Algo mas que aqeullo', 'fa fa-bars', function ($row) {
                        return base_url(['table', 'info_creditos', $row->id]);
                    }, false);
                    
                    $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
                    $this->crud->setRelation('role_id', 'roles', 'name');
                    $this->crud->displayAs([
                        'name'  => 'Nombre',
                        'photo' => 'Foto'
                    ]);
                    break;
                case 'menus':
                    $this->crud->setTexteditor(['description']);
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

            $this->viewTable($output, $component[0]->title, $component[0]->description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
