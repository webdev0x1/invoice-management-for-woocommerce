<?php

if (!class_exists('INV_EMPYE_settings')) {
    class INV_EMPYE_settings
    {
        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct() {
            add_action( 'admin_init', array($this, 'register_plugin_settings'));
        }

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

        /**
         * Register settings
         */
        public function register_plugin_settings()
        {
            register_setting(INV_EMPYE_POST_TYPE . '-settings-group', 'inv_empye_alert_stock_min');
        }

        public function settings_page_callback()
        {
            ?>
            <div class="wrap tf_empye tf_empye_settings">
                <h1><?php _e("Settings", INV_EMPYE_TEXT_DOMAIN); ?></h1>

                <form method="post" action="options.php">
                    <?php settings_fields(INV_EMPYE_POST_TYPE . '-settings-group'); ?>
                    <?php do_settings_sections(INV_EMPYE_POST_TYPE . '-settings-group'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e("Low stock alert (nb of items)", INV_EMPYE_TEXT_DOMAIN); ?></th>
                            <td>
                                <input type="text" name="inv_empye_alert_stock_min" value="<?php echo esc_attr( get_option('inv_empye_alert_stock_min')); ?>" />
                            </td>
                        </tr>
                    </table>

                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
	}
}

// Launch plugin
new INV_EMPYE_settings();
