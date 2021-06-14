import "sweetalert2";

require('jquery-ui/ui/widgets/droppable');
require('jquery-ui/ui/widgets/sortable');
require('jquery-ui/ui/widgets/selectable');
require('jquery-ui/ui/widgets/draggable');
require('jquery-ui/ui/widgets/resizable');
require('jquery-ui/ui/widgets/slider');

// import $ from 'jquery';
//
// const Routing = require('./routing');
// import swal from 'sweetalert2';


    /**
     * Auto-growing textareas; technique ripped from Facebook
     X

     *
     * https://github.com/jaz303/jquery-grab-bag/tree/master/javascripts/jquery.autogrow-textarea.js
     */
    $.fn.autogrow = function(options)
    {
        return this.filter('textarea').each(function()
        {
            var self         = this;
            var $self        = $(self);
            var minHeight    = $self.height();
            var noFlickerPad = $self.hasClass('autogrow-short') ? 0 : parseInt($self.css('lineHeight')) || 0;

            var shadow = $('<div></div>').css({
                position:    'absolute',
                top:         -10000,
                left:        -10000,
                width:       $self.width(),
                fontSize:    $self.css('fontSize'),
                fontFamily:  $self.css('fontFamily'),
                fontWeight:  $self.css('fontWeight'),
                lineHeight:  $self.css('lineHeight'),
                resize:      'none',
                'word-wrap': 'break-word'
            }).appendTo(document.body);

            var update = function(event)
            {
                var times = function(string, number)
                {
                    for (var i=0, r=''; i<number; i++) r += string;
                    return r;
                };

                var val = self.value.replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/&/g, '&amp;')
                    .replace(/\n$/, '<br/>&nbsp;')
                    .replace(/\n/g, '<br/>')
                    .replace(/ {2,}/g, function(space){ return times('&nbsp;', space.length - 1) + ' ' });

                // Did enter get pressed?  Resize in this keydown event so that the flicker doesn't occur.
                if (event && event.data && event.data.event === 'keydown' && event.keyCode === 13) {
                    val += '<br />';
                }

                shadow.css('width', $self.width());
                shadow.html(val + (noFlickerPad === 0 ? '...' : '')); // Append '...' to resize pre-emptively.
                $self.height(Math.max(shadow.height() + noFlickerPad, minHeight));
            };

            $self.change(update).keyup(update).keydown({event:'keydown'},update);
            $(window).resize(update);

            update();
        });
    };


var noteTemp =
        `<div class="note">
           <div class="overlay-note" style="display: none">
               <div class="overlay__inner">
                   <div class="overlay__content">
                       <div class="spinner"></div>
                   </div>
               </div>
           </div>
          <form id="form_note" name="notas" action="notas_new">
               <a href="javascript:;" class="badge badge-danger saveNote" style="display: none"><span class="fas fa-exclamation-triangle"></span> Click aquí para Guardar</a>
        	   <a href="javascript:;" class="button remove">X</a>
         	    <div class="note_cnt">
        		    <textarea class="title form-control" name="notas[titulo]" placeholder="Titulo para la nota..."></textarea>
         		    <textarea class="cnt form-control" name="notas[texto]" placeholder="Descripción de la nota..."></textarea>
         		    <input type="hidden" class="unidad form-control" name="notas[unidad]" value="${unidad}">
                   <input type="hidden" name="notas[_token]" value="${csfr}">
        	    </div>
           </form>
       </div>`
;


function deleteNote(){
    let $this =  $(this);
    let $badge = $this.prev();
    let title = $this.next().children('.title').val();
    let cnt = $this.next().children('.cnt').val();
    let $form = $this.parent();
    let $overlay = $form.prev('.overlay-note');
    let is_disabled = !!$this.attr('disabled');

    if ((title || cnt || $badge.hasClass('badge-warning') || $badge.hasClass('badge-success')) && !is_disabled){
        let message = "¡La nota no esta vacía!";
        if (!title && !cnt){
            message = '¡Esta nota esta guardada en sus registros, de continuar se eliminará permanentemente!'
        }
        swal.fire({
            title: '¿Esta seguro?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.value) {
                if ($badge.hasClass('badge-success') || $badge.hasClass('badge-warning')){
                    let url = Routing.generate('notas_delete');
                    let id = $form.attr('id').split('_').pop();
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {'id': id},
                        dataType: 'json',
                        beforeSend: function(){
                            $overlay.show();
                            $this.attr('disabled', true);
                        },
                        success: function(response){
                            if (response.success){
                                $this.parent().parent('.note').remove();
                                swal.fire(
                                    response.success,
                                    "",
                                    'success'
                                )
                            } else if (response.error) {
                                swal.fire(
                                    response.error,
                                    "",
                                    'error'
                                )
                            }
                        },
                        complete: function () {
                            $overlay.hide();
                            $this.attr('disabled', false);
                        }
                    });
                } else {
                    $this.parent().parent('.note').remove();
                }
            }
        })
    } else if (!is_disabled) {
        $(this).parent().parent('.note').remove();
    }

}

