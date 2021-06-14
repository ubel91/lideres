// import $ from 'jquery';

$(document).ready(()=>{
    let $btnActivar = $('#libro_activado_Activar');
    let $libroActivado = $('#libro_activado_codigo_activacion');
    $btnActivar.click((e) => {
        let typeCode = $libroActivado.val();
        let flag = false;
        let arrayMatch = codigos.filter((item)=>{
            if (item.codebook === typeCode){
                flag = true;
                return true;
            } else {
                return false;
            }
        });
        if (!flag){
            e.preventDefault();
            $('.invalid-feedback').remove();
            $libroActivado.removeClass("is-invalid");

            $libroActivado.parent().append(" <div class=\"invalid-feedback\">\n" +
                "        El código no es correcto" +
                "      </div> ");
            $libroActivado.addClass("is-invalid");
        }

    });
});
