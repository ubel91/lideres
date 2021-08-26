import '../css/unidad.css';

import 'jquery-ui';
import 'bootstrap';
import 'sweetalert2'
import Pageflip from './pageflip/page-flip';

import pdfjsLib from './pdfjs/pdf';
// import './pdf.worker';
import ImagedEditor from 'tui-image-editor';
import { customTheme } from './theme-image-editor/default-theme-editor';
import translate from '../i18n/image-editor';
// import {whiteTheme} from './theme-image-editor/white-theme';
import './sticky_notes';

// const Routing = require('./routing');

let pages = [], heights = [], width = [], height = 0, currentPage = 1;
let scale = 5;
let arrayImages = [];
let flip = {};
let $right = $('#right_block');
let block_height = 0;
let block_width = 0;
let resizeTimer = false;
let imageEditor = null;
let leftPage = null;
let rightPage = null;
let $view = $('#view');
let $delete_page = $('#delete_page');
let $saveBtn = $('#save');
let $toggleEditor = $('#tui-image-editor');
let $fullWidth = $('#fullWidth');
let $leftBody = $('#left_body');
let $navBar = $('.navbar');
const TXT_AMPLIAR = 'Ampliar';
const TXT_REDUCIR = 'Reducir';
const PIVOT_WIDTH = 1003.75;
const PIVOT_HEIGHT = 725.375;
let horizontalBehavior = false;
let onePageBehavior = true;
let actualHeight = 0;

let imagesGuardadasPage = {};

$(function () {
    if (typeof (imagenesGuardadas) !== 'undefined' && imagenesGuardadas.length)
        imagesGuardadasPage = convertArrayToObject(imagenesGuardadas, 'pagina');

})


$(window).on('resize', function (e) {

    if (!resizeTimer) {
        $(window).trigger('resizestart');
    }

    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {

        resizeTimer = false;
        $(window).trigger('resizeend');

    }, 100);

}).on('resizestart', function () {

}).on('resizeend', function () {

    let $pageFlip = $('#page-flip');

    let blockHeightAfter = $right.height();
    let blockWidthAfter = $right.width();

    let dimensionVarWidth = ((blockHeightAfter * blockWidthAfter) * $pageFlip.height()) / (block_height * block_width);

    $pageFlip.height(dimensionVarWidth);

    block_height = $right.height();
    block_width = $right.width();
});

function progressBarInit(currentPage, totalPages) {
    let progressLoading = currentPage * 100 / totalPages;
    $('.progress-bar').width(progressLoading + '%');
}

function loadImagesEditor(flipLeftPage, flipRightPage, preloadImage = null, canvasContainer) {
    let context = canvasContainer.getContext('2d');

    if (!preloadImage) {
        //
        let ancho = parseInt(900);
        let alto = parseInt(800);
        let middle = ancho / 14;

        console.log(horizontalBehavior)

        if (!horizontalBehavior) {
            // canvasContainer.width = ancho * 2 + middle;
            canvasContainer.width = ancho;
            canvasContainer.height = alto;
        } else {
            let tmpAncho = ancho * 4;
            ancho = alto * 5.5;
            middle = middle * 2;
            alto = tmpAncho;
            canvasContainer.width = ancho * 2 + middle;
            canvasContainer.height = alto;
        }


        let image1 = new Image();
        image1.src = flip.pages[flipLeftPage];
        let image2 = new Image();
        image2.src = flip.pages[flipRightPage];


        // let ancho = image1.width/1;
        //
        // let alto = image1.height;


        context.drawImage(image1, 0, 0, ancho, alto);
        // context.drawImage(image3, ancho, 0, middle, alto);
        if (flipRightPage != 'undefined')
            context.drawImage(image2, ancho + middle, 0, ancho, alto);

    } else {
        let ancho = canvasContainer.width;
        let alto = canvasContainer.height;

        context.drawImage(preloadImage, 0, 0, ancho, alto);
        preloadImage.src = canvasContainer.toDataURL('image/png');

        return preloadImage.src;
    }

    let combined = new Image();
    combined.src = canvasContainer.toDataURL('image/png');

    return combined.src;
}

