
<?php
$supplier = new INV_EMPYE_Supplier($post_id);
$products = $supplier->getProducts();
$nb_products = count($products);

$alert_stock_min = get_option('inv_empye_alert_stock_min');
?>

<div class="wrap inv_empye_supplier_products_page">
    <h1><?php echo sprintf(__("%s's products", INV_EMPYE_TEXT_DOMAIN), $supplier->getName()); ?></h1>

    <?php INV_EMPYE_editor::print_supplier_menu($post_id, 'products'); ?>

    <?php
    if ($nb_products) :
        ?>
        <table id="inv_empye_supplier_products_table">
            <thead>
                <tr>
                    <!-- <th class="checkbox" /> -->
                    <th class="product-column"><?php _e("Product", INV_EMPYE_TEXT_DOMAIN); ?></th>
                    <th class="stock-column"><?php _e("Stock", INV_EMPYE_TEXT_DOMAIN); ?></th>
                    <th class="price-column"><?php _e("Supplier price", INV_EMPYE_TEXT_DOMAIN); ?></th>
                    <th class="price-column"><?php _e("Sale price", INV_EMPYE_TEXT_DOMAIN); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr <?php if ($product->managing_stock() && $alert_stock_min >= $product->get_stock_quantity()) echo "class=\"warning-row\""; ?>>
                        <!-- <td>
                            <input type="checkbox" name="" value="">
                        </td> -->
                        <td>
                            <a href="<?php _e(get_edit_post_link($product->is_type('simple') ? $product->get_id() : $product->get_parent_id())); ?>" target="_blank">
                                <?php _e($product->get_name()); ?>
                            </a>
                            <small>
                                <?php
                                if ($product->is_type('simple')) {
                                    _e("Simple product", INV_EMPYE_TEXT_DOMAIN);
                                } else {
                                    _e("Variant product", INV_EMPYE_TEXT_DOMAIN);
                                }
                                ?>
                                -
                                <?php _e("SKU", INV_EMPYE_TEXT_DOMAIN); ?> : <?php echo esc_html($product->get_sku() ? $product->get_sku() : __("-", INV_EMPYE_TEXT_DOMAIN)); ?>
                            </small>
                        </td>
                        <td class="cell-text-right">
                            <?php echo $product->managing_stock() ? $product->get_stock_quantity() : __("-", INV_EMPYE_TEXT_DOMAIN); ?>
                        </td>
                        <td class="cell-text-right">
                            <?php
                            $product_supplier_price = get_post_meta($product->get_id(), 'inv_empye_supplier_price', true);
                            echo $product_supplier_price ? $product_supplier_price . get_woocommerce_currency_symbol() : __("-", INV_EMPYE_TEXT_DOMAIN);
                            ?>
                        </td>
                        <td class="cell-text-right">
                            <?php
                            $product_price = $product->get_price();
                            echo $product_price ? $product_price . get_woocommerce_currency_symbol() : __("-", INV_EMPYE_TEXT_DOMAIN);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
    else:
        ?>
        <p>
            <?php _e("No product linked to this supplier…", INV_EMPYE_TEXT_DOMAIN); ?>
            <a href="<?php _e(admin_url('edit.php?post_type=product')); ?>"><?php _e('Browse products', INV_EMPYE_TEXT_DOMAIN); ?></a>
        </p>
        <?php
    endif;
    ?>
</div>
