<?php
/**
 * Plugin Name: Invoice Management for Woocommerce
 * Plugin URI:  -
 * Description: Invoice Management for Woocommerce allows you to create purchase order with the products assigned to supplier, 
 * Version:     0.1.0
 * Author:      Empye
 * Author URI:  https://www.empye.org/
 * Text Domain: inv_empye
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('ABSPATH') or die('Nope, not accessing this');

// Define INV_EMPYE_PLUGIN_FILE
if (!defined('INV_EMPYE_PLUGIN_FILE')) {
	define('INV_EMPYE_PLUGIN_FILE', __FILE__);
}

if (!class_exists('INV_EMPYE_Plugin')) {
    class INV_EMPYE_Plugin
    {
        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct()
		{
            if (is_admin()) {

                $this->define_constants(); // Define plugin constants

                // Go out if Woocommerce is not installed…
                if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    add_action('admin_notices', array($this, 'show_woocommerce_notice'));
                    return;
                }

                $this->includes(); // Include plugin files
        		$this->init_hooks(); // Init hooks in Wordpress

				$inv_notice = new INV_EMPYE_Notice();
				$inv_notice->init();
            }
		}

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

    	/**
    	 * Define plugin constants
    	 */
    	private function define_constants()
        {
            $this->define('INV_EMPYE_PLUGIN_NAME', 'Invoice Management for Woocommerce');
            $this->define('INV_EMPYE_PLUGIN_VERSION', '0.2.0');

    		$this->define('INV_EMPYE_ABSPATH', dirname(INV_EMPYE_PLUGIN_FILE) . '/');
    		$this->define('INV_EMPYE_PLUGIN_BASENAME', plugin_basename(INV_EMPYE_PLUGIN_FILE));
    		$this->define('INV_EMPYE_PLUGIN_URL', __FILE__);
    		$this->define('INV_EMPYE_POST_TYPE', 'inv_supplier');
            $this->define('INV_EMPYE_TEXT_DOMAIN', 'inv_empye');
    	}

    	/**
    	 * Include any classes we need within admin.
    	 */
        public function includes()
        {
    		include_once(INV_EMPYE_ABSPATH . 'includes/supplier-post-type.class.php');

    		include_once(INV_EMPYE_ABSPATH . 'includes/admin/menu.class.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/admin/settings.class.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/admin/editor.class.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/admin/wc-products.class.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/admin/wc-product.class.php');
            
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/data-stores/class-wc-data-store-wp.php');
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/interfaces/class-wc-abstract-order-data-store-interface.php'); 
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/interfaces/class-wc-object-data-store-interface.php'); 
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/data-stores/abstract-wc-order-data-store-cpt.php');
            
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/admin/meta-boxes/class-wc-meta-box-order-items.php');
            include_once($_SERVER['DOCUMENT_ROOT'] . '/wp-includes/class-wp-customize-manager.php');

            include_once(INV_EMPYE_ABSPATH  . 'includes/admin/class-wc-admin-meta-boxes.php');
            include_once(INV_EMPYE_ABSPATH  . 'includes/admin/meta-boxes/class-wc-meta-box-order-items.php');
            include_once(INV_EMPYE_ABSPATH  . 'includes/class-wc-ajax.php');
            
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/interfaces/class-wc-order-data-store-interface.php');
            include_once(WP_PLUGIN_DIR  . '/woocommerce/includes/data-stores/abstract-wc-order-data-store-cpt.php');
            include_once(INV_EMPYE_ABSPATH . 'includes/classes/class-wc-purchase-order-data-store-cpt.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/classes/supplier.class.php');
            include_once(INV_EMPYE_ABSPATH . 'includes/classes/po.class.php');
    		include_once(INV_EMPYE_ABSPATH . 'includes/classes/notice.class.php');
        }

    	/**
    	 * Initiate plugin hooks in Wordpress
    	 */
        public function init_hooks()
        {
            add_action('plugins_loaded', array($this, 'load_translation_files')); // Load translation files
            add_action('admin_enqueue_scripts', array($this, 'admin_style'));
        }

        /**
         * Load translation files located in the /languages folder
         */
        public function load_translation_files()
        {
            load_plugin_textdomain(
                INV_EMPYE_TEXT_DOMAIN,
                false,
                basename(dirname(__FILE__)) . '/languages'
            );
        }

        public function admin_style()
        {
            // Enqueue style files
            wp_enqueue_style(INV_EMPYE_POST_TYPE . '_datatables-style', __FILE__ . "/assets/css/jquery.dataTables.min.css");
            wp_enqueue_style(INV_EMPYE_POST_TYPE . '_admin-style', __FILE__ . "/assets/css/" . INV_EMPYE_POST_TYPE . "_style.css");

            // Enqueue script files
            wp_enqueue_script(INV_EMPYE_POST_TYPE . '_datatables-script', __FILE__ . "/assets/js/jquery.dataTables.min.js", array('jquery'));
            // wp_enqueue_script(INV_EMPYE_POST_TYPE . '_admin-script', __FILE__ . "/assets/js/" . INV_EMPYE_POST_TYPE . "_script.js", array(INV_EMPYE_POST_TYPE . '_datatables-script'));
            wp_enqueue_script(
				'wpo-wcpdf',
				__FILE__ . '/assets/js/order-script.js',
				array( 'jquery' ),
				'0.0.1'
			);
            wp_dequeue_script('wc-admin-order-meta-boxes');
            wp_enqueue_script( 'wc-admin-order-meta-boxes', __FILE__ . '/assets/js/admin/meta-boxes-order.js', array( 'wc-admin-meta-boxes', 'wc-backbone-modal', 'selectWoo', 'wc-clipboard' ), '0.1.0' );

			wp_localize_script(
				'wpo-wcpdf',
				'wpo_wcpdf_ajax',
				array(
					'ajaxurl'				=> admin_url( 'admin-ajax.php' ), // URL to WordPress ajax handling page  
					'nonce'					=> wp_create_nonce('generate_wpo_wcpdf'),
					'confirm_delete'		=> __( 'Are you sure you want to delete this document? This cannot be undone.', 'woocommerce-pdf-invoices-packing-slips'),
					'confirm_regenerate'	=> __( 'Are you sure you want to regenerate this document? This will make the document reflect the most current settings (such as footer text, document name, etc.) rather than using historical settings.', 'woocommerce-pdf-invoices-packing-slips'),
				)
			);
        }

        // +---------+
        // | NOTICES |
        // +---------+

        /**
         * Show notice when Woocommerce is not installed
         * @return string
         */
        public function show_woocommerce_notice()
        {
            ?>
                <div class="notice notice-warning">
                    <p><?php _e("Woocommerce is not installed. Please install and active it to use <b>Invoice Management</b> plugin.", INV_EMPYE_TEXT_DOMAIN); ?></p>
                </div>
            <?php
        }

        // +--------+
        // | OTHERS |
        // +--------+

    	/**
    	 * Define constant if not already set.
    	 *
    	 * @param string      $name  Constant name.
    	 * @param string|bool $value Constant value.
    	 */
    	private function define($name, $value)
        {
    		if (!defined($name)) {
    			define($name, $value);
    		}
    	}

	}
}

// Launch plugin
new INV_EMPYE_Plugin();
