//
// import $ from "jquery";
// const Routing = require('./routing');

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