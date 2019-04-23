<?php
session_start();
include_once("config.php");//original ERP viejo 
include_once("core/config.php");
include_once(CORE."mainModel.php");
include_once(CORE."xajaxController.php");
include_once(CORE."mainController.php");
include_once("core/mainTemplate.php");	      

//sanitizar parametros GET
$Core=new  mainController();
$_REQUEST=$Core->sanitize($_REQUEST);

$tipo=$_REQUEST[tipo];
$modulo=$_REQUEST[modulo];
$metodo=$_REQUEST[metodo];
$token=$_REQUEST[token];
$parametros=$_REQUEST[parametros];
$user=$_REQUEST[user];

include_once(MODULE_PATH."/1/programa/modelo.php");
include_once(MODULE_PATH."/1/login/modelo.php");

$mPrograma= new programa($modulo);
$menu=$mPrograma->menu;
if($user!='') $_SESSION['usuario']=$user;
if($mPrograma->autenticado=='S'){
	if($token==''){
		header(':', true, '401');
		die("no envio token - $modulo::$metodo");
	}else{
		$mLogin= new login($_SESSION['usuario']);
		$mLogin->dominio=$_SERVER['SERVER_NAME'];
		$mLogin->getToken();
		if($token!=$mLogin->token){
			session_destroy(); 
			session_start();
			header('HTTP/1.0 401 Unauthorized');
			die("Token invalido");	
		}

	}
}
#Log de acceso
//$mPrograma->core_log_programa($modulo,$metodo);

if($menu!=''){
	$fileController=MODULE_PATH.$menu."/".$modulo."/controlador.php";
	$fileModel=MODULE_PATH.$menu."/".$modulo."/modelo.php";
}else{
	$fileController=MODULE_PATH.$modulo."/controlador.php";
	$fileModel=MODULE_PATH.$modulo."/modelo.php";
}

#aplica cuando se llama un componente CORE
if($tipo=='CORE'){
	$fileController=CORE."componentes/$modulo/controlador.php";
	$fileModel=CORE."componentes/$modulo/modelo.php";
	$clase="c".ucfirst(strtolower(trim($modulo)));
}else{
	$clase="servicio";
}

if(file_exists($fileController)){
	include_once($fileController);
}else{
	echo $modulo;
}
if(file_exists($fileModel)){
	include_once($fileModel);
}
$rest= new $clase();
$parametros = $rest->sanitize($parametros);
$rest->{$metodo}($parametros);
