<?php
session_start();
include_once("config.php");//original ERP viejo 
include_once("core/config.php");
include_once(CORE."mainModel.php");
include_once(CORE."xajaxController.php");
include_once(CORE."mainBusiness.php");
include_once(CORE."mainController.php");
include_once(CORE."mainTemplate.php");	      

//sanitizar parametros GET
$Core=new  mainController();
$_REQUEST=$Core->sanitize($_REQUEST);

if (isset($_REQUEST['tipo'])) {
	$tipo = $_REQUEST['tipo'];
}else {
	$tipo = '';
}

if (isset($_REQUEST['modulo'])) {
	$modulo = $_REQUEST['modulo'];
}else {
	$modulo = '';
}

if (isset($_REQUEST['metodo'])) {
	$metodo = $_REQUEST['metodo'];
}else {
	$metodo = '';
}

if (isset($_REQUEST['token'])) {
	$token = $_REQUEST['token'];
}else {
	$token = '';
}

if (isset($_REQUEST['parametros'])) {
	$parametros = $_REQUEST['parametros'];
}else {
	$parametros = '';
}

if (isset($_REQUEST['user'])) {
	$user = $_REQUEST['user'];
}else {
	$user = '';
}

include_once(MODULE_PATH."1/programa/modelo.php");
include_once(MODULE_PATH."1/login/negocio.php");
include_once(MODULE_PATH."1/login/modelo.php");

$mPrograma = new programa($modulo);
$menu = $mPrograma->menu;

if($user!=''){
	$_SESSION['usuario']=$user;
}

if($mPrograma->autenticado=='S'){
	if($token==''){
		header(':', true, '401');
		die("No envio token - $modulo::$metodo");
	}else{
		$mLogin = new loginBusiness($_SESSION['usuario']);
		$mLogin->getToken();
		if($token!=$mLogin->token){
			//print_r($mPrograma);			
			$mLogin->logOut();
			die("Token incorrecto o caducado");
			//header('HTTP/1.0 401 Unauthorized');
		}

	}
}
#Log de acceso
//$mPrograma->core_log_programa($modulo,$metodo);

if($menu!=''){
	$fileController=MODULE_PATH.$menu."/".$modulo."/controlador.php";
	$fileBusiness=MODULE_PATH.$menu."/".$modulo."/negocio.php";
	$fileModel=MODULE_PATH.$menu."/".$modulo."/modelo.php";
}else{
	print_r($_REQUEST);
	DIE("-");
}

#aplica cuando se llama un componente CORE
if($tipo=='CORE'){
	$fileController=CORE."componentes/$modulo/controlador.php";
	$fileModel=CORE."componentes/$modulo/modelo.php";
	$clase=$modulo."Controller";
}else{
	$clase=$modulo."Controller";
}

if(file_exists($fileController)){
	include_once($fileController);
}else{
	echo $modulo;
}
if(file_exists($fileModel)){
	include_once($fileModel);
}
if(file_exists($fileBusiness)){
	include_once($fileBusiness);
}else{
	die($fileBusiness);
}

if (class_exists($clase)) {
    $rest= new $clase();
	$parametros = $rest->sanitize($parametros);
	$rest->{$metodo}($parametros);
}

