<?php
class rolModel extends mainModel {  
	public $id;  
	public $nombre;  
	public $descripcion;  
	public $estado;  
	public $usuarios=array();  
	public $permisos=array();  
	public $existe;  
	                                                                    
	public function __construct($nombre="")  
	{  
		$this->nombre = strtoupper(trim($nombre));
		$this->Conectarse();
		$this->wait();
		if($nombre!=''){
			$this->traerRol();
		}
	}
	
	public function traerRol(){
		$this->isolation('dirty');
		$m=$this->lee_uno("SELECT id,nombre, descripcion, estado FROM core_roles where nombre='{$this->nombre}'");
		$this->id=$m->id;
		$this->nombre=$m->nombre;
		$this->descripcion=$m->descripcion;
		$this->estado=$m->estado;
		if($m->id!=''){
			$this->existe='S';
			#array de usuarios que pertenecen al rol
			$m1=$this->lee_todo("SELECT u.id as id_usuario FROM core_usuarios_roles ur, core_usuarios u where u.id=ur.id_usuario and ur.id_rol='{$this->id}'");
			for ($i=0; $i <count($m1) ; $i++) { 
				$this->usuarios[]=$m1[$i]->id_usuario;
			}
			#permisos concedidos al rol
			$m2=$this->lee_todo("SELECT id_programa,opcion, id_programa_opcion FROM `v_usuarios_permisos` WHERE id_rol='{$this->id}'");
			$this->permisos=$m2;
		}
	}

	public function grabar(){
		$this->isolation('committed');
		if($this->existe=='S'){
			$this->ejecuta_query("UPDATE core_roles set descripcion='{$this->descripcion}', estado='{$this->estado}' where id='{$this->id}'");
		}else{
			$this->ejecuta_query("INSERT INTO core_roles (nombre, descripcion, estado) values ('{$this->nombre}', '{$this->descripcion}', '{$this->estado}')");
		}
		#elimina usuarios del rol, para asignarlos nuevamente
		$this->ejecuta_query("DELETE FROM core_usuarios_roles where id_rol='{$this->id}'");
		if(is_array($this->usuarios)){
			$m=$this->usuarios;
			for ($i=0; $i <count($m) ; $i++) { 
				$this->ejecuta_query("INSERT INTO core_usuarios_roles (id_rol, id_usuario) values ('{$this->id}', '{$m[$i]}')");
			}	
		}
		
	}

	public function eliminarPermisos(){
		$this->isolation('committed');
		#elimina permiso del rol
		$this->ejecuta_query("DELETE FROM core_permisos where id_rol='{$this->id}'");
	}

	public function grabarPermisos(){
		$this->isolation('committed');
		#inserta permisos del rol
		if(is_array($this->permisos)){
			$m=$this->permisos;
			for ($i=0; $i <count($m) ; $i++) { 
				$this->ejecuta_query("INSERT INTO core_permisos (id_rol, id_programa_opcion) values ('{$this->id}', '{$m[$i]['id_programa_opcion']}')");
			}	
		}
		
	}

	public function getListado(){
		$this->isolation('dirty');
		#listado de rols (x estado. activos o inactivos)
		return $this->lee_todo("SELECT nombre,descripcion from core_roles where estado='{$this->estado}'");
	}

	public function traerRolsUsuario($usuario){
		$this->isolation('dirty');
		#trae los rols a los que pertenece un usuario
		$m=$this->lee_todo("SELECT a.nombre, a.descripcion FROM core_roles a, core_usuarios_roles b, core_usuarios c where a.id=b.id_rol and b.id_usuario=c.id and c.usuario='$usuario' and a.estado='A'");
		return $m;
	}
}  
