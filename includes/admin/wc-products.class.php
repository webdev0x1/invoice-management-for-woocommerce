<?php

if (!class_exists('INV_EMPYE_wc_products')) {
    class INV_EMPYE_wc_products
    {

        /**
         * Slug used to identify WC_Product post type
         * @var string
         */
        protected $wc_product_type_slug = 'product';

        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct()
		{
            $this->init_hooks(); // Init hooks
            $this->init_filters(); // Init filters
		}

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

    	/**
    	 * Initiate plugin hooks in Wordpress
    	 */
        public function init_hooks()
        {
            add_action('manage_'. $this->wc_product_type_slug .'_posts_custom_column' , array($this, 'populate_columns'), 10, 2);

            // Bulk action
			add_action('admin_footer-edit.php', array($this, 'custom_bulk_admin_footer'));
            add_filter('bulk_actions-edit-product', array($this, 'register_bulk_actions'), 20, 1);
            add_filter('handle_bulk_actions-edit-product', array($this, 'handle_bulk_actions'), 10, 3 );


            // Notices
            add_action('admin_notices', array($this, 'bulk_admin_notices'));
        }

    	/**
    	 * Add new bulk actions
    	 */
        public function register_bulk_actions($bulk_actions)
        {
            $bulk_actions[INV_EMPYE_POST_TYPE . '_set_supplier'] = __('Set supplier', INV_EMPYE_TEXT_DOMAIN);
            return $bulk_actions;
        }

    	/**
    	 * Handle bulk actions
    	 */
        public function handle_bulk_actions($redirect_to, $doaction, $post_ids)
        {
            if ($doaction !== INV_EMPYE_POST_TYPE . '_set_supplier') {
                return $redirect_to;
            }

            // Get selected supplier ID
            $supplier_id = sanitize_text_field($_REQUEST[INV_EMPYE_POST_TYPE . '_bulk_supplier_id']);

            // Update each product supplier
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, 'inv_empye_supplier', $supplier_id);
            }

            $redirect_to = add_query_arg('bulk_supplied_posts', count($post_ids), $redirect_to);

            return $redirect_to;
        }


    	/**
    	 * Initiate plugin filters in Wordpress
    	 */
        public function init_filters()
        {
            add_filter('manage_'. $this->wc_product_type_slug .'_posts_columns', array($this, 'register_columns'));
        }

        /**
         * Create custom columns in Woocommerce products table
         */
        public function register_columns($columns)
        {
            $columns[INV_EMPYE_POST_TYPE . '_supplier']             = __("Supplier", INV_EMPYE_TEXT_DOMAIN);
            $columns[INV_EMPYE_POST_TYPE . '_supplier_price']       = __("Supplier price", INV_EMPYE_TEXT_DOMAIN);

            return $columns;
        }

        /**
         * Custom columns in Woocommerce products table
         */
        public function populate_columns($column, $post_id)
        {
            switch ($column) :
                case INV_EMPYE_POST_TYPE . '_supplier':
                    if ($supplier_id = get_post_meta($post_id , 'inv_empye_supplier' , true)) {
                        $supplier = new INV_EMPYE_Supplier($supplier_id);
                        ?>
                        <a href="<?php _e($supplier->getEditPermalink()); ?>"><?php _e($supplier->getName()); ?></a>
                        <?php
                    }
                    else {
                        _e("–", INV_EMPYE_TEXT_DOMAIN);
                    }
                    break;
                case INV_EMPYE_POST_TYPE . '_supplier_price':
                    if ($price = get_post_meta($post_id , 'inv_empye_supplier_price' , true)) {
                        _e($price);
                    } else {
                        _e("–", INV_EMPYE_TEXT_DOMAIN);
                    }
                    break;
            endswitch;
        }

		public function custom_bulk_admin_footer()
		{
		    global $post_type;

		    if ($this->wc_product_type_slug == $post_type) {
                $suppliers = INV_EMPYE_Supplier::findAll();

                if (count($suppliers)) :
                    ?>
                    <script type="text/javascript" charset="utf-8">
                        jQuery(document).ready(function($) {

                            // Add a line in the bulk actions select
                            // $('<option>').val('<?php _e(INV_EMPYE_POST_TYPE); ?>_set_supplier').text("<?php _e('Set supplier', INV_EMPYE_TEXT_DOMAIN); ?>").appendTo("select[name='action'], select[name='action2']");

                            // Show/hide suppliers select
                            $('select[name="action"], select[name="action2"]').on('change', function()
                            {
                                if ('<?php _e(INV_EMPYE_POST_TYPE); ?>_set_supplier' == $(this).val()) {
                                    $('<select name="<?php _e(INV_EMPYE_POST_TYPE); ?>_bulk_supplier_id">').addClass('<?php _e(INV_EMPYE_POST_TYPE); ?>_bulk_supplier_select').insertAfter(this);
                                    $('<option>').val('').text("<?php _e('No supplier', INV_EMPYE_TEXT_DOMAIN); ?>").appendTo('select.<?php _e(INV_EMPYE_POST_TYPE); ?>_bulk_supplier_select');
                                    <?php foreach ($suppliers as $supplier) : ?>
                                        $('<option>').val('<?php _e($supplier->getId()); ?>').text("<?php _e($supplier->getName()); ?>").appendTo('select.<?php _e(INV_EMPYE_POST_TYPE); ?>_bulk_supplier_select');
                                    <?php endforeach; ?>
                                }
                                else {
                                    $('select.<?php _e(INV_EMPYE_POST_TYPE); ?>_bulk_supplier_select').remove();
                                }
                            });
                        });
                    </script>
                    <?php
                endif;
		    }
		}

        /**
         * Show notices after bulk actions
         */
		function bulk_admin_notices()
		{
            if (!empty($_REQUEST['bulk_supplied_posts'])) {
                $supplied_count = intval($_REQUEST['bulk_supplied_posts']);

                $message = sprintf(_n('product.updated', 'products.updated', $supplied_count, INV_EMPYE_TEXT_DOMAIN), number_format_i18n($supplied_count));
                ?>
                <div class="updated"><p><?php _e($message); ?></p></div>
                <?php
            }
		}
	}
}

// Launch plugin
new INV_EMPYE_wc_products();
