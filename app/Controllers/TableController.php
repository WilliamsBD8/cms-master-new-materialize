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
            $this->crud->callbackBeforeInsert(function ($stateParameters) {
                $stateParameters->data['created_at'] = date('Y-m-d H:i:s');
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');

                return $stateParameters;
            });
            $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                $stateParameters->data['updated_at'] = date('Y-m-d H:i:s');
                return $stateParameters;
            });
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

                case 'task_states':
                    $select_colors = ["color_background", "color_font"];

                    $this->crud->callbackColumn("name", function($value, $row){
                        $colores_font = explode(" ", $row->color_font);
                        $colores_font = 'text-' . implode(" text-", $colores_font);

                        return "<div class='badge rounded-pill $row->color_background $colores_font'>$value</div>";
                    });

                    $colors = getColors(true);

                    foreach ($select_colors as $key => $value) {

                        $this->crud->callbackColumn($value, function($value, $row){
                            return "<div style='padding:15px' class='$value'></div>";
                        });

                        $this->crud->callbackAddField($value, function () use ($colors, $value) {
                            $html = '<div class="form-floating form-floating-outline select-colors"> <select name="'.$value.'" class="form-floating form-floating-outline select2">';
                            $html .= '<option value="">Seleccionar valor</option>';
                            foreach ($colors as $color) {
                                $colores_font = explode(" ", $color->name);
                                $colores_font = 'text-' . implode(" text-", $colores_font);
                                
                                $html .= '<option value="' . $color->name . '" data-id="'.$color->value.'" class="'.$colores_font.'">' . $color->name . '</option>';
                            }
                            $html .= '</select></div>';
                            return $html;
                        });
        
                        $this->crud->callbackEditField($value, function ($fieldValue, $primaryKey) use ($colors, $value) {
                            $html = '<div class="form-floating form-floating-outline select-colors"><select name="'.$value.'" class="form-floating form-floating-outline select2">';
                            $html .= '<option value="">Seleccionar valor</option>';
                            foreach ($colors as $color) {
                                $colores_font = explode(" ", $color->name);
                                $colores_font = 'text-' . implode(" text-", $colores_font);
                                $selected = ($color->name === $fieldValue) ? 'selected' : '';
                                $html .= '<option value="' . $color->name . '" class="'.$colores_font.'" ' . $selected . '>' . $color->name . '</option>';
                            }
                            $html .= '</select></div>';
                            return $html;
                        });
                    }
                    $this->crud->unsetEditFields(['created_at', 'updated_at', 'deleted_at']);
                    $this->crud->unsetAddFields(['created_at', 'updated_at', 'deleted_at']);
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
