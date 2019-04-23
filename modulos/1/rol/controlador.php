<?php
$plantilla="default/default.html";

class rolController extends mainController{
	function buscarRol($parametros){
		$nombre=$parametros['nombre'];
		$modelo= new rolModel($nombre);
		$this->response($modelo);
	}
	function traerLista($parametros){
		$estado=$parametros['estado'];
		$modelo= new rolModel();
		$modelo->estado=$estado;
		$lista=$modelo->getListado();
		$this->response($lista);	
	}
	function grabarrol($parametros){
		$frm=$parametros['frm'];
		$modelo= new rolModel($frm['rol']);
		$modelo->nombre=strtoupper($frm['rol']);
		$modelo->descripcion=ucfirst(strtolower($frm['descripcion']));
		$modelo->estado=strtoupper($frm['estado']);
		$modelo->usuarios=$frm['usuarios'];
		$modelo->begin_work();
		$modelo->grabar();
		$respuesta["mensaje"]='exitoso';
		$modelo->commit();
		$this->response($respuesta);	
	}
	function grabarPermisos($parametros){
		$nombre=$parametros['nombre'];
		$permisos=$parametros['permisos'];
		$eliminar=$parametros['eliminar'];

		$modelo= new rolModel($nombre);
		if($modelo->existe!='S'){
			$respuesta["mensaje"]='rol '.$nombre.' no existe, verifique';
		}else{
			$modelo->begin_work();
			#organiza array de permisos para enviarlo al modelo.
			for ($i=0; $i <count($permisos) ; $i++) { 
				list($id_programa,$opcion, $id_programa_opcion)=explode("-",$permisos[$i]);
				if($id_programa!='' && $id_programa>0){
					$mpermisos[$i]['id_programa']=$id_programa;
					$mpermisos[$i]['opcion']=$opcion;
					$mpermisos[$i]['id_programa_opcion']=$id_programa_opcion;
				}
			}
			$modelo->permisos=$mpermisos;

			#elimina permimos del rol
			if($eliminar=='S'){
				$modelo->eliminarPermisos();	
			}
			$modelo->grabarPermisos();
			$respuesta["mensaje"]='exitoso';
			$modelo->commit();	
		}
		$this->response($respuesta);	
	}
}	

