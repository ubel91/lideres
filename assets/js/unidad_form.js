// import $ from "jquery";

require('jquery-ui/ui/widgets/draggable');
// const Routing = require('./routing');

let $formActivitiesNombre = $('#unidad_actividadForm_actividad_nombre'),
    $formActivitiesPagina = $('#unidad_actividadForm_pagina'),
    $formActivitiesUrl = $('#unidad_actividadForm_actividad_url'),
    $formActivitiesId  = $('#unidad_actividadForm_id'),
    $formActivitiesUnidad  = $('#unidad_actividadForm_unidad');

function setRequiredFields(param, inputs){
    inputs.forEach(function (input) {
        input.prop('required', param);
    });
}


function convertArrayToObject(array, key){
    const initialValue = {};
    return array.reduce((obj, item) => {
        return {
            ...obj,
            [item[key]]: item,
        };
    }, initialValue);
}

function modalReset(modal)
{
    modal.css({
        top: 0,
        left: 0
    });
    $formActivitiesNombre.val('');
    $formActivitiesPagina.val('');
    $formActivitiesUrl.val('');
    $formActivitiesUnidad.val('');
    $formActivitiesId.val('');
}

function errorTemplate(id, error)
{
    return `
    <div id="${id}_errors" class="mb-2"><span class="invalid-feedback d-block"><span class="d-block">
                    <span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">${error}</span>
                </span></span></div>
    `;
}

let templateItem = `<div class="list-group-item list-group-item-action">
                            <a class="activities-list" id="actividad_item" data-id ="0" aria-controls="profile"><i class="fas fa-clipboard-check mr-3"></i></a>                            
                            <div class="delete-item" id="nuevoItem" style="display: inline-block"><span class="far fa-trash-alt text-danger ml-5"></span></div>
                        </div>`;

