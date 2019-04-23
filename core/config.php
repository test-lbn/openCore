<?php
//definicion de rutas y constantes
//header("Access-Control-Allow-Origin: *");
define('ROOT_PATH',	".");
define('LIBS_PATH',	ROOT_PATH.'/libs/');
define('MODULE_PATH',	ROOT_PATH.'/modulos/');
define('STATIC_PATH',	ROOT_PATH.'/static/');
define('COMPONENT_PATH',	STATIC_PATH.'componentes/');
define('TEMPLATE_PATH',	STATIC_PATH.'template/');
define('CONF_PATH',  ROOT_PATH.'/conf/config.php');
define('CORE',ROOT_PATH.'/core/');
define ('BASE_URL_PATH', 'http://'.dirname($_SERVER['HTTP_HOST'].''.$_SERVER['SCRIPT_NAME']).'/');
//require CONF_PATH;

$file=CORE."/config/".$_SERVER['SERVER_NAME'].".ini";

if(!isset($_SESSION['config'])){
	
	if(file_exists($file) && $_SERVER['SERVER_PORT']!='8083'){
		$_SESSION['config']=parse_ini_file($file,true);
	}else{
		$file=CORE."/config/desarrollo.inscra.com.ini";		
		$_SESSION['config']=parse_ini_file($file,true);
	}
}
if($_SERVER['SERVER_PORT']=='8083'){
		$file=CORE."/config/desarrollo.inscra.com.ini";		
		$_SESSION['config']=parse_ini_file($file,true);
	}


if($_SERVER['SERVER_NAME']=='desarrollo.inscra.com'){
	header("Location: https://des.lebon.co");
}
if($_SERVER['SERVER_NAME']=='preproduccion.inscra.com'){
	header("Location: https://pre.lebon.co");
}



