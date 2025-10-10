<?php


namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;

use App\Models\User;
use App\Models\Role;
use Config\Services;


class UserController extends BaseController
{
    use ResponseTrait;

    public function perfile()
    {
		$r_model = new Role();
        $validation = Services::validation();
        $user = new User();
        $data = $user->find(session('user')->id);
        $roles = $r_model->findAll();

        return view('users/perfile', [
            'data'          => $data,
            'validation'    => $validation,
			'roles' 	    => $roles,
        ]);
    }

    public function updateUser()
    {
        try{
			$data = $this->request->getJson();
			$validation = \Config\Services::validation();
			$id = session('user')->id;

			$rules = [
				'name'      => 'required|min_length[3]|max_length[50]',
				'username'  => "required|alpha_numeric|min_length[4]|is_unique[users.username,id,{$id}]",
				'email'     => "required|valid_email|is_unique[users.email,id,{$id}]"
			];

			$messages = [
				'email' => [
					'is_unique' => 'El correo electrónico ya está registrado por otro usuario.'
				],
				'username' => [
					'is_unique' => 'El nombre de usuario ya está en uso.'
				]
			];

			if (!$validation->setRules($rules, $messages)->run((array) $data)) {
				return $this->respond([
					'status' 	=> 'error',
					'title'		=> 'Validación fallida '. $id,
					'errors' 	=> $validation->getErrors()
				], 200);
			}

			$user = [
				'id'		=> $id,
				'name'		=> $data->name,
				'username'	=> $data->username,
				'email'		=> $data->email
			];

			if($id == 1){
				$user['role_id'] = $data->rol;
			}

			$u_model = new User();
			$u_model->save($user);

			$info = $u_model->find($id);
			$session = session();
			$session->set('user', $info);

			return $this->respond([
				'status' => 'success',
				'message' => 'Datos de perfil actualizados correctamente.'
			], 200);

		}catch(\Exception $e){
			return $this->respond(['title' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
		}
    }


    public function updatePhoto()
    {
        $user = new User();
        if($img = $this->request->getFile('photo')){
            $newName = $img->getRandomName();
            $img->move('upload/images', $newName);
        }
        if($user->update(['photo' => $newName], ['id' => session('user')->id])){
            session('user')->photo = $newName;
            return redirect()->to('/perfile');
        }
    }

    public function deleteUser($id){
        $u_model = new User();
        $u_model->delete($id);
        session()->destroy();
        return redirect()->to(base_url(['login']));
    }
}