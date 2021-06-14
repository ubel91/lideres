// import $ from 'jquery';

$(document).ready(function () {

    let loginBtn = $('#btn-login');

    let $alert = $('.alert-danger');
    if ($alert.text()){
        initLogin(loginBtn);
    }

    loginBtn.click(function (){
        initLogin(loginBtn)
    });
});

function initLogin(btn) {
    btn.fadeOut();
    $('#logoLideres').fadeOut(function () {
        $('#login-form').fadeIn();
        $('#logoLideres').fadeIn();
    });
}

