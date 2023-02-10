import Pageflip from './pageflip/page-flip';

import pdfjsLib from './pdfjs/pdf';
$(function () {
    pdfjsLib.disableWorker = true;
    let loadingTask = pdfjsLib.getDocument(pdf);
});