function loadNote(nota, id) {
    let $note = $('.note');
    let top = 0;
    let row = 0;
    let notesQ = $note.length;
    if (notesQ >= 1){
        top = 5*notesQ;
    }
    if (notesQ < 5)
        $(noteTemp).hide().appendTo("#board").fadeIn().attr('id', 'note_' + id).css("top", top + 'rem').draggable();
    else{
        let rest = notesQ%5;
        let row = Math.floor(notesQ/5);
        top = rest*5;
        $(noteTemp).hide().appendTo("#board").fadeIn().attr('id', 'note_' + id).css({
            "top": top + 'rem',
            "right": row*2 + 'rem',
        }).draggable();
    }
    $('#form_note').attr('id', 'form_note_'+id);
    let $badge = $('#form_note_'+id+' .badge');

    $badge.removeClass('badge-danger');
    $badge.addClass('badge-success');
    $badge.html('<span class="fas fa-check-square"></span> Guardada');

    $('.remove').click(deleteNote);
    $badge.click(saveNote);

    let tituloInput = document.querySelector('#form_note_'+id +' div.note_cnt .title');
    $(tituloInput).val(nota.titulo);

    let textoInput = document.querySelector('#form_note_'+id +' div.note_cnt .cnt');
    $(textoInput).val(nota.texto);

    tituloInput.addEventListener('input',(e)=>{
        bageAnimation(e, tituloInput, $badge);
    });

    textoInput.addEventListener('input',(e)=>{
        bageAnimation(e, textoInput, $badge);
    });

    $('textarea').autogrow();

    return false;
}

function newNote() {
    let $note = $('.note');
    let top = 0;
    let notesQ = $note.length;
    if (notesQ >= 1){
        top = 5*notesQ;
    }
    let provId = 0;
    if (totalNotes)
        provId = totalNotes + notesQ;
    else
        provId = notesQ;
    $(noteTemp).hide().appendTo("#board").fadeIn().attr('id', 'note_' + provId).css("top", top + 'rem').draggable();
    $('#form_note').attr('id', 'form_note_'+provId);
    $('.remove').click(deleteNote);


    let tituloInput = document.querySelector('#form_note_'+provId +' div.note_cnt .title');
    let textoInput = document.querySelector('#form_note_'+provId +' div.note_cnt .cnt');
    let $badge = $('#form_note_'+provId +' .badge');

    $badge.click(saveNote);

    tituloInput.addEventListener('input',(e)=>{
        bageAnimation(e, tituloInput, $badge);
    });

    textoInput.addEventListener('input',(e)=>{
        bageAnimation(e, textoInput, $badge);
    });

    $('textarea').autogrow();

    return false;
}

function bageAnimation(e, inputELement, $badge) {
    let is_visible = $badge.css('display');
    if($badge.hasClass('badge-success')){
        $badge.removeClass('badge-success');
        $badge.addClass('badge-warning');
        $badge.html('<span class="fas fa-edit"></span> Editada');
    }
    if (inputELement.value && (is_visible === 'none')){
        $badge.fadeIn();
    } else if (!inputELement.value){
        $badge.fadeOut();
    }
}

function saveNote() {
    let $noteCnt = $(this).siblings('.note_cnt');
    let $noteBadge = $(this);
    let $title = $noteCnt.children('.title');
    let $cnt = $noteCnt.children('.cnt');
    let titleVal = $title.val();
    let cntVal = $cnt.val();

    if (!titleVal){
        $noteCnt.remove('.invalid-feedback');
        $title.removeClass('is-invalid');
        $title.addClass('is-invalid');
    }else{
        $noteCnt.remove('.invalid-feedback');
        $title.removeClass('is-invalid');
    }
    if (!cntVal){
        $noteCnt.remove('.invalid-feedback');
        $cnt.removeClass('is-invalid');
        $cnt.addClass('is-invalid');
    }else {
        $noteCnt.remove('.invalid-feedback');
        $cnt.removeClass('is-invalid');
    }

    if (titleVal && cntVal){
        let $form = $noteCnt.parent('form');
        let url = '';
        let data = {};
        let succesClass = '';
        data = $form.serialize();
        let $overlay = $form.prev('.overlay-note');
        if ($noteBadge.hasClass('badge-danger')){
            url = $form.attr('action');
            succesClass = 'badge-success';
            url = Routing.generate(url);
        } else {
            url = 'notas_edit';
            let id = $form.attr('id').split('_').pop();
            url = Routing.generate(url, {'id': id});
        }


        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function(){
                $overlay.show();
                $noteBadge.next().attr('disabled', true);
            },
            success: function(response){
                if (response.success){
                    let data = response.success.data;
                    if (succesClass){
                        $noteBadge.removeClass('badge-danger');
                        $noteBadge.addClass(succesClass);
                        $noteBadge.html('<span class="fas fa-check-square"></span> Guardada');
                        let newId = 'note_' + data.id;
                        $form.attr('id', newId);
                        $form.parent('.note').attr('id', newId);
                    } else {
                        if ($noteBadge.hasClass('badge-warning')){
                            $noteBadge.removeClass('badge-warning');
                            $noteBadge.addClass('badge-success');
                        }
                        $noteBadge.html('<span class="fas fa-check-square"></span> Guardada');
                    }
                } else if (!succesClass) {
                    $noteBadge.html('<span class="fas fa-exclamation-triangle"></span> NO se guardaron los cambios');
                }
            },
            complete: function () {
                $overlay.hide();
                $noteBadge.next().attr('disabled', false);
                if ($noteBadge.hasClass('badge-success')){
                    setTimeout(() => {
                        $noteBadge.fadeOut();
                    }, 1000)
                }
            }
        })



    }
}

function loadNotes(notes)
{
    notes.forEach((n)=>{
        loadNote(n, n.id)
    });
}

$(document).ready(function() {
    let totalNotes = 0;
    if (notas){
        loadNotes(notas);
        let totalNotes = notas.length;
    }

    $("#board").height($(document).height());

    $("#add_new").click(newNote);
    //
    // $('.remove').click(deleteNote);
    //
    // $('.saveNote').click(saveNote);

    // newNote();

    return false;
});