// JavaScript Functions
// Felice js v1.0 by Angelo Mazzilli
// http://themeforest.net/user/AngeloM

$(document).ready(function () {

  /* --- NavBar --- */
  $(".onePageNav").onePageNav({
    scrollOffset: 65
  });

  /* --- NiceScroll --- */
  $("html").niceScroll();

  /* --- Sequencejs Slider --- */
  var options = {
    nextButton: true,
    prevButton: true,
    pagination: true,
    animateStartingFrameIn: true,
    autoPlay: true,
    autoPlayDelay: 3000,
    preloader: true,
  };
  var mySequence = $("#sequence").sequence(options).data("sequence");

  /* --- Flex Carousel --- */
  $(".flexslider").flexslider({
    animation: "slide",
    animationLoop: true,
    itemWidth: 300,
    itemMargin: 0,
    prevText: "<i class='icon-angle-left'></i>",
    nextText: "<i class='icon-angle-right'></i>",
  });

  /* --- Flex Carousel Clients --- */
  $(".flexslider-clients").flexslider({
    animation: "slide",
    animationLoop: true,
    itemWidth: 150,
    itemMargin: 0,
    prevText: "<i class='icon-angle-left'></i>",
    nextText: "<i class='icon-angle-right'></i>",
  });

  /* --- Fancybox --- */
  $(".view-fancybox").fancybox({
    openEffect: 'elastic',
    closeEffect: 'elastic',
    next: '<i class="icon-smile"></i>',
    prev: '<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
  });

  /* --- Active Filter Menu --- */
  $(".filter a").click(function (e) {
    $(".filter a").removeClass("active");
    $(this).addClass("active");
    e.preventDefault();
  });

  /* --- Hover Effect --- */
  $('.masonry .item, .thumbnails li').mouseover(function () {
    $(this).siblings().css({opacity: 0.5}, 500)
  })
  .mouseout(function () {
    $(this).siblings().css({opacity: 1}, 500)
  });

  /* --- Google Map --- */
  var mapOptions = {
    center: new google.maps.LatLng(41.888637,12.482608),
    zoom: 15,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
  var image = "img/marker.png";
  var marker = new google.maps.Marker({
    position: mapOptions.center,
    map: map,
    icon: image
  });
  var styles = [{
    "featureType": "landscape",
    "stylers": [{
      "saturation": -100
    }]
  }, {
    "featureType": "poi",
    "stylers": [{
      "saturation": -100
    }]
  }, {
    "featureType": "road",
    "stylers": [{
      "saturation": -100
    }]
  }, {
    "featureType": "transit",
    "stylers": [{
      "saturation": -100
    }]
  }, {
    "featureType": "water",
    "stylers": [{
      "saturation": -100
    }]
  }, {
    "featureType": "administrative",
    "stylers": [{
      "saturation": -100
    }]
  }]
  var styledMap = new google.maps.StyledMapType(styles, {
    name: "map_style"
  });
  map.mapTypes.set('map_style', styledMap);
  map.setMapTypeId('map_style');

  /* --- toTop --- */
  $('#toTop').click(function () {
    $('body,html').animate({
      scrollTop: 0
    }, 1000);
  });
  $(window).scroll(function () {
    if ($(this).scrollTop() != 0) {
      $('#toTop').fadeIn();
    } else {
      $('#toTop').fadeOut();
    }
  });

});

$(window).load(function () {

  /* --- Masonry --- */
  if (jQuery().isotope) {
    $(".masonry").isotope({
      itemSelector: ".item"
    });
    $("#folio-filters a").click(function () {
      var selector = $(this).attr("data-filter");
      $(".masonry").isotope({
        itemSelector: ".item",
        filter: selector
      });
      return false;
    });
  }

});