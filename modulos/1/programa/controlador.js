$(document).ready(function() {
  traerMenus();
  //traerSubMenus();
  traerComponentes();
  
  $("#programa").change(function(event) {
  	buscarPrograma($("#programa").val());
  });
  $("#btn-grabar-general").click(function(event) {
    xajax_grabarGeneral(xajax.getFormValues("frm-general"));
  });
  $("#btn-eliminar-general").click(function(event) {
      toastr.info('Esta seguro de eliminar la aplicacion?. <button type="button" id="okBtn" onclick=eliminarPrograma($("#programa").val()) class="btn btn-flat btn-danger toastr-action">SI</button>', '');
  });
  $("#btn-grabar-permisos").click(function(event) {
    xajax_grabarPermisos(xajax.getFormValues("frm-permisos"),$("#programa").val());
  });
  $("#btn-grabar-componentes").click(function(event) {
    xajax_grabarComponentes(xajax.getFormValues("frm-componentes"),$("#programa").val());
  });
  $("#btn-agregar-permiso").click(function(event) {
    agregarPermiso();
  });
  $("#btn-publicar-programa").click(function(event) {
  	var programa=$("#programa").val();
    if(programa!=''){
      publicarPrograma(programa);
    }else{
      toastr.info("Debe seleccionar primero un programa");
    }
  });
  $(".select2").select2();

});

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
      $("#menu-programa").html('<option value="0">/</option>');
      $.each(data, function(index, val) {
         $("#menu-programa").append('<option value='+val.codigo+'>/'+val.nombre+'</option>');
         $.each(val.sub, function(index, val1) {
           $("#menu-programa").append('<option value='+val1.codigo+'>/'+val.nombre+'/'+val1.nombre+'</option>');
           $.each(val1.sub, function(index, val2) {
             $("#menu-programa").append('<option value='+val2.codigo+'>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'</option>');
             $.each(val2.sub, function(index, val3) {
               $("#menu-programa").append('<option value='+val3.codigo+'>/'+val.nombre+'/'+val1.nombre+'/'+val2.nombre+'/'+val3.nombre+'</option>');
            });
          });
        });
      });
      $("#menu-programa").trigger("change");
        
    }
    });
}
function traerComponentes(){
   $.ajax
  ({
    type: "POST",
    url: "rest.php",
    dataType: 'json',
    async: true,
    data: { "modulo":"programa", 
            "metodo":"traerComponentes",
            "token":getToken()
          },
    success: function (data){
    	$("#frm-componentes .card-body").html('');
      $.each(data, function(index, val) {
         $("#frm-componentes .card-body").append('<div class="form-group">'+
							'<label class="col-sm-12 fancy-checkbox">'+
              '<div class="checkbox checkbox-styled">'+
                '<label>'+
                  '<input type="checkbox" name="componentes[]" class="componentes" id="componente_'+index+'" value="'+index+'"> '+
                '<span class="">'+val.nombre+'</span> </label> '+
								'<span class="text-sm text-default-dark">'+val.descripcion+'</span></div>'+
							'</label>'+
						'</div>');
      });
      $('.checkbox-styled input, .radio-styled input').each(function(){if($(this).next('span').length===0){$(this).after('<span></span>');}});

      
        
    }
    });
}

function traerSubMenus(){
   $.ajax
  ({
    type: "POST",
    url: "rest.php",
    dataType: 'json',
    async: true,
    data: { "modulo":"programa", 
            "metodo":"traerSubmenu",
            "token":getToken()
          },
    success: function (data){
      $("#submenu-programa").html('<option value=""></option>');
      $.each(data, function(index, val) {
         $("#submenu-programa").append('<option value='+val.codigo+'>'+val.nombre+'</option>');
      });
      $("#submenu-programa").trigger('change');
      
        
    }
    });
}
function agregarPermiso(permiso,descripcion){
  permiso=permiso || "";
  descripcion=descripcion || "";
  $("#frm-permisos tbody").append("<tr>"+
    "<td><input type='text' class='form-control' name='permiso[]' value='"+permiso+"'  size=2></td>"+
    "<td><input type='text' class='form-control' name='descripcion[]' value='"+descripcion+"' placeHolder='Descripcion del permiso'></td>"+
  "</tr>");
}

function buscarPrograma(programa){
   $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: true,data: { "modulo":"programa", "metodo":"buscarPrograma","token":getToken(),
            "parametros":{"programa":programa}},
    success: function (data){
      if(data.existe=='S'){
        $('#programa').addClass('alert-success');
        $('#fieldset-nueva-aplicacion').hide();
      }else{
        $('#programa').removeClass('alert-success');
        $('#fieldset-nueva-aplicacion').show();
      }
      $("#programa").val(data.programa);  
      $("#descripcion").val(data.descripcion);  
      $("#menu-programa").val(data.menu).trigger('change');  
      $("#submenu-programa").val(data.submenu).trigger('change');  
      $("#xajaxDefault").val(data.xajaxDefault);  
      var autenticado=true;
      if(data.autenticado!='S') autenticado=false;
      $("#autenticado").prop( "checked", autenticado );

      $("#frm-permisos tbody").html("");
      $.each(data.permisos, function(codigo, nombre) {
         agregarPermiso(codigo,nombre);
      });

      $(".componentes").prop('checked', false);
      $.each(data.componentes, function(id,nombre) {
         if(nombre!=''){
          $("#componente_"+nombre).prop('checked', true);
        }
      });
    }
    });
}

function grabarPermisos(nombre,permisos){
    $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: true,data: { "modulo":"rol", "metodo":"grabarPermisos", "token":getToken(),
      "parametros":{"nombre":nombre,"permisos":permisos}},
    success: function (data){
      //console.log(permisos);
    }
  });
}

function eliminarPrograma(programa){
  $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: true,data: { "modulo":"programa", "metodo":"eliminarPrograma","token":getToken(),
            "parametros":{"programa":programa}},
    success: function (data){
      toastr.success("Programa "+programa+" eliminado");
      buscarPrograma(programa);
    }
  });
}

/*function publicarPrograma(programa){
  $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: true,data: { "modulo":"programa", "metodo":"publicarPrograma","token":getToken(),
            "parametros":{"programa":programa}},
    success: function (data){
      if(data.resultado=='success'){
        toastr.success("Programa "+programa+" publicado");  
      }else{
        toastr.error(data.resultado);  
      }
    }
  });
}*/