function convertToBlob(b64Data, contentType = '', sliceSize = 512) {
    const byteCharacters = atob(b64Data);
    const byteArrays = [];

    for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        const slice = byteCharacters.slice(offset, offset + sliceSize);

        const byteNumbers = new Array(slice.length);
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);
        byteArrays.push(byteArray);
    }
    return new Blob(byteArrays, { type: contentType });
}

function base64Encode(str) {
    let CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    let out = "", i = 0, len = str.length, c1, c2, c3;
    while (i < len) {
        c1 = str.charCodeAt(i++) & 0xff;
        if (i === len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt((c1 & 0x3) << 4);
            out += "==";
            break;
        }
        c2 = str.charCodeAt(i++);
        if (i === len) {
            out += CHARS.charAt(c1 >> 2);
            out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
            out += CHARS.charAt((c2 & 0xF) << 2);
            out += "=";
            break;
        }
        c3 = str.charCodeAt(i++);
        out += CHARS.charAt(c1 >> 2);
        out += CHARS.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
        out += CHARS.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
        out += CHARS.charAt(c3 & 0x3F);
    }
    return out;
}

function maximize() {
    let textBtn = $fullWidth.text();

    if (textBtn === TXT_AMPLIAR){
        $fullWidth.text(TXT_REDUCIR);
        $navBar.fadeOut();
    } else{
        $fullWidth.text(TXT_AMPLIAR);
        $navBar.fadeIn();
    }

    block_height = $right.height();
    block_width = $right.width();

    let $pageFlip = $('#page-flip');


    let blockHeightAfter = $right.height();
    let blockWidthAfter = $right.width();

    let dimensionVarWidth = ((blockHeightAfter*blockWidthAfter)*$pageFlip.height())/(block_height*block_width);

    // $pageFlip.height(dimensionVarWidth);

    $pageFlip.animate({
        height: dimensionVarWidth,
    });

    // $right.fadeOut(function () {
    //     if (!horizontalBehavior){
    //         $right.toggleClass('mx-5');
    //     }else {
    //         $right.removeClass('mx-5');
    //     }
    //     if ($leftBody.css('display') !== 'none'){
    //         $leftBody.toggle("slide");
    //         if (!horizontalBehavior)
    //             $navBar.slideUp(function () {
    //                 $right.fadeIn();
    //             });
    //         else
    //             $right.fadeIn();
    //     } else {
    //         $navBar.slideDown();
    //         if (!horizontalBehavior){
    //             $right.removeClass('col-lg-12');
    //             $right.addClass('col-lg-9');
    //             $leftBody.toggle("slide",function () {
    //                 $right.fadeIn();
    //             });
    //         }

    //         else
    //             $right.fadeIn();
    //     }
    // });
}

$fullWidth.click((e) => {
    maximize();
});

function convertArrayToObject(array, key) {
    const initialValue = {};
    return array.reduce((obj, item) => {
        return {
            ...obj,
            [item[key]]: item,
        };
    }, initialValue);
}

function viewDeleteBehavior(imagenesGuardadasPage, flip) {

    let paginaActual = flip.leftPage;

    if (imagenesGuardadasPage[paginaActual]) {

        let image = imagenesGuardadasPage[paginaActual];

        $view.show();
        $view.data('image_id', image.id);

        $delete_page.show();
        $delete_page.data('image_id', image.id);
        $delete_page.data('pagina_actual', paginaActual);

    } else {
        $delete_page.fadeOut();
        $view.children().replaceWith('<span class="fas fa-eye"></span>');
        $view.prop('title', 'Ver Página Editada');
        $view.hide();
    }
}

