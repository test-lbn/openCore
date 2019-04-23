<?php
$plantilla="default/default.html";
class configuracionController extends mainController{
	private $Business;
	
	public function __construct () {
		try{
			$this->Business = new configuracionBusiness();
		} catch (Exception $e) {
			$this->catchError($e);
		}
	}
	function loadParameters(){
		try{
			$resultado=$this->Business->loadParameters();
			$this->response($resultado);
		} catch (Exception $e) {
			$this->catchError($e);
		}
	}

	function saveParameters($parametros){
		try{
			$config=$this->Business->loadParameters();
			foreach ($config as $key => $value) {
				foreach ($value as $key2 => $value2) {
					$config[$key][$key2]=$parametros[$key2];
				}
			}
			$config=$this->Business->createIniFile($config);
			$this->Business->saveParameters($config);
			$resultado['estado']="exitoso";
			$this->response($resultado);
		} catch (Exception $e) {
			$this->catchError($e);
		}
	}
}	

?>