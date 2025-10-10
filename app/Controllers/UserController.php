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
		helper(['filesystem']);
        $user = new User();

		$data = $this->request->getJSON();
    	$base64 = $data->photo ?? null;

        if (!$base64) {
			return $this->respond([
				'status' => 'error',
				'message' => 'No se recibió ninguna imagen'
			]);
		}

		// Validar formato base64
		if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
			$type = strtolower($type[1]);
			if (!in_array($type, ['png', 'jpg', 'jpeg', 'webp'])) {
				return $this->respond([
					'status' => 'error',
					'message' => 'Formato de imagen no permitido'
				]);
			}
	
			// Decodificar y guardar archivo
			$base64 = substr($base64, strpos($base64, ',') + 1);
			$base64 = base64_decode($base64);
	
			$newName = uniqid('user_') . '.' . $type;
			$filePath = FCPATH . 'upload/images/' . $newName;
			write_file($filePath, $base64);
	
			// Actualizar base de datos
			$updated = $user->save(
				[
					'id'	=> session('user')->id,
					'photo' => $newName
				]
			);
	
			if ($updated) {
				// Actualizar la sesión
				$session = session();
				$userData = $session->get('user');
				$userData->photo = $newName;
				$session->set('user', $userData);
	
				return $this->respond([
					'status' => 'success',
					'message' => 'Foto de perfil actualizada correctamente',
					'file' => $newName
				]);
			}
	
			return $this->respond([
				'status' => 'error',
				'message' => 'No se pudo actualizar la foto'
			]);
		}
	
		return $this->respond([
			'status' => 'error',
			'message' => 'Formato Base64 inválido'
		]);
    }

    public function deleteUser($id){
        $u_model = new User();
        $u_model->delete($id);
        session()->destroy();
        return redirect()->to(base_url(['login']));
    }
}