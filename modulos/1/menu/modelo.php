<?php
class menuModel extends mainModel {  
	public $id;  
	public $modulo="";  
	public $id_sub;  
	public $icono;  
	public $orden;  
	public $existe='N';  
	                                                                    
	public function __construct($modulo="")  
	{  
		$this->modulo = $modulo;
		$this->Conectarse();
		if($this->modulo!=''){
			$this->getDatos();
		}
	}
	public function getDatos(){
		$m=$this->lee_todo("SELECT id, des_mod as nombre, id_sub,icon_mod as icono,orden from core_menus where des_mod='{$this->modulo}'");
		if(count($m)>0){
			$this->existe='S';
			$this->id=$m[0]->id;
			$this->id_sub=$m[0]->id_sub;
			$this->icono=$m[0]->icono;
			$this->orden=$m[0]->orden;
		}

	}
	public function getDatosId($id){
		$m=$this->lee_todo("SELECT id, des_mod as nombre, id_sub,icon_mod as icono,orden from core_menus where id='$id'");
		if(count($m)>0){
			$this->existe='S';
			$this->id=$m[0]->id;
			$this->id_sub=$m[0]->id_sub;
			$this->modulo=$m[0]->nombre;
			$this->icono=$m[0]->icono;
			$this->orden=$m[0]->orden;
		}

	}
	public function grabarModulo(){
		if($this->existe=='S'){
			$this->ejecuta_query("UPDATE core_menus set id_sub='{$this->id_sub}', icono='{$this->icono}', orden='{$this->orden}' where id='{$this->id}'");
		}else{
			$this->ejecuta_query("INSERT INTO core_menus (des_mod,id_sub,icon_mod,orden) values ('{$this->modulo}','{$this->id_sub}','{$this->icono}','{$this->orden}')");
		}
	}

	public function getMenus($sub){
		$consulta="SELECT id as codigo,trim(des_mod) as nombre FROM core_menus where id_sub='$sub' order by orden, des_mod";
		$data=$this->lee_todo($consulta);
		for ($i=0; $i <count($data) ; $i++) { 
			$data[$i]->sub=$this->getMenus($data[$i]->codigo);
		}
		return $data;
		
	}
}  
