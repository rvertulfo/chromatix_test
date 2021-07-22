<?php
/**
 * Functions and definitions
 */
$custom_post_types = array( 'car_brand', 'food');
add_theme_support( 'post-thumbnails' );
add_image_size( 'category-thumb', 300, 9999 ); //300 pixels wide (and unlimited height)

function theme_setup() {
    if (!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {

        wp_deregister_script('jquery');
        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', false, '1.11.2');
        wp_enqueue_script('jquery');
        wp_enqueue_style('normalize-css', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css', array());
        wp_enqueue_style('theme_style', get_template_directory_uri() . '/style.css', array('normalize-css'));
        wp_enqueue_script('script', get_template_directory_uri() . '/script.js', array ('jquery'));
    }


}
add_action( 'after_setup_theme', 'theme_setup' );

function add_script_defer ( $tag, $handle ) {

    if (in_array($handle, array('script')))
        return str_replace( ' src', ' defer="defer" src', $tag );

    return $tag;
}
add_filter( 'script_loader_tag', 'add_script_defer', 10, 2 );


function register_custom_post_types() {
    global $custom_post_types;


    $args['car_brand'] = array(
        'labels' => array(
            'name' => _x('Car Brands', 'post type general name'),
            'singular_name' => _x('Car Brand', 'post type singular name'),
            'add_new' => _x('Add New', 'Car Brand'),
            'add_new_item' => __('Add New Car Brand'),
            'edit_item' => __('Edit Car Brand'),
            'new_item' => __('New Car Brand'),
            'view_item' => __('View Car Brand'),
            'search_items' => __('Search Car Brands'),
            'not_found' =>  __('No Car Brands found'),
            'not_found_in_trash' => __('No Car Brands found in Trash'),
            'parent_item_colon' => ''
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'car-brands', 'with_front' => true),
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail', 'excerpt','page-attributes')
    );
    $args['food'] = array(
        'labels' => array(
            'name' => _x('Foods', 'post type general name'),
            'singular_name' => _x('Food', 'post type singular name'),
            'add_new' => _x('Add New', 'Food'),
            'add_new_item' => __('Add New Food'),
            'edit_item' => __('Edit Food'),
            'new_item' => __('New Food'),
            'view_item' => __('View Food'),
            'search_items' => __('Search Foods'),
            'not_found' =>  __('No Foods found'),
            'not_found_in_trash' => __('No Foods found in Trash'),
            'parent_item_colon' => ''
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'foods', 'with_front' => true),
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','editor','thumbnail', 'excerpt','page-attributes')
    );
    foreach ($custom_post_types as $custom_post_type) {
        register_post_type( $custom_post_type , $args[$custom_post_type]);
    }

}
add_action('init', 'register_custom_post_types');

//Add the meta box callback function
function add_parent_menu_meta_box(){
    global $custom_post_types;

    foreach ( $custom_post_types as $post_type ) {

        add_meta_box(
            'global-notice',
            __( 'Parent Menu'),
            'parent_menu_meta_box_callback',
            $post_type,
            'normal',
            'low'
        );
    }
}

add_action( 'add_meta_boxes', 'add_parent_menu_meta_box' );

//Meta box for setting the parent ID
function parent_menu_meta_box_callback() {
    global $post;
    $custom_post_type_parent_id = get_post_meta($post->ID, 'custom_post_type_parent_id', true);

    $dropdown_args = array(
        'post_type'         => 'page',
        'name'              => 'custom_post_type_parent_id',
        'sort_column'       => 'menu_order, post_title',
        'echo'              => 1,
        'show_option_none'  => 'Select Menu',
        'option_none_value'  => null,
        'selected'          => $custom_post_type_parent_id,
    );

    // Use nonce for verification
    wp_nonce_field('custom_post_type_parent_callback', 'custom_post_type_parent_id_nonce');

    //Dropdown of pages
    wp_dropdown_pages( $dropdown_args );
}

// Save the meta data
function parent_menu_meta_box_save_post($post_id) {
    // make sure data came from our meta box
    if (!wp_verify_nonce($_POST['custom_post_type_parent_id_nonce'], 'custom_post_type_parent_callback')) return $post_id;
    if(isset($post_id) && !empty($post_id)) {
        update_post_meta($post_id, 'custom_post_type_parent_id', $_POST['custom_post_type_parent_id']);
    }
}
add_action("save_post", "parent_menu_meta_box_save_post");



class Menu_Walker extends Walker_Nav_Menu {


    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $custom_post_types;

        $output .= '<li><a href="' . get_permalink($item->ID)  . '">' . $item->post_title . '</a>';
        $args = array(
            'post_type'=> $custom_post_types,
            'orderby' => 'post_title',
            'order'   => 'ASC',
            'meta_query' => array( array( 'key' => 'custom_post_type_parent_id', 'value' => $item->ID ) )
        );

        $the_query = new WP_Query( $args );
        if ($the_query->have_posts()) {

            $output .= '<div class="mega-menu-wrapper"><h2 class="container">' . $item->post_title . '</h2><div class="mega-menu-content"><div class="container"><ul>';
            $custom_posts = $the_query->posts;
            $default_image = NULL;
            foreach($custom_posts as $this_post) {
                $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $this_post->ID ), 'medium');
                $image_src = (!empty($image_data)) ? $image_data[0] : '';
                $default_image = (empty($default_image)) ? $image_src : $default_image;
                $output .= '<li><a href="" class="sub-menu-link" data-image="' . $image_src . '">' . $this_post->post_title . '</a></li>';
            }
            $output .= '</ul><div class="mega-menu-image"><img src="' . $default_image . '" /></div></div></div></div>';
        }


    }
    function end_el(&$output, $item, $depth=0, $args=array()) {
        $output .= "</li>\n";
    }

}
