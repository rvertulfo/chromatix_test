
$( document ).ready(function() {
    $(".sub-menu-link").hover( function() { // Changes the .image-holder's img src to the src defined in .list a's data attribute.
        var value = $(this).attr('data-image');
        var current_value = $(".mega-menu-image img").attr('src');

        if (value) {

            if (current_value != value) {
                $( ".mega-menu-image img" ).stop().fadeTo( 100, 0, function() {
                    $(".mega-menu-image img").attr("src", value);
                    $(".mega-menu-image img").stop().fadeTo(100, 1);
                });


            } else {
                if ($(".mega-menu-image img").css("opacity") == 0) {
                    $(".mega-menu-image img").stop().fadeTo(100, 1);
                }
            }
        } else {
            $(".mega-menu-image img").stop().fadeTo(100, 0);
        }




    }, function() {
        $(".mega-menu-image img").stop().fadeTo(100, 0);
    });
});