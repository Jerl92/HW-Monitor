<?php
/**
 * Plugin Name: HW Monitor
 * Description: Displays performance monitor, such as the Microsoft Windows Task Manager on WordPress.
 * Version: 1.0.0
 * Author: PRESSMAN
 * Author URI: https://www.pressman.ne.jp/
 * Text Domain: hw-monitor
 * Domain Path: /languages
 *
 * @author    PRESSMAN
 * @link      https://www.pressman.ne.jp/
 * @copyright Copyright (c) 2018, PRESSMAN
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 or higher
 */

/**
 * Class Hw_Monitor
 */
class Hw_Monitor {
	const VERSION = '1.0.0';

	/** @var stdClass */
	private $view;

	/**
	 * Hw_Monitor constructor.
	 */
	public function __construct() {
		require_once( plugin_dir_path( __FILE__ ) . 'addon/cpu.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'addon/memory.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'addon/filesystem.php' );

		$this->view = new stdClass();
	}

	/**
	 * Main function
	 */
	public function run() {
		register_activation_hook( __FILE__, array( $this, 'activate_plugin_options' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin_options' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );

		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_ajax_hwm', array( $this, 'admin_ajax' ) );
	}

	public function activate_plugin_options() {
		$ver = get_option( 'hw-monitor_version' );

		if ( ! $ver ) {
			add_option( 'hw-monitor_version', $this::VERSION );
		} elseif ( $ver !== $this::VERSION ) {
			update_option( 'hw-monitor_version', $this::VERSION );
		}

		$opts = unserialize( get_option( 'hw-monitor_options' ) );

		if ( ! $opts ) {
			add_option( 'hw-monitor_options', serialize( array( 'interval' => 2, ) ) );
		} else {
			$_ = function ( $k, $d ) use ( $opts ) {
				return ( isset( $opts[ $k ] ) ) ? $opts[ $k ] : $d;
				// PHP over 7.0
				//return $opts[$k] ?? $d;
			};

			update_option( 'hw-monitor_options', serialize( array( 'interval' => $_( 'interval', 2 ) ) ) );
		}
	}

	public function deactivate_plugin_options() {
		delete_option( 'hw-monitor_version' );
		delete_option( 'hw-monitor_options' );
	}

	public function add_action_links( $links ) {
		$a = '<a href="tools.php?page=hw-monitor/hw-monitor.php">' . __( 'Display', 'hw-monitor' ) . '</a>';
		array_unshift( $links, $a );

		return $links;
	}

	public function load_text_domain() {
		load_plugin_textdomain( 'hw-monitor', false, plugin_basename( plugin_dir_path( __FILE__ ) ) . '/languages' );
	}


	public function admin_menu() {
		require_once( ABSPATH . '/wp-admin/admin.php' );

		add_submenu_page( 'tools.php', 'HW Monitor', 'HW Monitor', 'edit_posts', __FILE__, array( $this, 'admin_page' ) );
	}

	public function admin_page() {
		$this->view->active_tab = filter_input( INPUT_GET, 'tab', FILTER_DEFAULT, array( 'options' => array( 'default' => 'monitor' ) ) );

		if ( $this->view->active_tab === 'setting' ) {
			if ( filter_input( INPUT_SERVER, 'REQUEST_METHOD' ) === 'POST' ) {
				$this->admin_page_post_setting();
			}
		} else {
			// load script
			wp_enqueue_script( 'd3js', plugin_dir_url( __FILE__ ) . 'admin/lib/d3/d3.min.js', array(), '5.7.0' );
			wp_enqueue_script( 'c3js', plugin_dir_url( __FILE__ ) . 'admin/lib/c3/c3.min.js', array(), '0.6.7' );
			wp_enqueue_script( 'hwmjs', plugin_dir_url( __FILE__ ) . 'admin/js/hwm.min.js', array(), '1.0.0' );
			// load style
			wp_enqueue_style( 'c3css', plugin_dir_url( __FILE__ ) . 'admin/lib/c3/c3.min.css', array(), '0.6.7' );
			wp_enqueue_style( 'hwmcss', plugin_dir_url( __FILE__ ) . 'admin/css/hwm.min.css', array(), '1.0.0' );
		}

		$opts = unserialize( get_option( 'hw-monitor_options' ) );

		$this->view->interval = $opts['interval'];

		include_once( plugin_dir_path( __FILE__ ) . 'admin/page-base.php' );
	}

	public function admin_page_post_setting() {
		$interval = filter_input( INPUT_POST, 'interval', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );

		if ( ! $interval ) {
			add_settings_error( 'interval', 'interval', __( 'Please enter an integer of 1 or more in the data collection interval.', 'hw-monitor' ) );

			return;
		}

		$opts = unserialize( get_option( 'hw-monitor_options' ) );

		$opts['interval'] = $interval;

		update_option( 'hw-monitor_options', serialize( $opts ) );
		add_settings_error( 'success', 'success', __( 'Has been updated.', 'hw-monitor' ), 'updated' );
	}

	public function admin_ajax() {
		$data = array();

		$data = apply_filters( 'add_hwm_data', $data );

		header( 'Content-Type: application/json' );
		echo json_encode( $data );

		die();
	}
}

$phm = new Hw_Monitor();
$phm->run();