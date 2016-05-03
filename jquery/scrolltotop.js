// Script to scroll the window to top.

$(document).ready(function() {
    // Easing function.
    var easing = 'easeOutCubic';
    // Fade duration for the button.
    var fade = 450;

    // Check to see if the window is top if not then display button.
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.scroll-to-top').fadeIn(fade, easing);
        }
        else {
            $('.scroll-to-top').fadeOut(fade, easing);
        }
    });

    // Click event to scroll to top.
    $('.scroll-to-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop : 0}, 800, easing);
    });

});
