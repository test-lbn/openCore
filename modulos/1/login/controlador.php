<?php
$plantilla="default/blank.html";
class loginController extends mainController{
    private $Business;
    
    /**
     * @param $parametros array Formulario de login
     */
    function autenticar($parametros){
        $username=$parametros['usuario'];
        $password=$parametros['password'];

        $this->Business = new loginBusiness($username);

        $db = isset($parametros['db']) ? $parametros['db'] : '';

        $this->Business->logOut();

        if($db!=''){//cambio de base de datos para desarrollo /pruebas
            $_SESSION['cambio_db']=$db;
            unset($GLOBALS['DB']);
        }
        if($this->Business->validarUsuarioExiste()){
            if($this->Business->validarContrasena($password)){
                $token=$this->Business->crearToken($password);
                $this->Business->grabarToken($token);
                $this->Business->iniciarSession();
                $resultado['mensaje']='Exitoso';
                $resultado['info']=array(
                						"usuario"=>$username, 
                                        "token"=>$token,
                                        "nombre"=>$this->Business->Model->nombre,
                                        "apellidos"=>$this->Business->Model->apellidos,
                                        "nit"=>$this->Business->Model->nit
                                    );
                
                if (isset($_GET['redir'])){
                    $resultado['referrer']=base64_decode($_GET['redir']);
                }else{
                    $resultado['referrer']='';
                }

            }else{
                $resultado['mensaje']='Contraseña no valida';
            }
        }else{
            $resultado['mensaje']='Usuario no existe';
        }
        $this->response($resultado);
    }
    function logOut(){
    	$this->Business = new loginBusiness($_SESSION['usuario']);
        $this->Business->logOut();
    }
    function ValidarMultipleDB(){
        $config=$_SESSION['config']['database'];
        $this->response(array("multiple"=>$config['multiple'],"defecto"=>$config['server']));
    }
}
