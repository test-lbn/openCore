$(document).ready(function() {
	$("#limpiar-formulario").click(function(event) {
		$("#usuario").attr('readonly', false);
		$("#frm")[0].reset();
		$("#usuario").focus();
		$('#frm .help-block').remove();
		$('#frm .form-group').removeClass("has-error");
	});
	$("#grabar-usuario").click(function(event) {
		grabarUsuario();
	});
	$("#usuario").change(function(event) {
		buscarUsuario($(this).val());
	});
	$("#listar-activos").click(function(event) {
		listarUsuarios('A');
	});
	$("#listar-inactivos").click(function(event) {
		listarUsuarios('');
	});

	$("#listar-activos").click();
});

function buscarUsuario(usuario){
	$.ajax({
		type: "POST",
		url: "rest.php",
		dataType: 'json',
		async: true,
		data: { 
			"modulo":"usuario", 
			"metodo":"traerUsuario", 
			"token":getToken(),
			"parametros":{
				"usuario":usuario
			}
		},
		success: function (data){
			if(data.existe=='S'){
				$("#usuario").attr('readonly', 'readonly');
				$("#password1").removeProp("required").removeProp("data-rule-minlength");
				$("#password2").removeProp("required").removeProp("data-rule-minlength");
			}
			$("#nombres").val(data.nombres).change();
			$("#apellidos").val(data.apellidos).change();
			$("#correo").val(data.correo).change();
			var estado;
			if(data.estado=='A') estado=true; else estado=false;
			$("#estado").prop('checked', estado);
		}
	});
}

function grabarUsuario(){
	var form = $( "#frm" );
	if(form.valid()){
		if($("#password1").val() != $("#password1").val()){
			toastr.error("Contrase&ntilde;a no concuerda, verifique");
			return;
		}
		$.ajax({
			type: "POST",
			url: "rest.php",
			dataType: 'json',
			async: true,
			data: { "modulo":"usuario", 
			"metodo":"grabarUsuario", 
			"token":getToken(),
			"parametros":{
				"frm":Core.FormToJSON('#frm')
			}
		},
		success: function (data){
			if(data.mensaje=='exitoso'){
				toastr.success("Usuario grabado");
				$("#limpiar-formulario").click();
				$("#listar-activos").click();
			}else{
				toastr.error(data.mensaje);
			}
		}
	});
	}else{
		toastr.warning("El formulario aun contiene errores, verifique");
	}
	return ;

}

function listarUsuarios(estado){
	$.ajax({
		type: "POST",
		url: "rest.php",
		dataType: 'json',
		async: true,
		data: { 
			"modulo":"usuario", 
			"metodo":"traerLista", 
			"token":getToken(),
			"parametros":{
				"estado":estado
			}
		},
		success: function (data){
			$("#tabla-listado tbody").html("");
			$.each(data, function(index, val) {
			
				 $("#tabla-listado tbody").append('<tr>'+
					'<td>'+val.usuario+'</td>'+
					'<td>'+val.nombres+'</td>'+
					'<td>'+val.apellidos+'</td>'+
					'<td>'+val.correo+'</td>'+
					'<td class="text-right">'+
						'<a href="javascript:void(0)" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Editar usuario" onclick=$("#usuario").val("'+val.usuario+'").change();><i class="fa fa-pencil"></i></a>'+
					'</td>'+
				'</tr>');
			});
		}
	});	
}
/*


 */