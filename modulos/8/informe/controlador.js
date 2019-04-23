/**
 * 
 * @autor xxx xxxx (xxxxxxx@lebon.com.co)
 * @fecha   
 * @version 
 */
(function(document, window, $, Core) {
    (function() {

        return informe = {

            /*
            ┏━╸╻ ╻┏┓╻┏━╸╻┏━┓┏┓╻
            ┣╸ ┃ ┃┃┗┫┃  ┃┃ ┃┃┗┫
            ╹  ┗━┛╹ ╹┗━╸╹┗━┛╹ ╹
            ┏━┓╻ ╻┏━╸   ╻┏┓╻┏━╸╻┏━┓╻  ╻╺━┓┏━┓
            ┃┓┃┃ ┃┣╸    ┃┃┗┫┃  ┃┣━┫┃  ┃┏━┛┣━┫
            ┗┻┛┗━┛┗━╸   ╹╹ ╹┗━╸╹╹ ╹┗━╸╹┗━╸╹ ╹
            ┏━╸┏━┓┏┳┓┏━┓┏━┓┏┓╻┏━╸┏┓╻╺┳╸┏━╸┏━┓
            ┃  ┃ ┃┃┃┃┣━┛┃ ┃┃┗┫┣╸ ┃┗┫ ┃ ┣╸ ┗━┓
            ┗━╸┗━┛╹ ╹╹  ┗━┛╹ ╹┗━╸╹ ╹ ╹ ┗━╸┗━┛
            */

            Initialize: function() {

                this.getInformacion();
            },

            /*
            ┏━┓┏━╸╺┳╸╻┏━╸╻┏━┓┏┓╻   
            ┣━┛┣╸  ┃ ┃┃  ┃┃ ┃┃┗┫   
            ╹  ┗━╸ ╹ ╹┗━╸╹┗━┛╹ ╹
            ┏━┓ ┏┓┏━┓╻ ╻
            ┣━┫  ┃┣━┫┏╋┛
            ╹ ╹┗━┛╹ ╹╹ ╹
            */

            ajaxRequest: function(data, type, method) {
                return $.ajax({
                    url: 'rest.php',
                    data: data,
                    type: type,
                    dataType: 'json',
                    async: true,
                    data: {
                        "modulo": "informe",
                        "metodo": method,
                        "token": getToken(),
                        "parametros": data
                    }
                })
            },

            getInformacion: function() {

                this.ajaxRequest({},'post', 'getInformacion')
                    .done(function(response){

                    $('#informe-datatable').DataTable().destroy();

                    $("#informe-datatable").DataTable({
                        "dom": 'T<"clear">lfrtip',
                        "aLengthMenu": [
                        [10, 50, 100, 300, -1],
                        [10, 50, 100, 300, "Todos"]
                        ],
                        "tableTools": {
                            "sSwfPath": "static/componentes/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                            "aButtons": [
                                // "copy",
                                // "print",
                                {
                                    "sExtends": "collection",
                                    "sButtonText": "Guardar",
                                    "aButtons": ["csv", "xls", "pdf"]
                                }
                            ]
                        },
                        "aaData": response,
                        "aoColumns": [
                            { "mDataProp": "id" },
                            { "mDataProp": "date_entered" },
                            { "mDataProp": "modified_user_id" },
                            { "mDataProp": "created_by" },
                            { "mDataProp": "deleted" }
                        ],
                        "bSort" : true,
                        "bPaginate": true,
                        language: {
                            "url": "static/componentes/DataTables/extensions/Spanish.json"
                        },
                    });
                });
            },
        }
    })()
    informe.Initialize()
})(document, window, jQuery, Core)