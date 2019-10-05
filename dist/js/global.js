//START Tooltips
$(function () {
    if (isTouchDevice() === false) {
        $('[data-toggle="tooltip"]').tooltip();
    }
})
//Controlla se è un telefono
function isTouchDevice() {
    return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
}
//END Tooltips

//Se il disoisitivo è touch allora
if (isTouchDevice() === true) {
    //Chiudi la sidebar se qualcosa viene cliccato (mobile)
    $("#sidebar li").click(function () {
        $('#sidebar').toggleClass('active');
        $('html, body').toggleClass('disable-x-scroll');
        $('.swipe-area').toggleClass('swipe-area-activate');
    });
} else {
    // disattiva la navbar mobile
    $('.swipe-area').removeClass('swipe-area-activate');
    $('.swipe-area').removeClass('swipe-area');
}

//Se il tasto #sidebarCollapse è premuto toogle sidebar
$(document).ready(function () {
    $('.sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('html, body').toggleClass('disable-x-scroll');
        $('.swipe-area').toggleClass('swipe-area-activate');
    });
});

//Toggle la SideBar
function toggleSideBar() {
    $('#sidebar').toggleClass('active');
    $('.swipe-area').toggleClass('swipe-area-activate');
}

//Menu Swipe
$(".swipe-area").click(function () {
    if ($(".swipe-area-activate")[0]) {
        $('#sidebar').addClass('active');
        $('html, body').removeClass('disable-x-scroll');
        $('.swipe-area').removeClass('swipe-area-activate');
    }   
});
$(".swipe-area").swipe({
    swipeStatus: function (event, phase, direction, distance, duration, fingers) {
        if (phase == "move" && direction == "right") {
            $('#sidebar').removeClass('active');
            $('html, body').addClass('disable-x-scroll');
            $('.swipe-area').addClass('swipe-area-activate');
            return false;
        }
        if (phase == "move" && direction == "left") {
            $('#sidebar').addClass('active');
            $('html, body').removeClass('disable-x-scroll');
            $('.swipe-area').removeClass('swipe-area-activate');
            return false;
        }
    }
});

// ---- Opzioni grafica ----
//Tasto menù
if (null == $.cookie("tastomenu")) {
    $('#tastomenu').show()
}
else if ($.parseJSON($.cookie("tastomenu"))) {
    $('#tastomenu').show()
}
