<?php
/**
 * Disk Usage add-on (default add-on)
 *
 * @param array $data
 *
 * @return array
 */
function pmhm_add_disk_usage_data( $data ) {
	$res = array(
		'id'    => 'disk_usage',
		'name'  => __( 'Disk Usage Rate (/)', 'pm-hw-monitor' ),
		'color' => '#4cAF50',
		'rate'  => '',
		'error' => '',
	);
	
	exec( 'df /', $output, $return_var );

	if ( ! ! $return_var ) {
		$res['error'] = __( "Failed to execute the 'df' command.", 'pm-hw-monitor' );
	} else {
		if ( count( $output ) != 2
		     || ! preg_match( '/^\S+\s+\S+\s+\S+\s+\S+\s+(?<cap>\d+)%\s+.*$/', $output[1], $m )
		) {
			$res['error'] = __( "The output of the 'df' command is an unexpected format.", 'pm-hw-monitor' );
		} else {
			$res['rate'] = $m['cap'];
		}
	}

	$data[] = $res;

	return $data;
}

add_filter( 'add_pmhm_data', 'pmhm_add_disk_usage_data', 2 );
