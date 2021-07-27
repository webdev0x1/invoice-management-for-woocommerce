<?php
use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;
require_once( ABSPATH . '/wp-content/plugins/woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php');

if (!class_exists('INV_EMPYE_editor')) {
    class INV_EMPYE_editor
    {
        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct()
		{
            $this->init_hooks(); // Init hooks in Wordpress
            $this->init_filters(); // Init filters in Wordpress
		}

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

    	/**
    	 * Initiate plugin hooks in Wordpress
    	 */
        public function init_hooks()
        {
            // add_action('admin_init', array($this, 'admin_init'));
            add_action('save_post_' . INV_EMPYE_POST_TYPE, array($this, 'save_post'), 10, 3);
            add_action('admin_menu', array($this, 'add_options_page'));
            add_action('add_meta_boxes', array($this, 'add_metaboxes'));
        }

    	/**
    	 * Initiate plugin filters in Wordpress
    	 */
        public function init_filters()
        {
            add_filter('enter_title_here', array($this, 'change_enter_title_here'));
            add_filter('gettext', array($this, 'change_publish_button'), 10, 2);
        }

        /**
         * Function used when the admin is initiated
         */
        function add_metaboxes()
        {
            global $post;

            // Supplier informations
            add_meta_box('inv_empye_post_informations',  __('Supplier informations', INV_EMPYE_TEXT_DOMAIN), array($this, 'post_informations_metabox'), INV_EMPYE_POST_TYPE, 'normal', 'low');

            if ("publish" == $post->post_status) {
                // Supplier linked products
                add_meta_box('inv_empye_wc_products_linked', __('Supplier products', INV_EMPYE_TEXT_DOMAIN), array($this, 'products_linked_metabox'), INV_EMPYE_POST_TYPE, 'side', 'low');

                // Add supplier menu
                add_action('edit_form_top', array($this, 'display_supplier_menu'));
            }

            add_meta_box(
                'wpo_wcpdf_send_emails_po',
                __( 'Send purchase order email', 'inv_empye' ),
                array( $this, 'send_order_email_meta_box_po' ),
                'purchase_order',
                'side',
                'high'
            );

            
            // create PDF buttons
            add_meta_box(
                'wpo_wcpdf-box_po',
                __( 'Create PDF', 'inv_empye' ),
                array( $this, 'pdf_actions_meta_box_po' ),
                'purchase_order',
                'side',
                'default'
            );

            // Invoice number & date
            add_meta_box(
                'wpo_wcpdf-data-input-box-po',
                __( 'PDF document data', 'inv_empye' ),
                array( $this, 'data_input_box_content_po' ),
                'purchase_order',
                'normal',
                'default'
            );
           //print_r($_REQUEST);die();
//echo wc_get_order_item_meta( 44, '_purpose' );die();
            // Add custom column headers here
            

            // Add custom column values here
            //add_action('woocommerce_admin_order_item_values', array( $this, 'my_woocommerce_admin_order_item_values', 10, 3));

           // add_meta_box( 'woocommerce_order_actions', 'Purchase Order action' , self::output($post) , 'purchase_order', 'side', 'high' );
          // add_action( 'woocommerce_admin_order_item_headers', array( $this, 'supplier_order_item_headers') );
          // add_action( 'woocommerce_admin_order_item_values', array( $this, 'supplier_order_item_values'), 10, 3 );
           //add_action('wc_add_order_item_meta', array( $this, 'add_order_item_meta', 10, 2));
           //add_action('wc_update_order_item_meta', array( $this, 'update_order_item_meta', 10, 2));
           //add_action('wc_checkout_create_order', array( $this, 'update_meta', 20, 2));
           //add_action('save_post_shop_order', array( $this, 'save_order_item_meta_value'), 10, 1);
           //add_action( 'woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 20, 4 );
           $wp_customize = new WP_Customize_Manager();
           $wp_customize->remove_setting( 'paper_size' );

           add_action( 'woocommerce_before_order_itemmeta_custom_purpose', array( $this, 'add_order_item_custom_field_purpose'), 10, 2 );
           add_action( 'woocommerce_before_order_itemmeta_custom_date_of_purchase', array( $this, 'add_order_item_custom_field_date_of_purchase'), 10, 2 );
           add_action( 'woocommerce_before_order_itemmeta_custom_supplier_name', array( $this, 'add_order_item_custom_field_supplier_name'), 10, 2 );
           add_action( 'woocommerce_before_order_itemmeta_custom_supplier_address', array( $this, 'add_order_item_custom_field_supplier_address'), 10, 2 );
           add_action( 'woocommerce_before_order_itemmeta_custom_supplier_price', array( $this, 'add_order_item_custom_field_supplier_price'), 10, 2 );
           add_action( 'woocommerce_process_shop_order_meta', 'INV_WC_Meta_Box_Order_Items::save', 10 );
           add_action( 'woocommerce_init', array( $this, 'wpse8170_woocommerce_init' ) );
           add_action('save_post_shop_order', array( $this,  'save_order_item_custom_field', 10000, 2 ));
           add_action('woocommerce_ajax_add_order_item_meta', array( $this,  'my_add_order_item_meta', 10000, 2 ));
           add_filter('woocommerce_order_item_display_meta_key', array( $this,  'filter_wc_order_item_display_meta_key', 20, 3 ));

           //add_filter('woocommerce_products_general_settings', array( $this,  'add_woocommerce_weight_units'));

           //print_r($_POST);die();
           //remove_meta_box('woocommerce-order-items', 'shop_order', 'normal');
           add_action( 'add_meta_boxes', array( $this, 'remove_shop_order_meta_boxe'), 90 );
        }

        public function add_woocommerce_weight_units ( $settings ) {
            foreach ( $settings as &$setting ) {

                if ( $setting['id'] == 'woocommerce_dimension_unit' ) {
            
                  $setting['options']['ft'] = __( 'ft' );  // foot
                  $setting['options']['mi'] = __( 'mi' );  // mile
            
                }
              }

              return $settings;
        }

        
        public function filter_wc_order_item_display_meta_key( $display_key, $meta, $item ) {
            // Change displayed label for specific order item meta key
            if( is_admin() && $item->get_type() === 'line_item' && $meta->key === '_purpose' ) {
                $display_key = __("Some label", "woocommerce" );
            }
            return $display_key;
        }

        public function my_add_order_item_meta( $item_id, $item, $order ){
            // Loop through order items
            foreach ( $order->get_items() as $item_id => $item ) {
                if( isset( $_POST['purpose_'.$item_id] ) ) {
                    $item->update_meta_data( '_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ) );
                    wc_update_order_item_meta('_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ));
                    wc_add_order_item_meta('_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ));
                    
                    $item->save();
                }

                if( isset( $_POST['date_of_purchase_'.$item_id] ) ) {
                    $item->update_meta_data( '_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ) );
                    wc_add_order_item_meta('_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ));
                    wc_update_order_item_meta('_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ));
                    $item->save();
                }

                $val = get_post_meta($item_id, 'inv_empye_supplier');
					$address =  get_post_meta($val, 'inv_empye_direct_name', true)."<br>";
					$address .=  get_post_meta($val, 'inv_empye_street', true)."<br>";
					$address .=  get_post_meta($val, 'inv_empye_city', true).", ";
					$address .=  get_post_meta($val, 'inv_empye_state', true).", ";
					$address .=  get_post_meta($val, 'inv_empye_country', true).", ";
					$address .=  get_post_meta($val, 'inv_empye_zipcode', true)."<br>";
					$address .=  get_post_meta($val, 'inv_empye_direct_email', true)."<br>";
					$address .=  get_post_meta($val, 'inv_empye_direct_phone', true)."<br>";
					
                if( null !==  get_the_title($val) ) {
					$item->update_meta_data( '_supplier_name', sanitize_text_field( get_the_title($val) ) );
                    wc_add_order_item_meta('_supplier_name', sanitize_text_field( get_the_title($val) ));
                    wc_update_order_item_meta('_supplier_name', sanitize_text_field( get_the_title($val) ));
                    $item->save();
                }

                if( isset( $address ) ) {
                    $item->update_meta_data( '_supplier_address', sanitize_text_field( $address ) );
                    wc_add_order_item_meta('_supplier_address', sanitize_text_field( $address ));
                    wc_update_order_item_meta('_supplier_address', sanitize_text_field( $address ));
                    $item->save();
                }
                $price = get_post_meta($item_id, 'inv_empye_supplier_price');
                if( isset( $price ) ) {
                    $item->update_meta_data( '_supplier_price', sanitize_text_field( $price ) );
                    wc_add_order_item_meta('_supplier_price', sanitize_text_field( $price ));
                    wc_update_order_item_meta('_supplier_price', sanitize_text_field( $price ));
                    $item->save();
                }
            }
            $order->save();
        }

        public function save_order_item_custom_field( $post_id, $post ){
            
            if ( 'shop_order' !== $post->post_type )
                return $post_id;
        
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return $post_id;
        
            if ( ! current_user_can( 'edit_shop_order', $post_id ) )
                return $post_id;
        
            $order = wc_get_order( $post_id );
            
            // Loop through order items
            foreach ( $order->get_items() as $item_id => $item ) {
                if( isset( $_POST['purpose_'.$item_id] ) ) {
                    $item->update_meta_data( '_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ) );
                    wc_update_order_item_meta('_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ));
                    wc_add_order_item_meta('_purpose', sanitize_text_field( $_POST['purpose_'.$item_id] ));
                    
                    $item->save();
                }

                if( isset( $_POST['date_of_purchase_'.$item_id] ) ) {
                    $item->update_meta_data( '_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ) );
                    wc_add_order_item_meta('_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ));
                    wc_update_order_item_meta('_date_of_purchase', sanitize_text_field( $_POST['date_of_purchase_'.$item_id] ));
                    $item->save();
                }

                $val = get_post_meta($item_id, 'inv_empye_supplier', true);
				$address =  get_post_meta($val, 'inv_empye_direct_name', true)."<br>";
				$address .=  get_post_meta($val, 'inv_empye_street', true)."<br>";
				$address .=  get_post_meta($val, 'inv_empye_city', true).", ";
				$address .=  get_post_meta($val, 'inv_empye_state', true).", ";
				$address .=  get_post_meta($val, 'inv_empye_country', true).", ";
				$address .=  get_post_meta($val, 'inv_empye_zipcode', true)."<br>";
				$address .=  get_post_meta($val, 'inv_empye_direct_email', true)."<br>";
				$address .=  get_post_meta($val, 'inv_empye_direct_phone', true)."<br>";

                if( null !==  get_the_title($val) ) {
                    $item->update_meta_data( '_supplier_name', sanitize_text_field( get_the_title($val) ) );
                    wc_add_order_item_meta('_supplier_name', sanitize_text_field( get_the_title($val) ));
                    wc_update_order_item_meta('_supplier_name', sanitize_text_field( get_the_title($val) ));
                    $item->save();
                }

                if( isset( $address ) ) {
                    $item->update_meta_data( '_supplier_address', sanitize_text_field( $address ) );
                    wc_add_order_item_meta('_supplier_address', sanitize_text_field( $address ));
                    wc_update_order_item_meta('_supplier_address', sanitize_text_field( $address ));
                    $item->save();
                }
            }
            $order->save();
        }

        public function remove_shop_order_meta_boxe() {
            remove_meta_box('woocommerce-order-items', 'shop_order', 'normal');
            add_meta_box( 'woocommerce-order-items', __( 'Items', 'inv_empye' ), 'INV_WC_Meta_Box_Order_Items::output', "shop_order", 'normal', 'high' );
        }

        public function add_order_item_custom_field_purpose( $item_id, $item ) {
            // Targeting line items type only
            if( $item->get_type() !== 'line_item' ) return;
        
            woocommerce_wp_text_input( array(
                'id'            => 'purpose_'.$item_id,
                'label'         => __( '', 'inv_empye' ),
                'description'   => __( '', 'inv_empye' ),
                'desc_tip'      => false,
                'custom_attributes' => array('readonly' => 'readonly'),
                'class'         => 'woocommerce',
                'value'         => wc_get_order_item_meta( $item_id, '_purpose' ),
            ) );
        }

        public function add_order_item_custom_field_date_of_purchase( $item_id, $item ) {
            // Targeting line items type only
            if( $item->get_type() !== 'line_item' ) return;
        
            woocommerce_wp_text_input( array(
                'id'            => 'date_of_purchase_'.$item_id,
                'label'         => __( '', 'inv_empye' ),
                'description'   => __( '', 'inv_empye' ),
                'desc_tip'      => false,
                'custom_attributes' => array('readonly' => 'readonly'),
                'class'         => 'woocommerce date',
                'value'         => wc_get_order_item_meta( $item_id, '_date_of_purchase' ),
            ) );
        }

        public function add_order_item_custom_field_supplier_name( $item_id, $item ) {
            // Targeting line items type only
            if( $item->get_type() !== 'line_item' ) return;
        
            woocommerce_wp_hidden_input( array(
                'id'            => 'supplier_name_'.$item_id,
                'label'         => __( '', 'inv_empye' ),
                'description'   => __( '', 'inv_empye' ),
                'desc_tip'      => false,
                'custom_attributes' => array('readonly' => 'readonly'),
                'class'         => 'woocommerce',
                'value'         => wc_get_order_item_meta( $item_id, '_supplier_name' ),
            ) );
        }

        public function add_order_item_custom_field_supplier_address( $item_id, $item ) {
            // Targeting line items type only
            if( $item->get_type() !== 'line_item' ) return;
        
            woocommerce_wp_text_input( array(
                'id'            => 'supplier_address_'.$item_id,
                'label'         => __( '', 'inv_empye' ),
                'description'   => __( '', 'inv_empye' ),
                'desc_tip'      => false,
                'custom_attributes' => array('readonly' => 'readonly'),
                'class'         => 'woocommerce disabled no-border',
                'value'         => wc_get_order_item_meta( $item_id, '_supplier_address' ),
            ) );
        }

        public function add_order_item_custom_field_supplier_price( $item_id, $item ) {
            // Targeting line items type only
            if( $item->get_type() !== 'line_item' ) return;
        
            woocommerce_wp_hidden_input( array(
                'id'            => 'supplier_price_'.$item_id,
                'label'         => __( '', 'inv_empye' ),
                'description'   => __( '', 'inv_empye' ),
                'desc_tip'      => false,
                'custom_attributes' => array('readonly' => 'readonly'),
                'class'         => 'woocommerce',
                'value'         => wc_get_order_item_meta( $item_id, '_supplier_price' ),
            ) );
        }
        
        public function save_order_item_meta_value( $post_id ){
            // Orders in backend only
            if( ! is_admin() ) return;

            // Get an instance of the WC_Order object (in a plugin)
            $order = new WC_Order( $post_id ); 

            // For testing purpose
            $trigger_status = get_post_meta( $post_id, 'purpose', true );

            // 1. Fired the first time you hit create a new order (before saving it)
            if( ! $update )
                update_post_meta( $post_id, 'purpose', 'Create new order' ); // Testing

            if( $update ){
                // 2. Fired when saving a new order
                if( 'Create new order' == $trigger_status ){
                    update_post_meta( $post_id, 'purpose', 'Save the new order' ); // Testing
                }
                // 3. Fired when Updating an order
                else{
                    update_post_meta( $post_id, 'purpose', 'Update  order' ); // Testing
                }
            }
        }

        public function update_meta( $order, $data ) {
            $order->update_meta_data( 'purpose', 'erer' );
            $order->update_meta_data( 'date_of_purchase', 'testg' );
        }
        public function wpse8170_woocommerce_init() {
            global $woocommerce;
        
            if ( !is_admin() || defined( 'DOING_AJAX' ) ) {
               //$mb = new My_WC_Meta_Box_Order_Items();
               //$woocommerce->add_meta_boxes = new My_WC_Admin_Meta_Boxes();
               //$woocommerce->add_order_item(false);
            }
        }        
        
        public function supplier_order_item_headers(){
            echo '<th class="" colspan="2" data-sort="string-ins">' . __( 'Supplier Name', 'woocommerce' ) .'</th>';
            echo '<th class="" colspan="2" data-sort="string-ins">' . __( 'Supplier Address', 'woocommerce' ) .'</th>';
            echo '<th class="" colspan="2" data-sort="string-ins">' . __( 'Usage (Gallons)', 'woocommerce' ) .'</th>';
            echo '<th class="" colspan="2" data-sort="string-ins">' . __( 'Purpose', 'woocommerce' ) .'</th>';
            echo '<th class="" colspan="2" data-sort="string-ins">' . __( 'Date of purchase', 'woocommerce' ) .'</th>';
        }
        
        public function supplier_order_item_values( $_product, $item, $item_id ){
            $val = get_post_meta($_product->get_id(), 'inv_empye_supplier', true);
            $address =  get_post_meta($val, 'inv_empye_direct_name', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_street', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_city', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_state', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_country', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_zipcode', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_direct_email', true)."<br>";
            $address .=  get_post_meta($val, 'inv_empye_direct_phone', true)."<br>";
            
            echo '<td colspan="1">' . get_the_title($val) . '<td>';
            echo '<td colspan="1">' . $address . '<td>';
            echo '<td colspan="1">' . $_product->get_weight() . '<td>';
            echo '<td colspan="2"><input type="text" class="" name="purpose" maxlength="10" value="'.wc_get_order_item_meta($item_id, 'purpose', true).'" /></td>';
            echo '<td colspan="2"><input type="text" class="date-picker" name="date_of_purchase" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" id="dp1621912214881" value="'.wc_get_order_item_meta($item_id, 'date_of_purchase', true).'"/></td>';
         }

        public function add_order_item_meta($item_id, $values) {
            $key = 'purpose';
            $value = wc_get_order_item_meta($item_id, 'purpose', true);
            $key2 = 'date_of_purchase';
            $value2 = wc_get_order_item_meta($item_id, 'date_of_purchase', true);
            
            wc_add_order_item_meta($item_id, $key, $value);
            wc_update_order_item_meta($item_id, $key, $value);
            wc_add_order_item_meta($item_id, $key2, $value2);
            wc_update_order_item_meta($item_id, $key2, $value2);
        }

        public function update_order_item_meta($item_id, $values) {
            $key = 'purpose';
            $value = wc_get_order_item_meta($item_id, 'purpose', true);
            $key2 = 'date_of_purchase';
            $value2 = wc_get_order_item_meta($item_id, 'date_of_purchase', true);
            
            wc_update_order_item_meta($item_id, $key, $value);
            wc_update_order_item_meta($item_id, $key2, $value2);
        }

        /**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function output( $post ) {
		global $theorder;

		// This is used by some callbacks attached to hooks such as woocommerce_order_actions which rely on the global to determine if actions should be displayed for certain orders.
		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}
		?>
		<ul class="order_actions submitbox">

			<?php do_action( 'woocommerce_order_actions_start_po', $post->ID ); ?>

			<li class="wide">
				<div id="delete-action">
					<?php
					if ( current_user_can( 'delete_post', $post->ID ) ) {

						if ( ! EMPTY_TRASH_DAYS ) {
							$delete_text = __( 'Delete permanently', 'woocommerce' );
						} else {
							$delete_text = __( 'Move to Trash', 'woocommerce' );
						}
						?>
						<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_html( $delete_text ); ?></a>
						<?php
					}
					?>
				</div>

				<button type="submit" class="button save_order button-primary" name="save" value="<?php echo 'auto-draft' === $post->post_status ? esc_attr__( 'Create', 'woocommerce' ) : esc_attr__( 'Update', 'woocommerce' ); ?>"><?php echo 'auto-draft' === $post->post_status ? esc_html__( 'Create', 'woocommerce' ) : esc_html__( 'Update', 'woocommerce' ); ?></button>
			</li>

			<?php do_action( 'woocommerce_order_actions_end_po', $post->ID ); ?>

		</ul>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post Object.
	 */
	public static function save( $post_id, $post ) {
		// Order data saved, now get it so we can manipulate status.
		$order = wc_get_order( $post_id );

		// Handle button actions.
		if ( ! empty( $_POST['wc_order_action'] ) ) { // @codingStandardsIgnoreLine

			$action = wc_clean( wp_unslash( $_POST['wc_order_action'] ) ); // @codingStandardsIgnoreLine

			if ( 'send_order_details' === $action ) {
				do_action( 'woocommerce_before_resend_order_emails_po', $order, 'customer_invoice' );

				// Send the customer invoice email.
				WC()->payment_gateways();
				WC()->shipping();
				WC()->mailer()->customer_invoice( $order );

				// Note the event.
				$order->add_order_note( __( 'Order details manually sent to customer.', 'woocommerce' ), false, true );

				do_action( 'woocommerce_after_resend_order_email_po', $order, 'customer_invoice' );

				// Change the post saved message.
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );

			} elseif ( 'send_order_details_admin' === $action ) {

				do_action( 'woocommerce_before_resend_order_emails_po', $order, 'new_order' );

				WC()->payment_gateways();
				WC()->shipping();
				add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );
				WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order->get_id(), $order, true );
				remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );

				do_action( 'woocommerce_after_resend_order_email_po', $order, 'new_order' );

				// Change the post saved message.
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );

			} elseif ( 'regenerate_download_permissions' === $action ) {

				$data_store = WC_Data_Store::load( 'customer-download' );
				$data_store->delete_by_order_id( $post_id );
				wc_downloadable_product_permissions( $post_id, true );

			} else {

				if ( ! did_action( 'woocommerce_order_action_' . sanitize_title( $action ) ) ) {
					do_action( 'woocommerce_order_action_' . sanitize_title( $action ), $order );
				}
			}
		}
	}

	/**
	 * Set the correct message ID.
	 *
	 * @param string $location Location.
	 * @since  2.3.0
	 * @static
	 * @return string
	 */
	public static function set_email_sent_message( $location ) {
		return add_query_arg( 'message', 11, $location );
	}

        public function data_input_box_content_po( $post ) {
            $order = WCX::get_order( $post->ID );
            $this->disable_storing_document_settings();
            $invoice = wcpdf_get_document( 'invoice', $order );
    
            do_action( 'wpo_wcpdf_meta_box_start_po', $order, $this );
    
            if ( $invoice ) {
                // data
                $data = array(
                    'number' => array(
                        'label'  => __( 'Invoice Number:', 'inv_empye' ),
                    ),
                    'date'   => array(
                        'label'  => __( 'Invoice Date:', 'inv_empye' ),
                    ),
                    'notes'  => array(
                        'label'  => __( 'Notes (printed in the invoice):', 'inv_empye' ),
                    ),
                );
                // output
                $this->output_number_date_edit_fields_po( $invoice, $data );
    
            }
    
            do_action( 'wpo_wcpdf_meta_box_end_po', $order, $this );
        }

        public function get_current_values_for_document_po( $document, $data ) {
            $current = array(
                'number' => array(
                    'plain'     => $document->exists() && ! empty( $document->get_number() ) ? $document->get_number()->get_plain() : '',
                    'formatted' => $document->exists() && ! empty( $document->get_number() ) ? $document->get_number()->get_formatted() : '',
                    'name'      => "_wcpdf_{$document->slug}_number",
                ),
                'date' => array(
                    'formatted' => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( wc_date_format().' @ '.wc_time_format() ) : '',
                    'date'      => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
                    'hour'      => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'H' ) : date_i18n( 'H' ),
                    'minute'    => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'i' ) : date_i18n( 'i' ),
                    'name'      => "_wcpdf_{$document->slug}_date",
                ),
            );
    
            if ( !empty( $data['notes'] ) ) {
                $current['notes'] = array(
                    'value' => $document->get_document_notes(),
                    'name'  =>"_wcpdf_{$document->slug}_notes",
                );
            }
    
            foreach ( $data as $key => $value ) {
                if ( isset( $current[$key] ) ) {
                    $data[$key] = array_merge( $current[$key], $value );
                }
            }
    
            return apply_filters( 'wpo_wcpdf_current_values_for_document_po', $data, $document );
        }

        public function output_number_date_edit_fields_po( $document, $data ) {
            if( empty( $document ) || empty( $data ) ) return;
            $data = $this->get_current_values_for_document_po( $document, $data );
            ?>
            <div class="wcpdf-data-fields" data-document="<?php echo $document->get_type(); ?>" data-order_id="<?php echo WCX_Order::get_id( $document->order ); ?>">
                <section class="wcpdf-data-fields-section number-date">
                    <!-- Title -->
                    <h4>
                        <?php echo $document->get_title(); ?>
                        <?php if( $document->exists() && ( isset( $data['number'] ) || isset( $data['date'] ) ) ) : ?>
                            <span class="wpo-wcpdf-edit-date-number dashicons dashicons-edit"></span>
                            <span class="wpo-wcpdf-delete-document dashicons dashicons-trash" data-nonce="<?php echo wp_create_nonce( "wpo_wcpdf_delete_document" ); ?>"></span>
                            <?php do_action( 'wpo_wcpdf_document_actions_po', $document ); ?>
                        <?php endif; ?>
                    </h4>
    
                    <!-- Read only -->
                    <div class="read-only">
                        <?php if( $document->exists() ) : ?>
                            <?php if( isset( $data['number'] ) ) : ?>
                            <div class="<?php $document->get_type(); ?>-number">
                                <p class="form-field <?php echo $data['number']['name']; ?>_field">	
                                    <p>
                                        <span><strong><?php echo $data['number']['label']; ?></strong></span>
                                        <span><?php $data['number']['formatted']; ?></span>
                                    </p>
                                </p>
                            </div>
                            <?php endif; ?>
                            <?php if( isset( $data['date'] ) ) : ?>
                            <div class="<?php echo $document->get_type(); ?>-date">
                                <p class="form-field form-field-wide">
                                    <p>
                                        <span><strong><?php echo $data['date']['label']; ?></strong></span>
                                        <span><?php $data['date']['formatted']; ?></span>
                                    </p>
                                </p>
                            </div>
                            <?php endif; ?>
                            <?php do_action( 'wpo_wcpdf_meta_box_after_document_data_po', $document, $document->order ); ?>
                        <?php else : ?>
                            <span class="wpo-wcpdf-set-date-number button"><?php printf( __( 'Set %s number & date', 'inv_empye' ), $document->get_title() ); ?></span>
                        <?php endif; ?>
                    </div>
    
                    <!-- Editable -->
                    <div class="editable">
                        <?php if( isset( $data['number'] ) ) : ?>
                        <p class="form-field <?php echo $data['number']['name']; ?>_field ">
                            <label for="<?php echo $data['number']['name']; ?>"><?php echo $data['number']['label']; ?></label>
                            <input type="text" class="short" style="" name="<?php echo $data['number']['name']; ?>" id="<?php echo  $data['number']['name']; ?>" value="<?php echo  $data['number']['plain']; ?>" disabled="disabled" > (<?php echo __( 'unformatted!', 'inv_empye' ) ?>)
                        </p>
                        <?php endif; ?>
                        <?php if( isset( $data['date'] ) ) : ?>
                        <p class="form-field form-field-wide">
                            <label for="<?php echo  $data['date']['name'] ?>[date]"><?php echo  $data['date']['label']; ?></label>
                            <input type="text" class="date-picker-field" name="<?php echo  $data['date']['name'] ?>[date]" id="<?php echo  $data['date']['name'] ?>[date]" maxlength="10" value="<?php echo  $data['date']['date']; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" disabled="disabled"/>@<input type="number" class="hour" disabled="disabled" placeholder="<?php _e( 'h', 'woocommerce' ); ?>" name="<?php echo  $data['date']['name']; ?>[hour]" id="<?php echo  $data['date']['name']; ?>[hour]" min="0" max="23" size="2" value="<?php echo  $data['date']['hour']; ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:<input type="number" class="minute" placeholder="<?php _e( 'm', 'woocommerce' ); ?>" name="<?php echo  $data['date']['name']; ?>[minute]" id="<?php echo  $data['date']['name']; ?>[minute]" min="0" max="59" size="2" value="<?php echo  $data['date']['minute']; ?>" pattern="[0-5]{1}[0-9]{1}"  disabled="disabled" />
                        </p>
                        <?php endif; ?>
                    </div>
                </section>
    
                <!-- Document Notes -->
                <?php if( array_key_exists( 'notes', $data ) ) : ?>
    
                    <?php do_action( 'wpo_wcpdf_meta_box_before_document_notes_po', $document, $document->order ); ?>
    
                    <section class="wcpdf-data-fields-section notes">
                        <p class="form-field form-field-wide">
                            <div>
                                <span><strong><?php echo $data['notes']['label']; ?></strong></span>
                                <span class="wpo-wcpdf-edit-document-notes dashicons dashicons-edit"></span>
                            </div>
                            <!-- Read only -->
                            <div class="read-only">
                                <p><?php echo $data['notes']['value']; ?></p>
                            </div>
                            <!-- Editable -->
                            <div class="editable">
                                <p class="form-field form-field-wide">
                                    <p><textarea name="<?php echo $data['notes']['name']; ?>" class="<?php echo  $data['notes']['name']; ?>" cols="60" rows="5" disabled="disabled"><?php echo  $data['notes']['value']; ?></textarea></p>
                                </p>
                            </div>
                        </p>
                    </section>
    
                    <?php do_action( 'wpo_wcpdf_meta_box_after_document_notes_po', $document, $document->order ); ?>
    
                <?php endif; ?>
                <!-- / Document Notes -->
    
            </div>
            <?php
        }

        /**
         * Resend order emails
         */
        public function send_order_email_meta_box_po( $post ) {
            global $theorder;
            // This is used by some callbacks attached to hooks such as woocommerce_resend_order_emails_available
            // which rely on the global to determine if emails should be displayed for certain orders.
            if ( ! is_object( $theorder ) ) {
                    $theorder = wc_get_order( $post->ID );
            }
            ?>
            <ul class="wpo_wcpdf_send_emails_po submitbox">
                    <li class="wide" id="actions">
                            <select name="wpo_wcpdf_send_emails_po">
                                    <option value=""><?php esc_html_e( 'Choose an email to send&hellip;', 'woocommerce' ); ?></option>
                                    <?php
                                    $mailer           = WC()->mailer();
                                    $available_emails = apply_filters( 'woocommerce_resend_order_emails_available', array( 'new_order', 'cancelled_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice' ) );
                                    $mails            = $mailer->get_emails();
                                    if ( ! empty( $mails ) && ! empty( $available_emails ) ) { ?>
                                            <?php
                                            foreach ( $mails as $mail ) {
                                                    if ( in_array( $mail->id, $available_emails ) && 'no' !== $mail->enabled ) {
                                                            echo '<option value="send_email_' . esc_attr( $mail->id ) . '">' . esc_html( $mail->title ) . '</option>';
                                                    }
                                            } ?>
                                            <?php
                                    }
                                    ?>
                            </select>
                            <input type="submit" class="button save_order button-primary" name="save" value="<?php esc_attr_e( 'Save order & send email', 'inv_empye' ); ?>" />
                            <?php
                            $title = __( 'Send email', 'inv_empye' );
                            $url = wp_nonce_url( add_query_arg('wpo_wcpdf_action','resend_email'), 'generate_wpo_wcpdf' );
                            ?>
                    </li>
            </ul>
            <?php
    }

    public function return_false(){
		return false;
	}

    /**
	 * Document objects are created in order to check for existence and retrieve data,
	 * but we don't want to store the settings for uninitialized documents.
	 * Only use in frontend/backed (page requests), otherwise settings will never be stored!
	 */
	public function disable_storing_document_settings() {
		add_filter( 'wpo_wcpdf_document_store_settings', array( $this, 'return_false' ), 9999 );
	}

    /**
	 * Create the meta box content on the single order page
	 */
	public function pdf_actions_meta_box_po( $post ) {
		global $post_id;
		$this->disable_storing_document_settings();
		$meta_box_actions = array();
		$documents = WPO_WCPDF()->documents->get_documents();
		$order = WCX::get_order( $post->ID );
		foreach ($documents as $document) {
			$document_title = $document->get_title();
			if ( $document = wcpdf_get_document( $document->get_type(), $order ) ) {
				$document_title = is_callable( array( $document, 'get_title' ) ) ? $document->get_title() : $document_title;
				$meta_box_actions[$document->get_type()] = array(
					'url'		=> wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type={$document->get_type()}&order_ids=" . $post_id ), 'generate_wpo_wcpdf' ),
					'alt'		=> esc_attr( "PDF " . $document_title ),
					'title'		=> "PDF " . $document_title,
					'exists'	=> is_callable( array( $document, 'exists' ) ) ? $document->exists() : false,
				);
			}
		}

		$meta_box_actions = apply_filters( 'wpo_wcpdf_meta_box_actions', $meta_box_actions, $post_id );

		?>
		<ul class="wpo_wcpdf-actions">
			<?php
			foreach ($meta_box_actions as $document_type => $data) {
				$exists = ( isset( $data['exists'] ) && $data['exists'] == true ) ? 'exists' : '';
				printf('<li><a href="%1$s" class="button %4$s" target="_blank" alt="%2$s">%3$s</a></li>', $data['url'], $data['alt'], $data['title'], $exists);
			}
			?>
		</ul>
		<?php
	}

        /**
         * Display supplier menu on editor pages (supplier and supplier order)
         */
        public function display_supplier_menu($post)
        {
            switch ($post->post_type) {
                case INV_EMPYE_POST_TYPE:
                    INV_EMPYE_editor::print_supplier_menu($post->ID, 'informations');
                    break;
            }
        }

        /**
         * Change title field placeholder in editor
         */
        function change_enter_title_here($title)
        {
            $screen = get_current_screen();

            if (INV_EMPYE_POST_TYPE == $screen->post_type) {
                $title = __('Supplier name', INV_EMPYE_TEXT_DOMAIN);
            }

            return $title;
        }

        /**
         * Change title field placeholder in editor
         */
        function change_publish_button($translation, $text)
        {
            if ('Publish' == $text) return __('Save', INV_EMPYE_TEXT_DOMAIN);
            return $translation;
        }

        /**
         * Show informations metabox in the editor
         */
        function post_informations_metabox($post)
        {
            $supplier = new INV_EMPYE_Supplier($post->ID);

            ?>
            <div class="informations-form-fields">
                <div class="form-field">
                    <label for="inv_empye_business_name_field"><?php _e('Business name', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_business_name_field"
                        name="inv_empye_business_name"
                        value="<?php _e($supplier->getBusinessName()); ?>"
                        placeholder="<?php _e('Business name', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_website_field"><?php _e('Website', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                    type="text"
                    id="inv_empye_website_field"
                    name="inv_empye_website"
                    value="<?php _e($supplier->getWebsite()); ?>"
                    placeholder="<?php _e('Website', INV_EMPYE_TEXT_DOMAIN); ?>"
                    />
                </div>
                <div class="form-field">
                    <label for="inv_empye_email_field"><?php _e('Email', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                    type="text"
                    id="inv_empye_email_field"
                    name="inv_empye_email"
                    value="<?php _e($supplier->getEmail()); ?>"
                    placeholder="<?php _e('Email', INV_EMPYE_TEXT_DOMAIN); ?>"
                    />
                </div>
                <div class="form-field">
                    <label for="inv_empye_phone_field"><?php _e('Phone', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                    type="text"
                    id="inv_empye_phone_field"
                    name="inv_empye_phone"
                    value="<?php _e($supplier->getPhone()); ?>"
                    placeholder="<?php _e('Phone', INV_EMPYE_TEXT_DOMAIN); ?>"
                    />
                </div>

                <div class="form-title">
                    <h4><?php _e('Direct contact', INV_EMPYE_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="form-field">
                    <label for="inv_empye_direct_name_field"><?php _e('Contact name', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_direct_name_field"
                        name="inv_empye_direct_name"
                        value="<?php _e($supplier->getDirectName()); ?>"
                        placeholder="<?php _e('Contact name', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_direct_email_field"><?php _e('Email', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                    type="text"
                    id="inv_empye_direct_email_field"
                    name="inv_empye_direct_email"
                    value="<?php _e($supplier->getDirectEmail()); ?>"
                    placeholder="<?php _e('Email', INV_EMPYE_TEXT_DOMAIN); ?>"
                    />
                </div>
                <div class="form-field">
                    <label for="inv_empye_direct_phone_field"><?php _e('Phone', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                    type="text"
                    id="inv_empye_direct_phone_field"
                    name="inv_empye_direct_phone"
                    value="<?php _e($supplier->getDirectPhone()); ?>"
                    placeholder="<?php _e('Phone', INV_EMPYE_TEXT_DOMAIN); ?>"
                    />
                </div>

                <div class="form-title">
                    <h4><?php _e('Address', INV_EMPYE_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="form-field">
                    <label for="inv_empye_street_field"><?php _e('Street', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_street_field"
                        name="inv_empye_street"
                        value="<?php _e($supplier->getStreet()); ?>"
                        placeholder="<?php _e('Street', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_zipcode_field"><?php _e('Zipcode', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_zipcode_field"
                        name="inv_empye_zipcode"
                        value="<?php _e($supplier->getZipcode()); ?>"
                        placeholder="<?php _e('Zipcode', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_city_field"><?php _e('City', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_city_field"
                        name="inv_empye_city"
                        value="<?php _e($supplier->getCity()); ?>"
                        placeholder="<?php _e('City', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_state_field"><?php _e('State', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_state_field"
                        name="inv_empye_state"
                        value="<?php _e($supplier->getState()); ?>"
                        placeholder="<?php _e('State', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>
                <div class="form-field">
                    <label for="inv_empye_country_field"><?php _e('Country', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <input
                        type="text"
                        id="inv_empye_country_field"
                        name="inv_empye_country"
                        value="<?php _e($supplier->getCountry()); ?>"
                        placeholder="<?php _e('Country', INV_EMPYE_TEXT_DOMAIN); ?>"
                        />
                </div>

                <div class="form-title">
                    <h4><?php _e('More', INV_EMPYE_TEXT_DOMAIN); ?></h4>
                </div>
                <div class="form-field">
                    <label for="inv_empye_comment_field"><?php _e('Comment', INV_EMPYE_TEXT_DOMAIN); ?></label>
                    <textarea
                        id="inv_empye_comment_field"
                        name="inv_empye_comment"
                        placeholder="<?php _e('Comment', INV_EMPYE_TEXT_DOMAIN); ?>"><?php _e($supplier->getComment()); ?></textarea>
                </div>
            </div>
            <?php
        }

        /**
         * Hook called when a post is saved
         */
        function save_post($post_id)
        {
        	// If this is just a revision, don't continue
        	if (wp_is_post_revision($post_id)) return;

            // Update datas
            $supplier = new INV_EMPYE_Supplier($post_id);

            if (isset($_POST['inv_empye_business_name'])) {
                $supplier->setBusinessName(sanitize_text_field($_POST['inv_empye_business_name']));
            }
            if (isset($_POST['inv_empye_website'])) {
                $supplier->setWebsite(sanitize_text_field($_POST['inv_empye_website']));
            }
            if (isset($_POST['inv_empye_email'])) {
                $supplier->setEmail(sanitize_text_field($_POST['inv_empye_email']));
            }
            if (isset($_POST['inv_empye_phone'])) {
                $supplier->setPhone(sanitize_text_field($_POST['inv_empye_phone']));
            }
            if (isset($_POST['inv_empye_direct_name'])) {
                $supplier->setDirectName(sanitize_text_field($_POST['inv_empye_direct_name']));
            }
            if (isset($_POST['inv_empye_direct_email'])) {
                $supplier->setDirectEmail(sanitize_text_field($_POST['inv_empye_direct_email']));
            }
            if (isset($_POST['inv_empye_direct_phone'])) {
                $supplier->setDirectPhone(sanitize_text_field($_POST['inv_empye_direct_phone']));
            }
            if (isset($_POST['inv_empye_street'])) {
                $supplier->setStreet(sanitize_text_field($_POST['inv_empye_street']));
            }
            if (isset($_POST['inv_empye_zipcode'])) {
                $supplier->setZipcode(sanitize_text_field($_POST['inv_empye_zipcode']));
            }
            if (isset($_POST['inv_empye_city'])) {
                $supplier->setCity(sanitize_text_field($_POST['inv_empye_city']));
            }
            if (isset($_POST['inv_empye_state'])) {
                $supplier->setState(sanitize_text_field($_POST['inv_empye_state']));
            }
            if (isset($_POST['inv_empye_country'])) {
                $supplier->setCountry(sanitize_text_field($_POST['inv_empye_country']));
            }
            if (isset($_POST['inv_empye_comment'])) {
                $supplier->setComment(sanitize_text_field($_POST['inv_empye_comment']));
            }

            $supplier->save();
	    }

        /**
         * Show products metabox in the editor
         */
        function products_linked_metabox($post)
        {
            $supplier = new INV_EMPYE_Supplier($post->ID);

            // Get informations about supplier's products
            $products = $supplier->getProducts();
            $low_stock_products = $supplier->getLowStockProducts();

            // Create URL to show supplier's products page
            $url = add_query_arg(
                array('post_id' => $post->ID),
                menu_page_url(INV_EMPYE_POST_TYPE . '_supplier_products', false)
            );
            ?>

            <div class="inv_empye_wc_products_linked">
                <div class="stats">
                    <div class="nb_products">
                        <p class="number"><?php echo count($products); ?></p>
                        <p class="label"><?php echo _n("product", "products", count($products), INV_EMPYE_TEXT_DOMAIN); ?></p>
                    </div>
                    <div class="nb_low_stocks">
                        <p class="number"><?php echo count($low_stock_products); ?></p>
                        <p class="label"><?php echo _n("low stock product", "low stock products", count($low_stock_products), INV_EMPYE_TEXT_DOMAIN); ?></p>
                    </div>
                </div>
                <a href="<?php echo esc_url($url); ?>" class="button-secondary"><?php _e("Show products", INV_EMPYE_TEXT_DOMAIN); ?></a>
            </div>
            <?php
        }

        /**
         * Add subpages (products, orders, etc.)
         */
        public function add_options_page()
        {
            // Supplier products subpage
            add_submenu_page(
                null,
                "",
                "",
                'manage_options',
                INV_EMPYE_POST_TYPE . '_supplier_products',
                array($this, 'render_supplier_products')
            );
        }

        /**
         * Supplier product subpage content
         */
        public function render_supplier_products()
        {
            $post_id = $_REQUEST['post_id'];

            if ($post_id && get_post_type($post_id)) {
                include_once(INV_EMPYE_ABSPATH . 'includes/views/supplier-products.php');
            } else {
                echo "bug";
            }
        }

        /**
         * Return supplier menu tabs
         * (used in PRO plugin to add "Orders" tab)
         */
        public static function get_tabs($post_id)
        {
            $tabs = array();

            $supplier = new INV_EMPYE_Supplier($post_id);
            $products = $supplier->getProducts();
            $nb_products = count($products);

            $products_url = add_query_arg(
                array('post_id' => $supplier->getId()),
                menu_page_url(INV_EMPYE_POST_TYPE . '_supplier_products', false)
            );

            $tabs['informations'] = [
                'label' => __("Supplier informations", INV_EMPYE_TEXT_DOMAIN),
                'url' => $supplier->getEditPermalink(),
            ];

            $tabs['products'] = [
                'label' => sprintf(_n("%s product", "%s products", $nb_products, INV_EMPYE_TEXT_DOMAIN), $nb_products),
                'url' => $products_url,
            ];

            $tabs = apply_filters(INV_EMPYE_POST_TYPE . '_get_tabs_array', $tabs, $post_id);

            return $tabs;
        }

        /**
         * Print supplier menu
         */
        public static function print_supplier_menu($post_id, $current = 'informations')
        {
            $tabs = INV_EMPYE_editor::get_tabs($post_id);
            ?>
            <div class="empye_supplier_menu">
                <ul>
                    <?php foreach($tabs as $index => $tab) :
                        if ($current == $index) :
                            ?>
                            <li class="current">
                                <a href="<?php echo esc_url($tab['url']); ?>"><?php echo $tab['label']; ?></a>
                            </li>
                        <?php else : ?>
                            <li>
                                <a href="<?php echo esc_url($tab['url']); ?>"><?php echo $tab['label']; ?></a>
                            </li>
                        <?php
                        endif;
                    endforeach; ?>
                </ul>
            </div>
            <?php
        }
	}
}

// Launch plugin
new INV_EMPYE_editor();
