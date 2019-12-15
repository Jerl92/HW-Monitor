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
    wp_register_script( 'wp-hardware-ajax-uptime-scripts', $url . "js/ajax.hardware.uptime.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-uptime-scripts', 'get_uptime_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-uptime-scripts' );

    wp_register_script( 'wp-hardware-ajax-memory-scripts', $url . "js/ajax.hardware.memory.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-memory-scripts', 'get_memory_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-memory-scripts' );

    wp_register_script( 'wp-hardware-ajax-cpu-scripts', $url . "js/ajax.hardware.cpu.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-cpu-scripts', 'get_cpu_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-cpu-scripts' );

    wp_register_script( 'wp-hardware-ajax-bandwidth-scripts', $url . "js/ajax.hardware.bandwidth.js", array( 'jquery' ), '1.0.1', true );
    wp_localize_script( 'wp-hardware-ajax-bandwidth-scripts', 'get_bandwidth_ajax_url', admin_url( 'admin-ajax.php' ) );
    wp_enqueue_script( 'wp-hardware-ajax-bandwidth-scripts' );
    	
}

/* AJAX action callback */
add_action( 'wp_ajax_get_uptime', 'ajax_get_uptime' );
add_action( 'wp_ajax_nopriv_get_uptime', 'ajax_get_uptime' );
function ajax_get_uptime($post) {
    $posts  = array();

    $uptime = floor(preg_replace ('/\.[0-9]+/', '', file_get_contents('/proc/uptime')));

    $html = secondsToTime($uptime);

    return wp_send_json ( $html );
}

add_action( 'wp_ajax_get_memory', 'ajax_get_memory' );
add_action( 'wp_ajax_nopriv_get_memory', 'ajax_get_memory' );
function ajax_get_memory($post) {
    $posts  = array();

    $memory = shell_exec("cat /proc/meminfo | grep 'Active:'");
    $memory_val = explode(" ", $memory);
    $memory_val = array_filter($memory_val);
    $memory_val = array_merge($memory_val);
    $memory_usage[] = $memory_val[1];
    $memory_usage[] .= '  Kb';
    
    return wp_send_json ( $memory_usage );
}

add_action( 'wp_ajax_get_cpu', 'ajax_get_cpu' );
add_action( 'wp_ajax_nopriv_get_cpu', 'ajax_get_cpu' );
function ajax_get_cpu($post) {
    $posts  = array();

    $lscpu_mhz = shell_exec("cat /proc/cpuinfo | grep 'cpu MHz'");
    $lscpu_mhz = (string)trim($lscpu_mhz);
    $lscpu = explode(" ", $lscpu_mhz);
    $lscpu = array_filter($lscpu);
    $lscpu = array_merge($lscpu);
    $speed = str_replace("cpu", "", $lscpu[2]);

    $cpu_usage = shell_exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'");

    $temp = file_get_contents("/sys/class/thermal/thermal_zone0/temp");

    $html[] = $cpu_usage;
    $html[] .= '%';
    $html[] .= ' - ';
    $html[] .= $speed;
    $html[] .= ' Mhz';
    $html[] .= ' - ';
    $html[] .= ($temp / 1000);
    $html[] .= '°C';

    return wp_send_json ( implode( $html ) );
}

add_action( 'wp_ajax_get_bandwidth', 'ajax_get_bandwidth' );
add_action( 'wp_ajax_nopriv_get_bandwidth', 'ajax_get_bandwidth' );
function ajax_get_bandwidth($post) {
    $posts  = array();

    $int="enp0s25";
    session_start();
    
    $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = @file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    sleep(1);
    $rx[] = @file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = @file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    
    $tbps = $tx[1] - $tx[0];
    $rbps = $rx[1] - $rx[0];
    
    $round_rx=round($rbps/1024, 2);
    $round_tx=round($tbps/1024, 2);
    
    $time=date("U")."000";
    $_SESSION['rx'][] = "[$time, $round_rx]";
    $_SESSION['tx'][] = "[$time, $round_tx]";
    $data['label'] = $int;
    $data['data'] = $_SESSION['rx'];
    # to make sure that the graph shows only the
    # last minute (saves some bandwitch to)
    if (count($_SESSION['rx'])>60)
    {
        $x = min(array_keys($_SESSION['rx']));
        unset($_SESSION['rx'][$x]);
    }
    
    # json_encode didnt work, if you found a workarround pls write me
    # echo json_encode($data, JSON_FORCE_OBJECT);

    $html[] = 'Download: ';
    $html[] .= round($rbps/1024, 2);
    $html[] .= ' Kb/s';
    $html[] .= ' - ';
    $html[] .= 'Upload: ';
    $html[] .= round($tbps/1024, 2);
    $html[] .= ' Kb/s';
    
    return wp_send_json ( implode($html) );
}

?>