function initEditor(preloadImage = null, deletePage = false) {

    let $actionBtn = $('#actionBtn');
    let imageBlob = null;

    console.log(preloadImage)

    let canvasEditor = document.getElementById('canvas');
    if (canvasEditor) {

        leftPage = {
            number: flip.leftPage,
            width: canvasEditor.width,
            height: canvasEditor.height
        };
        rightPage = {
            number: flip.rightPage,
            width: canvasEditor.width,
            height: canvasEditor.height
        };
        if (!preloadImage)
            imageBlob = loadImagesEditor(leftPage.number, rightPage.number, null, canvasEditor);
        else
            imageBlob = preloadImage.src;

        imageEditor = new ImagedEditor(document.querySelector('#tui-image-editor'), {
            includeUI: {
                loadImage: {
                    path: imageBlob,
                    name: 'EditImage'
                },
                // theme: customTheme,
                locale: translate,
                // initMenu: 'draw',
                // menu: ['draw', 'shape', 'icon', 'text'],
                menuBarPosition: 'left',
            },
            // cssMaxWidth: 1900,
            // cssMaxHeight: 1536,
            selectionStyle: {
                cornerSize: 20,
                rotatingPointOffset: 70
            },
            usageStatistics: false
        });
    }
    if (preloadImage) {
        // imageEditor.loadImageFromURL(preloadImage.src, 'Editar Imagen');

        imageEditor = new ImagedEditor(document.querySelector('#tui-image-editor'), {
            includeUI: {
                loadImage: {
                    path: preloadImage.src,
                    name: 'Editar Imagen'
                },
                theme: customTheme,
                locale: translate,
                // initMenu: 'draw',
                menu: ['draw', 'shape', 'icon', 'text'],
                menuBarPosition: 'left',
                uiSize: {
                    // width: '1900px'
                    // width: combined.width*2 + 'px',
                    //                 //     height: '800px'
                    //                     height: combined.height + 'px'
                },

            },
            cssMaxWidth: 2048,
            cssMaxHeight: 1536,
            selectionStyle: {
                cornerSize: 20,
                rotatingPointOffset: 70
            },
            usageStatistics: false
        });


    } else if (flip.leftPage !== leftPage.number || flip.rightPage !== rightPage.number || deletePage) {

        leftPage.number = flip.leftPage;
        rightPage.number = flip.rightPage;

        let canvasEditor = document.getElementById('canvasFlip');

        canvasEditor.width = leftPage.width;
        canvasEditor.height = leftPage.height;

        imageBlob = loadImagesEditor(leftPage.number, rightPage.number, null, canvasEditor);

        imageEditor.loadImageFromURL(imageBlob, 'Editar Imagen');
    }

    if ($myDiv.css('display') !== 'none') {
        $myDiv.fadeOut(function () {
            $toggleEditor.fadeIn();
            $saveBtn.fadeIn();
            $fullWidth.fadeOut();
            adjustSize();
        });
    } else {
        $toggleEditor.fadeOut(function () {
            $saveBtn.fadeOut();
            $myDiv.fadeIn();
            $fullWidth.fadeIn();
            adjustSize();
        });
    }

    $toggleEditor.contextmenu(function (e) {
        e.preventDefault();
    });
}

function adjustSize() {
    if ($right.hasClass('col-9')) {
        $right.removeClass('col-9');
        $right.addClass('col-12');
    } else {
        if (!horizontalBehavior) {
            // $right.removeClass('col-lg-12');
            // $right.addClass('col-lg-9');
        } else {
            $right.removeClass('mx-5');
        }
    }
}

$('#editor').click((e) => {
    if ($navBar.css('display') !== 'none') {
        maximize();
    }
    if ($('#contenedor').hasClass('col-md-5')) {
        $('#contenedor').removeClass('col-md-5').addClass('col-md-9 col-sm-11');
    }
    else
        $('#contenedor').removeClass('col-md-9 col-sm-11').addClass('col-md-5');

    if ($toggleEditor && $toggleEditor.css('display') === 'none') {
        $view.prop('disabled', true);
    } else {
        $view.prop('disabled', false);
    }

    initEditor();

});

