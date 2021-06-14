'use strict';
// const Routing = require('../routing');
// require('jquery-ui/ui/widgets/draggable');

class Pageflip {

  constructor(mainElement, pages, actividades, state, horizontal, onePage) {
    this.pages = pages;
    this.actividades = {};
    if (actividades.length)
      this.actividades = this.convertArrayToObject(actividades, 'pagina');
    this.audioUrl = 'page-flip.mp3';
    this.transitionMs = 500;
    this.hasPrevPage = false;
    this.hasNextPage = true;
    this.horizontal = horizontal;
    this.onePage = onePage;

    if(!state && !onePage){

      this.leftHiddenPage = -2;
      this.leftOverleaf = -1;
      this.leftPage = 0;
      this.rightPage = 1;
      this.rightOverleaf = 2;
      this.rightHiddenPage = 3;
    } else if (state && !onePage){

      this.leftHiddenPage = state.leftHiddenPage;
      this.leftOverleaf = state.leftOverleaf;
      this.leftPage = state.leftPage;
      this.rightPage = state.rightPage;
      this.rightOverleaf = state.rightOverleaf;
      this.rightHiddenPage = state.rightHiddenPage;

      if (this.rightOverleaf >= pages.length){
        this.hasNextPage = false;
      }
      if (this.leftPage !== 0){
        this.hasPrevPage = true;
      }
    } else if (!state && onePage){

      this.leftOverleaf = -1;
      this.leftPage = 0;
      this.rightOverleaf = 1;


    } else if (state && onePage){

      this.leftOverleaf = state.leftOverleaf;
      this.leftPage = state.leftPage;
      this.rightOverleaf = state.rightOverleaf;

      if (this.rightOverleaf >= pages.length){
        this.hasNextPage = false;
      }
      if (this.leftPage !== 0){
        this.hasPrevPage = true;
      }

    }

    this.checkingArray = '';

    var _this = this;
    this._el(mainElement).element.innerHTML = `
      <div id="page-flip"><span class='page-flip-loader'></span></div>
    `;
    this.preloadPages(function() {
      _this.preloadAudio();
      _this.buildMarkup(mainElement);
      _this.renderPages();
      _this.addClickOnGrabbers();
      if (_this.hasPrevPage){
        let $leftIconGrabber = _this._el('.page-left .page-grabber .icon-paging');
        $leftIconGrabber.removeClass('hide-icon-paging');
        $leftIconGrabber.addClass('show-icon-paging');
      }
      if (!_this.hasNextPage){
        let $rightIconGrabber = _this._el('.page-right .page-grabber .icon-paging');
        if (_this.onePage)
          $rightIconGrabber = _this._el('.page-grabber-right .icon-paging');

        $rightIconGrabber.removeClass('show-icon-paging');
        $rightIconGrabber.addClass('hide-icon-paging');
      }
    });
  };

  preloadPages(callback) {
    let _this = this,
    images    = [],
    loaded    = 0;

    for (let i = 0; i < this.pages.length; i++) {
      images[i] = new Image();
      images[i].onload = function() {
        if (++loaded === _this.pages.length) callback();
      };
      images[i].src = _this.pages[i];
    }
  };

  preloadAudio() {
    this.audio = new Audio();
    this.audio.preload = 'auto';
    this.audio.src = this.audioUrl;
  };

