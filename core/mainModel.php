<?php

abstract class mainModel
{
	private $DB;
	private $URL='';
	public $conexion=array(
		"motor"=>"",
		"host"=>"",
		"db"=>"",
		"usuario"=>"",
		"password"=>"",
		"puerto"=>"",
		"server"=>"",
		"status"=>"off",
		"default"=>false
	);
	public $exceptionMode ="die";

	function __construct(){
		
	}
	function Conectarse($conexion_=""){
		$this->conexion=(object) $this->conexion;
		#Conexion personalizada desde archivo de configuracion .ini
		if($conexion_!=''){
			$file=CORE."/config/".trim($conexion_).".ini";
			if(file_exists($file)){
				$config=parse_ini_file($file,true);
				$config=$config['database'];
				$this->conexion->motor=$config['motor'];
				$this->conexion->host=$config['servidor'];
				$this->conexion->db=$config['base'];
				$this->conexion->usuario=$config['usuario'];
				$this->conexion->password=$config['clave'];
				$this->conexion->puerto=$config['puerto'];
				$this->conexion->server=$config['server'];
			}

		#Conexion por defecto segun .ini que se determina por el dominio	
		}else if($this->conexion->host==''){
			$this->conexion->default=true;
			$config=$_SESSION['config']['database'];
			$this->conexion->motor=$config['motor'];
			$this->conexion->host=$config['servidor'];
			$this->conexion->db=$config['base'];
			$this->conexion->usuario=$config['usuario'];
			$this->conexion->password=$config['clave'];
			$this->conexion->puerto=$config['puerto'];
			$this->conexion->server=$config['server'];
			#Excepciones de conexion segun SELECT en el login
			if(isset($_SESSION['cambio_db'])){
				$this->conexion->server=$_SESSION['cambio_db'];
				switch($this->conexion->server){
					case 'inscra_':
					$this->conexion->host='10.0.2.24';
					$this->conexion->puerto='1526';
					break;
					case 'inscra_desarrollo':
					$this->conexion->host='10.0.2.124';
					$this->conexion->puerto='1532';
					break;
					case 'inscra_preproduccion':
					$this->conexion->host='10.0.2.224';
					$this->conexion->puerto='1530';
					break;
					case 'inscra_sec':
					$this->conexion->host='10.0.2.22';
					$this->conexion->puerto='1526';
					break;
					case 'inscra_arc':
					$this->conexion->host='10.0.2.20';
					$this->conexion->puerto='1526';
					$this->conexion->usuario='internet';
					$this->conexion->password='internet';
					break;
				}
			}
		}

		if(!isset($GLOBALS['DB']) || $this->conexion->default==false){
			#realiza conexion a la base de datos correspondiente
			try{
				switch ($this->conexion->motor) {
					case 'mysql':
					$dbHandle = new PDO("mysql:host={$this->conexion->host}; dbname={$this->conexion->db}", $this->conexion->usuario, $this->conexion->password);
					$dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
					//$dbHandle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
					break;
					case 'mssql':
					$dbHandle = new PDO("dblib:host={$this->conexion->host}:{$this->conexion->puerto}; dbname={$this->conexion->db}", $this->conexion->usuario, $this->conexion->password);
					$dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
					break;
					case 'informix':
					$dbHandle = new PDO("informix:host={$this->conexion->host};service={$this->conexion->puerto};database={$this->conexion->db};server={$this->conexion->server};protocol=onsoctcp;EnableScrollableCursors=1",$this->conexion->usuario,$this->conexion->password);
					$dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dbHandle->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
					break;

					default:
					die("Motor -{$this->conexion->motor}- no soportado aun");
					break;
				}
				$this->DB= $dbHandle;
				$this->conexion->status='on';
			}catch( PDOException $exception ){
				$this->error_PDO($exception,print_r($this->conexion,true));
			}
			if($this->conexion->default){
				$GLOBALS['DB']=$this->DB;
			}
		}else{
			if($this->conexion->default){
				$this->DB=$GLOBALS['DB'];
			}
		}
	}

	function lee_todo($query){

		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		if (!isset($base)){
			$base='';
		}

		if (!isset($_SESSION['usuario'])){
			$_SESSION['usuario']='';
		}

		if (!isset($_SESSION['datos_adicionales'])){
			$_SESSION['datos_adicionales']='';
		}

		if (!isset($_SESSION['nombreusu'])){
			$_SESSION['nombreusu']='';
		}

		$bk_query=$query;
		$query = "-- $base".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$query;
		try{ 
			$statement = $this->DB->query($query);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$query);
		}

