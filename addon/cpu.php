<?php
/**
 * CPU Usage add-on (default add-on)
 */

/**
 * Get CPU usage
 *
 * @param array $data
 *
 * @return array
 */
function hwm_add_cpu_data( $data ) {
	session_start();
	$res = array(
		'id'      => 'cpu_usage',
		'name'    => __( 'CPU', 'hw-monitor' ),
		'color'   => '#2196F3',
		'summary' => '',
		'rate'    => '',
		'desc'    => array(),
		'error'   => array(),
	);

	$desc = array(
		__( 'Utilization', 'hw-monitor' )        => '',
		__( 'Maximum speed', 'hw-monitor' )      => '',
		__( 'Sockets', 'hw-monitor' )            => '',
		__( 'Cores', 'hw-monitor' )              => '',
		__( 'Logical processors', 'hw-monitor' ) => '',
		__( 'Visualization', 'hw-monitor' )      => '',
		__( 'Processes', 'hw-monitor' )          => '',
		__( 'Up time', 'hw-monitor' )            => '',
	);

	if ( ! is_readable( '/proc/stat' ) ) {
		$res['error'][] = __( "Can't access to '/proc/stat' .", 'hw-monitor' );
	} else {
		foreach ( explode( PHP_EOL, file_get_contents( '/proc/stat' ) ) as $row ) {
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
			$desc[ __( 'Utilization', 'hw-monitor' ) ] = $res['rate'] . ' %';

			break;
		}

		if ( $res['rate'] === '' ) {
			$res['error'][] = __( 'Failed to acquire CPU usage rate.', 'hw-monitor' );
		}
	}

	if ( ! is_readable( '/proc/cpuinfo' ) ) {
		$res['error'][] = __( "Can't access to '/proc/cpuinfo' .", 'hw-monitor' );
	} else {
		$cpuinfo = array();
		$i       = 0;

		foreach ( explode( PHP_EOL, file_get_contents( '/proc/cpuinfo' ) ) as $row ) {
			if ( preg_match( '/^$/', $row ) ) {
				$i ++;
				continue;
			}
			list( $k, $v ) = explode( ':', $row );
			$cpuinfo[ $i ][ trim( $k ) ] = trim( $v );
		}

		$res['summary'] = $cpuinfo[0]['model name'];

		$desc[ __( 'Maximum speed', 'hw-monitor' ) ]      = sprintf( "%.2f GHz", round( $cpuinfo[0]['cpu MHz'] / 1000, 2 ) );
		$desc[ __( 'Sockets', 'hw-monitor' ) ]            = count( array_unique( array_column( $cpuinfo, 'physical id' ) ) );
		$desc[ __( 'Logical processors', 'hw-monitor' ) ] = count( $cpuinfo );
		$desc[ __( 'Visualization', 'hw-monitor' ) ]      = ( ( strpos( $cpuinfo[0]['flags'], 'vmx' ) !== false ) || ( strpos( $cpuinfo[0]['flags'], 'smx' ) !== false ) )
			? __( 'Enabled', 'hw-monitor' ) : __( 'Disabled', 'hw-monitor' );

		$lc = array();

		foreach ( $cpuinfo as $cpu ) {
			$lc[ $cpu['physical id'] ] = $cpu['cpu cores'];
		}

		$desc[ __( 'Cores', 'hw-monitor' ) ] = array_sum( $lc );
	}

	if ( ! is_readable( '/proc/uptime' ) ) {
		$res['error'][] = __( "Can't access to '/proc/uptime' .", 'hw-monitor' );
	} else {
		$uptime = file_get_contents( '/proc/uptime' );

		if ( ! preg_match( '/^(?<uptime>\d+(\.\d+)?)\s+(?<idle>\d+(\.\d+)?)/', $uptime, $m ) ) {
			$res['error'][] = __( "'/proc/uptime' file is an expected format.", 'hw-monitor' );
		}

		$days    = (int) ( $m['uptime'] / ( 60 * 60 * 24 ) );
		$d_mod   = $m['uptime'] % ( 60 * 60 * 24 );
		$hours   = str_pad( (int) ( $d_mod / ( 60 * 60 ) ), 2, '0', STR_PAD_LEFT );
		$h_mod   = $d_mod % ( 60 * 60 );
		$minuts  = str_pad( (int) ( $h_mod / 60 ), 2, '0', STR_PAD_LEFT );
		$seconds = str_pad( (int) ( $h_mod % 60 ), 2, '0', STR_PAD_LEFT );

		$desc[ __( 'Up time', 'hw-monitor' ) ] = "{$days}:{$hours}:{$minuts}:{$seconds}";
	}

	exec( 'ps aux', $output, $return_var );

	if ( ! ! $return_var ) {
		$res['error'][] = __( "Failed to execute the 'ps' command.", 'hw-monitor' );
	} else {
		$desc[ __( 'Processes', 'hw-monitor' ) ] = count( $output ) - 2;
	}

	$res['desc'] = $desc;
	$data[]      = $res;

	return $data;
}

add_filter( 'add_hwm_data', 'hwm_add_cpu_data', 0 );