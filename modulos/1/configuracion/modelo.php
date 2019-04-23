<?php
class configuracionModel extends mainModel {
	public $file;  
	public $config =array();  

	public function __construct(){  
		try{
			$this->file=CORE."config/".$_SERVER['SERVER_NAME'].".ini";
			if(file_exists($this->file)){
				$this->config=parse_ini_file($this->file,true);
			}else{
				throw new Exception("Archivo {$this->file} no existe", 1);
			}
			$this->Conectarse();
		} catch (Exception $e) {
			throw $e;	
		}
	}
	public function loadParameters(){
		try{
			return $this->config;
		} catch (Exception $e) {
			throw $e;	
		}
	}

	public function saveParameters($config)
	{
		try{
			$ffl=fopen($this->file,"w");
			fwrite($ffl,$config);
			fclose($ffl);
		} catch (Exception $e) {
			throw $e;
		}
	}
}  
