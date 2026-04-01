<?php
/*
Plugin Name: Auto Setup Logo
Description: Automatically injects the skincafe logo into the database and site settings.
*/

add_action('init', 'skincafe_auto_inject_logo');

function skincafe_auto_inject_logo() {
    // Only run this injection once
    if ( get_option( 'skincafe_logo_injected' ) ) {
        return;
    }

    $upload_dir = wp_upload_dir();
    $source_file = ABSPATH . 'wp-content/uploads/skincafe-logo.png';
    
    // Check if our copied logo exists
    if ( ! file_exists( $source_file ) ) {
        return; 
    }

    $filename = 'skincafe-logo.png';
    // Instead of copying, we will just use the file we already placed in uploads.
    // However, it's not in the year/month folder structure, which wp_insert_attachment expects.
    // Let's copy it to the proper structured upload directory.
    $target_file = $upload_dir['path'] . '/' . $filename;
    
    // If it's not the same path, copy it
    if ( $source_file !== $target_file ) {
        copy( $source_file, $target_file );
    }

    $wp_filetype = wp_check_filetype( $filename, null );

    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => 'SkinCafe Logo',
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $attach_id = wp_insert_attachment( $attachment, $target_file );
    if ( ! is_wp_error( $attach_id ) ) {
        $attach_data = wp_generate_attachment_metadata( $attach_id, $target_file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Set as custom logo for the site (Works for Astra, Storefront, etc.)
        set_theme_mod( 'custom_logo', $attach_id );
        
        // Also update the block theme setting if using Twenty Twenty Four / Five
        $site_logo_id = get_option( 'site_logo' );
        if ( ! $site_logo_id ) {
            update_option( 'site_logo', $attach_id );
        }
    }

    // Mark as done so it never runs again
    update_option( 'skincafe_logo_injected', true );
}
