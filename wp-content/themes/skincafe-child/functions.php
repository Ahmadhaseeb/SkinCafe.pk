<?php
/**
 * SkinCafe Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package SkinCafe Child
 * @since 1.0.0
 */

/**
 * Enqueue styles
 */
function skincafe_child_enqueue_styles() {
    // Astra already enqueues child theme styles naturally if configured right, 
    // but this ensures the parent stylesheet loads first.
    wp_enqueue_style( 'skincafe-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'skincafe-child-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        array( 'skincafe-parent-style' ), 
        wp_get_theme()->get('Version') 
    );
}
add_action( 'wp_enqueue_scripts', 'skincafe_child_enqueue_styles', 15 );
