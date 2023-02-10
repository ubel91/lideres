// import $ from 'jquery';
//
// const Routing = require('./routing');

let $deleteBtn;
var table;
let route;

$(document).ready( function () {


    function initDataTable(){

        table = $('#table').DataTable({
            "language": {
                "url": '../../build/assets/i18n/spanish.json'
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                { "extend": 'excelHtml5', "text":'<span class="fa fa-file-excel"></span> Exportar',"className": 'btn btn-default btn-xs' },,
                { "extend": 'csvHtml5', "text":'<span class="fa fa-file-excel"></span> Exportar',"className": 'btn btn-default btn-xs' },
            ],
            drawCallback: function () {
                let pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                pagination.toggle(this.api().page.info().pages > 1);
            }
        });


    }

    initDataTable();


    $deleteBtn = $('button.btnDelete');

    $deleteBtn.click((e)=>{

        let id = this.activeElement.id;
        let selectorBtn = "#"+id;
        let textDelete = "Esta acción <b class='text-danger'>NO</b> se puede deshacer.";
        if (typeof(delete_message) !== "undefined")
            textDelete = delete_message + textDelete;

        swal.fire({
            title: '<span class="text-danger">'+'Se desea borrar registro de docentes y estudiantes por periodo terminado'+'</span>',
            html: textDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                let route = $(selectorBtn).data('routename');
                let Ruta = Routing.generate(route,{id:id});
                $.ajax({
                    type: 'POST',
                    url: Ruta,
                    data: ({id: id}),
                    async: true,
                    beforeSend: () => {
                        $('.overlayGeneral').show();
                    },
                    success: (data, response) => {
                        if (data.success){
                            table.row('#tr'+id).remove().draw(false);
                            swal.fire(
                                'Borrado',
                                data.success,
                                'success'
                            );
                        } else if(data.error) {
                            swal.fire(
                                'Borrado',
                                data.error,
                                'error'
                            );
                        }

                    },
                    complete: function () {
                        $('.overlayGeneral').hide();
                    }
                })
            }
        });
    });
});




