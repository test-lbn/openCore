$(document).ready(function() {
	ValidarNotificaciones();
  	//setInterval(ValidarNotificaciones, 300000);
  
});
function ValidarNotificaciones(){
	var info=$.parseJSON(localStorage.getItem('usuario'));
	//var url='http://gestiondocumental.inscra.com/restWF/cuantasactividades.php';
	var url='';
	if(info!=null){
		$.ajax({type: "GET",url: url,dataType: 'json',async: true,
		    data: {"usuario":info.usuario,"token":getToken()},
		    success: function (data){
		    	var cantidad=data.cuantas_actividades;
		    	$(".gestion-documental >sup").remove();
		    	if(cantidad>0){
		    		toastr.info("Tienes "+cantidad+" notificaciones pendientes en Gestion Documental");
					var icono="style-danger";
					$(".gestion-documental").append('<sup class="badge '+icono+'">'+cantidad+'</sup>');
		    	}
		    }
	    });
	}
} 