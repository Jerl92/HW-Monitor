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
    wp_register_script( 'wp-hardware-ajax-hw-scripts', $url . "js/ajax.hardware.get.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-hw-scripts', 'get_hw_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-hw-scripts' );
    	
}

/* AJAX action callback */
add_action( 'wp_ajax_get_hw', 'ajax_get_hw' );
add_action( 'wp_ajax_nopriv_get_hw', 'ajax_get_hw' );
function ajax_get_hw($post) {
    $posts  = array();

    $html[] = '<span class="result">' . shapeSpace_kernel_version() . '</span>';
    $html[] .= '</br>';
    $html[] = '<span class="result">' . shapeSpace_system_model() . '</span>';
    $html[] .= '</br>';
    $html[] .= '<span class="description">CPU Usage: </span> <span class="result">';
    $html[] .= '<span id="hw-cpu">' . shapeSpace_system_load() . '</span>';
    $html[] .= '</br>';
    $html[] .= '<span class="description">Memory Usage:</span> <span class="result">';
    $html[] .= '<span id="hw-memory">' . get_server_memory_usage() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';
    $html[] .= '<span class="description">Bandwidth:</span> <span class="result">';
    $html[] .= '<span id="hw-bandwidth">' . shapeSpace_server_bandwidth() . '</span>';
    $html[] .= '</span>';
    $html[] .= '</br>';
    $html[] .= '<span class="description">Uptime:</span> <span class="result">';
    $html[] .= '<span id="hw-uptime">' . secondsToTime(shapeSpace_server_uptime()) . '</span>';
    $html[] .= '</span>';

    return wp_send_json (  implode( $html ) );
}

?>