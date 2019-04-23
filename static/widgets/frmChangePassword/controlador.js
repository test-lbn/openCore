function cambioContrasena(){
  $("#contrasenaModal").remove();
  $("body").append('<div class="modal fade" id="contrasenaModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true"></div>');
   $("#contrasenaModal").load("static/widgets/frmChangePassword/formulario.html", function() {
    $("#contrasenaModal").modal();
    $("#btn-cambiar-contrasena").click(function(event) {
      var actual=sha1($("#contrasena-actual").val());
      var nueva=sha1($("#contrasena-nueva").val());
      var nueva2=sha1($("#contrasena-nueva2").val());
      if(nueva=="" || nueva2=="" || actual==""){
        toastr.error("Todos los campos son obligatorios");
        return;
      }
      if(nueva!=nueva2){
        toastr.error("Nueva contrase&ntilde;a no coincide con verificaci&oacute;n");
        return;
      }
      $.ajax({
        type: "POST",
        url: "rest.php",
        dataType: 'json',
        async: true,
        data: { "modulo":"usuario", 
        "metodo":"cambioContrasena",
        "token":getToken(),
        "parametros":{
          "contrasena-actual":actual,
          "contrasena-nueva":nueva,
          "contrasena-nueva2":nueva2
        }
      },
      success: function (data){
        if(data.mensaje=='exitoso'){
          $("#contrasenaModal").modal();
          $("#contrasenaModal").remove();
          toastr.success("Cambio exitoso");
        }else{
          toastr.error(data.mensaje);
        }
      }
    });
    });  
});

}