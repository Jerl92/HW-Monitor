<?php
/**
 * hw Widget Class
 */
class hw_widget extends WP_Widget {

    /** constructor -- name this the same as the class above */
    function hw_widget() {
        parent::WP_Widget(false, $name = 'Hardware Info Widget');	
    }
 
    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {	
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                            <div id="hw-info">
                                <span class="description">WordPress </span>
                                <span class="result"><?php echo get_bloginfo( 'version' ); ?></span>
                                </br>
                                <span class="description">PHP </span>
                                <span class="result"><?php echo phpversion(); ?></span>
                                <!-- <span class="result"><?php echo shapeSpace_kernel_version(); ?></span> -->
                                </br>
                                <span class="result"><?php echo shapeSpace_system_model(); ?></span>
                                </br>
                                <span class="description">CPU Usage: </span> <span class="result">
                                <span id="hw-cpu"><?php echo shapeSpace_system_load(); ?></span>
                                </span>
                                </br>
                                <span class="description">Memory Usage:</span> <span class="result">
                                <span id="hw-memory"><?php echo get_server_memory_usage(); ?></span>
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
              <?php echo $after_widget; ?>
        <?php
    }
 
    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
 
    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {	
 
        $title 		= esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    } 
 
} // end class hw_widget
add_action('widgets_init', create_function('', 'return register_widget("hw_widget");'));

function get_server_memory_usage(){
    session_start();
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

	$res['desc'] = $desc;
    $data[]      = $res;
    
    $html[] = round( $m['used'] / ( 1024 * 1024 ), 2 );
    $html[] .= 'Gb';

    return implode( $html );
}

function shapeSpace_system_model() {
    session_start();
    $lscpu_model = shell_exec("cat /proc/cpuinfo | grep 'model name'");
    $lscpu_model = (string)trim($lscpu_model);
    $cpumodel = explode(":", $lscpu_model);
    $cpumodel = array_filter($cpumodel);
    $cpumodel = array_merge($cpumodel);
    $cpumodel_ = str_replace("model name", "", $cpumodel[1]);

    $html[] = $cpumodel_;

    return implode( $html );
}

function systemLoadInPercent($coreCount = 4,$interval = 1){
    session_start();
    $rs = sys_getloadavg();
    $load  = $rs[0];
    return round(($load * 100),2);
}

function shapeSpace_system_load() {
    session_start();
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
    $html[] .= $temp[8];
    $html[] .= '°C';

    return implode( $html );
}

function shapeSpace_system_rate() {
    session_start();
    $res = array(
        'id'      => 'cpu_usage',
		'rate'    => '',
		'error'   => array()
	);

	if ( ! is_readable( '/proc/stat' ) ) {
		$res['error'][] = array(
			'message' => __( "Can't access to '/proc/stat' .", 'hw-monitor' ),
			'detail'  => '',
		);
	} else {
        $stat = file_get_contents( '/proc/stat' );

        foreach ( explode( PHP_EOL, $stat ) as $row ) {
            if ( ! preg_match( '/^cpu\s+(?<user>\d+)\s+(?<nice>\d+)\s+(?<system>\d+)\s+(?<idle>\d+).*$/', $row, $m ) ) {
                continue;
            }

            $cur = array(
                'user'   => $m['user'],
                'nice'   => $m['nice'],
                'system' => $m['system'],
                'idle'   => $m['idle'],
            );

            
			if ( ! isset( $_SESSION['cpu_usage'] ) ) {
				$_SESSION['cpu_usage'] = $cur;
				$res['rate']           = 0;

				break;
			}

            $old                   = $_SESSION['cpu_usage'];
            $_SESSION['cpu_usage'] = $cur;

            $diff = array(
                'user'   => $cur['user'] - $old['user'],
                'nice'   => $cur['nice'] - $old['nice'],
                'system' => $cur['system'] - $old['system'],
                'idle'   => $cur['idle'] - $old['idle'],
            );
			$time                                      = $diff['user'] + $diff['nice'] + $diff['system'];
			$res['rate']                               = (int) ( $time / ( $time + $diff['idle'] ) * 100 );

			break;
        }
        return $res['rate'];
    }
}

function shapeSpace_kernel_version() {	
	$kernel = explode(',', file_get_contents('/proc/version'));
    $html[] = $kernel[8];
	return implode($kernel);
	
}

function Uptime() {
    $str   = file_get_contents('/proc/uptime');
    $num   = floatval($str);
    $secs  = $num % 60;
    $num   = (int)($num / 60);
    $mins  = $num % 60;
    $num   = (int)($num / 60);
    $hours = $num % 24;
    $num   = (int)($num / 24);
    $days  = $num;

    $html[] = $days;
    $html[] .= ' days, ';
    $html[] .= $hours;
    $html[] .= ' hours, ';
    $html[] .= $mins;
    $html[] .= ' minutes and ';
    $html[] .= $secs;
    $html[] .= ' seconds';

    return implode($html);
}

function shapeSpace_server_bandwidth() {	

    $int="enp0s25";
    
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