<?php
$hwm_url = menu_page_url( 'hw-monitor/hw-monitor.php', 0 );
?>
<div class="wrap">
    <h1>HW Monitor</h1>
	<?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'monitor' ), $hwm_url ) ); ?>"
           class="nav-tab <?php echo ( $this->view->active_tab === 'monitor' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Monitor', 'hw-monitor' ); ?>
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'setting' ), $hwm_url ) ); ?>"
           class="nav-tab <?php echo ( $this->view->active_tab === 'setting' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Setting', 'hw-monitor' ); ?>
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'addons' ), $hwm_url ) ); ?>"
           class="nav-tab <?php echo ( $this->view->active_tab === 'addons' ) ? 'nav-tab-active' : ''; ?>">
            <?php _e( 'Add-ons', 'hw-monitor' ); ?>
        </a>
    </h2>
    <div>
		<?php
		switch ( $this->view->active_tab ) :
			case 'setting':
				include_once( plugin_dir_path( __FILE__ ) . 'page-setting.php' );
				break;

			case 'monitor':
			default:
				include_once( plugin_dir_path( __FILE__ ) . 'page-monitor.php' );
				break;

			case 'addons':
				include_once( plugin_dir_path( __FILE__ ) . 'page-addons.php' );
		endswitch;
		?>
    </div>
</div>