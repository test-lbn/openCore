<?php
class configuracionBusiness extends mainBusiness{ 
	public function __construct () {
		try{
			$this->Model = new configuracionModel();
			$this->Model->wait();
		} catch (Exception $e) {
			throw $e;		
		}
	}
	public function loadParameters(){
		try{
			return $this->Model->loadParameters();
		} catch (Exception $e) {
			throw $e;
		}
	}

	function createIniFile($config){
		try{
			$fileContent = '';
			foreach ($config as $key => $value) {
				$fileContent .= "[".$key."]\n\r";
				foreach ($value as $key2 => $value2) {
					$fileContent .= $key2 . " = " . (is_numeric($value2) ? $value2 : '"'.$value2.'"') . "\n\r";
				}
			}
			return $fileContent;
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function saveParameters($config){
		try{
			$this->Model->saveParameters($config);
		} catch (Exception $e) {
			throw $e;
		}
	}
}