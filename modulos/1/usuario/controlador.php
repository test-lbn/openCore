<?php
$plantilla="default/default.html";

class usuarioController extends mainController{
	function traerUsuario($parametros){
		$usuario_=$parametros['usuario'];
		$modelo= new usuario($usuario_);
		$this->response($modelo);
	}
	function grabarUsuario($parametros){
		$frm=$parametros['frm'];
		$modelo= new usuario(trim(strtolower($frm['usuario'])));
		$modelo->nombres=ucwords(strtolower($frm['nombres']));
		$modelo->apellidos=ucwords(strtolower($frm['apellidos']));
		$modelo->correo=strtolower($frm['correo']);
		$modelo->estado=strtoupper($frm['estado']);
		$modelo->begin_work();
		$modelo->grabar();
		$respuesta["mensaje"]='exitoso';
		if($frm['password1']!=''){
			if($frm['password1']==$frm['password2']){
				$modelo->password=sha1($frm['password1']);
				$modelo->setPassword();	
			}else{
				$respuesta["mensaje"]='Contrase&ntilde;a no concuerda, verifique';
			}
		}
		$modelo->commit();
		$this->response($respuesta);	
	}

	function traerLista($parametros){
		$estado=$parametros['estado'];
		$modelo= new usuario();
		$modelo->estado=$estado;
		$lista=$modelo->getListado();
		$this->response($lista);	
	}
	function cambioContrasena($parametros){
		$actual=$parametros['contrasena-actual'];
		$nueva=$parametros['contrasena-nueva'];
		$nueva2=$parametros['contrasena-nueva2'];

		$modelo= new usuario($_SESSION['usuario']);
		if($nueva==$nueva2 && $nueva!="" && $modelo->password==$actual){
			$modelo->password=$nueva;
			$modelo->setPassword();	
			$respuesta["mensaje"]='exitoso';
		}else{
			$respuesta["mensaje"]="Contrase&ntilde;as no coinciden, verifique..";
		}
		$this->response($respuesta);	

	}
}	