		$colcount = $statement->columnCount();
		$encontrado = "no"; 
		$arr_bool = Array(); $arr_fechas = Array(); 
    	# FIX fechas o booleanos, compatibilidad APPS viejas
		if(in_array($this->conexion->motor, array("mysql","informix"))){ 
			for ($i=1; $i <= $colcount; $i++) { 
				$meta = $statement->getColumnMeta(($i-1));
				if($meta['native_type'] == "DATE"){
					$encontrado = "si";
					$arr_fechas[] = $meta['name'];
				} else if($meta['native_type'] == "BOOLEAN"){
					$encontrado = "si";
					$arr_bool[] = $meta['name'];
				}
				if($meta['name']==''){
					$encontrado = "si";
					$campo_vacio = "si";
				}
			} 
		}   
		try{ 
			$rows = $statement->fetchAll(PDO::FETCH_CLASS);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$query);            
		} 
		# Si encuentra campos del FIX los recorre para realizar la correccion
		if($encontrado == "si"){
			for ($i=0; $i < count($rows); $i++) { 
				// para corregir las fechas
				if(count($arr_fechas) > 0){
					$count_fechas=count($arr_fechas);
					for ($j=0; $j < $count_fechas; $j++) { 
						$registros='';
						@preg_match ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $rows[$i]->$arr_fechas[$j], $registros);
						if($registros!=''){//fix para campos tipo fecha nulos
							$rows[$i]->$arr_fechas[$j] = $registros[2]."/".$registros[3]."/".$registros[1];
						}
					}
				}
				// para corregir los booleanos
				if(count($arr_bool) > 0){
					$count_arr_bool=count($arr_bool);
					for ($j=0; $j < $count_arr_bool; $j++) { 
						$rows[$i]->$arr_bool[$j] = $rows[$i]->$arr_bool[$j]==0?"f":"t"; 
					}
				}
			}
		}
		#Informacion para debug.php

		if (!isset($_SESSION['debug_lee_todo'])){
			$_SESSION['debug_lee_todo']='';
		}

		if($_SESSION['debug_lee_todo'] == "1"){
			$_SESSION['contenido_debug'] .= "<div class='debug_lee_todo'>$bk_query<br><a href='javascript:void(0)' onclick='xajax_traer_lee_todo(\"".base64_encode($bk_query)."\")' />Ver resultados</a><br><b><i>-&gt; ".count($rows)." registros devueltos</i></b></div>";
		}
		return $rows;
	}
	function ejecuta_query($queri,$retorna='count'){

		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		if (!isset($base)){
			$base='';
		}

		if (!isset($_SESSION['usuario'])){
			$_SESSION['usuario']='';
		}

		if (!isset($_SESSION['datos_adicionales'])){
			$_SESSION['datos_adicionales']='';
		}

		if (!isset($_SESSION['nombreusu'])){
			$_SESSION['nombreusu']='';
		}

		$bk_query=$queri;
		$queri = "-- $base".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$queri;
		try{
			$cant=$this->DB->exec($queri);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$queri);
		}

		if (!isset($_SESSION['debug_ejecuta_query'])){
			$_SESSION['debug_ejecuta_query']='';
		}

		if($_SESSION['debug_ejecuta_query'] == "1"){
			$_SESSION['contenido_debug'] .= "<div class='debug_ejecuta_query'><br>$bk_query<br><b><i>-&gt; ".$cant." registros afectados</i></b></div>";
		}
		if($retorna=='count') return $cant;
		else return $this->DB->lastInsertId();
	}
	function begin_work(){
		$this->DB->beginTransaction();
	}
	function commit(){
		$this->DB->commit();
	}
	function rollback(){
		$this->DB->rollback();
	}
	function ejecuta_sp($queri){

		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		$queri = "-- $base".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$queri;
		try{
			$statement = $this->DB->query($queri);
		}catch( PDOException $exception ){
			error_PDO($exception,$queri);

		}
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		for ($i=0; $i <count($rows) ; $i++) { 
			$result[]=trim($rows[$i]['']);
		} 
		if(count($rows)==1) $result=$result[0];
		return $result;
	}

	function lee_uno($query){
		$mat=$this->lee_todo($query);
		return $mat['0'];
	}

	function isolation($tipo){
		switch ($this->conexion->motor) {
			case 'informix':
				switch (strtolower($tipo)) {
					case 'dirty':
					$sql_add="DIRTY READ";
					break;
					case 'committed':
					$sql_add="COMMITTED READ";
					break;
				}
				$this->ejecuta_query("SET ISOLATION TO $sql_add");
				break;
			default:
				# code...
				break;
		}
		
	}
	function wait(){
		switch ($this->conexion->motor) {
			case 'informix':
				$this->ejecuta_query("SET LOCK MODE TO WAIT");
				break;
			default:
				# code...
				break;
		}
	}


	function guardaLog($accion, $antes, $despues, $solicitud, $tabla=""){
		$usuario=$_SESSION['usuario'];
		$sql_guarda_log="INSERT INTO logs(id_solicitud, tabla, accion, usuario, fecha_grab, antes, despues) VALUES('$solicitud', '$tabla', '$accion', '$usuario', current, '$antes', '$despues')";
		$this->ejecuta_query($sql_guarda_log);
	}
	/**
 * [getSequence Ontiene el consecutivo de la tabla ]
 * @param  [type] $sequence [Nombre de la Tabla ]
 * @return [type]           [Consecutivo]
 */
	function getSequence($sequence){

		$consulta="execute procedure spUpdateSequence('$sequence')";
		$sequence=$this->ejecuta_sp($consulta);	  
		return $sequence;
	}

	function core_log_programa($modulo, $metodo){
		/*
		$usuario_=$_SESSION['usuario'];
		$consulta="SELECT usuario from ins_log_programas where usuario='$usuario_' and programa='$modulo' and parametros='$metodo'";
		$mat_logprog=$this->lee_todo($consulta);
		if(count($mat_logprog)==0){
			$inserta="INSERT into ins_log_programas (usuario, programa, parametros,core) values ('$usuario_','$modulo','$metodo','S')";
			$this->ejecuta_query($inserta);
		}else{
			$actualiza="UPDATE ins_log_programas set veces=veces+1, ultimo_ingreso=today where usuario='$usuario_' and programa='$modulo' and parametros='$metodo'";
			$this->ejecuta_query($actualiza);
		}
		*/
	}
	/*Funcion para cargar los datos de un array  a atributos del modelo*/
	function atributos($datos,$destino){
		foreach ((array)$datos as $key => $value) {
			$this->$destino->$key=trim($value);
		}
	}
	function error_PDO($exception,$query){
		switch ($this->exceptionMode) {
			case 'throw':
				throw $exception;
				break;
			case 'die':
				echo "<pre>";
				print_r($exception);
				print_r($query);
				die("");
				break;
		}	
	}
}

