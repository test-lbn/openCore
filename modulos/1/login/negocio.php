<?php
class loginBusiness extends mainBusiness{ 
	public $Model;

    public function __construct ($usuario) {
    	$this->Model = new loginModel();
    	$this->Model->wait();
    	$this->Model->usuario=strtolower(trim($usuario));

        if($this->Model->usuario!=''){
        	$data= $this->Model->getUsuario();

			if($data->id > 0){
				$this->Model->existe='S';
				$this->Model->id_usuario = $data->id;
				$this->Model->password=$data->password;
				$this->Model->nombre=utf8_encode($data->nombre);
				$this->Model->apellidos=utf8_encode($data->apellidos);
				$this->Model->correo=utf8_encode($data->correo);
			}
		}
    }

    public function crearToken($password){
		$token=sha1($this->Model->usuario.$password.rand().Time());
		return $token;
	}

	public function getToken(){
		$this->Model->dominio=$_SERVER['SERVER_NAME'];
		$token=$this->Model->getToken();
		$this->token=$token;
	}

	public function grabarToken($token){
		$this->Model->token=$token;
		$this->Model->grabarToken();
	}

	public function iniciarSession(){
		$_SESSION['id_usuario']=$this->Model->id_usuario;
        $_SESSION['usuario']=$this->Model->usuario;
	}

	public function logOut(){
		session_destroy();
        session_start();
	}

	public function	validarUsuarioExiste(){
		if($this->Model->existe=='S'){
			return true;
		}
	}
	public function	validarContrasena($password){
		if($password==$this->Model->password){
			$this->Model->dominio=$_SERVER['SERVER_NAME'];
            return true;
        }
	}




}