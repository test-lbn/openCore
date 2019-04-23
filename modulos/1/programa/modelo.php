<?php
include_once(CORE."mainModel.php");

class programa extends mainModel {  
	public $id;  
	public $programa;  
	public $descripcion;  
	public $menu;  
	public $submenu;  
	public $controladorJs;  
	public $funcionesJs=array();  
	public $controladorXajax;  
	public $funcionesXajax=array();  
	public $xajaxDefault='';  
	public $permisos=array();  
	public $componentes=array();  
	public $autenticado='S';
	public $existe='N';
	
                                                                        
	public function __construct($programa="")  
	{  
		$this->programa = strtoupper($programa);
		$this->Conectarse();
		if($programa!=''){
			$this->getDatos();
		}
	}
	public function agregarPermiso($codigo,$descripcion){
		$this->permisos[$codigo]=$descripcion;
	}
	public function getDatos(){

		$componetes='';

		$data=$this->lee_todo("SELECT * from core_programas where programa='{$this->programa}'");

		if(count($data)>0){
			$this->existe='S';
			$this->id=$data[0]->id;
			$this->descripcion=trim($data[0]->descripcion);
			$this->menu=trim($data[0]->id_menu);
			$this->submenu=trim($data[0]->id_submenu);
			//$this->xajaxDefault=trim($data[0]->xajaxdefault);
			$this->autenticado=trim($data[0]->autenticado);
			
			## cargar en el objeto los componentes
			$file=MODULE_PATH.$this->menu."/".strtolower($this->programa)."/componentes.ini";
			$link = @fopen($file,'r');
			if ($link){
				$size=filesize($file);
				if($size==0) $size=1;
				$componetes = fread($link,$size);
				fclose($link);
			}
			if($componetes!=''){
				$this->componentes= explode("\n",$componetes);
			}

		}else{
			$this->existe='N';
		}
	}
	public function getPermisos(){
		$data=$this->lee_todo("SELECT trim(pro.opcion) as codigo,trim(pro.descripcion) as nombre FROM core_programas pr, core_programas_opciones pro where pr.id=pro.id_programa and pr.programa='{$this->programa}' order by 1");
		for ($i=0; $i <count($data) ; $i++) { 
			$this->permisos[$data[$i]->codigo]=utf8_encode($data[$i]->nombre);
		}
	}


	public function grabarGeneral(){
		if($this->existe=='N'){
			//inserta
			$this->id=$this->ejecuta_query("INSERT into core_programas(programa,id_menu,descripcion,autenticado) values ('{$this->programa}','{$this->menu}','{$this->descripcion}','{$this->autenticado}')","id");
		}else{
			//actualiza
			$this->ejecuta_query("UPDATE core_programas set descripcion='{$this->descripcion}',id_menu='{$this->menu}',autenticado='{$this->autenticado}' where programa='{$this->programa}'");
		}
	} 
	public function grabarPermisos(){
		foreach ($this->permisos as $codigo => $nombre) {
			$mpermisos[]=$codigo;
			$mval=$this->lee_todo("SELECT opcion FROM core_programas_opciones WHERE id_programa='{$this->id}' and opcion='$codigo' ");
			if(count($mval)>0){
				//update
				$this->ejecuta_query("UPDATE core_programas_opciones set descripcion='$nombre' where id_programa='{$this->id}' and opcion='$codigo'");
			}else{
				//insert
				$this->ejecuta_query("INSERT into core_programas_opciones (id_programa,opcion,descripcion) values ('{$this->id}','$codigo','$nombre')");
			}
		}
		$this->ejecuta_query("DELETE FROM core_programas_opciones WHERE id_programa='{$this->programa}' and opcion not in ('".implode("','",$mpermisos)."')");

	}
	public function getPermiso($opcion='',$muestra_error=true){
		
		$programa=$this->programa;

		if($opcion!=''){ 
			$sql_add=" and po.opcion='$opcion'";
		}else {
			$sql_add="";
		}

		$consulta="SELECT
					DISTINCT po.opcion, po.descripcion as nombre
				FROM
					core_usuarios u,
					core_usuarios_roles ur,
					core_roles r, 
					core_permisos p,
					core_programas_opciones po,
					core_programas pg 
				WHERE
				    u.id=ur.id_usuario and
				    ur.id_rol=r.id and
				    r.id=p.id_rol and
				    p.id_programa_opcion=po.id and
				    po.id_programa=pg.id and
				    u.usuario='".$_SESSION['usuario']."' AND
					pg.id='{$this->id}' 
					$sql_add";
		$m=$this->lee_todo($consulta);
		if(count($m)>0){
			$this->permisos=$m;
			return true;	
		}else{
			return false;
		}
	}

	public function getOpciones(){
		return $this->lee_todo("SELECT a.id,a.programa, trim(b.opcion) opcion, lower(b.descripcion) as nombre, b.id as id_programa_opcion FROM core_programas a, core_programas_opciones b WHERE a.id=b.id_programa  order by 1,2");
	}

	public function logAcceso(){
		//$this->ejecuta_query("UPDATE nue_perpro set nro_ingresos=nro_ingresos+1 WHERE programa='{$this->programa}' AND usuario='".$_SESSION['usuario']."'");
	}

	public function eliminarPrograma(){
		$this->begin_work();
		$this->ejecuta_query("DELETE from core_programas_opciones where id_programa='{$this->id}'");
		$this->ejecuta_query("DELETE from core_programas where id='{$this->id}'");
		$this->ejecuta_query("DELETE from core_permisos where id_programa_opcion not in (select id from core_programas_opciones)");
		$this->commit();
	}
	public function getMenuProgramas($sub){
		#menus dependientes
		$consulta="SELECT distinct up.id_menu as id, up.orden_menu as orden, up.nombre_menu  as nombre, up.id_menu_parent as id_sub, up.icono from v_usuarios_permisos as up where id_usuario='".$_SESSION['id_usuario']."' and id_menu_parent='$sub' order by 1,2";
		$mmenu=$this->lee_todo($consulta);
		for ($i=0; $i <count($mmenu) ; $i++) { 
			$idsub=$mmenu[$i]->id;
			$mmenu[$i]->sub=$this->getMenuProgramas($idsub);
			
			#programas del menu
			$consulta="SELECT distinct up.id_programa, up.programa, up.descripcion_programa as descripcion , up.orden_programa as orden from  v_usuarios_permisos as up where id_usuario='".$_SESSION['id_usuario']."' and id_menu='$idsub' order by 4,3";
			$mmenu[$i]->progs=$this->lee_todo($consulta);

		}
		return $mmenu;
	}
}  

