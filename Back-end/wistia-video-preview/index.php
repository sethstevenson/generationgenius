<?php
/*
Plugin Name: Wistia Video Preview
Plugin URI: https://sethstevenson.net
Description: Gives a 1 minute preview of all Wistia videos on a page
Author: Seth Stevenson
Author URI: https://sethstevenson.net
Version: 1.0.0

Description: Limits all Wistia videos on a page to 1 minute of preview
then pops up a login form to allow the user to watch the full video.
*/


/*
 * Enqueue css styles 
 */
function wistia_enqueue_styles($hook) {
    wp_enqueue_style( 'wistia_video_preview_styles', plugin_dir_url( __FILE__ ) . 'css/wistia_video_preview_styles.css', false,   '1.0.0' );
  }
add_action('wp_enqueue_scripts', 'wistia_enqueue_styles');

/*
 * Enqueue scripts
 */
function ajax_login_init(){
    
    wp_enqueue_script('wistia_video_preview', plugin_dir_url( __FILE__ ) . 'js/wistia_video_preview.js', array('jquery') );

    wp_localize_script( 'wistia_video_preview', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Sending user info, please wait...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}
add_action('init', 'ajax_login_init');

/*
 * Inject HTML form at bottom of every page
 */
add_action('wp_footer', 'add_login_popup'); 
function add_login_popup() { 
    // Login form that will be in modal window
    echo
    '<form id="login" action="login" method="post">
    <h1>Login to continue</h1>
    <p class="status"></p>
    <label for="username">Username</label>
    <input id="username" type="text" name="username">
    <label for="password">Password</label>
    <input id="password" type="password" name="password">
    <input class="submit_button" type="submit" value="Login" name="submit">';
    wp_nonce_field( 'ajax-login-nonce', 'security' );
    echo '</form>';
}

/*
 * Ajax login function
 */
function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }

    die();
}