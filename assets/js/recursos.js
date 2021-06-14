// import $ from "jquery";

$(document).ready(function (e) {

    let $referenciaSelector = $('#recurso_referencia');
    let referenceValue = $referenciaSelector.val();
    let $referenciaSelectorFile = $('#recurso_referenciaFile');
    let $referenciaError = $('#recurso_referenciaFile_errors');
    let $btnSubmit = $('#btnSubmit');
    let $recursoTipo = $('#recurso_tipo');
    let tooltipMsg = '';
    const ERROR = 'error';
    const TIPO_URL = 'Youtube';
    const TIPO_ARCHIVO = 'Doc(x), xls(x), pdf, mp3, mp4';

    if ($referenciaError.length){
        $referenciaError.parent().css({display: 'block', position: 'relative', 'z-index': 100});
    }
    let style = {
        width: '80%',
        display: 'inline-block',
    };
    $referenciaSelectorFile.parent().css(style);
    $referenciaSelector.css(style);

    $btnSubmit.click(function (e) {
        let value = $recursoTipo.children("option:selected").val();
        let description = $recursoTipo.children("option:selected").text();
        if (!value){
            e.preventDefault();
            validateInput($recursoTipo, ERROR);
        } else if (description === TIPO_URL){
            let isValid = validateUrl($referenciaSelector.val());
            if (!isValid){
                e.preventDefault();
                validateInput($referenciaSelector, ERROR, 'Url no válida');
            }
        }
        if($referenciaSelector.parent().css('display') === 'none'){
            $referenciaSelector.val('');
            $referenciaSelector.removeAttr('required');
        }
        if ($referenciaSelectorFile.css('display') === 'none'){
            $referenciaSelectorFile.val('');
            $referenciaSelectorFile.removeAttr('required');
        }
    });

    let valueTipo = $recursoTipo.val();

    if(valueTipo){
        let textTipo = $recursoTipo.children('option[value=' + valueTipo +']').text();
        let referencia = $referenciaSelector.val();
        animationForm(textTipo, $referenciaSelector, $referenciaSelectorFile);
        if(referencia){
            $referenciaSelector.val(referencia);
        }
    }

    let inputFile = document.getElementById('recurso_referenciaFile');
    if (inputFile.files.length){
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    }

    $recursoTipo.change(function () {
        let value = this.value;
        if (value){
            let textToCompare = $(this).children('option[value=' + value +']').text();
            animationForm(textToCompare, $referenciaSelector, $referenciaSelectorFile);
        } else {
            $referenciaSelector.parent().fadeOut();
            $referenciaSelectorFile.parent().parent().fadeOut();
        }
    });

    function animationForm(textToCompare, $referenciaSelector, $referenciaSelectorFile) {
        if (textToCompare === TIPO_ARCHIVO){
            validateInput($recursoTipo);
            tooltipMsg = 'Debe escoger solo archivos .mp4, .mp3, .pdf, doc(x), xls(x), ppt(x)';
            let iconWarning = `<a href="#" data-toggle="tooltip" data-placement="right" title="${tooltipMsg}"><span class="fas fa-exclamation-circle" style="padding-top: 1rem; font-size: 2.5rem; color: #17a2b8; background-color: #fff;"></span></a>`;
            $referenciaSelectorFile.parent().parent().append(iconWarning);
            $referenciaSelector.parent().css('position', 'absolute');
            $referenciaSelector.parent().fadeOut();
            $referenciaSelector.prop('required', false);
            $referenciaSelector.val(referenceValue);
            // $referenciaSelectorFile.parent().find('.custom-file-label').html(refenciaValue);
            $referenciaSelectorFile.parent().parent().css({position: 'relative', 'z-index': 100});
            // $referenciaSelectorFile.css('position', 'relative');
            $referenciaSelectorFile.parent().parent().fadeIn();
            $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

        } else if(textToCompare === TIPO_URL) {
            tooltipMsg = 'Solo enlaces válidos de Youtube';
            let iconWarning = `<a href="#" data-toggle="tooltip" data-placement="right" title="${tooltipMsg}"><span class="fas fa-exclamation-circle" style="padding-top: 1rem; font-size: 2.5rem; color: #17a2b8; background-color: #fff;"></span></a>`;
            $referenciaSelector.parent().append(iconWarning);
            validateInput($recursoTipo);
            $referenciaSelectorFile.parent().parent().css({position: 'absolute', 'z-index': -1});
            // $referenciaSelectorFile.css('position', 'absolute');
            $referenciaSelectorFile.parent().parent().fadeOut();
            $referenciaSelector.val('');
            $referenciaSelector.parent().css("position", 'relative');
            $referenciaSelector.parent().fadeIn();
            $referenciaSelector.prop('required', true);
            $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

        }
    }
    function validateInput(input, error = '', message = 'Debe escojer un tipo de recurso') {
        input.removeClass('is-invalid');
        $('.invalid-feedback').remove();
        if (error){
            input.addClass('is-invalid');
            input.parent().append(`
                      <div class="invalid-feedback">
                        ${message}
                      </div>
                    `)
        }

    }
    function validateUrl(url) {
        let valid = false;
        let re = /\/\/(?:www\.)?youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/)?([a-z0-9_\-]+)/i;
        let matches = re.exec(url);

        if (matches){
            valid = true;
        }
        return valid;
    }
});