$(document).ready(function() {
	$("#nombre-modulo").change(function(event) {
		buscarModulo($(this).val());
	});
	$("#btn-grabar-modulo").click(function(event) {
		grabarModulo();
	});
	$("#icono-modulo").change(function(event) {
		$("#preview-icono").removeClass().addClass("fa").addClass($(this).val()).addClass("fa-2x");
	});

	if( $('.select2').length > 0) {
		$('.select2').select2();
	}
	traerMenus();

});

function buscarModulo(modulo){
	$.ajax({
		type: "POST",
		url: "rest.php",
		dataType: 'json',
		async: true,
		data: { "modulo":"menu", 
		"metodo":"buscarModulo", 
		"token":getToken(),
		"parametros":{
			"modulo":modulo
		}
	},
	success: function (data){
		//console.log(data);
		if(data.existe=='S'){
			$("#nombre-modulo").attr('readonly', 'readonly');
		}
		$("#nombre-modulo").val(data.modulo);
		$("#menu-modulo").val(data.id_sub).change();
		$("#icono-modulo").val(data.icono).change();
		$("#orden-modulo").val(data.orden).change();
	}
	});
}
function grabarModulo(){
	$.ajax({
		type: "POST",
		url: "rest.php",
		dataType: 'json',
		async: true,
		data: { "modulo":"menu", 
		"metodo":"grabarModulo", 
		"token":getToken(),
		"parametros":{
			"frm":Core.FormToJSON('#frm')
		}
	},
	success: function (data){
		if(data.mensaje=='exitoso'){
			toastr.success("Modulo grabado");
		}else{
			toastr.error(data.mensaje);
		}
	}
	});
}

function traerMenus(){
   $.ajax
  ({
    type: "POST",
    url: "rest.php",
    dataType: 'json',
    async: true,
    data: { "modulo":"menu", 
            "metodo":"traerMenu",
            "token":getToken()
          },
    success: function (data){
      console.log(data);
      $("#menu-modulo").html('<option value="0">/</option>');
     $("#menu-modulo").html('<option value="0">/</option>');
      $.each(data, function(index, val) {
         $("#menu-modulo").append('<option value='+val.codigo+'>/'+val.nombre+'</option>');
         $.each(val.sub, function(index, val1) {
           $("#menu-modulo").append('<option value='+val1.codigo+'>/'+val.nombre+'/'+val1.nombre+'</option>');
           $.each(val1.sub, function(index, val2) {
             $("#menu-modulo").append('<option value='+val2.codigo+'>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'</option>');
             $.each(val2.sub, function(index, val3) {
               $("#menu-modulo").append('<option value='+val3.codigo+'>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'/'+val3.nombre+'</option>');
            });
          });
        });
      });
      $("#menu-modulo").trigger("change");
        
    }
    });
}
