<?php

defined( 'ABSPATH' ) || exit;

if (!class_exists('INV_EMPYE_Supplier')) {
    class WC_PO extends WC_Abstract_Order {

        /**
         * Which data store to load.
         *
         * @var string
         */
        protected $data_store_name = 'purchase-order';

        /**
         * This is the name of this object type.
         *
         * @var string
         */
        protected $object_type = 'purchase_order';

        /**
         * Get internal type (post type.)
         *
         * @return string
         */
        public function get_type() {
            return 'purchase_order';
        }
    }
}