  organizePages(direction) {
    let _this  = this;
    let $pageLeft  = this._el('.page-left');
    let $pageGrabberLeft = this._el('.page-left .page-grabber');
    let $pageGrabberRight = this._el('.page-left .page-grabber-right');
    let $iconGrabberRight = this._el('.page-right .page-grabber .icon-paging');
    let $pageRight = this._el('.page-right');

    let $iconGrabberLeft = this._el('.page-left .page-grabber .icon-paging');

    if (_this.onePage)
       $iconGrabberRight = this._el('.page-grabber-right .icon-paging');

    if (direction === 'prev') {

       let compSelector = this.leftHiddenPage;
       let numCompare = 0;
       if (_this.onePage){
         compSelector = this.leftOverleaf;
         // numCompare = -1
       }

      if (compSelector <= numCompare) {
        $iconGrabberLeft.removeClass('show-icon-paging');
        $iconGrabberLeft.addClass('hide-icon-paging');

        if (_this.onePage){
          $iconGrabberRight.removeClass('show-icon-paging');
          $iconGrabberRight.addClass('hide-icon-paging');
        }

        $pageLeft.addClass('reduce-to-left');

        if (_this.onePage){
          setTimeout(function () {
            $iconGrabberRight.removeClass('hide-icon-paging');
            $iconGrabberRight.addClass('show-icon-paging');
          }, 500);
        }
      }
      if (!_this.onePage){
        this.addPage(this.leftHiddenPage, '.hidden-left-page');
      }
      this.addPage(this.leftOverleaf, '.prev-page');

      if (!_this.onePage){
        this.leftHiddenPage -= 2;
        this.leftOverleaf -= 2;
        this.leftPage -= 2;
        this.rightPage -= 2;
        this.rightOverleaf -= 2;
        this.rightHiddenPage -= 2;
      }
      else {
        this.leftOverleaf -= 1;
        this.leftPage -= 1;
        this.rightOverleaf -= 1;
      }


    } else {
      let $compSelector = this.rightHiddenPage;
      if (_this.onePage){
        $compSelector = this.rightOverleaf;
      }

      if ($compSelector >= this.pages.length-1) {
        if (!_this.onePage)
          $pageRight.addClass('reduce-to-right');
        else
          $pageLeft.addClass('reduce-to-right');

        $iconGrabberRight.removeClass('show-icon-paging');
        $iconGrabberRight.addClass('hide-icon-paging');
      }
      if (!_this.onePage)
        this.addPage(this.rightHiddenPage, '.hidden-right-page');
      this.addPage(this.rightOverleaf, '.next-page');

      if (!_this.onePage){
        this.leftHiddenPage += 2;
        this.leftOverleaf += 2;
        this.leftPage += 2;
        this.rightPage += 2;
        this.rightOverleaf += 2;
        this.rightHiddenPage += 2;
      } else {
        this.leftOverleaf += 1;
        this.leftPage += 1;
        this.rightOverleaf += 1;

      }

    }

    if (this.leftOverleaf <= -1) {
      if (!_this.onePage)
        $pageLeft.addClass('disable-click');
      else
        $pageGrabberLeft.addClass('disable-click');
      this.hasPrevPage = false;
    } else {
      if (!_this.onePage)
        $pageLeft.removeClass('disable-click');
      else
        $pageGrabberLeft.removeClass('disable-click');

      this.hasPrevPage = true;
    }

    if (this.rightOverleaf >= this.pages.length) {
      if (!_this.onePage)
        $pageRight.addClass('disable-click');
      else
        $pageGrabberRight.addClass('disable-click');
      this.hasNextPage = false;
    } else {
      if (!_this.onePage)
        $pageRight.removeClass('disable-click');
      else
        $pageGrabberRight.removeClass('disable-click');
      this.hasNextPage = true;
    }

    this.delayTransition(function() {
      _this.renderPages();
    });
  };

  renderPages() {
    this.addPage(this.leftPage, '.page-left');
    if (!this.onePage)
      this.addPage(this.rightPage, '.page-right');
  };

  addClickOnGrabbers() {
    let _this     = this;
    let $leftGrabber  = this._el('.page-left .page-grabber').element;
    let $rightGrabber = this._el('.page-right .page-grabber').element;

    if (_this.onePage)
       $rightGrabber = this._el('.page-grabber-right').element;

    $leftGrabber.addEventListener('click', function() { _this.leftGrabberOnClick() }, false);
    $rightGrabber.addEventListener('click', function() { _this.rightGrabberOnClick() }, false);
    if (!_this.onePage)
      this._el('.page-left').addClass('disable-click');
    else
      this._el('.page-grabber').addClass('disable-click');
  };

