<div class="wrap">
    <h1>PM HW Monitor</h1>
	<?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=pm-hw-monitor%2Fplugin.php&amp;tab=monitor"
           class="nav-tab <?php echo ( $this->view->active_tab === 'monitor' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Monitor', 'pm-hw-monitor' ); ?>
        </a>
        <a href="?page=pm-hw-monitor%2Fplugin.php&amp;tab=setting"
           class="nav-tab <?php echo ( $this->view->active_tab === 'setting' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Setting', 'pm-hw-monitor' ); ?>
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
		endswitch;
		?>
    </div>
</div>