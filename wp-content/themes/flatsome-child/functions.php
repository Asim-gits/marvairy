<?php
/*
 *  @author: Faisal
 *  @feedback: feedback-1
 */


include("inc/woocommerce/custom/custom_woofunctions.php");
include("inc/shortcodes/category_listing.php");
// Shortcode for navXT
if(function_exists('bcn_display'))
    {
    add_shortcode('breadcrumbXT', 'display_breadcrumb');
    }
    function display_breadcrumb(){
        return bcn_display($return = false, $linked = true, $reverse = false);
    }
/*********************************************************************/   
/*              change display order of rating and price             */
/*********************************************************************/
add_action( 'after_setup_theme', 'wpd_wp_head_function' );
function wpd_wp_head_function() {
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 2 );
}

/*********************************************************************/
/*          SHOW ONLY THE SMALLEST VARIATION PRICE                  */
/*          @date: March 21, 2107
/*********************************************************************/
 
add_filter( 'woocommerce_variable_sale_price_html', 'wc_wc20_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_wc20_variation_price_format', 10, 2 );
 
function wc_wc20_variation_price_format( $price, $product ) {
    // Main Price
    $prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
    $price = $prices[0] !== $prices[1] ? sprintf( __( 'Ab %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
    // Sale Price
    $prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
    sort( $prices );
    $saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'Ab %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
 
    if ( $price !== $saleprice ) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
 
    return $price;
}

// Enqueue Script

function add_custom_js() {
    wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'add_custom_js' );



/*New By asim Dated: 6-19-2017*/

/*cart Product Price on the basis of users roles*/

// define the woocommerce_cart_product_price callback 
function filter_woocommerce_cart_product_price( $wc_price, $product ) { 
    // make filter magic happen here... 
    global $woocommerce;
    if ( 'excl' === $woocommerce->cart->tax_display_cart ) {
            $product_price = wc_get_price_excluding_tax( $product );
/*New Here*/
            if(is_user_logged_in() && current_user_can("buy_wholesale")){
                $product_price = wc_get_price_excluding_tax( $product );
            }
        } else {
            $product_price = wc_get_price_including_tax( $product );
/*New Here*/
            if(is_user_logged_in() && current_user_can("buy_wholesale")){
                $product_price = wc_get_price_excluding_tax( $product );
            }
        }
    return wc_price($product_price); 
}; 
         
// add the filter 
add_filter( 'woocommerce_cart_product_price', 'filter_woocommerce_cart_product_price', 10, 2 ); 





/*cart Product subtotal on the basis of users roles*/

// define the woocommerce_cart_product_subtotal callback 
function filter_woocommerce_cart_product_subtotal( $product_subtotal, $product, $quantity, $instance ) { 
    global $woocommerce;
        $price   = $product->get_price();
        $taxable = $product->is_taxable();

        // Taxable
        if ( $taxable ) {

            if ( 'excl' === $woocommerce->cart->tax_display_cart ) {

                $row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
                $product_subtotal = wc_price( $row_price );
                /*New here*/
                if(is_user_logged_in() && current_user_can("buy_wholesale")){
                    $row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
                    $product_subtotal = wc_price( $row_price );
                }

                if ( $woocommerce->cart->prices_include_tax && $woocommerce->cart->tax_total > 0 ) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            } else {

                
                /*New here*/
                if(is_user_logged_in() && current_user_can("buy_wholesale")){
                    $row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
                    $product_subtotal = wc_price( $row_price );
                     $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                } else {
                    $row_price        = wc_get_price_including_tax( $product, array( 'qty' => $quantity ) );
                    $product_subtotal = wc_price( $row_price );
                    if ( ! $woocommerce->cart->prices_include_tax && $woocommerce->cart->tax_total > 0 ) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                     }
                }

                
            }

        // Non-taxable
        } else {

            $row_price        = $price * $quantity;
            $product_subtotal = wc_price( $row_price );

        }
    return $product_subtotal; 
}; 
         
// add the filter 
add_filter( 'woocommerce_cart_product_subtotal', 'filter_woocommerce_cart_product_subtotal', 10, 4 ); 




/*// define the woocommerce_countries_inc_tax_or_vat callback 
function filter_woocommerce_countries_inc_tax_or_vat( $return ) { 

    $return = __( '(inkl. MwSt)', 'woocommerce' );
    return $return; 
}; 
         
// add the filter 
add_filter( 'woocommerce_countries_inc_tax_or_vat', 'filter_woocommerce_countries_inc_tax_or_vat', 10, 1 ); */




/*// define the woocommerce_countries_ex_tax_or_vat callback 
function filter_woocommerce_countries_ex_tax_or_vat( $return ) { 
    $return = __( '(zzgl. MwSt)', 'woocommerce' );
    return $return; 
}; 
// add the filter 
add_filter( 'woocommerce_countries_ex_tax_or_vat', 'filter_woocommerce_countries_ex_tax_or_vat', 10, 1 ); */





/*cart subtotal on the basis of users roles*/

// define the woocommerce_cart_subtotal callback 
function filter_woocommerce_cart_subtotal( $cart_subtotal ) { 
    global $woocommerce;
        // If the cart has compound tax, we want to show the subtotal as
        // cart + shipping + non-compound taxes (after discount)
        if ( $compound ) {

            $cart_subtotal = wc_price( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total + $woocommerce->cart->get_taxes_total( false, false ) );
        
        // Otherwise we show cart items totals only (before discount)
        } else {
            // Display varies depending on settings
            if ( 'excl' === $woocommerce->cart->tax_display_cart ) {

                $cart_subtotal = wc_price( $woocommerce->cart->subtotal_ex_tax );
                if(is_user_logged_in() && current_user_can("buy_wholesale")){
                   $cart_subtotal = wc_price( $woocommerce->cart->subtotal_ex_tax );
                    
                }
                if ( $woocommerce->cart->tax_total > 0 && $woocommerce->cart->prices_include_tax ) {
                    $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            } else {

                if(is_user_logged_in() && current_user_can("buy_wholesale")){
                    $cart_subtotal = wc_price( $woocommerce->cart->subtotal_ex_tax );
                    $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
                else {
                    $cart_subtotal = wc_price( $woocommerce->cart->subtotal );
                    if ( $woocommerce->cart->tax_total > 0 && ! $woocommerce->cart->prices_include_tax ) {
                    $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
                }
                
            }
        }
        return $cart_subtotal;
}; 
         
// add the filter 
add_filter( 'woocommerce_cart_subtotal', 'filter_woocommerce_cart_subtotal', 9, 1 ); 