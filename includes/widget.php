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
                            <span class="result"><?php echo shapeSpace_system_model(); ?></span>
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
                                <span id="hw-uptime"><?php echo secondsToTime(shapeSpace_server_uptime()); ?></span>
                            </span>
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

    $memory = shell_exec("cat /proc/meminfo | grep 'Active:'");
    $memory_val = explode(" ", $memory);
    $memory_val = array_filter($memory_val);
    $memory_val = array_merge($memory_val);
    $memory_usage[] = $memory_val[1];
    $memory_usage[] .= '  Kb';
    
    return implode($memory_usage);
}

function shapeSpace_system_model() {
    
    $lscpu_model = shell_exec("cat /proc/cpuinfo | grep 'model name'");
    $lscpu_model = (string)trim($lscpu_model);
    $cpumodel = explode(":", $lscpu_model);
    $cpumodel = array_filter($cpumodel);
    $cpumodel = array_merge($cpumodel);
    $cpumodel_ = str_replace("model name", "", $cpumodel[1]);

    $html[] = $cpumodel_;
    $html[] .= '</br>';

    return implode( $html );
}

function systemLoadInPercent($coreCount = 4,$interval = 1){
    $rs = sys_getloadavg();
    $load  = $rs[0];
    return round(($load * 100),2);
}

function shapeSpace_system_load() {
    
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

    return implode( $html );
}

function shapeSpace_kernel_version() {	
	$kernel = explode(',', file_get_contents('/proc/version'));
    $html[] = $kernel[8];
	return implode($kernel);
	
}

function shapeSpace_server_uptime() {	
	$uptime = floor(preg_replace ('/\.[0-9]+/', '', file_get_contents('/proc/uptime')));
	return $uptime;	
}

function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}

function shapeSpace_server_bandwidth() {	

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
    
    return implode($html);

}

?>