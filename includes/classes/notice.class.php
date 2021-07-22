<?php

if (!class_exists('INV_EMPYE_Notice')) {

    class INV_EMPYE_Notice
    {
        public function init()
        {
           
        }

        public function enqueue_scripts($hook)
        {
            wp_enqueue_script(
                INV_EMPYE_POST_TYPE . '-admin-notice-js',
                __FILE__ . 'assets/scripts/admin-notices.js'
            );
        }
    }
}
