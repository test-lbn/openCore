<?php
session_start();

$plantilla='';

include_once("core/config.php");
include_once("config.php");//original ERP viejo 
include_once(CORE."mainModel.php");
include_once(CORE."mainBusiness.php");
include_once(CORE."mainController.php");
include_once(CORE."xajaxController.php");
include_once("core/mainTemplate.php");


//sanitizar parametros GET
$Core=new  mainController();
$_GET=$Core->sanitize($_GET);


if($_GET['modulo']){	
	$_GET['modulo']=strtolower($_GET['modulo']);
	include_once(MODULE_PATH."/1/programa/modelo.php");//clase controladora de aplicaciones
	$mPrograma= new programa($_GET['modulo']);
	$menu=$mPrograma->menu;

	if($mPrograma->autenticado=='S' && $_SESSION['usuario']==''){
		$_GET['modulo']=''; //forza autenticacion si no esta logeado
	}
}

if($_GET['modulo']){
	include_once(MODULE_PATH."/1/programa/modelo.php");//clase controladora de aplicaciones
	$mPrograma= new programa($_GET['modulo']);
	$menu=$mPrograma->menu;

	if($mPrograma->autenticado=='S' && $_SESSION['usuario']==''){
		die("ss4");
		//echo "<script>document.location='index.php?modulo=login&redir=".base64_encode($_SERVER['REQUEST_URI'])."'</script>";
	}

	#Valida permiso al modulo
	if(!$mPrograma->getPermiso('',false) && $mPrograma->programa!='INICIO' && $mPrograma->autenticado=='S'){
		echo "<script>alert('".$_GET['modulo'].": Modulo no existe. ".$mPrograma->mensaje."');document.location='index.php?modulo=inicio'</script>";
		die();
	}
	#Log de acceso
	//$mPrograma->core_log_programa($modulo,$parametros);

	$controlador=ROOT_PATH."/modulos/$menu/".$_GET['modulo']."/controlador.php";
	$modelo=ROOT_PATH."/modulos/$menu/".$_GET['modulo']."/modelo.php";
	$negocio=ROOT_PATH."/modulos/$menu/".$_GET['modulo']."/negocio.php";
	
	###---MODELO
	if(file_exists($modelo)){
		include_once($modelo);
	}

	###---BUSINESS
	if(file_exists($negocio)){
		include_once($negocio);
	}

	###---CONTROLADOR
	if(file_exists($controlador)){
		//include_once(CORE."xajaxController.php");
		include_once($controlador);
		if(class_exists($_GET['modulo']."_xajax")){
			define('PATH_XAJAX_JS',LIBS_PATH.'/xajax/');
			require LIBS_PATH.'xajax/xajax_core/xajax.inc.php';
			$GLOBALS['objAjax'] = new xajax();
			$control=inicializar();
			$control->processRequest();
		}
	}

	
	###---VISTA
	$template=new Template();
	$template->modulo=$_GET['modulo'];
	$template->template=$plantilla;
	$template->cargarTemplate();

	if (isset($_GET['redirect'])) {
		$redirect = $_GET['redirect'];
	}else {
		$redirect = '';
	}

	#Redireccion CORE
	if($redirect!=''){
		$url=explode("/",substr(base64_decode($_GET['redirect']),1));
		if($url[1]=='index.php' && count($url)==2){
			//do nothing
		}else{
			//die("ss2");
			echo "<script>setTimeout(function(){document.location='".base64_decode($_GET['redirect'])."';}, 1000);</script>";	
		}
		
	}
}else{
	if($_SESSION['usuario']==''){
		header("Location: index.php?modulo=login&redir=".base64_encode($_SERVER['REQUEST_URI']));
	}else{
		header("Location: index.php?modulo=inicio");
	}
}