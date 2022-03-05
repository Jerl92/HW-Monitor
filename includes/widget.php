<?php
/**
 * hw Widget Class
 */
class hw_widget extends WP_Widget {

    /** constructor -- name this the same as the class above */
    function __construct() {
        parent::__construct( 'hw_widget', 'Hardware Info Widget' );
    }

    function hw_widget() {
        parent::WP_Widget(false, $name = 'Hardware Info Widget');	
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $url 	= $instance['url'];
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . '<a href="' . $url . '">' . $title . '</a>' . $after_title; ?>
                            <div id="hw-info-wraper">

                                <span class="description" style="font-weight: 600;">WordPress <?php echo get_bloginfo( 'version' ); ?> - PHP <?php echo phpversion(); ?> - <?php echo $_SERVER['SERVER_SOFTWARE'] ?></span>
                                </br>

                                <span class="result" style="font-weight: 300;"><?php echo system_model(); ?> - <?php echo system_distri(); ?></span>
                                </br>

                                <!-- <span class="result"><?php echo shapeSpace_kernel_version(); ?></span> -->

                                <span class="result"><?php echo shapeSpace_system_model(); ?></span>
                                </br>

                                <div id="hw-info">

                                <span class="description">CPU Usage: </span> <span class="result">
                                <span id="hw-cpu"><?php echo shapeSpace_system_load(); ?></span>
                                </span>
                                </br>

                                <span class="description">Memory Usage:</span> <span class="result">
                                <span id="hw-memory"><?php echo get_server_memory_usage(); ?></span>
                                </span>     
                                -
                                <span class="description">Disk Usage:</span> <span class="result">
                                    <span id="hw-disk"><?php echo shapeSpace_disk_usage(); ?></span>
                                    </span>
                                </br>

                                <span class="description">Processes:</span> <span class="result">
                                    <span id="hw-processes"><?php echo shapeSpace_number_processes(); ?></span>
                                </span>                              
                                -
                                <span class="description">HTTP Connections:</span> <span class="result">
                                    <span id="hw-connections"><?php echo shapeSpace_http_connections(); ?></span>
                                </span>
                                </br>

                                <span class="description">Bandwidth:</span> <span class="result">
                                    <span id="hw-bandwidth"><?php echo shapeSpace_server_bandwidth(); ?></span>
                                </span>
                                </br>

                                <span class="description">Uptime:</span> <span class="result">
                                    <span id="hw-uptime"><?php echo Uptime(); ?></span>
                                </span>

                                </div>

                            </div>
              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['url'] = strip_tags($new_instance['url']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
 
        $title 		= esc_attr($instance['title']);
        $url	= esc_attr($instance['url']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Hardware url'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
        </p>
        <?php 
    } 
 
} // end class hw_widget
add_action( 'widgets_init', function(){	register_widget( 'hw_widget' );});

function get_server_memory_usage(){
	$res = array(
		'id'      => 'mem_usage',
		'name'    => __( 'Memory', 'hw-monitor' ),
		'color'   => '#9C27B0',
		'summary' => '',
		'rate'    => '',
		'desc'    => array(),
		'error'   => array(),
	);

	$desc = array(
		__( 'In use', 'hw-monitor' )    => '',
		__( 'Available', 'hw-monitor' ) => '',
		__( 'Cached', 'hw-monitor' )    => '',
	);

	exec( 'free', $output, $return_var );

	if ( ! ! $return_var ) {
		$res['error'][] = array(
			'message' => __( "Failed to execute the 'free' command.", 'hw-monitor' ),
			'detail'  => '<pre>' . implode( PHP_EOL, $output ) . '</pre>',
		);
	} else {
		foreach ( $output as $row ) {
			if ( ! preg_match( '/^Mem:\s+(?<total>\d+)\s+(?<used>\d+)\s+(?<free>\d+)\s+(?<shared>\d+)\s+(?<bufferd>\d+)\s+(?<cached>\d+).*$/', $row, $m ) ) {
				continue;
			}

			$res['rate']    = (int) ( $m['used'] / $m['total'] * 100 );
			$res['summary'] = sprintf( "%.1f GB", round( $m['total'] / ( 1024 * 1024 ), 1 ) );

			$desc[ __( 'In use', 'hw-monitor' ) ]    = sprintf( "%.1f GB", round( $m['used'] / ( 1024 * 1024 ), 1 ) );
			$desc[ __( 'Available', 'hw-monitor' ) ] = sprintf( "%.1f GB", round( $m['free'] / ( 1024 * 1024 ), 1 ) );
			$desc[ __( 'Cached', 'hw-monitor' ) ]    = sprintf( "%.1f GB", round( $m['cached'] / ( 1024 * 1024 ), 1 ) );

			break;
		}

		if ( $res['rate'] === '' ) {
			$res['error'][] = array(
				'message' => __( 'Failed to acquire Memory usage rate', 'hw-monitor' ),
				'detail'  => '<pre>' . implode( PHP_EOL, $output ) . '</pre>',
			);
		}
	}
    
    $html[] = round( $m['used'] / ( 1024 * 1024 ), 2 );
    $html[] .= 'Gb';

    return implode( $html );
}

function shapeSpace_number_processes() {
	
	$proc_count = 0;
	$dh = opendir('/proc');
	
	while ($dir = readdir($dh)) {
		if (is_dir('/proc/' . $dir)) {
			if (preg_match('/^[0-9]+$/', $dir)) {
				$proc_count ++;
			}
		}
	}
	
	return $proc_count;
	
}

function system_model() {
    $lscpu_mhz = shell_exec("inxi -Fx | grep 'Machine'");
    $lscpu = explode(" ", $lscpu_mhz);

    $html[] = $lscpu[6];
    $html[] .= ' ';
    $html[] .= $lscpu[8];
    $html[] .= ' ';
    $html[] .= $lscpu[9];

    return implode( $html );
}

function system_distri() {
    $lscpu_mhz = shell_exec("inxi -Fx | grep 'Distro'");
    $lscpu = explode(" ", $lscpu_mhz);

    $html[] = $lscpu[14];
    $html[] .= ' ';
    $html[] .= $lscpu[15];

    return implode( $html );
}

function shapeSpace_system_model() {
    $lscpu_model = shell_exec("cat /proc/cpuinfo | grep 'model name'");
    $lscpu_model = (string)trim($lscpu_model);
    $cpumodel = explode(":", $lscpu_model);
    $cpumodel = array_filter($cpumodel);
    $cpumodel = array_merge($cpumodel);
    $cpumodel_ = str_replace("model name", "", $cpumodel[1]);

    $html[] = $cpumodel_;

    return implode( $html );
}

function shapeSpace_disk_usage() {
	
	$disktotal = disk_total_space ('/');
	$diskfree  = disk_free_space  ('/');
	$diskuse   = round (100 - (($diskfree / $disktotal) * 100)) .'%';
	
	return $diskuse;
	
}

function shapeSpace_system_load() {
    $lscpu_mhz = shell_exec("cat /proc/cpuinfo | grep 'cpu MHz'");
    $lscpu_mhz = (string)trim($lscpu_mhz);
    $lscpu = explode(" ", $lscpu_mhz);
    $lscpu = array_filter($lscpu);
    $lscpu = array_merge($lscpu);
    $speed = str_replace("cpu", "", $lscpu[2]);

    $temp_exec = shell_exec("sensors | grep 'Core 0:'");
    $temp = explode(" ", $temp_exec);

    $html[] = shapeSpace_system_rate(); 
    $html[] .= '%';
    $html[] .= ' - ';
    $html[] .= $speed;
    $html[] .= ' Mhz';
    $html[] .= ' - ';
    $html[] .= $temp[9];
    $html[] .= '°C';

    return implode( $html );
}

function shapeSpace_system_rate() {
    $stat1 = file('/proc/stat'); 
    sleep(1); 
    $stat2 = file('/proc/stat'); 
    $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0])); 
    $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0])); 
    $dif = array(); 
    $dif['user'] = $info2[0] - $info1[0]; 
    $dif['nice'] = $info2[1] - $info1[1]; 
    $dif['sys'] = $info2[2] - $info1[2]; 
    $dif['idle'] = $info2[3] - $info1[3]; 
    $total = array_sum($dif); 
    $cpu = array(); 
    foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
    return ($cpu['sys'] + $cpu['user']);
}