  leftGrabberOnClick() {

    let $iconGrabber = this._el('.page-left .page-grabber .icon-paging');
    if (!this.hasPrevPage && !this.isTurningPage){
      $iconGrabber.addClass('hide-icon-paging');
      $iconGrabber.removeClass('show-icon-paging');
      return;
    }

    this.audio.play();
    this.isTurningPage = true;

    let _this       = this;
    let $prevPage       = this._el('.prev-page');
    let $leftBrightness = this._el('.left-brightness');
    let $hiddenLeftPage = null;
    if (!_this.onePage)
       $hiddenLeftPage = this._el('.hidden-left-page');
    let $pageGrabber    = this._el('.page-grabber');
    let $rightIconGrabber = this._el('.page-right .page-grabber .icon-paging');
    if (_this.onePage)
       $rightIconGrabber = this._el('.page-grabber-right .icon-paging');

    if (!_this.onePage)
      $prevPage.addClass('turning-prev-page');
    else {
      $prevPage.addClass('turning-prev-page');
    }
    $leftBrightness.addClass('turning-right');
    if (!_this.onePage)
      $hiddenLeftPage.addClass('show-hidden-left-page');

    $pageGrabber.addClass('hide-page-fold');
    $iconGrabber.addClass('show-icon-paging');
    $rightIconGrabber.removeClass('hide-icon-paging');
    $rightIconGrabber.addClass('show-icon-paging');

    _this.organizePages('prev');

    this.delayTransition(function() {
      $prevPage.removeClass('turning-prev-page');
      $leftBrightness.removeClass('turning-right');
      if (!_this.onePage)
        $hiddenLeftPage.removeClass('show-hidden-left-page');
      $pageGrabber.removeClass('hide-page-fold');
      _this._el('.page-left').removeClass('reduce-to-left');
      _this.reset();
    });
  };

  rightGrabberOnClick() {
    let $iconGrabber = this._el('.page-right .page-grabber .icon-paging');
    if (this.onePage)
       $iconGrabber = this._el('.page-grabber-right .icon-paging');
    this._el('.page-right .page-grabber .icon-paging');
    if (!this.hasNextPage && !this.isTurningPage){
      $iconGrabber.addClass('hide-icon-paging');
      $iconGrabber.removeClass('show-icon-paging');
      return;
    }

    this.audio.play();
    this.isTurningPage = true;

    let _this        = this;
    let $nextPage        = this._el('.next-page');
    let $rightBrightness = this._el('.right-brightness');
    let $hiddenRightPage = null;
    if (!_this.onePage)
       $hiddenRightPage = this._el('.hidden-right-page');
    let $pageGrabber     = this._el('.page-grabber');
    if (_this.onePage)
       $pageGrabber     = this._el('.page-grabber-right');
    let $leftIconGrabber = this._el('.page-left .page-grabber .icon-paging');

    $nextPage.addClass('turning-next-page');
    $rightBrightness.addClass('turning-left');
    if (!_this.onePage){
      $hiddenRightPage.addClass('show-hidden-right-page');
      $pageGrabber.addClass('hide-page-fold');
    }


    $iconGrabber.addClass('show-icon-paging');
    $leftIconGrabber.removeClass('hide-icon-paging');
    $leftIconGrabber.addClass('show-icon-paging');

    _this.organizePages('next');

    this.delayTransition(function() {
      $nextPage.removeClass('turning-next-page');
      $rightBrightness.removeClass('turning-left');
      if (!_this.onePage)
        $hiddenRightPage.removeClass('show-hidden-right-page');
      $pageGrabber.removeClass('hide-page-fold');
      if (!_this.onePage)
        _this._el('.page-right').removeClass('reduce-to-right');
      else
        _this._el('.page-left').removeClass('reduce-to-right');

      _this.reset();
    });
  };

  delayTransition(callback) {
    setTimeout(function() {
      callback();
    }, this.transitionMs);
  };

