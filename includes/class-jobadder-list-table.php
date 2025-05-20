<?php
/**
 * Custom WP_List_Table for displaying JobAdder jobs
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Jobadder_List_Table extends WP_List_Table {

    /**
     * Text domain for localization
     * 
     * @var string
     */
    protected $plugin_text_domain = 'jobadder-plugin';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct( array(
            'singular' => 'job',
            'plural'   => 'jobs',
            'ajax'     => false,
        ) );
    }

    /**
     * Get data from JobAdder API (or helper function)
     *
     * @return array
     */
    public function get_jobadderdata() {
        $jobadderArray = array();

        if ( function_exists( 'get_jobadderads_data' ) ) {
            $jobadderData = get_jobadderads_data();

            if ( ! empty( $jobadderData['items'] ) ) {
                $featuredJobs = unserialize( get_option( 'featured_job_data' ) );

                foreach ( $jobadderData['items'] as $row ) {
                    $isFeatured = ( ! empty( $featuredJobs ) && in_array( $row['reference'], $featuredJobs ) ) ? 'Yes' : 'No';

                    $jobadderArray[] = array(
                        'jobId'         => $row['reference'],
                        'jobTitle'      => $row['title'],
                        'userFavourite' => $isFeatured,
                    );
                }
            }
        }

        return $jobadderArray;
    }

    /**
     * Fetch job ads data from JobAdder API using WordPress HTTP API.
     *
     * @return array
     */
    function get_jobadderads_data() {
        // Replace with your actual JobAdder API endpoint
        $api_url = 'https://api.jobadder.com/v2/jobs';

        // Replace with your actual API token
        $api_token = 'YOUR_API_TOKEN_HERE';

        $response = wp_remote_get( $api_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_token,
                'Accept'        => 'application/json',
            ],
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            error_log( 'JobAdder API error: ' . $response->get_error_message() );
            return [ 'items' => [] ];
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code !== 200 ) {
            error_log( 'JobAdder API returned HTTP code ' . $status_code );
            return [ 'items' => [] ];
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        // Validate data format
        if ( empty( $data['items'] ) || ! is_array( $data['items'] ) ) {
            error_log( 'JobAdder API response missing "items" or invalid format.' );
            return [ 'items' => [] ];
        }

        // Return job ads data as received
        return $data;
    }

    /**
     * Checkbox column for bulk actions
     */
    public function column_cb( $item ) {
        $featuredJobs = unserialize( get_option( 'featured_job_data' ) );
        $checked = in_array( $item['jobId'], $featuredJobs ) ? 'checked' : '';

        return sprintf(
            '<label class="screen-reader-text" for="user_%1$s">%2$s</label><input type="checkbox" name="users[]" id="user_%1$s" value="%1$s" %3$s/>',
            esc_attr( $item['jobId'] ),
            __( 'Select', $this->plugin_text_domain ),
            $checked
        );
    }

    /**
     * Define table columns
     */
    public function get_columns() {
        return array(
            'cb'            => '<input type="checkbox" />',
            'jobId'         => __( 'Job ID', $this->plugin_text_domain ),
            'jobTitle'      => __( 'Job Title', $this->plugin_text_domain ),
            'userFavourite' => __( 'Featured', $this->plugin_text_domain ),
        );
    }

    /**
     * Prepare table items
     */
    public function prepare_items() {
        $this->_column_headers = array(
            $this->get_columns(),
            array(), // hidden
            array()  // sortable
        );

        $this->process_bulk_action();
        $this->items = $this->get_jobadderdata();
    }

    /**
     * Handle bulk actions
     */
    public function get_bulk_actions() {
        return array(
            'feature' => __( 'Mark As Featured / Non-Featured', $this->plugin_text_domain ),
        );
    }

    /**
     * Process bulk feature action
     */
    public function process_bulk_action() {
        if (
            ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'feature' ) ||
            ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'feature' )
        ) {
            $selectedJobs = isset( $_REQUEST['users'] ) ? array_map( 'sanitize_text_field', $_REQUEST['users'] ) : array();

            if ( ! empty( $selectedJobs ) ) {
                update_option( 'featured_job_data', serialize( $selectedJobs ) );
                printf(
                    '<div class="notice notice-success is-dismissible"><p>%d jobs marked as featured.</p></div>',
                    count( $selectedJobs )
                );
            } else {
                update_option( 'featured_job_data', '' );
                echo '<div class="notice notice-success is-dismissible"><p>All jobs unmarked as featured.</p></div>';
            }
        }
    }

    /**
     * Default column display
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'jobId':
            case 'jobTitle':
            case 'userFavourite':
                return esc_html( $item[ $column_name ] );
            default:
                return print_r( $item, true ); // For debugging
        }
    }
}