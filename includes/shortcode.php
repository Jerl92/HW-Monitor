<?php 

add_action( 'wp_enqueue_scripts', 'process_post' );

function process_post() {
    // load script
    wp_enqueue_script( 'd3js', plugin_dir_url( __FILE__ ) . 'js/d3/d3.min.js', array(), '5.9.1' );
    wp_enqueue_script( 'c3js', plugin_dir_url( __FILE__ ) . 'js/c3/c3.min.js', array(), '0.6.12' );
    // load style
    wp_enqueue_style( 'c3css', plugin_dir_url( __FILE__ ) . 'js/c3/c3.min.css', array(), '0.6.12' );
    wp_enqueue_style( 'hwmcss', plugin_dir_url( __FILE__ ) . 'css/hwm.min.css', array(), '0.6.12' );
}


function hw_shortcode() {

    ?>
    <?php $sec = 5; ?>
    <input type="hidden" id="interval" value="5">
    <input type="hidden" id="sec" value="<?php echo sprintf( __( '%% Utilization, %s seconds', 'hw-monitor' ), $sec ); ?>">
    <div id="hwm-area">Loading...</div>
    <?php
}

add_shortcode( 'hw', 'hw_shortcode' );

?>