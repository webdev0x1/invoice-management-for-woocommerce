<?php

if (!class_exists('INV_EMPYE_Supplier_Post_Type')) {
    class INV_EMPYE_Supplier_Post_Type
    {
		// Contructor
		// ----------

		public function __construct()
		{
            $this->init_hooks(); // Init hooks in Wordpress
            $this->init_filters(); // Init filters
		}

        public function init_hooks()
        {
            add_action('init', array($this, 'register_post_type'), 5); // Register custom post type
            add_action('manage_'. INV_EMPYE_POST_TYPE .'_posts_custom_column' , array($this, 'populate_columns'), 10, 2);
            add_action('wp_trash_post', array($this, 'trash_post'));
        }

    	/**
    	 * Initiate plugin filters in Wordpress
    	 */
        public function init_filters()
        {
            add_filter('manage_'. INV_EMPYE_POST_TYPE .'_posts_columns', array($this, 'register_columns'));

            if (isset($_REQUEST['post_type']) && (INV_EMPYE_POST_TYPE == $_REQUEST['post_type']))
                add_filter('months_dropdown_results', '__return_empty_array');
        }

        /**
         * Register the custom post type "inv_supplier" in Wordpress Core
         */
        public function register_post_type()
        {
        /*    if ( post_type_exists( 'purchase_order' ) ) {
                return false;
              }
            
            wc_register_order_type(
                'purchase_order',
                apply_filters( 'woocommerce_register_post_type_purchase_order',
                    array(
                        // register_post_type() params
                        'labels'                           => array(
                            'name'               => __( 'Purchase Orders', 'inv_empye' ),
                            'singular_name'      => __( 'Purchase Order', 'inv_empye' ),
                            'add_new'            => _x( 'Add Purchase Order', 'custom post type setting', 'inv_empye' ),
                            'add_new_item'       => _x( 'Add New Purchase Order', 'custom post type setting', 'inv_empye' ),
                            'edit'               => _x( 'Edit', 'custom post type setting', 'inv_empye' ),
                            'edit_item'          => _x( 'Edit Purchase Order', 'custom post type setting', 'inv_empye' ),
                            'new_item'           => _x( 'New Item', 'custom post type setting', 'inv_empye' ),
                            'view'               => _x( 'View Purchase Order', 'custom post type setting', 'inv_empye' ),
                            'view_item'          => _x( 'View Purchase Order', 'custom post type setting', 'inv_empye' ),
                            'search_items'       => __( 'Search Purchase Order', 'inv_empye' ),
                            'not_found'          => self::get_not_found_text(),
                            'not_found_in_trash' => _x( 'No Purchase Order found in trash', 'custom post type setting', 'inv_empye' ),
                            'parent'             => _x( 'Parent Purchase Orders', 'custom post type setting', 'inv_empye' ),
                            'menu_name'          => __( 'Purchase Orders', 'inv_empye' ),
                        ),
                        'description'                      => __( 'This is where purchase orders are stored.', 'inv_empye' ),
                        'public'                           => false,
                        'show_ui'                          => true,
                        'capability_type'                  => 'shop_order',
                        'map_meta_cap'                     => true,
                        'publicly_queryable'               => false,
                        'exclude_from_search'              => true,
                        'show_in_menu'                     => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
                        'hierarchical'                     => false,
                        'show_in_nav_menus'                => false,
                        'rewrite'                          => false,
                        'query_var'                        => true,
                        'supports'                         => array( 'title', 'comments', 'custom-fields' ),
                        'has_archive'                      => true,
    
                        // wc_register_order_type() params
                        'exclude_from_orders_screen'       => true,
                        'add_order_meta_boxes'             => true,
                        'exclude_from_order_count'         => true,
                        'exclude_from_order_views'         => true,
                        'exclude_from_order_webhooks'      => true,
                        'exclude_from_order_reports'       => true,
                        'exclude_from_order_sales_reports' => true,
                        'class_name'                       => 'WC_Order',
                    )
                )
            );*/
            
            if (post_type_exists(INV_EMPYE_POST_TYPE)) {
    			return;
    		}

            //register post type
            return register_post_type(INV_EMPYE_POST_TYPE,
                array(
                    'labels' => array(
                        'name'               => __('All suppliers', INV_EMPYE_TEXT_DOMAIN),
                        'singular_name'      => __('Supplier', INV_EMPYE_TEXT_DOMAIN),
                        'menu_name'          => __('Suppliers', INV_EMPYE_TEXT_DOMAIN),
                        'add_new'            => __('New', INV_EMPYE_TEXT_DOMAIN),
                        'add_new_item'       => __('New supplier', INV_EMPYE_TEXT_DOMAIN),
                        'new_item'           => __('New supplier', INV_EMPYE_TEXT_DOMAIN),
                        'edit_item'          => __('Edit', INV_EMPYE_TEXT_DOMAIN),
                        'view_item'          => __('Show', INV_EMPYE_TEXT_DOMAIN),
                        'all_items'          => __('All suppliers', INV_EMPYE_TEXT_DOMAIN),
                        'search_items'       => __('Search supplier', INV_EMPYE_TEXT_DOMAIN),
                        'parent_item_colon'  => '',
                        'not_found'          => __('No supplier found…', INV_EMPYE_TEXT_DOMAIN),
                        'not_found_in_trash' => __('No supplier found in trash…', INV_EMPYE_TEXT_DOMAIN),
                    ),
                    'description'       => __('Description', INV_EMPYE_TEXT_DOMAIN),
                    // 'public'            => true,
                    // 'publicly_queryable'=> true,
                    'public'            => false,
                    'publicly_queryable'=> false,
                    'show_ui'           => true,
                    'show_in_menu'      => false,
                    'query_var'         => true,
                    'hierarchical'      => false,
                    'supports'          => array('title'/*, 'editor'*/),
                    'rewrite'           => array('slug' => 'supplier', 'with_front' => 'true'),
                    'capability_type'   => 'post',
                    'map_meta_cap'      => true,
                )
            );
        }

        public static function get_not_found_text() {
            return "Purchase order will appear here for you to view and manage.";
        }

        /**
         * Create custom columns in suppliers table
         */
        public function register_columns($columns)
        {
            unset($columns['date']);
            $columns[INV_EMPYE_POST_TYPE . '_nb_products'] = __('Number of products', INV_EMPYE_TEXT_DOMAIN);

            return $columns;
        }

        /**
         * Custom columns in suppliers table
         */
        public function populate_columns($column, $post_id)
        {
            $supplier = new INV_EMPYE_supplier($post_id);

            switch ($column) :
                case INV_EMPYE_POST_TYPE . '_nb_products' :
                    _e(count($supplier->getProducts()) ? count($supplier->getProducts()) : "-");
                    break;
            endswitch;
        }

        /**
         * When we move a supplier to the trash
         */
        public function trash_post($post_id)
        {
            // Delete products link
            $supplier = new INV_EMPYE_Supplier($post_id);
            $products = $supplier->getProducts();

            foreach ($products as $product) {
                delete_post_meta($product->get_id(), 'inv_empye_supplier');
                delete_post_meta($product->get_id(), 'inv_empye_supplier_price');
                delete_post_meta($product->get_id(), 'inv_empye_supplier_packaging');
            }
        }
	}
}

// Launch plugin
new INV_EMPYE_Supplier_Post_Type();
