$(document).ready(function () {

    let $photo = $('#change_password_form_photo');
    $photo.parent('.custom-file').addClass('custom-file-class');


    $photo.change((e)=>{
        let imageFile =document.getElementById('change_password_form_photo').files[0];
        const _PREVIEW_URL = URL.createObjectURL(imageFile);
        let $imageContainer = $('#profilePic');
        // $imageContainer.hide();
        let formData = new FormData();
        formData.append('photo', imageFile);

        $.ajax({
            type: 'post',
            url: ROUTE_PHOTO,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#change_password_form_photo').prop('disabled', true);
            },
            success: function (data) {
                $imageContainer.prop('src', _PREVIEW_URL);
                $imageContainer.prop('width', 100);
                $imageContainer.height('6.25rem');
                $('#avatar').prop('src', _PREVIEW_URL);
            },
            complete: function () {
                $('#change_password_form_photo').prop('disabled', false);
            }
        })
    });

    let $formSelector = document.getElementById('changePasswordForm');
    let $buttonHandler = $('#change_password_form_Cambiar');
    $buttonHandler.click((e)=>{
        e.preventDefault();
        let formData = new FormData($formSelector);
        $.ajax({
            url: formURL,
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            contentType: false,
            dataType: 'JSON',
            cache: false,
            processData: false,
            beforeSend: function(){
                $buttonHandler.prop("disabled", true);
            },
            success: function (data) {
                let $alert = $('#formAlert');
                $('#change_password_form_plainPassword_first').val("");
                $('#change_password_form_plainPassword_second').val("");
                if (data.success){
                    $alert.empty();
                    $alert.removeClass("alert-danger");
                    $alert.addClass("alert-success");
                    $alert.fadeIn();
                    $alert.append('<span class="fas fa-check"></span> ' + '<b class="ml-1" style="font-size: 0.85rem"> ' + data.success + '</b>');
                } else {
                    $alert.empty();
                    $alert.removeClass("alert-success");
                    $alert.addClass("alert-danger");
                    $alert.fadeIn();
                    $alert.append('<span class="fas fa-exclamation-triangle"></span> ' + '<b class="ml-1" style="font-size: 0.85rem"> ' + data.error + '</b>');
                }
            },
            complete: function () {
                $buttonHandler.prop("disabled", false);
                setTimeout(function () {
                    $('#formAlert').fadeOut();
                }, 4000);
            }
        });
    });
});

