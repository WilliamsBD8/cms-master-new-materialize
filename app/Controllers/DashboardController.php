<?php

namespace App\Controllers;



class DashboardController extends BaseController
{

	public function index()
	{

		$fechaEspecifica = new \DateTime(session('user')->password->created_at);
		$fechaActual = new \DateTime('now');
		$diferencia = $fechaEspecifica->diff($fechaActual);

		// var_dump($diferencia); die;

	  return  view('pages/home', [
			'day' => (90 - $diferencia->days)
		]);
	}

	public function about()
  {
    return view('pages/about');
  }

}
