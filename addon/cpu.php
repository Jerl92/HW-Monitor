<?php
/**
 * CPU Usage add-on (default add-on)
 *
 * @param array $data
 *
 * @return array
 */
function pmhm_add_cpu_data( $data ) {
	session_start();
	$res = array(
		'id'    => 'cpu_usage',
		'name'  => __( 'CPU Usage Rate', 'pm-hw-monitor' ),
		'color' => '#2196F3',
		'rate'  => '',
		'error' => '',
	);

	if ( ! is_readable( '/proc/stat' ) ) {
		$res['error'] = __( "Can't access to '/proc/stat' .", 'pm-hw-monitor' );
	} else {
		$stat = file_get_contents( '/proc/stat' );

		foreach ( explode( PHP_EOL, $stat ) as $row ) {
			if ( preg_match( '/^cpu\s+(?<user>\d+)\s+(?<nice>\d+)\s+(?<system>\d+)\s+(?<idle>\d+).*$/', $row, $m ) ) {
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

				$time        = $diff['user'] + $diff['nice'] + $diff['system'];
				$res['rate'] = (int) ( $time / ( $time + $diff['idle'] ) * 100 );

				break;
			}
		}

		if ( $res['rate'] === '' ) {
			$res['error'] = __( 'Failed to acquire CPU usage rate.', 'pm-hw-monitor' );
		}
	}

	$data[] = $res;

	return $data;
}

add_filter( 'add_pmhm_data', 'pmhm_add_cpu_data', 0 );