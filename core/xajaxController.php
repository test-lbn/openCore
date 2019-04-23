<?php
/**
* 
* Clase que contiene el controlador base para todo el modelo
*/
class xajaxController
{	
	/**
	 * Objeto xajax
	 *
	 * Objeto de xajax pasado como referencia del original pasado como parametro dentro del constructor.
	 * @access private
	 * @var object
	 */
	private $xajaxObject;

	/**
	 * Respuesta xajax
	 *
	 * Recibe todas las peticiones de respuesta de xajax para poder permitir que este atributo se permita retornar en los metodos de la clases hijas.
	 * @ignore
	 * @var string
	 */
	protected $response;

		private $arrayInvalidMethods = array ('__get','__set','__call',
	   '__isset','__unset','__sleep','__wakeup','__clone','__construct',
	   '__destruct','alert', 'assign', 'replace', 'clear', 'redirect', 'script', 
	   'append', 'prepend', 'call', 'remove', 'create', 'insert', 'insertAfter',
	   'createInput', 'insertInput', 'insertInputAfter', 'addEvent', 'setEvent', 'addHandler', 
	   'removeHandler', 'setFunction', 'wrapFunction', 'includeScript', 
	   'includeScriptOnce', 'removeScript', 'includeCSS', 'removeCSS',
	   'waitForCSS', 'waitFor', 'sleep', 'setReturnValue', 'getContentType', 
	   'getOutput', 'getCommandCount', 'loadCommands', 'closeWindow', 
	   'window', 'closeMessageBox', 'messageBox','notificationWindow',
	   'modalWindow','closeModalWindow','loadHtmlFromFile','modalWindowFromUrl','errorBox','confirmCommands','permiso'
	);
	
	function __construct()
	{
/*		global $MYCONTROLLER_REGISTERED_FUNCTIONS;

			require_once LIBS_PATH.'xajax/xajax_core/xajaxResponse.inc.php';
			$this->response = new xajaxResponse();
			$this->xajaxObject = $GLOBALS['objAjax'];
		if (!count($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'])){
			$methods = get_class_methods( get_class($this) );
			foreach ( $methods as $method ){
				if (!in_array($method,$this->arrayInvalidMethods)){
					if (!isset($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method])){
							$this->xajaxObject->registerFunction(array($method, $this, $method));
						$GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method] = $method;
					}
				}
			}
		}*/
	}


	/**
	 * Procesa la repuesta de Ajax (dependiendo del motor) si existe para ser mostrada o ejecutada en el navegador.
	 */
	public function processRequest (){
		global $MYCONTROLLER_REGISTERED_FUNCTIONS;

			require_once LIBS_PATH.'xajax/xajax_core/xajaxResponse.inc.php';
			$this->response = new xajaxResponse();
			$this->xajaxObject = $GLOBALS['objAjax'];
		
		$GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS']=array();
		if ( is_array( $GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'] ) ) {
			if (!count($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'])){
				$methods = get_class_methods( get_class($this) );
				foreach ( $methods as $method ){
					if (!in_array($method,$this->arrayInvalidMethods)){
						if (!isset($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method])){
								$this->xajaxObject->registerFunction(array($method, $this, $method));
							$GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method] = $method;
						}
					}
				}
			}
		}

		/*if (!count($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'])){
			$methods = get_class_methods( get_class($this) );
			foreach ( $methods as $method ){
				if (!in_array($method,$this->arrayInvalidMethods)){
					if (!isset($GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method])){
							$this->xajaxObject->registerFunction(array($method, $this, $method));
						$GLOBALS['MYCONTROLLER_REGISTERED_FUNCTIONS'][$method] = $method;
					}
				}
			}
		}*/
		$this->xajaxObject->processRequest();
		
	}

	/**
	 * Muestra una alerta.
	 *
	 * Muestra un dialogo de alerta al usuario.
	 * @param string $srtMsg El mensaje a mostrar.
	 */
	public function alert($srtMsg){

		$this->response->alert($srtMsg);
	}
	
	public function confirmCommands($lines,$mensaje){

		$this->response->confirmCommands($lines,$mensaje);
	}


	/**
	 * Asigna un contenido.
	 *
	 * Asigna un nuevo contenido a un elemento definido mediante DOM.
	 * @param  string idElement El id del elemento HTML en el browser.
	 * @param  string $propertyElement La propiedad del elemento a usar para la asigancion.
	 * @param  string $newValue El valor a ser asignado a la propiedad.
	 */
	public function assign($idElement, $propertyElement, $newValue){

		$this->response->assign($idElement, $propertyElement, $newValue);
	}

