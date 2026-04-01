<?php
/*
Plugin Name: SkinCafe Auto Setup (WooCommerce & Content)
Description: Automatically configures WooCommerce for Pakistan, activates the theme, and creates demo content.
*/

add_action('init', 'skincafe_run_auto_setup');

function skincafe_run_auto_setup() {
    if ( get_option( 'skincafe_setup_completed' ) ) {
        return;
    }

    // 1. Activate Child Theme
    switch_theme('skincafe-child');

    // 2. Configure WooCommerce for Pakistan
    if ( class_exists('WooCommerce') ) {
        update_option('woocommerce_currency', 'PKR');
        update_option('woocommerce_default_country', 'PK:PK-PB'); // Base location Punjab, Pakistan
        update_option('woocommerce_allowed_countries', 'specific');
        update_option('woocommerce_specific_allowed_countries', array('PK')); // Only sell in Pakistan
        update_option('woocommerce_currency_pos', 'left');
        
        // Enable COD
        $cod_settings = get_option('woocommerce_cod_settings', array());
        $cod_settings['enabled'] = 'yes';
        $cod_settings['title'] = 'Cash on Delivery';
        $cod_settings['description'] = 'Pay with cash upon delivery.';
        update_option('woocommerce_cod_settings', $cod_settings);
    }

    // 3. Create pages
    $home_page_id = skincafe_create_page('Home', 'Welcome to SkinCafe.pk, where organic beauty meets modern radiance. Explore our curated selection of skincare essentials.');
    $about_page_id = skincafe_create_page('About Us', 'SkinCafe is dedicated to bringing you the finest organic skincare products designed specifically for your skin.');

    if ( $home_page_id ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_page_id );
    }

    // 4. Create demo products if they don't exist
    if ( class_exists('WC_Product_Simple') ) {
        $products = array(
            array(
                'title' => 'Organic Coffee Face Scrub',
                'price' => '1500',
                'desc' => 'Exfoliate and nourish your skin with our signature organic coffee scrub.',
                'image' => get_option('site_logo') // Use the logo temporarily as a placeholder image
            ),
            array(
                'title' => 'Glowing Radiance Serum',
                'price' => '2500',
                'desc' => 'Achieve a natural, healthy glow with our deeply hydrating serum.',
                'image' => get_option('site_logo')
            ),
            array(
                'title' => 'Hydrating Rose Mist',
                'price' => '850',
                'desc' => 'Refresh your skin anytime. Formulated with real rose extracts.',
                'image' => get_option('site_logo')
            )
        );

        foreach ($products as $prod) {
            $existing = get_page_by_title( $prod['title'], OBJECT, 'product' );
            if ( ! $existing ) {
                $product = new WC_Product_Simple();
                $product->set_name( $prod['title'] );
                $product->set_regular_price( $prod['price'] );
                $product->set_description( $prod['desc'] );
                $product->set_short_description( $prod['desc'] );
                $product->set_status('publish'); // Set to publish
                
                if ( $prod['image'] ) {
                    $product->set_image_id( $prod['image'] );
                }
                
                $product->save();
            }
        }
    }

    // Done
    update_option( 'skincafe_setup_completed', true );
}

function skincafe_create_page($title, $content) {
    $existing = get_page_by_title($title);
    if (! $existing) {
        $page = array(
            'post_type' => 'page',
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => 1,
        );
        return wp_insert_post($page);
    }
    return $existing->ID;
}