$saveBtn.click(function (e) {

    let paginaImagenG = leftPage.number;
    let editedImagen = false;
    let idImagen = '';
    let url = '';
    let mimeType = '';

    let imgEl = new Image();
    imgEl.src = imageEditor.toDataURL();

    const contentType = 'image/png';
    let b64Data = imgEl.src;
    b64Data = b64Data.replace(/^data:image\/(png|jpg);base64,/, "");
    const b64toBlob = convertToBlob(b64Data, contentType);
    let imageName = unidad_nombre + '_' + leftPage.number;
    let formData = new FormData();

    if (imagesGuardadasPage)
        for (let imagen in imagesGuardadasPage) {
            if (imagesGuardadasPage.hasOwnProperty(imagen) && imagesGuardadasPage[imagen].hasOwnProperty('pagina') && imagesGuardadasPage[imagen].pagina === paginaImagenG) {
                editedImagen = true;
                imageName = imagesGuardadasPage[imagen].nombre;
                idImagen = imagesGuardadasPage[imagen].id;
                mimeType = imagesGuardadasPage[imagen].mimeType;
            }
        }


    formData.append('imagen_guardada[archivo]', b64toBlob, imageName);
    formData.append('imagen_guardada[nombre]', imageName);
    formData.append('imagen_guardada[pagina]', paginaImagenG);
    formData.append('imagen_guardada[unidad]', unidad);
    formData.append('imagen_guardada[_token]', csfrImagenGuardada);


    if (!editedImagen)
        url = Routing.generate('imagen_guardada_new');
    else {
        formData.append('imagen_guardada[mimeType]', mimeType);
        url = Routing.generate('imagen_guardada_edit', { 'id': idImagen });
    }
    $.ajax({
        url: url,
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        mimeType: "multipart/form-data",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            // $('.overlayGeneral').show();
        },
        success: function (data) {
            let resultData = data.data;
            swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                // timerProgressBar: true,
                icon: data.result,
                title: data.message
            });

            resultData = JSON.parse(resultData);
            let responsePage = resultData.pagina;
            imagesGuardadasPage[responsePage] = {
                archivo: resultData.archivo,
                id: resultData.id,
                mimeType: resultData.mimeType,
                nombre: resultData.nombre,
                pagina: responsePage,
                unidad: []
            };

            $fullWidth.attr('disabled', true);
            $('#editor').attr('disabled', true);
            $delete_page.fadeIn();

            $delete_page.data('image_id', resultData.id);
            $delete_page.data('pagina_actual', responsePage);

            $view.show().children().replaceWith('<span class="fas fa-eye-slash"></span>');
            $view.prop('title', 'Volver al Libro');
            $view.attr('disabled', false);
            $view.data('image_id', resultData.id);
        },
        complete: function () {
            // $('.overlayGeneral').hide();
        }

    })

});

$view.click(function (e) {

    if (!$view.children().hasClass('fa-eye-slash')) {

        let id = $view.data('image_id');
        let imageExist = new Image();
        let urlFecth = Routing.generate('imagen_guardada', { 'id': id });

        $.ajax({
            url: urlFecth,
            mimeType: "image/png",
            cache: false,
            xhr: function () {
                let xhr = new XMLHttpRequest();
                xhr.responseType = 'blob';
                return xhr;
            },
            beforeSend: function () {
                $('.overlayGeneral').show();
            },
            success: function (data) {
                if ($navBar.css('display') !== 'none')
                    maximize();
                let url = window.URL || window.webkitURL;
                imageExist.src = url.createObjectURL(data);
                initEditor(imageExist);
                $fullWidth.attr('disabled', true);
                $('#editor').attr('disabled', true);
                $view.children().replaceWith('<span class="fas fa-eye-slash"></span>');
                $view.prop('title', 'Volver al Libro');

            },
            complete: function () {
                $('.overlayGeneral').hide();
            }
        });
    } else {
        if ($navBar.css('display') !== 'none')
            maximize();
        initEditor();
        $fullWidth.attr('disabled', false);
        $('#editor').attr('disabled', false);
        $view.children().replaceWith('<span class="fas fa-eye"></span>');
        $view.prop('title', 'Ver Página Editada');
    }

});

