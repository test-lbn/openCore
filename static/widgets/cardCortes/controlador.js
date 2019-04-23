var obj = function () {var o = this;$(document).ready(function () {o.initialize();});}
var cardCortes = obj.prototype;

cardCortes.initialize=function (){
  this.info();
}
$(document).ready(function() {
  Core.CrearOffCanvas("offcanvasDetalleCorte","6","right"); 
  $(Core.paramWidget['cardCortes']['destino']).append('<div class="col-md-3 col-sm-6" id="cardCortes"></div>');
    //Core.CargarComponente("campana");
    var campana=Campana.TraerCampana("interna");

   $("#cardCortes").load("static/widgets/cardCortes/vista.html?10", function() {
    
    $(".cardCortes-campana").html(campana);
    $.ajax({type: "POST",url: "restWidget.php",dataType: 'json',async: true,data: { "modulo":"cardCortes","metodo":"consultaCortes","parametros":{"campana":campana}},
      success: function (data){
        $.each(data, function(index, val) {
          //console.log(val);
          //$("#cardCortes .btn-"+val.corte).attr("title","Zonas cerradas :"+val.cerradas+"/"+val.zonas);

          $("#cardCortes .btn-"+val.corte).attr("data-original-title","Zonas cerradas :"+val.cerradas+"/"+val.zonas)
          .tooltip();;
          $("#cardCortes .btn-"+val.corte).data(val).data("campana",campana).click(cardCortes.info);;
          if(val.zonas==val.cerradas){
           $("#cardCortes .btn-"+val.corte).removeClass('btn-default-bright').addClass('btn-info');
          }else if(val.cerradas>0){
           $("#cardCortes .btn-"+val.corte).removeClass('btn-default-bright').addClass('btn-warning');
          }
        });
      }
    });
    materialadmin.AppOffcanvas.initialize();
  });
});

cardCortes.info =function(){
  var corte=$(this).data("corte");
  var campana=$(this).data("campana");
  $.ajax({type: "POST",url: "restWidget.php",dataType: 'json',async: true,data: { "modulo":"cardCortes","metodo":"detalleCorte","parametros":{"campana":campana,"corte":corte}},
      success: function (data){
        $("#offcanvasDetalleCorte .offcanvas-head header").html("Zonas corte "+corte);
        $("#offcanvasDetalleCorte .offcanvas-body").html("<table class='table table-condensed table-striped'><thead><tr><td>#<td>Zona<td>Estado</thead><tbody></tbody></table>");
        $.each(data, function(index, val) {
          $("#offcanvasDetalleCorte .offcanvas-body tbody").append("<tr><td>"+(index+1)+"<td>"+val.zona+"<td>"+val.estado);
          //console.log(val);
        });
      }
    });
}