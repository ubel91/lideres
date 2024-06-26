// import $ from "jquery";
//
// const Routing = require('./routing');


function menuMedia(item, menuId){
    let menu = $(menuId);
    let $misVideos = menu.find('#misVideos');
    let $misDocumentos = menu.find('#misDocumentos');
    let $misCanciones = menu.find('#misCanciones');
    let $webs = menu.find('#webs');
    let icon = '';
    let template = null;
    if (item.tipo.nombre === TIPO_URL){
        icon = 'fas fa-link';
        template = templateMedia(icon, item);
        $misVideos.append(template);
    }else if(item.tipo.nombre === 'web_url'){
        icon = 'fa fa-link';
        template = templateMedia(icon, item);
        $webs.append(template);
    } else {
        switch (item.mimeType) {
            case 'application/pdf':
                icon = 'far fa-file-pdf';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/x-pdf':
                icon = 'far fa-file-pdf';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'video/mp4':
                icon = 'far fa-file-video';
                template = templateMedia(icon, item);
                $misVideos.append(template);
                break;
            case 'audio/mpeg':
                icon = 'far fa-file-audio';
                template = templateMedia(icon, item);
                $misCanciones.append(template);
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                icon = 'far fa-file-word';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/msword':
                icon = 'far fa-file-word';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                icon = 'far fa-file-powerpoint';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/vnd.ms-powerpoint':
                icon = 'far fa-file-powerpoint';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
                icon = 'far fa-file-powerpoint';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                icon = 'far fa-file-excel';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
            case 'application/vnd.ms-excel':
                icon = 'far fa-file-excel';
                template = templateMedia(icon, item);
                $misDocumentos.append(template);
                break;
        }
    }
}
function templateMedia(icon, item){

    let customId = item.tipo.nombre + '_' + item.id;

    if (item.tipo.nombre === TIPO_URL){
        return `
            <div class="media my-2" style="border-bottom: 1px solid #00000020">
              <div class="align-self-center mr-3"><i class="${icon}"></i></div>
              <div class="media-body">
                <a href="${item.referencia}" id="${customId}" data-mimetype="${item.mimeType}" data-referencia="${item.referencia}" class="link-item" ><h6 class="mt-0">${item.nombreRecurso}</h6></a>
              </div>
            </div>
        `;
    } else if(item.tipo.nombre === 'web_url'){
        return `
        <div class="media my-2" style="border-bottom: 1px solid #00000020">
          <div class="align-self-center mr-3"><i class="${icon}"></i></div>
          <div class="media-body">
            <a href="${item.enlace_web}" id="${customId}" target="_balnk" class="" ><h6 class="mt-0">${item.nombreRecurso}</h6></a>
          </div>
        </div>
    `;
    }else {
        if (item.mimeType === "audio/mpeg" || item.mimeType === 'video/mp4')
            return `
                <div class="media my-2" style="border-bottom: 1px solid #00000020">
                  <div class="align-self-center mr-3"><i class="${icon}"></i></div>
                  <div class="media-body">
                    <a href="${item.referencia}" id="${customId}" data-mimetype="${item.mimeType}" class="link-item"><h6 class="mt-0">${item.nombreRecurso}</h6></a>
                  </div>
                </div>
            `;
        else{
            let url = Routing.generate('resourceLoader', {'id': item.id});
            return `
                <div class="media my-2" style="border-bottom: 1px solid #00000020">
                  <div class="align-self-center mr-3"><i class="${icon}"></i></div>
                  <div class="media-body">
                    <a href="${url}" id="${customId}" data-mimetype="${item.mimeType}" class="link-item" target="_blank"><h6 class="mt-0">${item.nombreRecurso}</h6></a>
                  </div>
                </div>
            `;
        }

    }
}

$(document).ready(function (e) {
    if(recursos)
        for (let i = 0; i < recursos.length; i++) {

            let element = recursos[i][0];
            let id = (element.libro.nombre).replace(/ /g, "");

            id = '#'+id+element.id;
            recursos[i].forEach(function (item) {
                menuMedia(item, id);
            });
        }

    $('.rotate').parent().click(function () {
        $(this).children().toggleClass('right');
    });


    $('.link-item').click(function (e) {

        let id = this.id.split('_').pop();
        let data = $(this).data();

        if (data.mimetype !== 'application/pdf' && data.mimetype !== 'application/x-pdf' &&
            data.mimetype !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' && data.mimetype !== 'application/msword' &&
            data.mimetype !== 'application/vnd.openxmlformats-officedocument.presentationml.presentation' && data.mimetype !== 'application/vnd.ms-powerpoint' &&
            data.mimetype !== 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' && data.mimetype !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
            data.mimetype !== 'application/vnd.ms-excel')
            e.preventDefault();

        let title = $(this).children().text();
        let urlModal = "";
        if (data.referencia){
            let modal = $('#modalVideoY').modal('show');
            let $modalIframe = modal.find('#iframeVideo');
            let $modalEmbed = $modalIframe.parent();
            urlModal = data.referencia;
            let matches = urlModal.match(/[\\?\\&]v=([^\\?\\&]+)/);
            let idUrl = matches[1];
            $modalEmbed.show();
            $modalIframe.prop('src', 'https://www.youtube.com/embed/'+idUrl+'?rel=0&showinfo=0&color=white&iv_load_policy=3?enablejsapi=1');
        } else if (data.mimetype === 'audio/mpeg' || data.mimetype === 'video/mp4'){
            let modal = $('#modalMediaLocal').modal('show');
            modal.find('.modal-title').text(title);
            let $modalMedia = null;
            if (data.mimetype === 'audio/mpeg'){
                $modalMedia = modal.find('audio');
            } else {
                $modalMedia = modal.find('video');
                modal.find('.modal-dialog').addClass('modal-lg');
            }
            $modalMedia.show();
            urlModal = Routing.generate('resourceLoader', {'id': id});
            $modalMedia.children('source').prop('src', urlModal);
            $modalMedia[0].load();
        }
    });


    $('#modalMediaLocal').on('hidden.bs.modal', function (e) {
        let $modalMedia =$(this).find('audio');
        if($modalMedia.css('display') === 'none'){
            $modalMedia = $(this).find('video');
            $(this).find('.modal-dialog').removeClass('modal-lg');
        }
        $modalMedia.hide();
        $modalMedia[0].pause();
    });

    $('#modalVideoY').on('hidden.bs.modal', function (e) {
        let $modalVideo = $(this).find('#videoFrame');
        document.getElementById('iframeVideo').contentWindow.postMessage('{"event":"command","func": "pauseVideo", "args":""}','*');
        document.getElementById('iframeVideo').src = "";
        // document.getElementById('iframeVideo').stopVideo();
    });


});