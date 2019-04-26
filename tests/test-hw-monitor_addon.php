<?php

namespace Pressman;

class Hw_Monitor_Addon_Test extends \WP_UnitTestCase {
	public function test__cpu() {
		$this->assertNotFalse( has_action( 'add_hwm_data', 'hwm_add_cpu_data' ) );

		$res = current( hwm_add_cpu_data( [] ) );
		$this->assertEquals( 'cpu_usage', $res['id'] );
		$this->assertEquals( 'CPU', $res['name'] );
		$this->assertEquals( '#2196F3', $res['color'] );
		$this->assertTrue( isset( $res['summary'] ) );
		$this->assertTrue( isset( $res['rate'] ) );
		$this->assertTrue( isset( $res['desc'] ) );
		$this->assertTrue( isset( $res['error'] ) );
	}

	public function test__memory() {
		$this->assertNotfalse( has_action( 'add_hwm_data', 'hwm_add_mem_data' ) );

		$res = current( hwm_add_mem_data( [] ) );
		$this->assertEquals( 'mem_usage', $res['id'] );
		$this->assertEquals( 'メモリ', $res['name'] );
		$this->assertEquals( '#9C27B0', $res['color'] );
		$this->assertTrue( isset( $res['summary'] ) );
		$this->assertTrue( isset( $res['rate'] ) );
		$this->assertTrue( isset( $res['desc'] ) );
		$this->assertTrue( isset( $res['error'] ) );
	}

	public function test__filesystem() {
		$this->assertNotFalse( has_action( 'add_hwm_data', 'hwm_add_filesystem_usage_data' ) );

		$res = current( hwm_add_filesystem_usage_data( [] ) );
		$this->assertEquals( 'filesystem_usage', $res['id'] );
		$this->assertEquals( 'ファイルシステム', $res['name'] );
		$this->assertEquals( '#4cAF50', $res['color'] );
		$this->assertTrue( isset( $res['summary'] ) );
		$this->assertTrue( isset( $res['rate'] ) );
		$this->assertTrue( isset( $res['desc'] ) );
		$this->assertTrue( isset( $res['error'] ) );
	}
}