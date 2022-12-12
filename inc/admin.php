<?php

/**
 * Hook it into Wordpress
 */
add_action('admin_menu', 'plugin_name_menu_pages'); 

/**
 * Place all the add_menu_page functions in here
 */
function plugin_name_menu_pages(){
  $admin_page_name = "Booking Ext Settings";
  add_menu_page( $admin_page_name, $admin_page_name, 'manage_options', 'booking-ext-settings', 'booking_ext_admin_callback' );

}

/**
 * Admin page function
 */
function booking_ext_admin_callback(){

  $message = NULL;

  $options = array();

  if ( !current_user_can( 'manage_options' ) )  {
  
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );  
  }

  if( isset( $_POST['Publish'] ) ){
    
    update_option( 'booking_ext_settings', $_POST );
  }
  
  $options = get_option( 'booking_ext_settings' );

  ob_start(); include dirname(__DIR__) . '/partial/admin.php'; $template = ob_get_clean();

  echo $template;
}