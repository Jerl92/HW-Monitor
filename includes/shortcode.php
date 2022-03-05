<?php 

function hw_shortcode() {

    // load script
    wp_enqueue_script( 'd3js', plugin_dir_url( __FILE__ ) . 'js/d3/d3.min.js', array(), '5.9.1' );
    wp_enqueue_script( 'c3js', plugin_dir_url( __FILE__ ) . 'js/c3/c3.min.js', array(), '0.6.12' );
    wp_enqueue_script( 'hwmjs', plugin_dir_url( __FILE__ ) . 'js/ajax.hardware.c3.js', array(), '0.6.12' );
    // load style
    wp_enqueue_style( 'c3css', plugin_dir_url( __FILE__ ) . 'js/c3/c3.min.css', array(), '0.6.12' );
    wp_enqueue_style( 'hwmcss', plugin_dir_url( __FILE__ ) . 'css/hwm.min.css', array(), '0.6.12' );

    ?>
    <?php $sec = 3; ?>
    <input type="hidden" id="interval" value="3">
    <input type="hidden" id="sec" value="<?php echo sprintf( __( '%% Utilization, %s seconds', 'hw-monitor' ), $sec ); ?>">
    <div id="hwm-area"></div>
    <?php
}

add_shortcode( 'hw', 'hw_shortcode' );

?>