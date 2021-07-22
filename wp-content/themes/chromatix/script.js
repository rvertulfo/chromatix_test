
$( document ).ready(function() {
    $(".sub-menu-link").hover( function() { // Changes the .image-holder's img src to the src defined in .list a's data attribute.
        var value=$(this).attr('data-image');
        $(".mega-menu-image img").attr("src", value);
        $(".mega-menu-image").
    });
});