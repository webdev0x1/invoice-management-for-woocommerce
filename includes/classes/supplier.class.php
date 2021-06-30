<?php

if (!class_exists('INV_EMPYE_Supplier')) {
    class INV_EMPYE_Supplier
    {

        /**
    	 * Reference to custom post object.
    	 *
    	 * @var WP_Post
         */
        protected $post = null;

        protected $id            = null;
        protected $name          = null;
        protected $description   = null;
        protected $email         = null;
        protected $direct_name   = null;
        protected $direct_phone  = null;
        protected $direct_email  = null;
        protected $phone         = null;
        protected $street        = null;
        protected $zipcode       = null;
        protected $city          = null;
        protected $state         = null;
        protected $country       = null;
        protected $free_delivery = null;
        protected $delivery_time = null;
        protected $comment       = null;

        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct($post_id = null)
		{
            if ($post_id) $this->find(intval($post_id));
        }

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

        public function getPost() { return $this->post; }
        public function setPost($post) { $this->post = $post; }

        public function getId() { return $this->id; }
        public function setId($id) { $this->id = $id; }

        public function getName() { return $this->name; }
        public function setName($name) { $this->name = $name; }

        public function getDescription() { return $this->description; }
        public function setDescription($description) { $this->description = $description; }

        public function getBusinessName() { return $this->business_name; }
        public function setBusinessName($business_name) { $this->business_name = $business_name; }

        public function getWebsite() { return $this->website; }
        public function setWebsite($website) { $this->website = $website; }

        public function getEmail() { return $this->email; }
        public function setEmail($email) { $this->email = $email; }

        public function getPhone() { return $this->phone; }
        public function setPhone($phone) { $this->phone = $phone; }

        public function getDirectName() { return $this->direct_name; }
        public function setDirectName($name) { $this->direct_name = $name; }

        public function getDirectEmail() { return $this->direct_email; }
        public function setDirectEmail($email) { $this->direct_email = $email; }

        public function getDirectPhone() { return $this->direct_phone; }
        public function setDirectPhone($phone) { $this->direct_phone = $phone; }

        public function getStreet() { return $this->street; }
        public function setStreet($street) { $this->street = $street; }

        public function getZipcode() { return $this->zipcode; }
        public function setZipcode($zipcode) { $this->zipcode = $zipcode; }

        public function getCity() { return $this->city; }
        public function setCity($city) { $this->city = $city; }

        public function getState() { return $this->state; }
        public function setState($state) { $this->state = $state; }

        public function getCountry() { return $this->country; }
        public function setCountry($country) { $this->country = $country; }

        public function getComment() { return $this->comment; }
        public function setComment($comment) { $this->comment = $comment; }

        public function getDeliveryTime() { return $this->delivery_time; }
        public function setDeliveryTime($delivery_time) { $this->delivery_time = $delivery_time; }

        public function getFreeDelivery() { return $this->free_delivery; }
        public function setFreeDelivery($free_delivery) { $this->free_delivery = $free_delivery; }

    	/**
    	 * Get supplier edit permalink
         * @return string
    	 */
        public function getEditPermalink()
        {
            return get_edit_post_link($this->post->ID);
        }

    	/**
    	 * Get supplier's list of products
         * @return array
    	 */
        public function getProducts($post_id = null)
        {
            global $wpdb;
            $post = $post_id ? $this->find($post_id) : $this->post;

            $query = $wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->postmeta}, {$wpdb->posts}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND ({$wpdb->posts}.post_type = 'product' OR {$wpdb->posts}.post_type = 'product_variation')
                AND meta_key='inv_empye_supplier' AND meta_value=%d"
                , $post->ID);
            $results = $wpdb->get_results($query, ARRAY_A);

            $products = array();
            foreach ($results as $result) {
                $product = wc_get_product($result['post_id']);

                // Product is a variable product ? => We put each variation in the array
                if ($product->is_type('variable')) {
                    $variations = $product->get_available_variations();

                    foreach ($variations as $variation) {
                        $products[] = wc_get_product($variation['variation_id']);
                    }
                }
                // Product is a simple product ? => We simply put product in the array
                elseif ($product->is_type('simple')) {
                    $products[] = $product;
                }
            }

            return $products;
        }

    	/**
    	 * Get supplier's list of products
         * @return array
    	 */
        public function getLowStockProducts($post_id = null)
        {
            global $wpdb;
            $post = $post_id ? $this->find($post_id) : $this->post;
            $alert_stock_min = get_option('inv_empye_alert_stock_min');

            $query = $wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->postmeta}, {$wpdb->posts}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND ({$wpdb->posts}.post_type = 'product' OR {$wpdb->posts}.post_type = 'product_variation')
                AND meta_key='inv_empye_supplier' AND meta_value=%d"
                , $post->ID);
            $results = $wpdb->get_results($query, ARRAY_A);

            $products = array();
            foreach ($results as $result) {
                $product = wc_get_product($result['post_id']);

                // Product is a variable product ? => We put each variation in the array
                if ($product->is_type('variable')) {
                    $variations = $product->get_available_variations();

                    foreach ($variations as $variation) {
                        $product = wc_get_product($variation['variation_id']);
                        if ($product->get_manage_stock() && ($alert_stock_min >= $product->get_stock_quantity())) {
                            $products[] = $product;
                        }
                    }
                }
                // Product is a simple product ? => We simply put product in the array
                elseif ($product->is_type('simple')) {
                    if ($product->get_manage_stock() && ($alert_stock_min >= $product->get_stock_quantity())) {
                        $products[] = $product;
                    }
                }
            }

            return $products;
        }

        // +----------------+
        // | STATIC METHODS |
        // +----------------+

    	/**
    	 * Get all suppliers
         * @return array
    	 */
        public static function findAll()
        {
            $args = array(
	            'posts_per_page'   => -1,
            	'offset'           => 0,
            	'orderby'          => 'title',
            	'order'            => 'DESC',
            	'post_type'        => INV_EMPYE_POST_TYPE,
            	'post_status'      => 'publish',
            	'suppress_filters' => true
            );
            $results = get_posts($args);

            $suppliers = array();
            foreach ($results as $post) {
                $suppliers[] = new INV_EMPYE_Supplier($post->ID);
            }

            return $suppliers;
        }

    	/**
    	 * Fill object
    	 */
        public function find($post_id)
        {
            $this->setId($post_id);
            $this->setPost(get_post($this->getId()));
            $this->setName($this->post->post_title);
            $this->setDescription($this->post->post_content);
            $this->setBusinessName(get_post_meta($this->post->ID, 'inv_empye_business_name', true));
            $this->setWebsite(get_post_meta($this->post->ID,      'inv_empye_website', true));
            $this->setEmail(get_post_meta($this->post->ID,        'inv_empye_email', true));
            $this->setPhone(get_post_meta($this->post->ID,        'inv_empye_phone', true));
            $this->setDirectName(get_post_meta($this->post->ID,   'inv_empye_direct_name', true));
            $this->setDirectEmail(get_post_meta($this->post->ID,  'inv_empye_direct_email', true));
            $this->setDirectPhone(get_post_meta($this->post->ID,  'inv_empye_direct_phone', true));
            $this->setStreet(get_post_meta($this->post->ID,       'inv_empye_street', true));
            $this->setZipcode(get_post_meta($this->post->ID,      'inv_empye_zipcode', true));
            $this->setCity(get_post_meta($this->post->ID,         'inv_empye_city', true));
            $this->setState(get_post_meta($this->post->ID,        'inv_empye_state', true));
            $this->setCountry(get_post_meta($this->post->ID,      'inv_empye_country', true));
            $this->setComment(get_post_meta($this->post->ID,      'inv_empye_comment', true));
        }

    	/**
    	 * Save object in database
    	 */
        public function save()
        {
            update_post_meta($this->getId(), 'inv_empye_business_name', $this->getBusinessName());
            update_post_meta($this->getId(), 'inv_empye_website',       $this->getWebsite());
            update_post_meta($this->getId(), 'inv_empye_email',         $this->getEmail());
            update_post_meta($this->getId(), 'inv_empye_phone',         $this->getPhone());
            update_post_meta($this->getId(), 'inv_empye_direct_name',   $this->getDirectName());
            update_post_meta($this->getId(), 'inv_empye_direct_email',  $this->getDirectEmail());
            update_post_meta($this->getId(), 'inv_empye_direct_phone',  $this->getDirectPhone());
            update_post_meta($this->getId(), 'inv_empye_street',        $this->getStreet());
            update_post_meta($this->getId(), 'inv_empye_zipcode',       $this->getZipcode());
            update_post_meta($this->getId(), 'inv_empye_city',          $this->getCity());
            update_post_meta($this->getId(), 'inv_empye_state',         $this->getState());
            update_post_meta($this->getId(), 'inv_empye_country',       $this->getCountry());
            update_post_meta($this->getId(), 'inv_empye_comment',       $this->getComment());
        }

    	/**
    	 * Get number of suppliers
         * @return integer
    	 */
        public static function getNbSuppliers()
        {
            $suppliers = INV_EMPYE_Supplier::findAll();

            return count($suppliers);
        }
	}
}

