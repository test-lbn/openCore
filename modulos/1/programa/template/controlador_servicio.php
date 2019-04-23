class [[programa]]Controller extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new [[programa]]Business();
	}
	function funcionRest($parametros){
		$resultado['campo1']=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
}	
