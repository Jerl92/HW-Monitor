<form method="post" action="?page=pm-hw-monitor%2Fplugin.php&amp;tab=setting">
    <table class="form-table">
        <tr>
            <th><?php _e( "Data collection interval:", 'pm-hw-monitor' ); ?></th>
            <td>
                <input type="number" name="interval" min="1" required value="<?php echo $this->view->interval; ?>">
                &nbsp;<?php _e( 'sec.', 'pm-hw-monitor' ); ?>
            </td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" class="button button-primary" name="submit"
               value="<? _e( 'Update', 'pm-hw-monitor' ); ?>"/>
    </p>
</form>