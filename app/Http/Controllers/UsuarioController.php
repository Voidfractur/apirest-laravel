<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioController extends Controller
{
	//
	public function index(Request $request)
	{
		$token = $request->header('Authorization');

		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {
			$x = "Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]);

			if ($x == $token) {
				$usuarios = Usuario::all();
				if (!empty($usuarios)) {
					$json = array(
						"status" => 200,
						"total_registros" => count($usuarios),
						"detalles" => $usuarios
					);
					return json_encode($json, true);
				} else {
					$json = array(
						"status" => 200,
						"total_registros" => 0,
						"detalles" => "Sin Registros"
					);
					return json_encode($json, true);
				}
			}
		}
	}

	public function login(Request $request)
	{
			$token = $request->header('Authorization');
			$usuarios = Usuario::all();
			foreach ($usuarios as $key => $value) {
				if ("Basic ".base64_encode($value["id_usuario"].":".$value["llave_secreta"]) == $token) {
					$json = array(
		    			"status"=>200,
		    			"id_usuario"=>$value["id_usuario"],
		    			"llave_secreta"=>$value["llave_secreta"],
						"cve_usu"=>$value["id"],
		    		);
		    		return json_encode($json,true);
				}
			}
			$json = array(
				"status"=>404,
				"detalles"=>"No coinciden las credenciales"
			);
			return json_encode($json,true);
	}

	public function store(Request $request)
	{
		//Obtenemos datos de una solicitud
		$datos = array(
			"nombre" => $request->input("nombre"),
			"apellido" => $request->input("apellido"),
			"correo" => $request->input("correo")
		);
		//echo "<pre>"; print_r($datos); echo "</pre>";

		//Validamos datos
		$validator = Validator::make($datos, [
			"nombre" => "required|string|max:100",
			"apellido" => "required|string|max:100",
			"correo" => "required|string|email|max:100"
		]);

		if ($validator->fails()) {
			$json = array(
				"detalles" => "Error en los datos"
			);
			return json_encode($json, true);
		} else {

			$id_usuario = Hash::make($datos["nombre"] . $datos["apellido"] . $datos["correo"]);
			//echo "<pre>"; print_r($id_usuario); echo "</pre>";

			$llave_secreta = Hash::make($datos["correo"] . $datos["apellido"] . $datos["nombre"], ['rounds' => 12]);
			//echo "<pre>"; print_r($llave_secreta); echo "</pre>";

			$usuario = new Usuario();
			$usuario->nombre = $datos["nombre"];
			$usuario->apellido = $datos["apellido"];
			$usuario->correo = $datos["correo"];
			$usuario->id_usuario = str_replace('$', 'a', $id_usuario);
			$usuario->llave_secreta = str_replace('$', 'o', $llave_secreta);

			$usuario->save();

			$json = array(
				"status" => "200",
				"detalles" => "Registro exitoso, guardar sus credenciales para poder acceder posteriormente",
				"credenciales" => array(
					"id_usuario" => str_replace('$', 'a', $id_usuario),
					"llave_secreta" => str_replace('$', 'o', $llave_secreta)
				)
			);

			return json_encode($json, true);
		}
	}
}
