<?php
/**
 * Memory Usage add-onn (default add-on)
 *
 * @param array $data
 *
 * @return array
 */
function pmhm_add_mem_data( $data ) {
	$res = array(
		'id'    => 'mem_usage',
		'name'  => __( 'Memory Usage Rate', 'pm-hw-monitor' ),
		'color' => '#9C27B0',
		'rate'  => '',
		'error' => '',
	);

	exec( 'free', $output, $return_var );

	if ( ! ! $return_var ) {
		$res['error'] = __( "Failed to execute the 'free' command.", 'pm-hw-monitor' );
	} else {
		foreach ( $output as $row ) {
			if ( preg_match( '/^Mem:\s+(?<total>\d+)\s+(?<used>\d+)\s+.*$/', $row, $m ) ) {
				$res['rate'] = (int) ( $m['used'] / $m['total'] * 100 );
				break;
			}
		}

		if ( $res['rate'] === '' ) {
			$res['error'] = __( 'Failed to acquire Memory usage rate', 'pm-hw-monitor' );
		}
	}

	$data[] = $res;

	return $data;
}

add_filter( 'add_pmhm_data', 'pmhm_add_mem_data', 1 );