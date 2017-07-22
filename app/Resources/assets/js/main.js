$(document).ready(function () {
   var swiper = new Swiper ('.swiper-container', {
       nextButton: '.swiper-button-next',
       prevButton: '.swiper-button-prev',
       slidesPerView: 6,
       spaceBetween: 1,
       initialSlide: 5-2
   });

    $("[data-fancybox]").fancybox();
});