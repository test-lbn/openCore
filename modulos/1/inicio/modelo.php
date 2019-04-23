<?php
/**
 * Explicacion corta del contenido de archivos y funciones
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin Y-M-D
 * @category     Area
 * @package      Modulo
 * @subpackage   SubModulo
 * @version         Version
 */
class usuario extends mainModel {  
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
		$m=$this->lee_todo("SELECT * from nue_usuario where usuario='{$this->usuario}'");
		if(count($m)>0){
			$m=$m[0];
			$this->nombres=$m->nombre;
			$this->apellidos=$m->apellidos;
			$this->correo=$m->correo;
			$this->password=$m->usr_pass2;
			$this->estado=$m->estado;	
			$this->existe='S';	
		}
		
	}

	public function grabar(){
		if($this->existe=='S'){
			$this->ejecuta_query("UPDATE nue_usuario set nombres='{$this->nombres}',apellidos='{$this->apellidos}',correo='{$this->correo}', estado='{$this->estado}' where usuario='{$this->usuario}'");
		}else{
			$this->ejecuta_query("INSERT INTO nue_usuario(usuario, nombres, apellidos, correo, password, estado, fecha_registro) values ('{$this->usuario}', '{$this->nombres}', '{$this->apellidos}', '{$this->correo}', '{$this->password}', '{$this->estado}', now())");
		}
	}

	public function setPassword(){
		$this->ejecuta_query("UPDATE nue_usuario set usr_pass2='{$this->password}', f_expira_p=today+20 where usuario='{$this->usuario}'");
	}

	public function getListado(){
		return $this->lee_todo("SELECT usuario,nombre as nombres,usuario_ldap as correo from nue_usuario where clave='rsn'");
	}
}  
