<?php

function bext_add_cors_http_header(){
    header("Access-Control-Allow-Origin: *");
}
add_action('init','bext_add_cors_http_header');

add_action( 'rest_api_init', function () {
  register_rest_route( 'bookingwp-ext/v1', '/meeting/retrieve', array(
    'methods' => 'GET',
    'callback' => 'bext_get_meeting_options_callback',
  ) );
  register_rest_route( 'bookingwp-ext/v1', '/meeting/authenticate', array(
    'methods' => 'POST',
    'callback' => 'bext_authenticate_meeting_callback',
  ) );
} );


function bext_authenticate_meeting_callback(){
  $username = $_POST['username'];
  $password = $_POST['password'];

  $user = wp_authenticate($username, $password);
  if(!is_wp_error($user)) {
    $first_name = $user->first_name;
    die(json_encode(['status' => 'success']));
  } else {
    die(json_encode(['status' => 'failed']));
  }
}

function bext_get_meeting_options_callback() {
  global $wpdb; // this is how you get access to the database

  $meeting_url = $_GET['meeting_url'];

  $row = $wpdb->get_row("
    SELECT `pm1`.`post_id`, 
    (
      SELECT `pm2`.`meta_value` 
      FROM `{$wpdb->prefix}postmeta` as `pm2` 
      WHERE `pm2`.`meta_key` ='_meeting_password' 
      AND `pm2`.`post_id` = `pm1`.`post_id`
    ) as password, 
    (
      SELECT `pm3`.`meta_value` 
      FROM `{$wpdb->prefix}postmeta` as `pm3` 
      WHERE `pm3`.`meta_key` ='_billing_first_name' 
      AND `pm3`.`post_id` = `pm1`.`post_id`
    ) as fname,
    (
      SELECT `pm4`.`meta_value` 
      FROM `{$wpdb->prefix}postmeta` as `pm4` 
      WHERE `pm4`.`meta_key` ='_billing_last_name' 
      AND `pm4`.`post_id` = `pm1`.`post_id`
    ) as lname
    FROM `{$wpdb->prefix}postmeta` as `pm1` 
    WHERE `pm1`.`meta_value` = '{$meeting_url}' 
  ");

  
  die(json_encode($row));
}