  addPage(page, selector) {
    if (this.pages[page] !== undefined) {
      let tempSelector = '';
      if (selector === ".prev-page" || selector === ".hidden-left-page" || selector === ".page-left"){
        tempSelector = '.page-left';
      }
      else if (selector === ".next-page" || selector === ".hidden-right-page" || selector === ".page-right"){
        if (!this.onePage)
          tempSelector = '.page-right';
        else
          tempSelector = '.page-left';
      }
      if (tempSelector){
        let $activitiesFooter = this._el(tempSelector + ' .activities-footer');
        if (this.actividades[page]){
          let item = this.actividades[page];
          let url = Routing.generate('actividades_show', {'id': item.id} ) ;
          $activitiesFooter.element.setAttribute('href', url );
          $activitiesFooter.element.innerText = item.nombre;
          setTimeout(function (e) {
            $activitiesFooter.removeClass('hide-activities');
            if ($('.right-a').text() !== 'Ver Actividad')
              $('.right-a').fadeIn();
          }, 300);
        } else {
          $activitiesFooter.element.setAttribute('href', '#' );
          $activitiesFooter.addClass('hide-activities');
        }
      }
      this._el(selector).element.style.backgroundImage = 'url("'+ this.pages[page] +'")';
    } else {
      $('.right-a').hide();
      $('.right-a').text('Ver Actividad');
      this._el(selector).element.style.backgroundImage = null;
    }
  };

  reset() {
    this.audio.pause();
    this.audio.currentTime = 0;
    this.isTurningPage = false;
  };

  buildMarkup(mainElement) {
if (!this.onePage)
    this._el('#page-flip').element.innerHTML = `
      <div class="pages-container">
        <div class="hidden-left-page"></div>
        <div class="left-brightness"></div>
        <div class="prev-page"></div>
        <div class="page-left">
          <div class="page-grabber">
            <div class="page-fold"></div>
            <div class="icon-paging hide-icon-paging"><i class="fas fa-chevron-left fa-2x ml-3"></i></div>
          </div>
          <a href="#" class="btn btn-success activities-footer hide-activities left-a" style="text-decoration: none; margin-left: 7rem" target="_blank">Ver Actividad</a>
          <div class="page-middle"></div>          
        </div>
        <div class="page-right">
          <div class="page-middle"></div>
          <a href="#" class="btn btn-success activities-footer hide-activities right-a" style="text-decoration: none" target="_blank">Ver Actividad</a>
          <div class="page-grabber">            
            <div class="page-fold"></div>
            <div class="icon-paging"><i class="fas fa-chevron-right fa-2x" style="margin-left: 5rem"></i></div>
          </div>
        </div>
        <div class="next-page"></div>
        <div class="right-brightness"></div>
        <div class="hidden-right-page"></div>
      </div>
    `;
else {
  this._el('#page-flip').element.innerHTML = `
      <div class="pages-container" style="left: 25%">
        <div class="left-brightness"></div>
        <div class="prev-page" style="left: 0%"></div>
        <div class="page-left">
          <div class="page-grabber">
            <div class="page-fold"></div>
            <div class="icon-paging hide-icon-paging"><i class="fas fa-chevron-left fa-2x ml-3"></i></div>
          </div>
          <a href="#" class="btn btn-success activities-footer hide-activities left-a" style="text-decoration: none; margin-left: 7rem" target="_blank">Ver Actividad</a>
          
          <div class="page-grabber-right">            
              <div class="page-fold-right"></div>
              <div class="icon-paging"><i class="fas fa-chevron-right fa-2x" style="margin-left: 5rem"></i></div>
          </div>
        </div>
          
        <div class="next-page" style="right: 50%"></div>
        <div class="right-brightness"></div>
      </div>
    `;
}

    $('.activities-footer').draggable({
      cancel: false
    });

    if (this.horizontal){
      let $pageGrabber = $('.page-grabber');
      $pageGrabber.hover(function (e) {
        $(this).children('.page-fold').css({
          "width": "2%",
          "height": "3%"
        });
      }, function (e) {
        $(this).children('.page-fold').css({
          "width": "0%",
          "height": "0%"
        });
      });
    }
  };

  _el(selector) {
    this.element;
    return {
      element: document.querySelector(selector),
      addClass: function(className) {
        this.element.classList.add(className);
        return this;
      },
      removeClass: function(className) {
        this.element.classList.remove(className);
        return this;
      }
    };
  };

  convertArrayToObject(array, key){
    const initialValue = {};
    return array.reduce((obj, item) => {
      return {
        ...obj,
        [item[key]]: item,
      };
    }, initialValue);
  };


}

export default Pageflip