function deleteActivities(element, activitiesTransform)
{
        let id =  $(element).attr('id');
        id = id.split('_').pop();
        let textDelete = "Esta acción <b class='text-danger'>NO</b> se puede deshacer.";

        swal.fire({
            title: '<span class="text-danger">'+'¿Esta seguro de borrar este elemento?'+'</span>',
            html: textDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                let route = 'actividades_delete';
                let Ruta = Routing.generate(route);
                $.ajax({
                    type: 'POST',
                    url: Ruta,
                    data: ({id: id}),
                    async: true,
                    dataType: "json",
                    beforeSend: () => {
                        $('.overlay').show();
                    },
                    success: (data, response) => {
                        if (data.success){
                            $('#delete_'+id).parent().remove();
                            delete activitiesTransform[id];
                            swal.fire(
                                'Actividad Eliminada',
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
                        $('.overlay').hide();
                    }
                })
            }
        });
}

function displayModal(element, activitiesTransform)
{
       let $modal = $('#actividadesModal');

        let id = $(element).data('id');
        if (id && activitiesTransform){
            let item = activitiesTransform[id];
            $modal.find('.modal-title').text(item.nombre);

            $formActivitiesNombre.val(item.nombre);
            $formActivitiesPagina.val(item.pagina);
            $formActivitiesUrl.val(item.url);
            if (typeof(unidad_id) !== 'undefined')
                $formActivitiesUnidad.val(unidad_id);
            $formActivitiesId.val(item.id);

        }
        $modal.modal({
            keyboard: false
        });
        $modal.modal('show');
}

$(document).ready(function (e) {
    let activitiesTransform = {};
    if (typeof activities !== 'undefined' && activities.length)
        activitiesTransform = convertArrayToObject(activities, 'id');

    document.getElementById('unidad_actividadForm_actividad_nombre').addEventListener('input', function () {
        document.getElementById('actividadesModalLabel').innerText = this.value;
    });

    let $modalHeader = $('.modal-header');

    $modalHeader.mousedown(function (e) {
        $(this).css('cursor', 'grabbing');
    });
    $modalHeader.mouseup(function (e) {
        $(this).css('cursor', 'grab');
    });

    $('.activities-list').click(function (e) {
        displayModal(this, activitiesTransform);
    });

    $('#displayActividad').click(function (e) {
        e.preventDefault();
        let $modal =$('#actividadesModal');
        $modal.find('.modal-title').text('Nueva Actividad');

        if (typeof(unidad_id) !== 'undefined')
            $formActivitiesUnidad.val(unidad_id);

        $modal.modal({
            keyboard: false
        });
        $modal.modal('show');
    });

    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

    let inputFile = document.getElementById('unidad_archivo');
    if (inputFile.files.length){
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    }
    $('#submitBtn').click(function (e) {

        let inputActivities = [];
        inputActivities.push($formActivitiesNombre);
        inputActivities.push($formActivitiesPagina);
        // inputActivities.push($formActivitiesUrl);

        setRequiredFields(false, inputActivities);
    });

    $('#saveModal').click(function (e) {
        e.preventDefault();
        let inputActivities = [];
        inputActivities.push($formActivitiesNombre);
        inputActivities.push($formActivitiesPagina);
        // inputActivities.push($formActivitiesUrl);
        setRequiredFields(true, inputActivities);

        let $formSelector = $('#unidad_form');
        let form = $formSelector[0];
        let formData = new FormData(form);
        let validate = true;

        inputActivities.forEach(function (item) {
            if (!item.val()){
                let key = item.attr('name');
                let $elementForm = $formSelector.find('[name*="'+key+'"]').eq(0);
                let templateError = errorTemplate($elementForm.attr('id'), "Este campo no debe estar vacío");
                $elementForm.addClass('is-invalid');
                $elementForm.before(templateError);
                validate = false;
            }
        });
        if (!validate)
            return;
        $.ajax({
            url: formURLUnidad,
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(){
                $('.overlay').show();
                let id = '#' + $formActivitiesNombre.attr('id')+'_errors';
                $(id).remove();
                $formActivitiesNombre.removeClass('is-invalid');
                 id = '#' + $formActivitiesPagina.attr('id')+'_errors';
                $(id).remove();
                $formActivitiesPagina.prev(id);
                $formActivitiesPagina.removeClass('is-invalid');
                 id = '#' + $formActivitiesUrl.attr('id')+'_errors';
                $(id).remove();
                // $formActivitiesUrl.prev(id);
                // $formActivitiesUrl.removeClass('is-invalid');
            },
            success: function (data) {
                if(data.result === 'error') {
                    let responseErrors = data.data;
                    for (let key in responseErrors) {
                        if (key !== 'actividadForm'){
                            let $elementForm = $formSelector.find('[name*="'+key+'"]').eq(0);
                            let templateError = errorTemplate($elementForm.attr('id'), responseErrors[key]);
                            $elementForm.addClass('is-invalid');
                            $elementForm.before(templateError);
                        } else {
                            for (let keyNested in responseErrors[key]){
                                let $elementForm = $formSelector.find('[name*="'+keyNested+'"]').eq(0);
                                let templateError = errorTemplate($elementForm.attr('id'), responseErrors[key][keyNested]);
                                $elementForm.addClass('is-invalid');
                                $elementForm.before(templateError);
                            }
                        }

                    }
                } else {
                    if (!data.newUnit){
                        let mensaje = '';
                        let responseData = data.data;
                        responseData = JSON.parse(responseData);

                        if (!data.edited){
                            let deleteId = 'delete_'+responseData.id;

                            $(templateItem).appendTo('#list-tab');
                            $('.list-group-item:last').children('a').attr({
                                id: "actividad_"+responseData.id,
                                'data-id': responseData.id,
                            }).append(responseData.nombre);

                            $("#nuevoItem").attr({
                                id:  deleteId
                            });

                            $('.delete-item').click(function (e) {
                                deleteActivities(this, activitiesTransform);
                            });
                            $('.activities-list').click(function (e) {
                                displayModal(this, activitiesTransform);
                            });
                            activitiesTransform[responseData.id] = responseData;

                            mensaje = '¡Actividad asignada con éxito!';
                        } else {
                            activitiesTransform[responseData.id].nombre = responseData.nombre;
                            activitiesTransform[responseData.id].pagina = responseData.pagina;
                            activitiesTransform[responseData.id].url = responseData.url;
                            let html =`<i class="fas fa-clipboard-check mr-3"></i>${responseData.nombre}`;
                            $('#actividad_'+ responseData.id).html(html);
                            mensaje = '¡Actividad editada con éxito!';
                        }

                        swal.fire(
                            mensaje,
                            responseData.nombre,
                            'success'
                        );
                        $('#actividadesModal').modal('hide');
                    } else {
                        $('#actividadesModal').modal('hide');
                        window.document.location = Routing.generate('unidad_edit', {'id': data.data});
                    }
                }
            },
            complete: function () {
                $('.overlay').hide();
                setTimeout(function () {
                    $('#formAlert').fadeOut();
                }, 4000);
            }
        });
    });

    $('.delete-item').click(function (e) {
        deleteActivities(this, activitiesTransform);
    });


    $('#actividadesModal').on('hidden.bs.modal', function (e) {
        modalReset($('.modal-dialog'));
    })

});



// $(document).ready(function (e) {
//
//     $fielSet = $('#unidad_actividadForm').parent();
//     $btnActividad = $('#displayActividad');
//     $btnSubmit = $('#submitBtn');
//     $inputNombre = $('#unidad_actividadForm_nombre');
//     $inputPagina = $('#unidad_actividadForm_pagina');
//     $inputUrl = $('#unidad_actividadForm_url');
//
//     if ($inputNombre.val() || $inputPagina.val() || $inputUrl.val()){
//         $fielSet.fadeIn();
//         $btnActividad.removeClass('btn-success');
//         $btnActividad.addClass('btn-danger');
//         $btnActividad.html('<span class="fas fa-minus-circle"></span> Eliminar Actividad');
//     }
//     $btnSubmit.click(function (e) {
//         if ($fielSet.css('display') === 'none'){
//             $inputNombre.val('');
//             $inputPagina.val('');
//             $inputUrl.val('');
//         }
//     });
//     $btnActividad.click(function (e) {
//         e.preventDefault();
//         $btnActividad.hide();
//         $btnSubmit.hide();
//         let isRequired = $inputNombre.prop('required');
//         if ($fielSet.css('display') === 'none'){
//             $btnActividad.removeClass('btn-success');
//             $btnActividad.addClass('btn-danger');
//             $btnActividad.html('<span class="fas fa-minus-circle"></span> Eliminar Actividad');
//             if (!isRequired){
//                 setRequiredFields(!isRequired);
//             }
//             $fielSet.fadeIn()
//         }else {
//             if (isRequired){
//                 setRequiredFields(isRequired);
//             }
//             $fielSet.fadeOut();
//             $btnActividad.removeClass('btn-danger');
//             $btnActividad.addClass('btn-success');
//             $btnActividad.html('<span class="fas fa-plus-circle"></span> Agregar Actividad');
//         }
//         setTimeout(function (e) {
//             $btnActividad.fadeIn();
//             $btnSubmit.fadeIn();
//         }, 300);
//     });
// });