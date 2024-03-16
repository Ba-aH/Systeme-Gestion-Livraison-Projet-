const slider = tns({
    container: '.my-slider',
    items: 1,
    nav: false,    
    autoplay: true,
    autoplayHoverPause: true,
    speed: 300,
    autoplayTimeout: 4500,
    autoplayButtonOutput: false,
    mouseDrag: false,
    lazyload: true,
    arrowKeys: true,
    controlsContainer: "#customize-controls",    
});

function initMap() {
    var uluru = {lat: 10.493582, lng: -66.876168};
    var map = new google.maps.Map(
    document.getElementById('map'), {zoom: 18, center: uluru});
    var marker = new google.maps.Marker({position: uluru, map: map});
}

(function() {
    var burger = document.querySelector('.burger');
    var nav = document.querySelector('#'+burger.dataset.target);
   
    burger.addEventListener('click', function(){
      burger.classList.toggle('is-active');
      nav.classList.toggle('is-active');
    });
})();