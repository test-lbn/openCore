<?php
/**
 * Modelo de datos tabla de usuarios
 *
 * @author       Alvaro Pulgarin <aepulgarin@gmail.com>
 * @copyright    Alvaro Pulgarin 
 * @category     Area
 * @package      Core
 * @subpackage   Model
 * @version      2.0
 */
class usuario extends mainModel {  
	public $id_usuario;  
	public $usuario;  
	public $nombres;  
	public $apellidos;  
	public $correo;  
	public $password;  
	public $estado;  
	public $existe;  
	                                                                    
	public function __construct($usuario="")  
	{  
		$this->usuario = strtolower($usuario);
		$this->Conectarse();
		if($usuario!=''){
			$this->getDatos();
		}
	}
	public function getDatos(){
		$m=$this->lee_todo("SELECT * from core_usuarios where usuario='{$this->usuario}'");
		if(count($m)>0){
			$m=$m[0];
			$this->nombres=$m->nombre;
			$this->apellidos=$m->apellidos;
			$this->correo=$m->correo;
			$this->password=$m->usr_pass;
			$this->estado=$m->estado;	
			$this->existe='S';	
		}
		
	}

	public function grabar(){
		if($this->existe=='S'){
			$this->ejecuta_query("UPDATE core_usuarios set nombre='{$this->nombres}',apellidos='{$this->apellidos}',correo='{$this->correo}', estado='{$this->estado}' where usuario='{$this->usuario}'");
		}else{
			$this->ejecuta_query("INSERT INTO core_usuarios(usuario, nombre, apellidos, correo, usr_pass, estado, f_expira_p) values ('{$this->usuario}', '{$this->nombres}', '{$this->apellidos}', '{$this->correo}', '{$this->password}', '{$this->estado}',NOW())");
		}
	}

	public function setPassword(){
		$this->ejecuta_query("UPDATE core_usuarios set usr_pass='{$this->password}', f_expira_p=NOW()+20 where usuario='{$this->usuario}'");
	}

	public function getListado(){
		return $this->lee_todo("SELECT usuario,nombre as nombres, correo, apellidos, id as id_usuario from core_usuarios where estado='A'");
	}
}  
