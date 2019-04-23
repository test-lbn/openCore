<?php
$plantilla="default/default.html";

class programa_xajax extends xajaxController
{
	private $debug='S';
	function __construct(){
	}

	function grabarGeneral($frm){
		#validaciones
		$programa=strtoupper($frm['programa']);
		$descripcion=strtoupper($frm['descripcion']);
		$menu=$frm['menu'];
		$submenu=$frm['submenu'];
		$controladorJs=$frm['controladorJs'];
		$funcionesJs=$frm['funcionesJs'];
		$controladorXajax=$frm['controladorXajax'];
		$funcionesXajax=$frm['funcionesXajax'];
		$xajaxDefault=$frm['xajaxDefault'];
		$autenticado=$frm['autenticado'];
		if($xajaxDefault!=''){
			if(!strstr($xajaxDefault,"(")){
				if(!strstr($funcionesXajax,$xajaxDefault)){
					$funcionesXajax.=",".$xajaxDefault;
				}
				$xajaxDefault=$xajaxDefault."()";
			}
		}

		$myProg= new programa($programa);

		if($programa==''){$errores[]="nombre de aplicacion no valido";}
		if($descripcion==''){$errores[]="descripcion no valida";}
		if($menu==''){$errores[]="debe seleccionar un menu valido";}
		if($controladorJs=='' && $controladorXajax=='' && $myProg->existe=='N'){$errores[]="debe seleccionar un controlador";}

		if(is_array($errores)){
			//$this->Alert();
			$this->Script("toastr.warning('<b>Errores:</b><br>".implode("<br>", $errores)."')");
			return $this->response;	
		}
		

		$myProg->descripcion=$descripcion;
		$menu_ant=$myProg->menu;//backup id menu anterior
		$myProg->menu=$menu;
		$myProg->submenu=$submenu;
		$myProg->xajaxDefault=$xajaxDefault;
		
		$myProg->begin_work();
		$myProg->grabarGeneral();

		#CREAMOS TEMPLATES
		if($myProg->existe=='N'){
			$rutaTemplate=MODULE_PATH."1/programa/template/";
			
			$carpetaMenu=MODULE_PATH.$myProg->menu;
			Template::makeDir($carpetaMenu);
			
			$carpetaModulo=MODULE_PATH.$myProg->menu."/".strtolower($programa);
			Template::makeDir($carpetaModulo);
			

			###---MODELO
			$data=Template::getFile($rutaTemplate."modelo.php");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$archivoModelo=$carpetaModulo."/modelo.php";
			Template::putFile($archivoModelo,$data);

			###---NEGOCIO
			$data=Template::getFile($rutaTemplate."negocio.php");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$archivoModelo=$carpetaModulo."/negocio.php";
			Template::putFile($archivoModelo,$data);

			###---CONTROLADOR PHP
			if($controladorJs=='S'){
				$data1=Template::getFile($rutaTemplate."controlador_servicio.php");	
			}
			if($controladorXajax=='S'){
				$data2=Template::getFile($rutaTemplate."controlador_xajax.php");	
				$mfuncionesXajax=explode(",",str_replace(" ", "", $funcionesXajax));
				if(is_array($mfuncionesXajax)){
					foreach ($mfuncionesXajax as $nombre) {
						if($nombre!=''){
							$dataFunc.="\n".'	function '.$nombre.'(){'."\n".'		$this->Alert("funcion '.$nombre.'");'."\n".'		return $this->response;'."\n".'	}'."\n";
						}
					}
				}
				$data2=str_replace("[[funciones]]",$dataFunc, $data2);
			}
			$data=str_replace("[[programa]]",strtolower($programa), '<?php'."\n".'$plantilla="default/default.html";'."\n".$data1."\n".$data2.'?>');
			$archivoControlador=$carpetaModulo."/controlador.php";
			Template::putFile($archivoControlador,$data);

			###---CONTROLADOR JS
			if($controladorJs=='S'){
				$data=Template::getFile($rutaTemplate."controlador.js");
				$data=str_replace("[[programa]]",strtolower($programa), $data);
				$mfuncionesJS=explode(",",str_replace(" ", "", $funcionesJs));
				if(is_array($mfuncionesJS)){
					foreach ($mfuncionesJS as $nombre) {
						if($nombre!=''){
							$data.="\n".'function '.$nombre.'(){'."\n".'	alert("funcion '.$nombre.'");'."\n".'}'."\n";
						}
					}
					
				}
				$archivoModelo=$carpetaModulo."/controlador.js";
				Template::putFile($archivoModelo,$data);
			}

			###---VISTA
			$data=Template::getFile($rutaTemplate."vista.html");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$data=str_replace("[[titulo]]",ucwords(strtolower($programa)), $data);
			$data=str_replace("[[subtitulo]]",ucfirst(strtolower($descripcion)), $data);
			$archivoModelo=$carpetaModulo."/vista.html";
			Template::putFile($archivoModelo,$data);

			###---TEST JS
			$data=Template::getFile($rutaTemplate."test.js");
			$data=str_replace("[[programa]]",strtolower($programa), $data);
			$archivoModelo=$carpetaModulo."/test.js";
			Template::putFile($archivoModelo,$data);

			###Crea primer opcion automaticamente y asigna permiso a admin
			$myProg->agregarPermiso('A','Acceso a la aplicacion');
			$this->Script("grabarPermisos('ADMIN',{'0':'{$myProg->id}-A'});toastr.info('Desea ir a la aplicacion?. <button type=\"button\" id=\"okBtn\" onclick=window.open(\"index.php?modulo=".strtolower($programa)."\",\"\",\"\") class=\"btn btn-flat btn-danger toastr-action\">SI</button>', \"\");");
		}
		if($myProg->existe=='S'){
			if($menu_ant!='' && $menu != $menu_ant){
				if(!file_exists(MODULE_PATH."$menu/")){
					mkdir(MODULE_PATH."$menu/");
					chmod(MODULE_PATH."$menu/", 0777);
				}
				rename(MODULE_PATH."$menu_ant/".strtolower($programa), MODULE_PATH."$menu/".strtolower($programa));
			}else{
				//die("$menu_ant!='' && $menu != $menu_ant");
			}

			#validar que el archivo de control "nombre del proceso - carpeta" exista
			$servicio = new servicio('local');
			$cModulo=$servicio->cargarModulo("menu");
			$cModulo->buscarModuloId(array("id"=>$menu));
			$nivel1='';
			$nivelN=trim(utf8_encode($cModulo->response->modulo));
			
			if($cModulo->response->id_sub!=0){
				$cModulo->buscarModuloId(array("id"=>$cModulo->response->id_sub));
				$nivel1=trim(utf8_encode($cModulo->response->modulo));
			}
			$archivo_id=$menu.".".$nivel1."-".$nivelN;

			//print_r($cModulo);
			//die($archivo_id);
			if(!file_exists(MODULE_PATH.$archivo_id)){
				touch(MODULE_PATH.$archivo_id);
				chmod(MODULE_PATH.$archivo_id, 0777);
			}
			

		}
		$myProg->commit();


		$this->Script("toastr.success('Programa $programa grabado')");
		return $this->response;	
	}
	function grabarPermisos($frm,$programa){
		$programa=strtoupper($programa);
		$myProg= new programa($programa);
		$permisos=$frm['permiso'];
		$descripciones=$frm['descripcion'];

		foreach ($permisos as $key => $permiso) {
			$permiso=strtoupper($permiso);
			$descripcion=trim($descripciones[$key]);
			if($permiso!=''){
				$myProg->agregarPermiso(trim($permiso),trim($descripcion));
				
			}
		}
		$myProg->begin_work();
		$myProg->grabarPermisos();
		$myProg->commit();
		$this->Script("toastr.success('Permisos grabados ($programa)')");
		$this->Script("buscarPrograma('$programa')");
		$this->Script("grabarPermisos('ADMINISTRADOR',{'0':'{$myProg->id}-A'});toastr.info('Desea ir a la aplicacion?. <button type=\"button\" id=\"okBtn\" onclick=window.open(\"index.php?modulo=".strtolower($programa)."\",\"\",\"\") class=\"btn btn-flat btn-danger toastr-action\">SI</button>', \"\");");
		return $this->response;	
	}
	function grabarComponentes($frm,$programa){
		$myProg= new programa($programa);
		$componetes_anteriores=Template::getFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/componentes.ini");
		if($componetes_anteriores!=''){
			$componetes_anteriores= explode("\n",$componetes_anteriores);
		}else{
			$componetes_anteriores=array();
		}
		
		#crear archivo de configuracion de cada programa
		if(is_array($frm['componentes'])){
			foreach ($frm['componentes'] as $key => $value) {
				$contenido.=$value.PHP_EOL;
			}
		}
		Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/componentes.ini",$contenido,'w+');

		if(is_array($frm['componentes'])){
			foreach ($frm['componentes'] as $key => $value) {
				if(!in_array($value,$componetes_anteriores)){
					#cargar la JS demo del componente
					$componenteJS=COMPONENT_PATH."/$value/config.ini";	
					if(file_exists($componenteJS)){
						$config=parse_ini_file($componenteJS);
						if($config['templateJS']!=''){
							$config['templateJS']=str_replace("[[programa]]", strtolower($programa), $config['templateJS']);
							$dataAnt=Template::getFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/controlador.js");
							$dataAnt=str_replace('$(document).ready(function() {', '$(document).ready(function() {'.$config['templateJS'], $dataAnt);
							Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/controlador.js",$dataAnt,'w+');
						}
					}
					#cargar template el demo del componente
					$templateHTML=COMPONENT_PATH."/$value/template.html";	
					if(file_exists($templateHTML)){
						$templateHTML=Template::getFile($templateHTML);
						$templateHTML=str_replace("[[programa]]", strtolower($programa), $templateHTML);
						Template::putFile(MODULE_PATH.$myProg->menu."/".strtolower($programa)."/vista.html",$templateHTML);
					}
				}
				$contenido.=$value.PHP_EOL;
			}
		}
		
		


		$this->Script("toastr.success('Componentes grabados ($programa)')");
		$this->Script("grabarPermisos('ADMIN',{'0':'{$myProg->id}-A'});toastr.info('Desea ir a la aplicacion?. <button type=\"button\" id=\"okBtn\" onclick=window.open(\"index.php?modulo=".strtolower($programa)."\",\"\",\"\") class=\"btn btn-flat btn-danger toastr-action\">SI</button>', \"\");");
		return $this->response;	
	}
}

	class programaController  extends mainController{
		function buscarPrograma($parametros){
			$programa=strtoupper($parametros['programa']);
			$myProg= new programa($programa);
			$myProg->getPermisos();
			$this->response($myProg);
		}
		function traerComponentes(){
			$gestor=opendir(COMPONENT_PATH);
			while (false !== ($componente=readdir($gestor))) {
				$file_ini=COMPONENT_PATH."/$componente/config.ini";	
				if(file_exists($file_ini)){
					$config=parse_ini_file($file_ini);
					$mcomponentes[$componente]=$config;
				}
			}
			$this->response($mcomponentes); 
		}
		function getPermiso($parametros){
			$programa= new programa(strtoupper($parametros['programa']));
			$tienePermiso=$programa->getPermiso(strtoupper($parametros['opcion']),false);
			$this->response(array("permiso"=>$tienePermiso,"listado"=>$programa->permisos)); 	
		}
		function getOpciones(){
			$programa= new programa();
			$opciones=$programa->getOpciones();
			for ($i=0; $i <count($opciones) ; $i++) { 
				$opciones[$i]->nombre=utf8_encode($opciones[$i]->nombre);
			}
			$this->response($opciones); 		
		}
		function getMenuProgramas(){
			$programa= new programa();
			$menu=$programa->getMenuProgramas(0);
			$this->response($menu); 		
		}
		function eliminarPrograma($parametros){
			$programa=$parametros['programa'];
			$myProg= new programa($programa);
			$carpetaModulo=MODULE_PATH.$myProg->menu."/".strtolower($programa);
			Template::rmDir($carpetaModulo);
			$myProg->eliminarPrograma();
			$this->response(array("resultado"=>"success")); 	
		}
	}	



if(!function_exists("inicializar")){
	function inicializar(){
		$controlador = new programa_xajax();
		return $controlador;
	}
}

