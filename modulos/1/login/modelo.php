<?php
class loginModel extends mainModel {  
	public $id_usuario;  
	public $usuario;  
	public $password;  
	public $nombres;  
	public $nit;  
	public $existe='N';  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
	}

	public function getUsuario(){
		$data=$this->lee_uno("SELECT id,usuario,usr_pass as password, nombre, apellidos, correo from core_usuarios where usuario='{$this->usuario}' and estado != 'I'");
		return $data;
	}
	public function grabarToken(){
		$m=$this->lee_uno("SELECT id,token FROM core_token WHERE id_usuario='{$this->id_usuario}' and dominio='{$this->dominio}'");
		if ($m->id>0){
			$this->ejecuta_query("UPDATE core_token set token='{$this->token}', vigencia=CURDATE(), fecha_hora=CURRENT_TIMESTAMP where id='{$m->id}' and id_usuario=(select id from core_usuarios where usuario='{$this->usuario}' and usr_pass='{$this->password}' and estado !='I')");	
		}else{
			$this->ejecuta_query("INSERT INTO core_token(id_usuario, dominio, token, vigencia, fecha_hora) VALUES('{$this->id_usuario}', '{$this->dominio}', '{$this->token}', CURDATE(), CURRENT_TIMESTAMP)");	
		}
		
	}
	public function getToken(){
		$m=$this->lee_uno("SELECT token FROM core_token WHERE id_usuario='{$this->id_usuario}' and dominio='{$this->dominio}' and vigencia>=CURDATE()");
		return $m->token;
	}
}  