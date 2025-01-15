<?php

/**
    Check detailed explanation: https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// get all our custom posts
$properties = get_posts(['post_type' => ['property', 'agent'], 'numberposts' => -1]);

// delete all custom posts
foreach ($properties as $property) {
    wp_delete_post($property->ID, true);
}
