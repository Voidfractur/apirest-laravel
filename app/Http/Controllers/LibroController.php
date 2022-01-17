<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Libro;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
	public function index(Request $request)
	{

		$token = $request->header('Authorization');
		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {
			$x = "Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]);
			//echo "<pre>"; print_r($x); echo "</pre>";

			if ("Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]) == $token) {
				$libros = Libro::all();
				if (!empty($libros)) {
					$json = array(
						"status" => 200,
						"total_registros" => count($libros),
						"detalles" => $libros
					);
					return json_encode($json, true);
				} else {
					$json = array(
						"status" => 200,
						"total_registros" => 0,
						"detalles" => "Sin registros"
					);
					return json_encode($json, true);
				}
			}/*else{
    			$json = array(
		    		"status"=>404,
		    		"detalles"=>"No coinciden las credenciales",
		    		"token"=>$token,
		    		"x"=>$x
		    	);
		    	return json_encode($json,true);
    		}*/
		}
		$json = array(
			"status" => 404,
			"detalles" => "No coinciden las credenciales"
		);
		//return json_encode($json,true);
	}
	public function store(Request $request)
	{
		$token = $request->header('Authorization');

		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {

			if ("Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]) == $token) {
				//obtenemos datos
				$datos = array(
					"titulo"    => $request->input("titulo"),
					"editorial" => $request->input("editorial"),
					"area"      => $request->input("area"),
					"autor"     => $request->input("autor"),
					"imagen"    => $request->input("imagen"),
					"precio"    => $request->input("precio"),
				);

				//validar los datos

				$validator = Validator::make($datos, [
					"titulo"    => "required|string|max:255|unique:libros",
					"editorial" => "required|string|max:255",
					"area"      => "required|string|max:255",
					"autor"     => "required|string|max:255",
					"imagen"    => "required|string|max:255",
					"precio"    => "required|numeric"
				]);
				if ($validator->fails()) {
					$json = array(
						"status" => 404,
						"detalles" => "Verificar cada campo, posible titulo repetido, posible ruta de imagen ecede numero de caracteres repetidos, posible precio erroneo"
					);
					return json_encode($json, true);
					//echo '<pre>'; print_r($datos); echo '</pre>';
				}
				$libros = new Libro();
				$libros->titulo 	= $datos["titulo"];
				$libros->editorial 	= $datos["editorial"];
				$libros->area 		= $datos["area"];
				$libros->autor 		= $datos["autor"];
				$libros->imagen 	= $datos["imagen"];
				$libros->precio 	= $datos["precio"];
				$libros->id_creador = $value["id"];

				$libros->save();
				$json = array(
					"status" => 202,
					"detalles" => "Registro exitoso"
				);
				return json_encode($json, true);
			}
		}
		//else
		$json = array(
			"status" => 404,
			"detalles" => "No coinciden las credenciales"

		);
		return json_encode($json, true);
	}

	public function update($id, Request $request)
	{

		$token = $request->header("Authorization");
		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {
			$credenciales = "Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]);
			if ($credenciales == $token) {
				$datos = array(
					"titulo"	=> $request->input("titulo"),
					"editorial"	=> $request->input("editorial"),
					"area"		=> $request->input("area"),
					"autor"		=> $request->input("autor"),
					"imagen"	=> $request->input("imagen"),
					"precio"	=> $request->input("precio")
				);
				//echo "<pre>"; print_r($datos); echo "</pre>";
				//return;
				//valida que los campos contengan essto required no tiene que estar vacio tipo y rango maximo de caracteres
				$validator = Validator::make($datos, [
					"titulo"	=> "required|string|max:255",
					"editorial"	=> "required|string|max:255",
					"area"		=> "required|string|max:255",
					"autor"		=> "required|string|max:255",
					"imagen"	=> "required|string|max:500",
					"precio"	=> "required|numeric"
				]);

				if ($validator->fails()) {

					$showErrores = $validator->errors();

					$json = array(
						"status" => 404,
						"detalles" => "Verificar los campos",
						"Error" => $showErrores
					);
					return json_encode($json, true);
				}

				$getLibro = Libro::where("id", $id)->get();
				if ($value["id"] == $getLibro[0]["id_creador"]) {
					$datos = array(
						"titulo"	=> $datos["titulo"],
						"editorial"	=> $datos["editorial"],
						"area"		=> $datos["area"],
						"autor"		=> $datos["autor"],
						"imagen"	=> $datos["imagen"],
						"precio"	=> $datos["precio"]
					);
					$libros = Libro::where("id", $id)->update($datos);

					$json = array(
						"status" => 202,
						"detalles" => "Cambio exitoso"
					);
					return json_encode($json, true);
				} else {
					$json = array(
						"status" => 404,
						"detalles" => "Sin autorizacion para modificar este registro"
					);
					return json_encode($json, true);
				}
			}
			/*
            else{
	    		$json = array(
    				"status"=>404,
    				"detalles"=>"Error en la credenciales"
    			);
    			return json_encode($json,true);
	    	}*/
		}
		$json = array(
			"status" => 404,
			"detalles" => "Error en la credenciales"
		);
		return json_encode($json, true);
	}
	public function destroy($id, Request $request)
	{

		$token = $request->header("Authorization");
		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {
			$credenciales = "Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]);
			if ($credenciales == $token) {
				$validar = Libro::where("id", $id)->get();
				if (!empty($validar)) {
					if ($value["id"] == $validar[0]["id_creador"]) {

						$libros = Libro::where("id", $id)->delete();

						$json = array(
							"status" => 202,
							"detalles" => "Se ha eliminado el libro con exito"
						);
						return json_encode($json, true);
					} else {
						$json = array(
							"status" => 403,
							"detalles" => "Sin autorizacion para eliminar este libro"
						);
						return json_encode($json, true);
					}
				} else {
					$json = array(
						"status" => 404,
						"detalles" => "El libro no existe"
					);
					return json_encode($json, true);
				}
			}/*
            else {
	    		$json = array(
	    			"status"=>404,
	    			"detalles"=>"Las credenciales no son validas"
	    		);
	    		return json_encode($json,true);
	    	}
            */
		}
		$json = array(
			"status" => 405,
			"detalles" => "Las credenciales no son validas"
		);
		return json_encode($json, true);
	}
	public function buscarLibro($id, Request $request)
	{

		$token = $request->header('Authorization');
		$usuarios = Usuario::all();
		foreach ($usuarios as $key => $value) {
			$x = "Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]);
			//echo "<pre>"; print_r($x); echo "</pre>";

			if ("Basic " . base64_encode($value["id_usuario"] . ":" . $value["llave_secreta"]) == $token) {
				//$libros = DB::select('select * from libros where id_creador ='.$id.';');
				$libros = Libro::where("id_creador", $id)->get();
				if (!empty($libros)) {
					$json = array(
						"status" => 200,
						"total_registros" => count($libros),
						"detalles" => $libros
					);
					return json_encode($json, true);
				} else {
					$json = array(
						"status" => 200,
						"total_registros" => 0,
						"detalles" => "Sin registros"
					);
					return json_encode($json, true);
				}
			}/*else{
    			$json = array(
		    		"status"=>404,
		    		"detalles"=>"No coinciden las credenciales",
		    		"token"=>$token,
		    		"x"=>$x
		    	);
		    	return json_encode($json,true);
    		}*/
		}
		$json = array(
			"status" => 404,
			"detalles" => "No coinciden las credenciales"
		);
		//return json_encode($json,true);
	}
}
