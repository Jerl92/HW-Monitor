<?php 

function hwm_add_download_data($data) {	

    foreach($data as $data_) {
       $ratealldownload[] .= $data_['desc']['Download'];
    }

    $max = max($ratealldownload);

    $int="enp3s0";
    
	$res = array(
		'id'      => 'download_usage',
		'name'    => __( 'Download', 'hw-monitor' ),
		'color'   => '#9C27B0',
		'summary' => '',
		'rate'    => '',
		'desc'    => array(),
		'error'   => array(),
        'max'     => $max,
	);

	$desc = array(
		__( 'Download', 'hw-monitor' )    => ''
	);

    $rx[] = file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    sleep(1);
    $rx[] = file_get_contents("/sys/class/net/$int/statistics/rx_bytes");
    
    $rbps = $rx[1] - $rx[0];

    $res['summary'] = '';
    $res['rate'] = round($rbps/1024, 2);

    $desc[ __( 'Download', 'hw-monitor' ) ]    = round($rbps/1024, 2) . ' Kb/s';

    $res['desc'] = $desc;
    $data[]      = $res;

	return $data;

}

add_filter( 'add_hwm_data', 'hwm_add_download_data', 3 );
?>