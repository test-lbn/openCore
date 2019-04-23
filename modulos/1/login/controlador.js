/**
 * 
 * @autor Alvaro Pulgarin (aepulgarin@gmail.com)
 * @fecha 09/01/2019  
 * @version 2.0
 */
 (function(document, window, $, Core) {
  (function() {

    return login = {

      usuario: "",
            //INICIALIZA COMPONENTES
            Initialize: function() {
              var self = this;

              $("#db").parent().hide();
              localStorage.removeItem('menu');
              localStorage.removeItem('usuario');
              self.ValidarMultipleDB();

              $("#password").keydown(function(e) {
                if(e.keyCode=='13'){
                  self.autenticar();
                }
              });

              $("#btn-ingreso-app").click(function(event) {
                self.autenticar();
              });
              usuario=localStorage.getItem('username');
              if(usuario!=null){
                $('#username').val(usuario).change();
                $("#recordarme").prop('checked',true);
                $('#password').focus();
              }
            },

            //PETICION AJAX
            ajaxRequest: function(data, type, method) {
              return $.ajax({
                url: 'rest.php',
                data: data,
                type: type,
                dataType: 'json',
                async: true,
                data: {
                  "modulo": "login",
                  "metodo": method,
                  "token": getToken(),
                  "parametros": data
                }
              })
            },

            ValidarMultipleDB: function() {
              var self = this;
              self.ajaxRequest({
              },
              'post', 'ValidarMultipleDB')
              .done(function(response) {
                if(response.multiple=='S'){
                  $("#db").parent().show();
                  $("#db").val(response.defecto);
                }else{
                  $("#db").parent().remove();
                }

              });
            },

            autenticar: function() {
              var self = this;
              self.ajaxRequest({
                "usuario": $('#username').val(),
                "password": sha1($('#password').val()),
                "db": $('#db').val()
              },
              'post', 'autenticar')
              .done(function(response) {
                if(response.mensaje=='Exitoso'){
                  localStorage.setItem('usuario',JSON.stringify(response.info));
                  if($("#recordarme").prop('checked')){
                    localStorage.setItem('username',$('#username').val());
                  }else{
                    localStorage.removeItem('username');
                  }
                  var redir=Core.GetUrlParameter('redir');
                  if(redir){
                    redir="&redirect="+redir;
                  }else{
                    redir="";
                  }
                  document.location='index.php?modulo=inicio'+redir;
                }else{
                  toastr.error(response.mensaje);
                }
              });
            },
          }
        })()
        login.Initialize()
      })(document, window, jQuery, Core)