	/**
	 * Reemplaza un contenido.
	 *
	 * Reemplaza el o parte del contenido de un elemento definido.
	 * @param  string $idElement El id del elemento HTML en el browser.
	 * @param  string $propertyElement La propiedad del elemento a actualizar.
	 * @param  string $strToFind El valor que desea reemplazar.
	 * @param  string $newValue El nuevo dato que reemplazara el valor buscado.
	 */
	public function replace($idElement, $propertyElement, $strToFind, $newValue){

		$this->response->replace($idElement,$propertyElement, $strToFind, $newValue);
	}

	public function loadCommands($func){

		$this->response->loadCommands($func);
	}

	/**
	 * Response command used to clear the specified property of the given element.
	 *
	 * @param  string $idElement The id of the element to be updated.
	 * @param  string $propertyElement The property to be clared.
	 */
	public function clear ($idElement, $propertyElement){

		$this->response->clear($idElement, $propertyElement);
	}

	/**
	 * Response command that causes the browser to navigate to the specified URL.
	 *
	 * @param  string $strUrl The relative or fully qualified URL.
	 * @param  integer $intDelaySeconds Optional number of seconds to delay before the redirect occurs.
	 */
	public function redirect($strUrl, $intDelaySeconds = 1){

		$this->response->redirect($strUrl, $intDelaySeconds);
	}

	/**
	 * Ejecutar script
	 *
	 * Response command that is used  to execute a
	 * portion of javascript on the browser.
	 * The script  runs  in its own  context,  so
	 * variables declared locally, using the 'var'
	 * keyword, will  no longer be available after
	 * the call.
	 * To construct a variable that will be accessable
	 * globally,  even after the script  has executed,
	 * leave off the �var� keyword.
	 *
	 * @param  string $strJs The script to execute.
	 */
	public function script ($strJs){

		$this->response->script($strJs);
	}

	/**
	 * Response  command that indicates the specified
	 * data should be appended to the given elements
	 * property.
	 *
	 * @param  string $idElement The id of the element to be updated.
	 * @param  string $propertyElement The name of the property to be appended to.
	 * @param  string $dataAppended The data to be appended to the property.
	 */
	public function append ($idElement, $propertyElement, $dataAppended){

		$this->response->append($idElement, $propertyElement, $dataAppended);
	}

	/**
	 * Response command to prepend the specified value
	 * onto the given element�s property.
	 *
	 * @param  string $idElement The id of the element to be updated.
	 * @param  string $propertyElement The property to be updated.
	 * @param  string $dataPrepended The value to be prepended.
	 */
	public function prepend ($idElement, $propertyElement, $dataPrepended){

		$this->response->prepend($idElement, $propertyElement, $dataPrepended);
	}

	/**
	 * Response command that indicates that the  specified
	 * javascript function should be called with the given
	 * (optional) parameters.
	 *
	 * @param  string $strFunctionToCall The name of the function to call.
	 * @param  arg2 .. argn .. arguments to be passed to the function.
	 */
	public function call ($strFunctionToCall, $params){

		$this->response->call($strFunctionToCall, $params);
	}

	/**
	 * Response command used to remove an element from the document.
	 *
	 * @param  string $strFunctionToCall The id of the element to be removed.
	 */
	public function remove ($strFunctionToCall){

		$this->response->remove($strFunctionToCall);
	}

	/**
	 * Response command used to create a new element on the browser.
	 *
	 * @param  string $idParentElement The id of the parent element.
	 * @param  string $tagNewElement The tag name to be used for the new element.
	 * @param  string $idNewElement The id to assign to the new element.
	 * @param  string $tagType optional: The type of tag, deprecated, use xajaxResponse->createInput instead.
	 */
	public function create ($idParentElement, $tagNewElement, $idNewElement, $tagType){

		$this->response->create($idParentElement, $tagNewElement, $idNewElement, $tagType = '');
	}

	/**
	 * Response command used to insert a new element just
	 * prior to the specified element.
	 *
	 * @param  string $idElementRef The element used as a reference point for the insertion.
	 * @param  string $tagNewElement The tag to be used for the new element.
	 * @param  string $idNewElement The id to be used for the new element.
	 */
	public function insert ($idElementRef, $tagNewElement, $idNewElement){

		$this->response->insert($idElementRef, $tagNewElement, $idNewElement);
	}

