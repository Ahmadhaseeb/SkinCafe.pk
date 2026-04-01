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

/**
 * Add Product Search Bar to Astra Header
 */
function skincafe_add_header_product_search() {
    if ( class_exists( 'WooCommerce' ) ) {
        echo '<div class="skincafe-header-search-container">';
        get_product_search_form();
        echo '</div>';
    }
}
// Hook into Astra's top header to ensure it displays prominently
add_action('astra_masthead_top', 'skincafe_add_header_product_search');

// Also provide a shortcode just in case they prefer to use Astra's drag-and-drop Header Builder widget
add_shortcode('skincafe_product_search', function() {
    ob_start();
    get_product_search_form();
    return '<div class="skincafe-header-search-container">' . ob_get_clean() . '</div>';
});

/**
 * Register Gutenberg Block Pattern for the SkinCafe Premium Hero Section
 */
function skincafe_register_block_pattern() {
    register_block_pattern(
        'skincafe/hero-banner',
        array(
            'title'       => __( 'SkinCafe Hero Banner', 'skincafe-child' ),
            'description' => _x( 'A premium glassmorphism hero banner for the homepage.', 'Block pattern description', 'skincafe-child' ),
            'content'     => '<!-- wp:html -->
<div class="skincafe-hero-banner">
    <div class="skincafe-hero-content">
        <h1>Organic Care for Glowing Skin</h1>
        <p>Discover our curated selection of premium, natural skincare essentials designed to bring out your inner radiance.</p>
        <a href="/shop/" class="button">Shop Now</a>
    </div>
</div>
<!-- /wp:html -->',
        )
    );
}
add_action( 'init', 'skincafe_register_block_pattern' );
