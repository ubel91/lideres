$(document).ready(function () {
    $('#libro_categoria').change(function () {
        let data = {
            id: $(this).val()
        };
        let subCatSelector = $('#libro_subCategoria');
        $.ajax({
            type: 'post',
            url: route,
            data: data,
            beforeSend: function() {
                subCatSelector.prop('disabled', true);
                $('#libro_registrar').prop('disabled', true);
            },
            success: function (data) {
                subCatSelector.html('<option>Seleccione una Sub-Categoría...</option>');
                for (let i = 0, total = data.length; i < total; i++){
                    subCatSelector.append('<option value="'+ data[i].id + '">' + data[i].nombre + '</option>')
                }
                subCatSelector.prop('disabled', false);
                $('#libro_registrar').prop('disabled', false);
            }
        })
    });
    }
);