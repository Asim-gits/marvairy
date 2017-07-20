<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.1 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} 

global $product, $woocommerce_loop;

//get request uri and explode query string.
parse_str(parse_url($_SERVER['REQUEST_URI'])['query'], $custom_query_string);
if ($custom_query_string) {
    $min_price_range = $custom_query_string['min_price'];
    $max_price_range = $custom_query_string['max_price'];
}

$product_price_with_tax = $product->get_price_including_tax();
// Store loop count we're currently on
if (empty($woocommerce_loop['loop']))
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns']))
    $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);

// Ensure visibility
if (!$product || !$product->is_visible())
    return;

// Check stock status
$out_of_stock = get_post_meta($post->ID, '_stock_status', true) == 'outofstock';

// Extra post classes
$classes = array();
$classes[] = 'product-small';
$classes[] = 'col';
$classes[] = 'has-hover';

if ($out_of_stock)
    $classes[] = 'out-of-stock';

if (!empty($min_price_range) && !empty($max_price_range)) {
    ?>

    <?php if ($product_price_with_tax >= $min_price_range && $product_price_with_tax <= $max_price_range): ?>
        <div <?php post_class($classes); ?>>
            <div class="col-inner">	
                <?php do_action('woocommerce_before_shop_loop_item'); ?>
                <div class="product-small box <?php echo flatsome_product_box_class(); ?>">
                    <div class="box-image">
                        <div class="<?php echo flatsome_product_box_image_class(); ?>">
                            <a href="<?php echo get_the_permalink(); ?>">
                                <?php
                                /**
                                 *
                                 * @hooked woocommerce_get_alt_product_thumbnail - 11
                                 * @hooked woocommerce_template_loop_product_thumbnail - 10
                                 */
                                do_action('flatsome_woocommerce_shop_loop_images');
                                ?>
                            </a>
                        </div>
                        <!--			<div class="image-tools is-small top right show-on-hover">
                        <?php //do_action('flatsome_product_box_tools_top'); ?>
                                                </div>-->
                        <div class="image-tools is-small hide-for-small bottom left show-on-hover">
                            <?php do_action('flatsome_product_box_tools_bottom'); ?>
                        </div>
                        <div class="image-tools <?php echo flatsome_product_box_actions_class(); ?>">
                            <?php do_action('flatsome_product_box_actions'); ?>
                        </div>
                        <?php if ($out_of_stock) { ?><div class="out-of-stock-label"><?php _e('Out of stock', 'woocommerce'); ?></div><?php } ?>
                    </div><!-- box-image -->

                    <div class="box-text <?php echo flatsome_product_box_text_class(); ?>">
                        <?php
                        do_action('woocommerce_before_shop_loop_item_title');

                        echo '<div class="title-wrapper">';
                        do_action('woocommerce_shop_loop_item_title');
                        echo '</div>';


                        echo '<div class="price-wrapper">';
                        do_action('woocommerce_after_shop_loop_item_title');
                        echo '</div>';

                        do_action('flatsome_product_box_after');
                        ?>
                    </div><!-- box-text -->
                </div><!-- box -->
                <?php do_action('woocommerce_after_shop_loop_item'); ?>
            </div><!-- .col-inner -->
        </div><!-- col -->

    <?php endif; ?>

    <?php
} else {
    ?>
    <div <?php post_class($classes); ?>>
        <div class="col-inner">	
            <?php do_action('woocommerce_before_shop_loop_item'); ?>
            <div class="product-small box <?php echo flatsome_product_box_class(); ?>">
                <div class="box-image">
                    <div class="<?php echo flatsome_product_box_image_class(); ?>">
                        <a href="<?php echo get_the_permalink(); ?>">
                            <?php
                            /**
                             *
                             * @hooked woocommerce_get_alt_product_thumbnail - 11
                             * @hooked woocommerce_template_loop_product_thumbnail - 10
                             */
                            do_action('flatsome_woocommerce_shop_loop_images');
                            ?>
                        </a>
                    </div>
                    <!--			<div class="image-tools is-small top right show-on-hover">
                    <?php //do_action('flatsome_product_box_tools_top');  ?>
                                            </div>-->
                    <div class="image-tools is-small hide-for-small bottom left show-on-hover">
                        <?php do_action('flatsome_product_box_tools_bottom'); ?>
                    </div>
                    <div class="image-tools <?php echo flatsome_product_box_actions_class(); ?>">
                        <?php do_action('flatsome_product_box_actions'); ?>
                    </div>
                    <?php if ($out_of_stock) { ?><div class="out-of-stock-label"><?php _e('Out of stock', 'woocommerce'); ?></div><?php } ?>
                </div><!-- box-image -->

                <div class="box-text <?php echo flatsome_product_box_text_class(); ?>">
                    <?php
                    do_action('woocommerce_before_shop_loop_item_title');

                    echo '<div class="title-wrapper">';
                    do_action('woocommerce_shop_loop_item_title');
                    echo '</div>';


                    echo '<div class="price-wrapper">';
                    do_action('woocommerce_after_shop_loop_item_title');
                    echo '</div>';

                    do_action('flatsome_product_box_after');
                    ?>
                </div><!-- box-text -->
            </div><!-- box -->
            <?php do_action('woocommerce_after_shop_loop_item'); ?>
        </div><!-- .col-inner -->
    </div><!-- col -->

    <?php
}
?>
