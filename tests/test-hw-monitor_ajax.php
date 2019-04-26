<?php

namespace Pressman;

class Hw_Monitor_Ajax_Test extends \WP_Ajax_UnitTestCase {
	protected static $test_data = [
		'foo' => 'FOO',
		'bar' => 'BAR'
	];

	public function setUp() {
		parent::setUp();
		add_filter( 'locale', function ( $locale ) {
			return 'ja';
		} );
		wp_set_current_user( self::factory()->user->create( [
			'role' => 'administrator',
		] ) );
	}

	public function test__admin_ajax() {
		$test_data = self::$test_data;
		add_filter( 'add_hwm_data', function ( $data ) use ( $test_data ) {
			return $test_data;
		} );

		try {
			$this->_handleAjax( 'hwm' );
		} catch ( \WPAjaxDieContinueException $e ) {
		}

		$this->assertEquals( json_encode( self::$test_data ), $this->_last_response );
	}
}