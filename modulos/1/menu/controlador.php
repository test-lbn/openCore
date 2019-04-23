<?php
$plantilla="default/default.html";
class menuController extends mainController{
		function buscarModulo($parametros){
			$modulo=ucfirst(strtolower($parametros['modulo']));
			$modelo= new menuModel($modulo);
			$this->response($modelo);
		}
		function buscarModuloId($parametros){
			$modelo= new menuModel();
			$modelo->getDatosId($parametros['id']);
			$this->response($modelo);
		}
		function grabarModulo($parametros){
			$frm=$parametros['frm'];
			$modulo=ucfirst(strtolower($frm['nombre-modulo']));
			$modelo= new menuModel($modulo);
			$modelo->id_sub=intval($frm['menu-modulo']);
			$modelo->icono=$frm['icono-modulo'];
			$modelo->orden=intval($frm['orden-modulo']);
			$modelo->grabarModulo();
			$this->response(array("mensaje"=>"exitoso"));
		}
		function traerMenu($parametros){
			$modelo= new menuModel();
			$data=$modelo->getMenus(0);
			$this->response($data);
		}
		function traerSubmenu(){
			$modelo= new menuModel();
			$data=$modelo->getSubmemus();
			$this->response($data);
		}

	}
