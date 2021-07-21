
   const ESTUDIANTES = 'estudiantes';
   const PROFESORES = 'profesores';
   const INSTITUTO_DEFAULT = 'Sistema';

function hideAndShow(){

    let $radioEstudiante = $('[data="radioEstudiante"]:radio');
    let $radioProfesor = $('[data="radioProfesor"]:radio');
    $('#container').removeClass('hidden')
    if ($radioEstudiante.prop('checked') === true)
    {
        registerBehavior(ESTUDIANTES);
    }
    else if( $radioProfesor.prop('checked') === true){
        registerBehavior(PROFESORES);
    }
}
$(document).ready(function () {

    $('.js-datepicker').datepicker({
        format: 'DD/MM/YYYY',
        changeYear: true,
        yearRange: "-100:+0"
    });

    let $radioEstudiante = $('[data="radioEstudiante"]:radio');
    let $radioProfesor = $('[data="radioProfesor"]:radio');
    let $institutionInput = $('#registration_form_nombre_institucion');
    let $photo = $('#registration_form_photo');

   
    hideAndShow();

    $radioEstudiante.on('change',function () {
        hideAndShow();
    });
    $radioProfesor.on('change',function () {
        hideAndShow();
    });

    $radioEstudiante.trigger('change');


    function registerBehavior(param) {
        if (param === ESTUDIANTES){
            $('#profesores_form').fadeOut(function () {
                $('#estudiantes_form').fadeIn();

                $('#registration_form_profesorForm_numero_identificacion').removeAttr('required');
                $('#registration_form_profesorForm_celular').removeAttr('required');

                $('#registration_form_estudiantesForm_fecha_nacimiento').prop('required', true);
                $('#registration_form_estudiantesForm_nombre_representante').prop('required', true);
                $('#registration_form_estudiantesForm_primer_apellido_representante').prop('required', true);
                $('#registration_form_estudiantesForm_segundo_apellido_representante').prop('required', true);
                $('#registration_form_estudiantesForm_numero_identificacion').prop('required', true);
                $('#registration_form_estudiantesForm_celular').prop('required', true);
                $('#registration_form_estudiantesForm_grado').prop('required', true);

                // $('#register_form').trigger("reset");
                // .trigger("reset");
            })
        } else if (param === PROFESORES){
            $('#estudiantes_form').fadeOut(function () {
                $('#profesores_form').fadeIn();

                $('#registration_form_profesorForm_numero_identificacion').prop('required', true);
                $('#registration_form_profesorForm_celular').prop('required', true);

                $('#registration_form_estudiantesForm_fecha_nacimiento').removeAttr('required');
                $('#registration_form_estudiantesForm_nombre_representante').removeAttr('required');
                $('#registration_form_estudiantesForm_primer_apellido_representante').removeAttr('required');
                $('#registration_form_estudiantesForm_segundo_apellido_representante').removeAttr('required');
                $('#registration_form_estudiantesForm_numero_identificacion').removeAttr('required');
                $('#registration_form_estudiantesForm_celular').removeAttr('required');
                $('#registration_form_estudiantesForm_grado').removeAttr('required');

                // $('#register_form').trigger("reset");
                //.trigger("reset");
                // .reset();
            });
        }
    }

    if ($institutionInput.val() === INSTITUTO_DEFAULT)
        $institutionInput.val("");

    $photo.change((e)=>{
        let imageFile =document.getElementById('registration_form_photo').files[0];
        const _PREVIEW_URL = URL.createObjectURL(imageFile);
        let $imageContainer = $('#profilePic');

        if (role[0] !== 'ROLE_USER'){
            let formData = new FormData();
            formData.append('photo', imageFile);

            $.ajax({
                type: 'post',
                url: route_photo,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#change_password_form_photo').prop('disabled', true);
                },
                success: function (data) {
                    $('#change_password_form_photo').prop('disabled', false);
                    $imageContainer.prop('src', _PREVIEW_URL);
                    $imageContainer.prop('width', 100);
                    $imageContainer.height('6.25rem');
                    $('#avatar').prop('src', _PREVIEW_URL);
                }
            })
        } else {
            $imageContainer.hide();
            $imageContainer.prop('src', _PREVIEW_URL);
            $imageContainer.prop('width', 100);
            $imageContainer.height('6.25rem');
            $imageContainer.fadeIn();
        }
    });

    if (role[0] === 'ROLE_ESTUDIANTE'){
        $radioEstudiante.prop('checked', true)
    } else if (role[0] === 'ROLE_PROFESOR') {
        $radioProfesor.prop('checked', true)
    }

    $radioEstudiante.click(function () {
        registerBehavior(ESTUDIANTES);
    });
    $radioProfesor.click(function () {
        registerBehavior(PROFESORES);
    });

    if ($radioEstudiante.prop('checked') === true){
        registerBehavior(ESTUDIANTES);
    } else if ($radioProfesor.prop('checked') === true){
        registerBehavior(PROFESORES);
    }



    let $provinciaSelector = $('#registration_form_provincia');
    let $cantonSelector = $('#registration_form_canton');

    $provinciaSelector.change(function () {
        let data = {
            id: $(this).val()
        };
        $.ajax({
            type: 'post',
            url: urlCanton,
            data: data,
            beforeSend: function(){
                $provinciaSelector.prop('disabled',true);
                $cantonSelector.prop('disabled',true);
            },
            success: function (data) {
                $provinciaSelector.prop('disabled',false);
                $cantonSelector.prop('disabled',false);
                $cantonSelector.html('<option>Seleccione un Canton...</option>');
                for (let i = 0, total = data.length; i < total; i++){
                    $cantonSelector.append('<option value="'+ data[i].id + '">' + data[i].nombre + '</option>')
                }
            }
        })
    });
});