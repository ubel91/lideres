
$(document).ready(function () {

    let inputSearcher = document.getElementById('searchInput');

    inputSearcher.addEventListener('input', function (e) {
        let value = this.value;
        Object.values(booksJson).forEach(function (item) {
            let position = item.nombre.toLowerCase().search(value.toLowerCase());
            if (position > -1){
                $('#libro_' + item.id).fadeIn();
            } else if (position === -1){
                $('#libro_' + item.id).fadeOut();
            }
        });
    });
});