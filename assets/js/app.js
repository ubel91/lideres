/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import '../css/app.css';

const $ = require('jquery');
global.$ = global.jQuery = $;
import 'jquery-ui';
import 'bootstrap';
import '@fortawesome/fontawesome-free/js/all.min';
import 'datatables.net';
import 'datatables.net-bs4';
import 'datatables.net-buttons-bs4';
import 'datatables.net-responsive'
import swal from 'sweetalert2';
require ('jquery-ui/ui/i18n/datepicker-es');
require ('jquery-ui/themes/base/all.css');
global.swal = swal;
import Dropzone from 'dropzone';
import FileSaver from 'file-saver';
import { jsPDF } from 'pdfjs'
const imagesContext = require.context('../images/', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

$(document).ready(function () {
    $('.custom-file-input').on('change', function(event) {
        let inputFile = event.currentTarget;
        $(inputFile).parent()
            .find('.custom-file-label')
            .html(inputFile.files[0].name);
    });
    
    setTimeout(() => {
            $('#flashSection').slideUp();
    }, 3000);

});

console.log('Webpack Encore running!!!');
