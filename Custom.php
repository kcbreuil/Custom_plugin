<?php
/**
 * Plugin Name:       Custom
 * Description:       Custom plugin that integrates an API as a custom post type
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Kaitlyn Breuil 
 */

 /* Start Adding Functions Below this Line */

 defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );

 add_action('init', 'register_collection_cpt');
 
 function register_collection_cpt() {
    
    register_post_type('Item', [
         'label' => 'Collection',
         'public' => true,
         'capability_type' => 'post',
         'menu_icon' => 'dashicons-admin-customizer',
     ]);
 }

 add_action( 'init', 'get_item_from_api' );

 function get_item_from_api() {

    $collection = [];

    $url = 'https://www.boredapi.com/api/activity';
    $arguments = array (
        'method' => 'GET'
    );
    
    $response = wp_remote_get( $url, $arguments );

    $results = json_decode( wp_remote_retrieve_body($response) );


    
    if ( is_wp_error( $results ) ) {
        $error_message = $results->get_error_message();
        return "Something went wrong: $error_message";
    } 
        // var_dump($collection);

        store_inside_post($results);

}

function store_inside_post($results) {
    
    // Create post object
    $my_post = array(
        'post_type' => 'item',
        'post_title'=> 'Item: ' . sanitize_title($results->key),
        'post_content' =>  'Activity: ' . $results->activity . ' | ' . 'Type: ' . $results->type . ' | ' . 'Number of Participants: ' . $results->participants,
        'post_status' => 'publish',
        'post_author' => 1
    );
    
//     // Insert the post into the database
    $post_id = wp_insert_post( $my_post );
    
    if ($post_id) {
        update_post_meta($post_id, 'activity', sanitize_text_field($results->activity));
        update_post_meta($post_id, 'type', sanitize_text_field($results->type));
        update_post_meta($post_id, 'participants', sanitize_text_field($results->participants));
        update_post_meta($post_id, 'price', sanitize_text_field($results->price));
        update_post_meta($post_id, 'link', sanitize_text_field($results->link));
        update_post_meta($post_id, 'key', sanitize_text_field($results->key));
        update_post_meta($post_id, 'accessibility', sanitize_text_field($results->accessibility));
    }
}

// flush_rewrite_rules( false );

 /* Stop Adding Functions Below this Line */


?>