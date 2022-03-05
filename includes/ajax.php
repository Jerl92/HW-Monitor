<?php

/* Enqueue Script */
add_action( 'wp_enqueue_scripts', 'wp_hardware_ajax_scripts' );

/**
 * Scripts
 */
function wp_hardware_ajax_scripts() {
	/* Plugin DIR URL */
	$url = trailingslashit( plugin_dir_url( __FILE__ ) );
    //

    wp_register_script( 'wp-hardware-ajax-hw-shortcode', $url . "js/ajax.hardware.shortcode.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-hw-shortcode', 'get_hw_shortcode_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-hw-shortcode' );

    wp_register_script( 'wp-hardware-ajax-hw-scripts', $url . "js/ajax.hardware.get.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-hw-scripts', 'get_hw_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-hw-scripts' );

    wp_register_script( 'wp-hardware-ajax-c3-scripts', $url . "js/ajax.hardware.c3.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-c3-scripts', 'get_c3_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-c3-scripts' );
    	
}

/* AJAX action callback */
add_action( 'wp_ajax_get_hw', 'ajax_get_hw' );
add_action( 'wp_ajax_nopriv_get_hw', 'ajax_get_hw' );
function ajax_get_hw() {

    $html[] = '<span class="description">CPU Usage: </span> <span class="result">';
    $html[] .= '<span id="hw-cpu">' . shapeSpace_system_load() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';

    $html[] .= '<span class="description">Memory Usage:</span> <span class="result">';
    $html[] .= '<span id="hw-memory">' . get_server_memory_usage() . '</span>';
    $html[] .= '</span>';
    $html[] .= ' - ';

    $html[] .= '<span class="description">Disk Usage:</span> <span class="result">';
    $html[] .= '<span id="hw-disk">' . shapeSpace_disk_usage() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';

    $html[] .= '<span class="description">Processes:</span> <span class="result">';
    $html[] .= '<span id="hw-processes">' . shapeSpace_number_processes() . '</span>';
    $html[] .= '</span>';
    $html[] .= ' - ';

    $html[] .= '<span class="description">HTTP Connections:</span> <span class="result">';
    $html[] .= '<span id="hw-connections">' . shapeSpace_http_connections() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';

    $html[] .= '<span class="description">Bandwidth:</span> <span class="result">';
    $html[] .= '<span id="hw-bandwidth">' . shapeSpace_server_bandwidth() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';

    $html[] .= '<span class="description">Uptime:</span> <span class="result">';
    $html[] .= '<span id="hw-uptime">' . Uptime() . '</span>';
    $html[] .= '</span>';

    return wp_send_json (  implode( $html ) );
}

/* AJAX action callback */
add_action( 'wp_ajax_get_hw_shortcode', 'ajax_get_hw_shortcode' );
add_action( 'wp_ajax_nopriv_get_hw_shortcode', 'ajax_get_hw_shortcode' );
function ajax_get_hw_shortcode() {

    $html[0] = '<li>' . shapeSpace_system_load() . '</li>';
    $html[1] = '<li>' . get_server_memory_usage() . '</li>';
    $html[2] = '<li>' . shapeSpace_server_bandwidth() . '</li>';

    return wp_send_json ( $html );
}

add_action( 'wp_ajax_get_c3', 'ajax_get_c3' );
add_action( 'wp_ajax_nopriv_get_c3', 'ajax_get_c3' );
function ajax_get_c3() {
    $data = array();

    $data = apply_filters( 'add_hwm_data', $data );

    return wp_send_json($data);
}

?>