function shapeSpace_kernel_version() {	
	$kernel = explode(',', file_get_contents('/proc/version'));
    $html[] = $kernel[8];
	return implode($kernel);	
}

function Uptime() {
    $str   = file_get_contents('/proc/uptime');
    $num   = floatval($str);
    $secs  = fmod($num, 60); $num = intdiv($num, 60);
    $mins  = $num % 60;      $num = intdiv($num, 60);
    $hours = $num % 24;      $num = intdiv($num, 24);
    $days  = $num;

    /*
    $html[] = '999 ';
    $html[] .= ' days, ';
    $html[] .= '24 ';
    $html[] .= ' hours, ';
    $html[] .= '60 ';
    $html[] .= ' minutes and ';
    $html[] .= '60 ';
    $html[] .= ' seconds';
    */

    $html[] = $days;
    $html[] .= ' days, ';
    $html[] .= $hours;
    $html[] .= ' hours, ';
    $html[] .= $mins;
    $html[] .= ' minutes and ';
    $html[] .= round($secs, 0);
    $html[] .= ' seconds';

    return implode($html);
}

function shapeSpace_http_connections() {
	
	if (function_exists('exec')) {
		
		$www_total_count = 0;
		@exec ('netstat -an | egrep \':80|:443\' | awk \'{print $5}\' | grep -v \':::\*\' |  grep -v \'0.0.0.0\'', $results);
		
		foreach ($results as $result) {
			$array = explode(':', $result);
			$www_total_count ++;
			
			if (preg_match('/^::/', $result)) {
				$ipaddr = $array[3];
			} else {
				$ipaddr = $array[0];
			}
			
			if (!in_array($ipaddr, $unique)) {
				$unique[] = $ipaddr;
				$www_unique_count ++;
			}
		}
		
		unset ($results);
		
		return count($unique);
		
	}
	
}

function shapeSpace_server_bandwidth() {	

    $int="enp3s0";
    
    $rx[] = file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    sleep(1);
    $rx[] = file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    $tx[] = file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    
    $tbps = $tx[1] - $tx[0];
    $rbps = $rx[1] - $rx[0];

    $html[] = 'Download: ';
    $html[] .= round($rbps/1024, 2);
    $html[] .= ' Kb/s';
    $html[] .= ' - ';
    $html[] .= 'Upload: ';
    $html[] .= round($tbps/1024, 2);
    $html[] .= ' Kb/s';
    
    return implode($html);

}

?>