$delete_page.click(function (e) {

    let id = $delete_page.data('image_id');
    let paginaActual = $delete_page.data('pagina_actual');
    let textDelete = "Esta acción <b class='text-danger'>NO</b> se puede deshacer.";

    swal.fire({
        title: '<span class="text-danger">' + '¿Esta seguro de borrar la página marcada?' + '</span>',
        html: textDelete,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
            let Ruta = Routing.generate('imagen_guardada_delete', { 'id': id });
            $.ajax({
                type: 'POST',
                url: Ruta,
                data: ({ id: id }),
                async: true,
                dataType: "json",
                beforeSend: () => {
                    $('.overlayGeneral').show();
                },
                success: (data, response) => {
                    if (data.success) {
                        swal.fire(
                            'Borrado',
                            data.success,
                            'success'
                        );

                        delete imagesGuardadasPage[paginaActual];

                        $delete_page.fadeOut();
                        $view.children().replaceWith('<span class="fas fa-eye"></span>');
                        $view.prop('title', 'Ver Página Editada');
                        $view.fadeOut();
                        $fullWidth.attr('disabled', false);
                        $('#editor').attr('disabled', false);
                        $('#save').fadeOut();
                        let textBtn = $fullWidth.text();
                        initEditor(null, true);

                        if (textBtn === TXT_AMPLIAR) {
                            $leftBody.fadeIn();
                            $('#tui-image-editor').hide();
                            if ($navBar.css('display') === 'none')
                                $navBar.slideDown();
                        } else {
                            maximize();
                            $('#tui-image-editor').hide();
                            $('#myDiv').fadeIn();
                        }

                        if (horizontalBehavior) {
                            $leftBody.fadeOut();
                            let newHeight = actualHeight * PIVOT_HEIGHT / PIVOT_WIDTH;
                            newHeight = newHeight / 1.20;
                            $('#page-flip').height(newHeight);
                            $('#page-flip').width('125%');
                            adjustSize();
                        }


                    } else if (data.error) {
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

$('#one_page').click(function (e) {
    let $element = $(this).children();
    if ($element.hasClass('fa-file-alt')) {
        onePageBehavior = true;
        $('.pages-container').fadeOut(function () {
            makeFlip(arrayImages);
        });
        $element.removeClass('fa-file-alt');
        $element.addClass('fa-book-open');
    } else {
        onePageBehavior = false;
        $('.pages-container').fadeOut(function () {
            makeFlip(arrayImages);
        });
        $element.removeClass('fa-book-open');
        $element.addClass('fa-file-alt');
    }
});

function horizontalTransf() {
    maximize();
    $right.removeClass('col-9');
    $right.addClass('col-12');
    $right.toggleClass('mx-5');
    $fullWidth.remove();
}

function makeFlip(arrayPages) {
    let newHeight;
    let horizontalWidth = '100%';
    let divisor = 1.20;
    if (!flip.hasOwnProperty('pages')) {
        flip = new Pageflip('#myDiv', arrayPages, activities, null, horizontalBehavior, onePageBehavior);
        flip.audioUrl = '/pageflip/page-flip.mp3';
        let $pageFlip = $('#page-flip');
        actualHeight = $pageFlip.height();
        newHeight = actualHeight * PIVOT_HEIGHT / PIVOT_WIDTH;
        // let newHeight = actualHeight * PIVOT_WIDTH / PIVOT_HEIGHT;
        if (horizontalBehavior) {
            newHeight = newHeight / divisor;
            $pageFlip.width(horizontalWidth);
        }
        $pageFlip.height(newHeight);

    } else {
        let currentPageFlip = flip.leftPage;
        let flipState = {};
        if (!onePageBehavior) {
            if (currentPageFlip % 2 !== 0) {
                currentPageFlip -= 1;
            }
            flipState = {
                leftHiddenPage: currentPageFlip - 2,
                leftOverleaf: currentPageFlip - 1,
                leftPage: currentPageFlip,
                rightPage: currentPageFlip + 1,
                rightOverleaf: currentPageFlip + 2,
                rightHiddenPage: currentPageFlip + 3
            };
        } else {
            flipState = {
                leftOverleaf: currentPageFlip - 1,
                leftPage: currentPageFlip,
                rightOverleaf: currentPageFlip + 1,
            };
        }

        flip = new Pageflip('#myDiv', arrayPages, activities, flipState, horizontalBehavior, onePageBehavior);
        flip.audioUrl = '/pageflip/page-flip.mp3';
        let $pageFlip = $('#page-flip');
        newHeight = actualHeight * PIVOT_HEIGHT / PIVOT_WIDTH;

        if (horizontalBehavior) {
            newHeight = newHeight / divisor;
            $pageFlip.width(horizontalWidth);
            $right.removeClass('col-9');
            $right.addClass('col-12');
        }
        $pageFlip.height(newHeight);

    }

    document.querySelector("#page-flip").style.height = canvas.height + 'px';
    document.querySelector("#page-flip").style.lineHeight = canvas.height + 'px';

    viewDeleteBehavior(imagesGuardadasPage, flip);
    if (horizontalBehavior) {
        horizontalTransf();
    }
}

// function saveCache(base64Img, num){
//
//     let url = Routing.generate('unidad_cache');
//     $.ajax({
//         type: "POST",
//         url: url,
//         data: {folderName: unidad_id, img: encodeURIComponent(base64Img), name: num},
//         contentType: "application/x-www-form-urlencoded; charset=UTF-8",
//         success: function () {
//             console.log("image saved");
//         }
//
//     })
// }

function draw(cheigth, limitDraw = 0) {
    let canvas = document.getElementById('canvasFlip'), ctx = canvas.getContext('2d');

    canvas.width = width;
    canvas.height = cheigth;
    if (arrayImages.length > 0) {
        arrayImages = [];
    }

    let forLimit = pages.length;

    if (limitDraw) {
        forLimit = limitDraw;
    }

    $.each(pages, function (key, value) {
        if (key < forLimit) {
            ctx.putImageData(value, 0, 0);
            arrayImages.push(canvas.toDataURL());
        }
    });
    makeFlip(arrayImages);
}

// function drawPivotChecker(currentPage, limit) {
//
//     let drawPivot = currentPage;
//
//     // if (drawPivot <= 6)
//     //     drawPivot = currentPage;
//     // else {
//     //     drawPivot-=2;
//     // }
//
//     if (limit <= 6){
//         drawLimit = limit;
//     } else {
//         if (drawLimit === 0)
//             drawLimit = 6;
//         else if (drawLimit > limit){
//             drawLimit = limit;
//         }
//     }
//     return drawPivot;
// }

pdfjsLib.disableWorker = true;
// let loadingTask = pdfjsLib.getDocument(pdf);
let loadingTask = pdfjsLib.getDocument({ url: pdf, disableAutoFetch: true, disableStream: true })
let loop = 4;
let limit = 0;
let drawLimit = 6;
let pageRendered = false;
let realCurrentPage = 0;
let cheight = 0;

loadingTask.promise.then(function (pdf) {
    if (drawLimit > pdf.numPages)
        drawLimit = pdf.numPages;
    limit = pdf.numPages;

    let observer = new MutationObserver(function (mutations) {
        let $pageGrabberRight = $('div.page-right > div.page-grabber');
        if (onePageBehavior)
            $pageGrabberRight = $('div.page-grabber-right');

        if ($pageGrabberRight.length) {

            $pageGrabberRight.click(function (e) {
                let timeout = 100;
                realCurrentPage = flip.rightPage + 1;
                if (onePageBehavior)
                    realCurrentPage = flip.leftPage;

                if (realCurrentPage === 4 && realCurrentPage !== pdf.numPages && realCurrentPage + 2 === drawLimit) {
                    // drawLimit += 4;
                    if ((drawLimit + 10) >= pdf.numPages) {
                        drawLimit = pdf.numPages;
                        if (drawLimit !== 6) {
                            setTimeout(function () {
                                draw(cheight, drawLimit);
                            }, timeout);

                            // currentPage++;
                            // getPage();
                        }
                    } else {
                        drawLimit += 10;
                        // if (currentPage < drawLimit) {
                        setTimeout(function () {
                            draw(cheight, drawLimit);
                        }, timeout);
                        // currentPage++;
                        // getPage();
                        // }
                    }
                } else if (realCurrentPage > 4 && realCurrentPage !== pdf.numPages && realCurrentPage + 10 === drawLimit) {
                    if ((drawLimit + 10) >= pdf.numPages) {
                        drawLimit = pdf.numPages;
                        // if (currentPage < drawLimit) {
                        setTimeout(function () {
                            draw(cheight, drawLimit);
                        }, timeout);
                        // currentPage++;
                        // getPage();
                        // }
                    } else {
                        drawLimit += 10;
                        // if (currentPage < drawLimit) {
                        setTimeout(function () {
                            draw(cheight, drawLimit);
                        }, timeout);
                        // currentPage++;
                        // getPage();
                        // }
                    }
                }

                viewDeleteBehavior(imagesGuardadasPage, flip);
                if (horizontalBehavior) {
                    $right.removeClass('col-lg-9');
                    $right.addClass('col-lg-12');
                }

            });
            if (drawLimit === pdf.numPages)
                observer.disconnect();
        }
    });

    getPage();

    observer.observe($('div#myDiv')[0], { attributes: false, childList: true, characterData: false, subtree: true });


    function getPage() {

        pdf.getPage(currentPage).then(function (page) {

            let viewport = page.getViewport({ scale: scale });

            let canvas = document.createElement('canvas'), ctx = canvas.getContext('2d');

            if (viewport.height < viewport.width)
                horizontalBehavior = true;
            else
                horizontalBehavior = false;

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            let renderContext = { canvasContext: ctx, viewport: viewport };


            page.render(renderContext).promise.then(function () {
                pages.push(ctx.getImageData(0, 0, canvas.width, canvas.height));

                heights.push(height);
                // height = canvas.height;

                if (width < canvas.width)
                    width = canvas.width;

                progressBarInit(currentPage, drawLimit);


                if (currentPage < limit) {
                    currentPage++;
                    getPage();
                } else {
                    let $pageGrabber = $('.page-grabber');
                    $pageGrabber.click(function (e) {
                        if ($(this).parent().hasClass('page-right')) {
                            let realCurrentPage = flip.rightPage + 1;
                            if (realCurrentPage >= drawLimit - 1 && realCurrentPage !== pdf.numPages) {
                                drawLimit += 2;
                                if ((limit + loop) >= pdf.numPages) {
                                    limit = pdf.numPages;
                                    if (currentPage < limit) {
                                        getPage();
                                    }
                                } else {
                                    limit += loop;
                                    if (currentPage < limit) {
                                        getPage();
                                    }
                                }
                            }
                        }


                        if ($(this).parent().hasClass('page-right')) {
                            let realCurrentPage = flip.rightPage + 1;
                            draw(canvas.height);
                            if (realCurrentPage >= limit && realCurrentPage !== pdf.numPages) {
                                if ((limit + loop) >= pdf.numPages) {
                                    limit = pdf.numPages;
                                    if (currentPage < limit) {
                                        // $('.progress-bar').width(0);
                                        $('.overlayGeneral').css('background', "rgba(0,0,0, 0.9)");
                                        $('.overlayGeneral').show();
                                        // currentPage++;
                                        getPage();
                                    }
                                } else {
                                    limit += loop;
                                    if (currentPage < limit) {
                                        // $('.progress-bar').width(0);
                                        $('.overlayGeneral').css('background', "rgba(0,0,0, 0.9)");
                                        $('.overlayGeneral').show();
                                        // currentPage++;
                                        getPage();
                                    }
                                }
                            }
                        }
                        viewDeleteBehavior(imagesGuardadasPage, flip);
                    });
                    $('.overlayGeneral').hide();
                    $('.overlayGeneral').css('background', "rgba(0,0,0, 0.5)");

                    if (horizontalBehavior) {
                        $right.removeClass('col-9');
                        $right.addClass('col-12');
                    }
                }
            })
                .then(function () {
                    // let drawPivot = drawPivotChecker(currentPage, limit);
                    // if (drawPivot === drawLimit && pages.length >= drawPivot){
                    if (drawLimit === pages.length) {
                        cheight = canvas.height;
                        draw(canvas.height);
                        $('.overlayUnidad').hide();
                        block_height = $right.height();
                        block_width = $right.width();

                    }
                })
        })
    }
});
if (horizontalBehavior) {
    $right.removeClass('col-9');
    $right.addClass('col-12');
}
