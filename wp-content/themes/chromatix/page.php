<?php
/**
 * The template for displaying all single posts
 */

get_header();

while ( have_posts() ) :
    the_post();
endwhile; // End of the loop

get_footer();
