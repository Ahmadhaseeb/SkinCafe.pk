<?php
/*
Plugin Name: SkinCafe Auto Price Updater
Description: Automatically converts all product prices (multiplies by 100) to match PKR formatting.
*/

add_action('init', 'skincafe_auto_update_prices');

function skincafe_auto_update_prices() {
    // Only run this once!
    if ( get_option( 'skincafe_prices_multiplied' ) ) {
        return;
    }

    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // Get all products
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1, // get all products
        'post_status'    => 'any',
    );
    $products = get_posts( $args );

    foreach ( $products as $post ) {
        $product = wc_get_product( $post->ID );
        if ( ! $product ) {
            continue;
        }

        // Handle variations if it's a variable product
        if ( $product->is_type( 'variable' ) ) {
            $variations = $product->get_children();
            foreach ( $variations as $variation_id ) {
                $variation = wc_get_product( $variation_id );
                skincafe_multiply_product_price( $variation );
            }
        } 
        
        // Handle the main product price
        skincafe_multiply_product_price( $product );
    }

    // Mark as done so it doesn't accidentally run again and make things 150,000!
    update_option( 'skincafe_prices_multiplied', true );
}

function skincafe_multiply_product_price( $product ) {
    $regular_price = $product->get_regular_price();
    if ( is_numeric( $regular_price ) && $regular_price > 0 ) {
        $product->set_regular_price( $regular_price * 100 );
    }

    $sale_price = $product->get_sale_price();
    if ( is_numeric( $sale_price ) && $sale_price > 0 ) {
        $product->set_sale_price( $sale_price * 100 );
    }

    // Update the active price
    $price = $product->get_price();
    if ( is_numeric( $price ) && $price > 0 ) {
        $product->set_price( $price * 100 );
    }

    $product->save();
}
