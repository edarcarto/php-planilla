<?php
/**
* Controlador Principal
*/
class Controller
{
	public function model($model)
	{
		require_once '../app/Models/'.$model.'.php';
		// $x = "App\\Models\\".$model;
		return new $model();
	}

	public function view($view,$data = [])
	{
		require_once '../app/Views/'.$view.'.php';
	}

	public function auth()
	{
		if (!isset($_SESSION['username']) && !isset($_SESSION['fullname']) 
			&& !isset($_SESSION['company_name']) && !isset($_SESSION['id'])) {
			return header("Location: /");
		}
	}

	public function debug($variable)
	{
		print_r("<pre>");
		print_r($variable);
		print_r("</pre>");
		exit;
	}

	public function _404()
	{
		$export = array('error' => '_404');
		echo json_encode($export);
	}
}