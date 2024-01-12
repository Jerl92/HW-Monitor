<?php
/**
 * FileSystem Usage add-on (default add-on)
 */

function roundsize($size){
    $i=0;
    $iec = array("B", "Kb", "Mb", "Gb", "Tb");
    while (($size/1024)>1) {
        $size=$size/1024;
        $i++;
    }
    return(round($size,1)." ".$iec[$i]);
}

/**
 * Get disk usage
 *
 * @param array $data
 *
 * @return array
 */
function hwm_add_hdd_usage_data( $data ) {
	$res = array(
		'id'      => 'hdd_usage',
		'name'    => __( 'HDD', 'hw-monitor' ),
		'color'   => '#4cAF50',
		'summary' => '',
		'rate'    => '',
		'desc'    => array(),
		'error'   => array(),
		'max'     => 100,
	);

	$desc = array(
		__( 'Mounted on', 'hw-monitor' ) => '',
		__( 'In use', 'hw-monitor' )     => '',
		__( 'Available', 'hw-monitor' )  => '',
	);

    $res['summary'] = '1TB HDD';
    $res['rate']    = round((disk_total_space('/media/ST1000VM002-1ET1/') - disk_free_space('/media/ST1000VM002-1ET1/')) / disk_total_space('/media/ST1000VM002-1ET1/') * 100);


    $desc[ __( 'Mounted on', 'hw-monitor' ) ] = '/media/ST1000VM002-1ET1/';
    $desc[ __( 'In use', 'hw-monitor' ) ]     = sprintf( "%.1f GB", roundsize(disk_total_space('/media/ST1000VM002-1ET1/') - disk_free_space('/media/ST1000VM002-1ET1/') ), 1 );
    $desc[ __( 'Available', 'hw-monitor' ) ]  = sprintf( "%.1f GB", roundsize(disk_free_space('/media/ST1000VM002-1ET1/') ), 1 );

	$res['desc'] = $desc;
	$data[]      = $res;

	return $data;
}

add_filter( 'add_hwm_data', 'hwm_add_hdd_usage_data', 5 );