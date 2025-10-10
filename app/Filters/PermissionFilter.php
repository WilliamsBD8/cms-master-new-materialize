<?php


namespace App\Filters;


use App\Models\Permission;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {

        $request = Services::request();
        $url = $request->uri->getSegment(1);
        $method =  $request->uri->getSegment(2);
        $permission = new Permission();

        if($url == 'table' || $url == 'config') {
            $data = $permission->select('*')
                ->join('menus', 'menus.id = permissions.menu_id')
                ->join('roles', 'roles.id = permissions.role_id')
                ->where(['menus.url' =>  $method, 'role_id' => session('user')->role_id ] )
                ->get()
                ->getResult();
                

            if(count($data) == 0 && session('user')->role_id != 1) {
                if(session('user')->role_id != 2 && $method != 'config'){
                    echo  view('errors/html/error_401');
                    exit;
                }
            }
        }else if($url == 'dashboard' && $method != 'perfile'){
            $urls = $request->uri->getSegments();
            if(count($urls) > 1){
                $method = $request->getMethod();
                array_shift($urls);
                if (is_numeric(end($urls))) {
                    array_pop($urls);
                }

                switch (end($urls)) {
                    case 'create':
                    case 'update':
                    case 'pdf':
                    case 'indicators':
                        array_pop($urls);
                        break;
                    
                    default:
                        break;
                }

                $url_option = implode("/", $urls);
                $permission->select('*')
                    ->join('menus', 'menus.id = permissions.menu_id')
                    ->join('roles', 'roles.id = permissions.role_id')
                    ->where(['menus.url' =>  $url_option, 'role_id' => session('user')->role_id ] );

                switch ($method) {
                    case 'get':
                        $permission->where(['permissions.read' => 1]);
                        break;
                    case 'post':
                        $permission->where(['permissions.created' => 1]);
                        break;
                    case 'put':
                        $permission->where(['permissions.updated' => 1]);
                        break;
                    case 'delete':
                        $permission->where(['permissions.delete' => 1]);
                        break;
                    
                    default:
                    
                        break;
                }
                $data = $permission->first();
                
                if(empty($data) && session('user')->role_id != 1) {
                    echo  view('errors/html/error_401');
                    exit;
                }
            }
        } else {
            if($url != 'home') {

                $data = $permission->select('*')
                    ->join('menus', 'menus.id = permissions.menu_id')
                    ->join('roles', 'roles.id = permissions.role_id')
                    ->where(['menus.url' => $url . '/' . $method, 'role_id' => session('user')->role_id])
                    ->get()
                    ->getResult();
                if (!$data && session('user')->role_id != 1) {
                    echo  view('errors/html/error_401');
                    exit;
                }
            }
        }


    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // TODO: Implement after() method.
    }
}