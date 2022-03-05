<?php 

function hwm_add_upload_data($data) {	

    foreach($data as $data_) {
        $rateallupload[] .= $data_['desc']['Upload'];
    }
 
    $max = max($rateallupload);

    $int="enp3s0";
    
	$res = array(
		'id'      => 'upload_usage',
		'name'    => __( 'Upload', 'hw-monitor' ),
		'color'   => '#9C27B0',
		'summary' => '',
		'rate'    => '',
		'desc'    => array(),
		'error'   => array(),
        'max'     => $max,
	);

	$desc = array(
		__( 'Upload', 'hw-monitor' ) => ''
	);

    $tx[] = file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    sleep(1);
    $tx[] = file_get_contents("/sys/class/net/$int/statistics/tx_bytes");
    
    $tbps = $tx[1] - $tx[0];

    $res['summary'] = '';
    $res['rate'] = round($tbps/1024, 2);

    $desc[ __( 'Upload', 'hw-monitor' ) ] = round($tbps/1024, 2) . ' Kb/s';

    $res['desc'] = $desc;
    $data[]      = $res;

	return $data;

}

add_filter( 'add_hwm_data', 'hwm_add_upload_data', 4 );
?>