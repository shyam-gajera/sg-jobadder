<?php
/**
 * Plugin Name: Featured Job From JobAdder
 * Description: A plugin to fetch and manage featured jobs from JobAdder.
 * Version:     1.0.0
 * Author:      Shyam Gajera
 * Author URI:  https://shyamgajera.netlify.app/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jobadder-plugin
 */

defined( 'ABSPATH' ) || exit;

// Define plugin directory constant
define( 'JOBADDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Include the custom WP_List_Table class
 */
require_once JOBADDER_PLUGIN_DIR . 'includes/class-jobadder-list-table.php';

/**
 * Register the admin menu
 */
function jobadder_admin_menu() {
    add_menu_page(
        __( 'Job Adder', 'jobadder-plugin' ),
        __( 'Job Adder', 'jobadder-plugin' ),
        'manage_options',
        'job-adder',
        'job_adder_admin_page_contents',
        'dashicons-schedule',
        6
    );
}
add_action( 'admin_menu', 'jobadder_admin_menu' );

/**
 * Output the plugin's admin page content
 */
function job_adder_admin_page_contents() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'jobadder-plugin' ) );
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';

    echo '<form id="jobadder-list" method="get">';
    echo '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';

    $table = new Jobadder_List_Table();
    $table->prepare_items();
    $table->display();

    echo '</form>';
    echo '</div>';
}

/**
 * Register plugin scripts (future use)
 */
function register_my_plugin_scripts() {
    wp_register_style( 'my-plugin-style', plugins_url( 'assets/css/plugin.css', __FILE__ ) );
    wp_register_script( 'my-plugin-script', plugins_url( 'assets/js/plugin.js', __FILE__ ), [], false, true );
}
// Uncomment below if/when needed:
// add_action( 'admin_enqueue_scripts', 'register_my_plugin_scripts' );

/**
 * Conditionally load scripts on plugin admin page
 */
function load_my_plugin_scripts( $hook ) {
    if ( $hook !== 'toplevel_page_job-adder' ) {
        return;
    }
    wp_enqueue_style( 'my-plugin-style' );
    wp_enqueue_script( 'my-plugin-script' );
}
// Uncomment below if/when needed:
// add_action( 'admin_enqueue_scripts', 'load_my_plugin_scripts' );