	/**
	 * Response command used to insert a new element after
	 * the specified one.
	 *
	 * @param  string $idElementRef The id of the element that will be used as a reference for the insertion.
	 * @param  string $tagNewElement The tag name to be used for the new element.
	 * @param  string $idNewElement The id to be used for the new element.
	 */
	public function insertAfter ($idElementRef, $tagNewElement, $idNewElement){

		$this->response->insertAfter($idElementRef, $tagNewElement, $idNewElement);
	}

	/**
	 * Response command used to create an input element on
	 * the browser.
	 *
	 * @param  string $idParentElement The id of the parent element.
	 * @param  string $typeNewElement The type of the new input element.
	 * @param  string $strNameNewElement The name of the new input element.
	 * @param  string $idNewElement The id of the new element.
	 */
	public function createInput ($idParentElement, $typeNewElement, $strNameNewElement, $idNewElement){

		$this->response->createInput($idParentElement, $typeNewElement, $strNameNewElement, $idNewElement);
	}

	/**
	 * Response command used to insert a new input element
	 * preceeding the specified element.
	 *
	 * @param  string $idElementRef The id of the element to be used as the reference point for the insertion.
	 * @param  string $typeNewElement The type of the new input element.
	 * @param  string $strNameNewElement The name of the new input element.
	 * @param  string $idNewElement The id of the new input element.
	 */
	public function insertInput ($idElementRef, $typeNewElement, $strNameNewElement, $idNewElement){
			
		$this->response->insertInput($idElementRef, $typeNewElement, $strNameNewElement, $idNewElement);
	}

	/**
	 * Response command used to insert a new input element
	 * after the specified element.
	 *
	 * @param  string $idElementRef The id of the element that is to be used as the insertion point for the new element.
	 * @param  string $typeNewElement The type of the new input element.
	 * @param  string $strNameNewElement The name of the new input element.
	 * @param  string $idNewElement The id of the new input element.
	 */
	public function insertInputAfter ($idElementRef, $typeNewElement, $strNameNewElement, $idNewElement){
			
		$this->response->insertInputAfter($idElementRef, $typeNewElement, $strNameNewElement, $idNewElement);
	}

	/**
	 * Response command used to add an event handler on the
	 * browser.
	 *
	 * @param  string $idELement The id of the element that contains the event.
	 * @param  string $strNameEvent The name of the event.
	 * @param  string $strNameFunction The javascript to execute when the event is fired.
	 */
	public function addEvent ($idELement, $strNameEvent, $strNameFunction){

		$this->response->addEvent($idELement, $strNameEvent, $strNameFunction);
	}

	/**
	 * Response command used to set an event handler on the
	 * browser.
	 *
	 * @param  string $idELement The id of the element that contains the event.
	 * @param  string $strNameEvent The name of the event.
	 * @param  string $strNameFunction The javascript to execute when the event is fired.
	 */
	public function setEvent ($idELement, $strNameEvent, $strNameFunction){

		$this->response->setEvent($idELement, $strNameEvent, $strNameFunction);
	}

	/**
	 * Response command used to install an event handler on the
	 * specified element.
	 *
	 * @param  string $idElement The id of the element.
	 * @param  string $strNameEvent The name of the event to add the handler to.
	 * @param  string $strNameFunction The javascript function to call when the event is fired.
	 *
	 * You can add more than one event handler to an element�s event using this method.
	 */
	public function addHandler ($idElement, $strNameEvent, $strNameFunction){
			
		$this->response->addHandler($idElement, $strNameEvent, $strNameFunction);
	}

	/**
	 * Response command used to remove an event handler from
	 * an element.
	 *
	 * @param  string $idElement The id of the element.
	 * @param  string $strNameEvent The name of the event.
	 * @param  string $strNameFunction The javascript function that is called when the event is fired.
	 */
	public function removeHandler ($idElement, $strNameEvent, $strNameFunction){

		$this->response->removeHandler($idElement, $strNameEvent, $strNameFunction);
	}

	/**
	 * Response command used to construct a javascript function
	 * on the browser.
	 *
	 * @param  string $strNameFunction The name of the function to construct.
	 * @param  string $params Comma separated list of parameter names.
	 * @param  string $jsCode The javascript code that will become the body of the function.
	 */
	public function setFunction ($strNameFunction, $params, $jsCode){

		$this->response->setFunction($strNameFunction, $params, $jsCode);
	}