// class mainModelMongo extends MongoClient
// {
// 	private $DBMongo;
// 	private $server_;
// 	private $usuario;
// 	private $password;
// 	private $host;
// 	private $puerto;
	

// 	function __construct($db){
// 		$config=$_SESSION['config']['database'];
// 		$this->server_=$config['server'];
// 		#Excepciones de conexion segun SELECT en el login
// 		if(isset($_SESSION['cambio_db'])){
// 			$this->server_=$_SESSION['cambio_db'];
// 		}
// 		switch($this->server_){
// 			case 'inscra':
// 			$this->host='10.0.2.25';
// 			$this->puerto='27017';
// 			break;
// 			case 'inscra_desarrollo':
// 			$this->host='10.0.2.25';
// 			$this->puerto='27017';
// 			break;
// 			case 'inscra_preproduccion':
// 			$this->host='10.0.2.225';
// 			$this->puerto='27017';
// 			break;
// 			default:
// 			$this->host='10.0.2.125';
// 			$this->puerto='27017';
// 			break;
// 		}
		
		
// 		$string_conexion="mongodb://{$this->host}:{$this->puerto}";
// 		//echo $string_conexion;
// 		parent::__construct($string_conexion);
// 		$this->db=$this->$db;
// 	}
// }

//compat esquema viejo
if(!function_exists('guardar_log_errores')){
	function guardar_log_errores($tipo,$ruta,$funcion,$parametros, $desc_error=''){
		global $conex, $ver_log;
		$usuario = $_SESSION['usuario'];
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => "Content-Type: text/xml\r\n".
				"Authorization: Basic ".base64_encode("rsn:vinotinto")."\r\n",
				'content' => $body,
				'timeout' => 60
			)
		);

		$context  = stream_context_create($opts);
		if(!in_array($_SESSION['cambio_db'],array("inscra","") )){
			$rutaLog="http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/";
		}else{
			$rutaLog="http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/".DIRECTORIO."/";
		}
		$url = $rutaLog."log_errores.php?usuario=$usuario&tipo=$tipo&ruta=$ruta&funcion=$funcion&parametros=$parametros&desc_error=$desc_error";
		$regLog = file_get_contents($url, false, $context, -1, 40000);

		return $regLog;
	}
}

