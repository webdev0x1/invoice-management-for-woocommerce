<?php

if (!class_exists('INV_EMPYE_menu')) {
    class INV_EMPYE_menu
    {
        // +-------------------+
		// | CLASS CONSTRUCTOR |
		// +-------------------+

		public function __construct()
		{
            $this->init_hooks(); // Init hooks in Wordpress
		}

        // +---------------+
        // | CLASS METHODS |
        // +---------------+

    	/**
    	 * Initiate plugin hooks in Wordpress
    	 */
        public function init_hooks()
        {
            add_action('admin_menu', array($this, 'adjust_the_wp_menu'), 998);
        }

        /**
         * Build admin menu for Suppliers post type
         */
        public function adjust_the_wp_menu()
        {
            add_menu_page(
                __('All suppliers', INV_EMPYE_TEXT_DOMAIN),
                __('Suppliers', INV_EMPYE_TEXT_DOMAIN),
                'manage_options',
                // 'edit.php?post_type=' . INV_EMPYE_POST_TYPE, // If no supplier => root to another page
                INV_EMPYE_POST_TYPE . '_suppliers',
                array(INV_EMPYE_menu::class, 'suppliers_page_callback'),
                'dashicons-store',
                56
            );

        }

        public function suppliers_page_callback()
        {
            // S'il y a des fournisseurs, on redirige vers la liste.
            $supplier = new INV_EMPYE_Supplier();
            if (count($supplier->findAll())) {
                wp_redirect(admin_url('edit.php?post_type=' . INV_EMPYE_POST_TYPE));
                exit;
            }

            // Sinon, on affiche une boite invitant à créer le premier fournisseur
            ?>
            <div class="wrap tf_empye tf_empye_go_pro">
                <div class="no-supplier-card">
                    <div class="content">
                        <h3><?php _e("No supplier…", INV_EMPYE_TEXT_DOMAIN); ?></h3>
                        <p><?php _e("Your suppliers list is empty. To use perfectly this plugin, start by adding your first supplier by clicking on the button bellow.", INV_EMPYE_TEXT_DOMAIN); ?></p>
                        <a href="<?php _e(admin_url('post-new.php?post_type=' . INV_EMPYE_POST_TYPE)); ?>"><?php _e("Create a supplier", INV_EMPYE_TEXT_DOMAIN); ?></a>
                    </div>
                    <div class="illustration">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 200 200"><defs><style>.a{opacity:0.2;}.b,.c{fill:#9b59b6;}.b{opacity:0.3;}.d{fill:url(#a);}.e{fill:#fff;}</style><linearGradient id="a" x1="40.71" y1="71.58" x2="121.45" y2="152.33" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#8e44ae"/><stop offset="1" stop-color="#9b59b6"/></linearGradient></defs><title>Plan de travail 6 copie</title><g class="a"><path class="b" d="M93.59,65.8,53.62,135a17.14,17.14,0,0,0,14.84,25.71H148.4A17.14,17.14,0,0,0,163.24,135l-40-69.23A17.14,17.14,0,0,0,93.59,65.8Z"/><path class="c" d="M95.45,81.25l13-22.48,38.2,66.17a5.87,5.87,0,0,0,8,2.14l2.32-1.33a5.87,5.87,0,0,0,2.14-8l-40-69.25a12.36,12.36,0,0,0-21.41,0L83,74a5.88,5.88,0,0,0,2.14,8l2.32,1.34A5.87,5.87,0,0,0,95.45,81.25Z"/><rect class="c" x="44.41" y="105.52" width="49.09" height="14.41" rx="5.87" transform="translate(-63.14 116.08) rotate(-60)"/><rect class="c" x="98.73" y="151.4" width="15.85" height="14.41" rx="5.87"/><path class="c" d="M176,147l-6-10.35a5.88,5.88,0,0,0-8-2.15l-2.31,1.34a5.87,5.87,0,0,0-2.15,8l4.36,7.56H130.54a5.87,5.87,0,0,0-5.87,5.87v2.67a5.87,5.87,0,0,0,5.87,5.87h34.93a12.23,12.23,0,0,0,10.74-6.24A12.71,12.71,0,0,0,176,147Z"/><path class="c" d="M82.78,151.4H55a5.88,5.88,0,0,0-2.15-8l-2.32-1.33a5.86,5.86,0,0,0-8,2.15L40.86,147a12.71,12.71,0,0,0-.21,12.59,12.23,12.23,0,0,0,10.74,6.24H82.78a5.86,5.86,0,0,0,5.86-5.87v-2.67A5.86,5.86,0,0,0,82.78,151.4Z"/></g><path class="d" d="M74.89,37.76,23.19,127.3A7.16,7.16,0,0,0,29.39,138H132.78A7.15,7.15,0,0,0,139,127.3L87.28,37.76A7.16,7.16,0,0,0,74.89,37.76Z"/><path class="e" d="M80.32,65h1.52A5.13,5.13,0,0,1,87,70.28L86.2,102a5.12,5.12,0,0,1-5.12,5h0A5.12,5.12,0,0,1,76,102l-.75-31.76A5.12,5.12,0,0,1,80.32,65Z"/><rect class="e" x="76.08" y="113.03" width="10" height="10" rx="5"/></svg>
                    </div>
                </div>
            </div>
            <?php
        }

        public function go_pro_page_callback()
        {
            ?>
            <div class="wrap tf_empye tf_empye_go_pro">
                <div class="go_pro_header">
                    <h2><?php _e('Get the PRO version of this plugin', INV_EMPYE_TEXT_DOMAIN); ?></h2>
                    <div class="separator"></div>
                    <h3><?php _e('Improve your business with premium features', INV_EMPYE_TEXT_DOMAIN); ?></h3>
                    <p><?php _e('We provide an extended version of this plugin for Woocommerce users who want to go further in their suppliers management. We work a lot to add some valuable features.', INV_EMPYE_TEXT_DOMAIN); ?></p>
                </div>

                <section class="go_pro_features">
                    <div class="go_pro_feature">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 120"><defs><style>.a{fill:#9b59b6;}</style></defs><title>Plan de travail 9</title><path class="a" d="M107.87,92H42.13a8.74,8.74,0,0,1-8.73-8.73V36.73A8.74,8.74,0,0,1,42.13,28h65.74a8.74,8.74,0,0,1,8.73,8.73V83.27A8.74,8.74,0,0,1,107.87,92ZM42.13,33a3.73,3.73,0,0,0-3.73,3.73V83.27A3.73,3.73,0,0,0,42.13,87h65.74a3.73,3.73,0,0,0,3.73-3.73V36.73A3.73,3.73,0,0,0,107.87,33Z"/><path class="a" d="M74.64,70a6.77,6.77,0,0,1-4.74-1.92L35.13,33.94l3.5-3.56L73.41,64.52a1.77,1.77,0,0,0,2.47,0L111,30l3.5,3.57L79.39,68.09A6.78,6.78,0,0,1,74.64,70Z"/><rect class="a" x="93.8" y="55.83" width="5" height="40.95" transform="translate(-22.24 108.61) rotate(-53.63)"/><rect class="a" x="33.13" y="73.6" width="40.27" height="5" transform="translate(-34.75 46.4) rotate(-36.37)"/></svg>
                        <h3><?php _e('Email your suppliers', INV_EMPYE_TEXT_DOMAIN); ?></h3>
                        <p><?php _e('Send email and follow discussion with your suppliers directly in the Wordpress administration.', INV_EMPYE_TEXT_DOMAIN); ?></p>
                    </div>
                    <div class="go_pro_feature">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 120"><defs><style>.a{fill:#9b59b6;}</style></defs><title>Plan de travail 9 copie</title><path class="a" d="M75,93.5A33.5,33.5,0,1,1,108.5,60,33.54,33.54,0,0,1,75,93.5Zm0-62A28.5,28.5,0,1,0,103.5,60,28.53,28.53,0,0,0,75,31.5Z"/><path class="a" d="M75,73a2.5,2.5,0,0,1-2.5-2.5v-28a2.5,2.5,0,0,1,5,0v28A2.5,2.5,0,0,1,75,73Z"/><circle class="a" cx="75" cy="78" r="2.5"/></svg>
                        <h3><?php _e('Get alerted when stocks get low', INV_EMPYE_TEXT_DOMAIN); ?></h3>
                        <p><?php _e('Be sure to avoid order missing because of out-of-stock products. Get alerted when the stocks get low.', INV_EMPYE_TEXT_DOMAIN); ?></p>
                    </div>
                    <div class="go_pro_feature">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 120"><defs><style>.a{fill:#9b59b6;}</style></defs><title>Plan de travail 9 copie 2</title><path class="a" d="M100.61,92.72H50.23a6.05,6.05,0,0,1-6-6V39.45a9.21,9.21,0,0,1,9.21-9.21H97.45a9.21,9.21,0,0,1,9.22,9.21V86.67A6.05,6.05,0,0,1,100.61,92.72Zm-51.43-5h52.49V39.45a4.22,4.22,0,0,0-4.22-4.21H53.39a4.22,4.22,0,0,0-4.21,4.21Z"/><path class="a" d="M101.48,92.72H49.37a5.18,5.18,0,0,1-5.19-5.18V71.66h62.49V87.54A5.19,5.19,0,0,1,101.48,92.72Zm-52.3-5h52.49V76.66H49.18Z"/><path class="a" d="M75.42,92.72a2.5,2.5,0,0,1-2.5-2.5V74.58a2.5,2.5,0,0,1,5,0V90.22A2.5,2.5,0,0,1,75.42,92.72Z"/><path class="a" d="M84.72,92.72a2.5,2.5,0,0,1-2.5-2.5V74.58a2.5,2.5,0,0,1,5,0V90.22A2.5,2.5,0,0,1,84.72,92.72Z"/><path class="a" d="M94,92.72a2.5,2.5,0,0,1-2.5-2.5V74.58a2.5,2.5,0,0,1,5,0V90.22A2.5,2.5,0,0,1,94,92.72Z"/><path class="a" d="M66.12,92.72a2.5,2.5,0,0,1-2.5-2.5V74.58a2.5,2.5,0,0,1,5,0V90.22A2.5,2.5,0,0,1,66.12,92.72Z"/><path class="a" d="M56.82,92.72a2.5,2.5,0,0,1-2.5-2.5V74.58a2.5,2.5,0,0,1,5,0V90.22A2.49,2.49,0,0,1,56.82,92.72Z"/><path class="a" d="M104.17,72.43a2.5,2.5,0,0,1,0-5,9.34,9.34,0,0,0,0-18.67,2.5,2.5,0,0,1,0-5,14.34,14.34,0,0,1,0,28.67Z"/><path class="a" d="M45.83,72.43a14.34,14.34,0,0,1,0-28.67,2.5,2.5,0,0,1,0,5,9.34,9.34,0,0,0,0,18.67,2.5,2.5,0,0,1,0,5Z"/><path class="a" d="M96.52,61.44H54.32V49.68h42.2Zm-37.2-5h32.2V54.68H59.32Z"/><path class="a" d="M86.41,99.48h-22a5.88,5.88,0,1,1,0-11.76h22a5.88,5.88,0,1,1,0,11.76Zm-22-6.76a.88.88,0,1,0,0,1.76h22a.88.88,0,0,0,0-1.76Z"/><path class="a" d="M86.41,106.25h-22a5.89,5.89,0,0,1,0-11.77h22a5.89,5.89,0,0,1,0,11.77Zm-22-6.77a.89.89,0,0,0,0,1.77h22a.89.89,0,0,0,0-1.77Z"/><path class="a" d="M75.63,25.52a5.89,5.89,0,1,1,5.89-5.89A5.89,5.89,0,0,1,75.63,25.52Zm0-6.77a.89.89,0,1,0,.89.88A.89.89,0,0,0,75.63,18.75Z"/><path class="a" d="M75.63,35.24a2.43,2.43,0,0,1-2.5-2.35V23.38a2.51,2.51,0,0,1,5,0v9.51A2.43,2.43,0,0,1,75.63,35.24Z"/></svg>
                        <h3><?php _e('AI stocks management', INV_EMPYE_TEXT_DOMAIN); ?></h3>
                        <p><?php _e('Use the power of artificial intelligence to get your stock at a right level.', INV_EMPYE_TEXT_DOMAIN); ?></p>
                    </div>
                </section>

                <section class="go_pro_ctas">
                    <p><?php _e('Get a one-year license to use the PRO version of this plugin by visiting our website :', INV_EMPYE_TEXT_DOMAIN); ?></p>
                </section>
            </div>
            <?php
        }
	}
}

// Launch plugin
new INV_EMPYE_menu();