	/**
	 * Response command used to construct a wrapper function
	 * around and existing javascript function on the browser.
	 *
	 * @param  string $strNameFunction The name of the existing function to wrap.
	 * @param  string $params The comma separated list of parameters for the function.
	 * @param  array $mixedJsCode  An array of javascript code snippets that will be used to build the body of the function.  The first piece of code specified in the array will occur before the call to the original function, the second will occur after the original function is called.
	 * @param  string $varReturn The name of the variable that will retain the return value from the call to the original function.
	 */
	public function wrapFunction ($strNameFunction, $params, $mixedJsCode, $varReturn){
			
		$this->response->wrapFunction($strNameFunction, $params, $mixedJsCode, $varReturn);
	}

	/**
	 * Response command used to load a javascript file on the
	 * browser.
	 *
	 * @param  string $strUri The relative or fully qualified URI of the javascript file.
	 */
	public function includeScript ($strUri){

		$this->response->includeScript($strUri);
	}

	/**
	 * Response command used to include a javascript file on
	 * the browser if it has not already been loaded.
	 *
	 * @param  string $strUri The relative for fully qualified URI of the javascript file.
	 */
	public function  includeScriptOnce ($strUri){

		$this->response->includeScriptOnce($strUri);
	}

	/**
	 * Response command used to remove a SCRIPT reference to
	 * a javascript file on the browser.  Optionally,    you
	 * can call a javascript function just prior to the file
	 * being unloaded (for cleanup).
	 *
	 * @param  string $strUri The relative or fully qualified URI of the javascript file.
	 * @param  string $strNameFunction Name of a javascript function to call prior to unlaoding the file.
	 */
	public function removeScript ($strUri, $strNameFunction){

		$this->response->removeScript($strUri, $strNameFunction);
	}

	/**
	 * Response command used to include a LINK reference to
	 * the specified CSS file on the browser.     This will
	 * cause the browser to load and apply the style sheet.
	 *
	 * @param  string $strUri The relative or fully qualified URI of the css file.
	 */
	public function includeCSS ($strUri){

		$this->response->includeCSS($strUri);
	}

	/**
	 * Response command used to remove a LINK  reference to a
	 * CSS file on the browser.  This causes the  browser  to
	 * unload the style sheet, effectively removing the style
	 * changes it caused.
	 *
	 * @param  string $strUri The relative or fully qualified URI of the css file.
	 */
	public function removeCSS ($strUri){

		$this->response->removeCSS($strUri);
	}

	/**
	 * Response command instructing xajax to pause while the CSS
	 * files are loaded.
	 * The browser is not typically a multi-threading application,
	 * with regards to javascript code.  Therefore, the CSS files
	 * included or removed with xajaxResponse->includeCSS and
	 * xajaxResponse->removeCSS respectively, will not be loaded
	 * or removed until the browser regains control from the script.
	 * This command returns control back to the browser and pauses
	 * the execution of the response until the CSS files, included
	 * previously, are loaded.
	 *
	 * @param  integer $insSeconds The number of 1/10ths of a second to pause before timing out and continuing with the execution of the response commands.
	 */
	public function  waitForCSS ($insSeconds){

		$this->response->waitForCSS($insSeconds);
	}

	/**
	 * Response command instructing xajax to delay execution of the
	 * response commands until a specified condition is met.
	 * Note, this returns control to the browser, so that other
	 * script operations can execute.  xajax will continue to
	 * monitor the specified condition and, when it evaulates to
	 * true, will continue processing response commands.
	 *
	 * @param  string $jsPiece A piece of javascript code that evaulates to true or false.
	 * @param  integer $intSeconds The number of 1/10ths of a second to wait before timing out and continuing with the execution of the response commands.
	 */
	public function waitFor ($jsPiece, $intSeconds){

		$this->response->waitFor($jsPiece, $intSeconds);
	}

	/**
	 * Response command which instructs xajax to pause execution
	 * of the response commands, returning control to the browser
	 * so it can perform other commands asynchronously.
	 * After the specified delay, xajax will continue execution of the response commands.
	 *
	 * @param  integer $intSeconds The number of 1/10ths of a second to sleep.
	 */
	public function sleep ($intSeconds){

		$this->response->sleep($intSeconds);
	}

	/**
	 * Stores a value that will be passed back as part of the response.
	 * When making synchronous requests, the calling javascript can
	 * obtain this value immediately as the return value of the xajax.call function.
	 *
	 * @param  mixed $strValue Any value.
	 */
	public function setReturnValue ($strValue){
			
		$this->response->setReturnValue($strValue);
	}

	/**
	 * Cierra una ventana.
	 *
	 * Cierra la ventana actualmente abierta.
	 */
	public function closeWindow (){

		$this->response->script('window.close()');
	}
}

