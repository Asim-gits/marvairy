<?php
/*
Plugin Name: WooCommerce Wholesale Pricing Plus
Plugin URI: http://ignitewoo.com
Description: Works in conjunction with WooCommerce Wholesale Pricing plugin from IgniteWoo.com. This add-on allows the store to be used for retail and wholesale purchases and provides the ability to eliminate shipping charges and payment charges during the checkout process. Be sure to read the README file for details. SINGLE SITE LICENSE. Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved. 
Author: IgniteWoo.com
Version: 2.3.25
Author URI: http://ignitewoo.com
*/

/** 

Copyright (c) 2012 - IgniteWoo.com - ALL RIGHTS RESERVED 

LICENSE: 

This software is developed for single site use. Each site where this plugin is used
requires an individual site license. 

The software is distrbuted WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE. You use this software at your own risk.

*/

// Portions are Copyright: (c) 2011 Lucas Stark
/**
* Required functions
*/
if ( ! function_exists( 'ignitewoo_queue_update' ) )
	require_once( dirname( __FILE__ ) . '/ignitewoo_updater/ignitewoo_update_api.php' );

$this_plugin_base = plugin_basename( __FILE__ );

add_action( "after_plugin_row_" . $this_plugin_base, 'ignite_plugin_update_row', 1, 2 );


/**
* Plugin updates
*/
ignitewoo_queue_update( plugin_basename( __FILE__ ), '59d63b9e87eaac3a42a4bc8ed8eb460f', '521' );


class IgniteWoo_Wholesale_Pricing_Plus { 

	var $plugin_url, $disable_coupons, $disable_coupons_error_msg, $filter_products, $retail_filter_products, $without_vat_price;
	var $compatible = false;

	function __construct() { 

		if ( !$this->pre_init() )
			return;

		if ( !class_exists( 'woocommerce_wholesale_pricing' ) && !class_exists( 'woocommerce_tiered_pricing' ) ) 
			return;

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->load_settings();

		add_filter ( 'woocommerce_settings_tabs_array', array( &$this, 'add_tab' ), -99, 1 );

		add_action( 'woocommerce_settings_tabs_wholesale_plus', array( &$this, 'wholesale_settings' ) );

		add_action( 'woocommerce_update_options_wholesale_plus',  array( &$this, 'save_settings' ) );

		add_filter( 'woocommerce_coupon_is_valid', array( &$this, 'disable_coupons' ), 1, 2 );

		add_filter( 'woocommerce_coupon_error', array( &$this, 'disable_coupons_error_msg' ), 999999999, 2 );

		if ( 'yes' == $this->filter_products ) { 
			
			remove_all_actions( 'woocommerce_variable_add_to_cart' ); //, 'woocommerce_variable_add_to_cart', 30 );
			
			add_action( 'woocommerce_variable_add_to_cart', array( &$this, 'woocommerce_variable_add_to_cart' ), 20 );
			
		}
		
		if ( 'yes' == $this->display_when_no_retail ) { 

			add_filter( 'woocommerce_is_purchasable', array( &$this, 'is_purchasable' ), 999999999, 2 );

			add_filter( 'woocommerce_empty_price_html', array( &$this, 'empty_price_html' ), 999999999, 2 );

		}

		add_action( 'init', array( &$this, 'init' ), 99999 );
		add_action( 'wp', array( &$this, 'init' ), 99999 );

		add_action('wp_ajax_ignitewoo_wholesale_create_new_ruleset', array( &$this, 'create_new_ruleset' ) );
		
		// VAT handlers = display price without tax, charge tax in cart - if that setting is on
		add_filter( 'woocommerce_get_price_excluding_tax', array( &$this, 'maybe_get_price_excluding_tax' ), 999, 3 ); 
		add_filter( 'woocommerce_get_price_html', array( &$this, 'maybe_get_price_html' ), 9999999, 2 );

		// Runs AFTER the hook in WC Wholesale Pricing base plugin
		add_filter( 'woocommerce_available_variation', array( &$this, 'maybe_adjust_variations' ), 2, 3 );
		
	}


	function pre_init() { 
		global $ignitewoo_wholesale_pricing_base_version;

		if ( !is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		
			$this->compatible = true;
		
			return true;
		}

		$res = true;

		if ( class_exists( 'woocommerce_dynamic_pricing' ) ) { 

			add_action( 'admin_notices', array( &$this, 'warning' ), -1 );

			$res = false;
		}

		if ( class_exists( 'woocommerce_tiered_pricing' ) ) {
			$res = true;
			
		} else if ( !class_exists( 'woocommerce_wholesale_pricing' )  && !class_exists( 'woocommerce_tiered_pricing' ) ) { 

			add_action( 'admin_notices', array( &$this, 'required' ), -1 );

			$res = false;

		} else if ( !$ignitewoo_wholesale_pricing_base_version || version_compare( $ignitewoo_wholesale_pricing_base_version, '2.2.2', '>=' ) <= 0 ) {

			add_action( 'admin_notices', array( &$this, 'base_required' ), -1 );

			$res = false;

		}

		$this->compatible = $res; 

		return $res; 
	}


	function load_settings() { 
	
		$this->filter_products = get_option( 'woocommerce_wholesale_pricing_plus_filter_products', false );
		
		$this->retail_filter_products = get_option( 'woocommerce_wholesale_pricing_plus_retail_filter_products', false );

		$this->alt_filter = get_option( 'woocommerce_wholesale_pricing_plus_alt_filter', false );

		$this->disable_coupons = get_option( 'woocommerce_wholesale_pricing_plus_disable_coupons', false );

		$this->disable_coupons_error_msg = get_option( 'woocommerce_wholesale_pricing_plus_disable_coupons_msg', false );

		$this->display_when_no_retail = get_option( 'woocommerce_wholesale_pricing_plus_display_when_no_retail', false );

		$this->disable_tax = get_option( 'woocommerce_wholesale_pricing_plus_disable_tax', false );

		if ( '' == trim( $this->disable_coupons_error_msg ) )
			$this->disable_coupons_error_msg = __( 'As a wholesale buyer you may not use coupon codes.', 'ignitewoo_wholesale' );
			
	}
	
	function warning() { 

		echo '<div class="error" style="font-weight: bold; font-style: italic; color: #cf0000" ><p>';
		_e( 'Warning: The Wholesale Pricing Plus plugin is not compatible with the WooCommerce Dynamic Pricing plugin. Deactivate one or the other.', 'ignitewoo_wholesale');
		echo '</div>';

	}


	function required() { 

		echo '<div class="error" style="font-weight: bold; font-style: italic; color: #cf0000" ><p>';
		_e( 'Warning: The WooCommerce Wholesale Pricing Plus plugin requires the Woocommerce Wholesale Pricing base plugin, WooCommerce Tiered Pricing - both from IgniteWoo.com. Please visit <a href="http://ignitewoo.com" target="_blank">IgniteWoo.com</a> to obtain a copy.', 'ignitewoo_wholesale');
		echo '</div>';

	}


	function base_required() { 

		echo '<div class="error" style="font-weight: bold; font-style: italic; color: #cf0000" ><p>';
		_e( 'Warning: The WooCommerce Wholesale Pricing Plus plugin requires Woocommerce Wholesale Pricing base plugin version 2.2.2 or newer.  Please visit <a href="http://ignitewoo.com" target="_blank">IgniteWoo.com</a> to obtain a copy.', 'ignitewoo_wholesale');
		echo '</div>';

	}

	function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_wholesale' );

		load_textdomain( 'ignitewoo_wholesale', WP_LANG_DIR.'/woocommerce/ignitewoo_wholesale-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		load_plugin_textdomain( 'ignitewoo_wholesale', false, $plugin_rel_path );

	}
	
	function init() { 
		global $woocommerce, $ignitewoo_remove_wholesale_tax;

		wp_enqueue_script('jquery-ui-sortable');

		if ( !current_user_can( 'wholesale_buyer' ) )
			return;
			
		if ( 'yes' == $this->disable_tax ) { 
			
			$ignitewoo_remove_wholesale_tax = true;

		// Cart and checkout for EU users
		} else if ( 'eu' == $this->disable_tax && ( is_checkout() || is_cart() || ( !empty( $_REQUEST['action'] ) && ( 'woocommerce_update_order_review' == $_REQUEST['action'] || 'woocommerce_checkout' == $_REQUEST['action'] ) ) ) ) {

			$ignitewoo_remove_wholesale_tax = false;
		
			if ( !empty( $woocommerce->customer ) )
				$woocommerce->customer->set_is_vat_exempt( false );
				
		// Shop and product pages for EU users
		} else if ( 'eu' == $this->disable_tax ) { 

			$ignitewoo_remove_wholesale_tax = true;
			
			if ( !empty( $woocommerce->customer ) )
				$woocommerce->customer->set_is_vat_exempt( true );

		} else if ( 'no' == $this->disable_tax && ( is_checkout() || is_cart() ) ) {
			
			$ignitewoo_remove_wholesale_tax = false;
			
			if ( !empty( $woocommerce->customer ) )
				$woocommerce->customer->set_is_vat_exempt( false );
			
		} else { 

			$ignitewoo_remove_wholesale_tax = false;
			
			if ( !empty( $woocommerce->customer ) )
				$woocommerce->customer->set_is_vat_exempt( false );

		}

	}
	
	
	function maybe_get_price_excluding_tax( $price, $qty, $_product ) { 
		global $wp_roles;

		$is_exempt = false;
		
		if ( isset( $wp_roles->roles ) ) {
			foreach( $wp_roles->roles as $role => $data ) { 
			
				if ( 'ignite_level_' != substr( $role, 0, 13 ) )
					continue; 
					
				$is_exempt = current_user_can( $role );
			}
		}
		
		if ( !$is_exempt )
			$is_exempt = current_user_can( 'wholesale_buyer' );
		
		if ( !$is_exempt )
			return $price;

		if ( 'yes' == $this->disable_tax ) { 

			return $price;

		// Cart and checkout for EU users
		} else if ( 'eu' == $this->disable_tax && ( is_checkout() || is_cart() || ( !empty( $_REQUEST['action'] ) && 'woocommerce_update_order_review' == $_REQUEST['action'] ) ) ) {

			return $price;
				
		// Shop and product pages for EU users
		} else if ( 'eu' == $this->disable_tax ) { 

			if ( $_product->is_type( 'simple' ) || $_product->is_type( 'variation' ) )
				$wprice = get_post_meta( $_product->id, 'wholesale_price', true );

			if ( !empty( $wprice ) && 'yes' == get_option('woocommerce_prices_include_tax') ) {

				$_tax = new WC_Tax();
				$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
				$taxes = $_tax->calc_tax( $wprice * $qty, $tax_rates, true );
				$price = $_tax->round( $wprice * $qty - array_sum( $taxes ) );
				
				if ( !empty( $_product->variation_id ) )
					$this->without_vat_price[ $_product->variation_id ] = $price; 
				else 
					$this->without_vat_price[ $_product->id ] = $price; 
				
				return $price;
			}
			
			return $price;

		} else { 

			return $price;
			
		}

		return $price;
		
	}
	

	function maybe_get_price_html( $price, $_product ) {
		global $wp_roles; 

		$is_exempt = false;

		if ( isset( $wp_roles->roles ) ) {
			foreach( $wp_roles->roles as $role => $data ) { 
			
				if ( 'ignite_level_' != substr( $role, 0, 13 ) )
					continue; 
					
				$is_exempt = current_user_can( $role );
			}
		}

		if ( !$is_exempt )
			$is_exempt = current_user_can( 'wholesale_buyer' );

		if ( !$is_exempt )
			return $price;

		// Disable tax can have 3 conditions, yes, no, and eu
		if ( 'yes' == $this->disable_tax ) { 

			$wprice = get_post_meta( $_product->id, 'wholesale_price', true );
			
			if ( !empty( $wprice ) && 'yes' == get_option('woocommerce_prices_include_tax') ) {

				$_tax = new WC_Tax();
				
				$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
				
				$taxes = $_tax->calc_tax( $wprice * 1, $tax_rates, true );
				
				$price = $_tax->round( $wprice * 1 - array_sum( $taxes ) );	
				
				if ( !empty( $_product->variation_id ) )
					$this->without_vat_price[ $_product->variation_id ] = $price; 
				else 
					$this->without_vat_price[ $_product->id ] = $price; 
			
				if ( function_exists( 'wc_price' ) )
					$price = wc_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
				else 
					$price = woocommerce_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
			}

			if ( !empty( $_product->variation_id ) )
				$wtext = get_post_meta( $_product->variation_id, 'wholesale_price_text', true );
			else 
				$wtext = get_post_meta( $_product->id, 'wholesale_price_text', true );
			
			if ( !empty( $wtext ) )
				return $price . ' <span class="wholesale_text">' . $wtext . '</span>';
			else 
				return $price;
			
		} else if ( empty( $this->disable_tax ) || 'no' == $this->disable_tax ) {

			if ( !empty( $_product->variation_id ) )
				$wtext = get_post_meta( $_product->variation_id, 'wholesale_price_text', true );
			else 
				$wtext = get_post_meta( $_product->id, 'wholesale_price_text', true );
			
			if ( !empty( $wtext ) && false === strpos( $price, $wtext ) )
				return $price . ' <span class="wholesale_text">' . $wtext . '</span>';
			else 
				return $price;
							
		}

		
		// Must be set to "eu", calc the price without tax for display
		
		if ( $_product->is_type( 'simple' ) || $_product->is_type( 'variation' ) || $_product->is_type( 'variable' ) )
			$wprice = get_post_meta( $_product->id, 'wholesale_price', true );

		if ( !empty( $wprice ) )
			$price = $wprice;
			
		if ( !empty( $wprice ) && 'yes' == get_option('woocommerce_prices_include_tax') ) {

			$_tax = new WC_Tax();
			
			$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
			
			$taxes = $_tax->calc_tax( $wprice * 1, $tax_rates, true );
			
			$price = $_tax->round( $wprice * 1 - array_sum( $taxes ) );	
			
			if ( !empty( $_product->variation_id ) )
				$this->without_vat_price[ $_product->variation_id ] = $price; 
			else 
				$this->without_vat_price[ $_product->id ] = $price; 
		
			if ( function_exists( 'wc_price' ) )
				$price =  wc_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
			else 
				$price = woocommerce_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
				
			if ( !empty( $_product->variation_id ) )
				$wtext = get_post_meta( $_product->variation_id, 'wholesale_price_text', true );
			else 
				$wtext = get_post_meta( $_product->id, 'wholesale_price_text', true );
			
			if ( !empty( $wtext ) && false === strpos( $price, $wtext ) )
				return $price . ' <span class="wholesale_text">' . $wtext . '</span>';
			else 
				return $price;
				
			
		}
		

		// Must be set to "eu" then
		if ( 'eu' == $this->disable_tax && !empty( $this->without_vat_price[ $_product->id ] ) && $this->without_vat_price[ $_product->id ] > 0 ) {
		
			if ( function_exists( 'wc_price' ) ) { 

				$price = woocommerce_price( round( $this->without_vat_price[ $_product->id ], absint( get_option( 'woocommerce_price_num_decimals' ) ) ) );
				
			} else {  
			
				$price = woocommerce_price( round( $this->without_vat_price[ $_product->id ], absint( get_option( 'woocommerce_price_num_decimals' ) ) ) );
			}
			
		} else {

			if ( $_product->is_type( 'variable' ) )
				return '<span class="from">' . __( 'From:', 'woocommerce' ) . ' ' . woocommerce_price( $wprice ) . ' </span>';
			else { 
			
				$sym = get_woocommerce_currency_symbol();
				
				$price = strip_tags( $price );
			
				$price = str_replace( $sym, '', $price );
			
				if ( function_exists( 'wc_price' ) )
					$price = wc_price( $price );
				else 
					$price = woocommerce_price( $price );
			}
		}


		if ( $_product->is_type( 'variable' ) ) { 

			return '<span class="from">' . __( 'From:', 'woocommerce' ) . ' ' . $price . ' </span>';
		
		} else {
		
			if ( !empty( $_product->variation_id ) )
				$wtext = get_post_meta( $_product->variation_id, 'wholesale_price_text', true );
			else 
				$wtext = get_post_meta( $_product->id, 'wholesale_price_text', true );
			
			if ( !empty( $wtext ) && false === strpos( $price, $wtext ) )
				return $price . ' <span class="wholesale_text">' . $wtext . '</span>';
			else 
				return $price;
 
	
		}
	}
	
	
	// Handles inject price info for form submission
	function maybe_adjust_variations( $variation = '', $obj = '' , $variation_obj  = '') { 
		global $current_user, $woocommerce_wholesale_pricing;

		if ( !isset( $current_user->ID ) ) 
			$current_user = get_currentuserdata(); 

		if ( !current_user_can( 'wholesale_buyer' ) ) { 
			return $variation;

		}

		// Does the product have a wholesale price set? If not don't load any wholesale text string
		if ( $w_price = get_post_meta( $variation['variation_id'], 'wholesale_price', true ) )
			$wtext = get_post_meta( $variation['variation_id'], 'wholesale_price_text', true );
		else 
			$wtext = '';

		if ( 'yes' == $this->disable_tax && 'yes' == get_option('woocommerce_prices_include_tax') ) { 
			
			$wprice = get_post_meta( $variation['variation_id'], 'wholesale_price', true );
			
			if ( !empty( $wprice ) ) {

				$_tax = new WC_Tax();
				
				$_product = get_product( $variation['variation_id'] );
				
				$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
				
				$taxes = $_tax->calc_tax( $wprice * 1, $tax_rates, true );
				
				$price = $_tax->round( $wprice * 1 - array_sum( $taxes ) );	
				
				if ( !empty( $_product->variation_id ) )
					$woocommerce_wholesale_pricing->without_vat_price[ $_product->variation_id ] = $price; 
				else 
					$woocommerce_wholesale_pricing->without_vat_price[ $_product->id ] = $price; 
			
				if ( function_exists( 'wc_price' ) )
					$vprice = woocommerce_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
				else 
					$vprice = woocommerce_price( round( $price, get_option( 'woocommerce_price_num_decimals', 2 ) ) );
			}

		} else { 
				
			$vprice = $woocommerce_wholesale_pricing->maybe_return_variation_price( '', $variation_obj );
			
		}
		
		if ( !empty( $vprice ) && !empty( $wtext ) )
			$vprice .= ' <span class="wholesale_text">' . $wtext . '</span>';
			
		//$variation['price_html'] = $obj->product_custom_fields['min_variation_wholesale_price'][0] != $obj->product_custom_fields['max_variation_wholesale_price'][0] ? '<span class="price">' . $this->maybe_return_variation_price( '', $variation_obj ) . '</span>' : '';
		
		$variation['price_html'] = '<span class="price">' . $vprice . '</span>';

		return $variation;

	}
	

	function add_tab( $tabs = '' ) { 

		$tabs['wholesale_plus'] = __( 'Wholesale', 'woothemes' );

		return $tabs;

	}


	function wholesale_settings() { 

		$rules = get_option( 'woocommerce_wholesale_pricing_plus_discount_rules', false );

		?>
		<style>
		    #woocommerce_extensions { display: none !important; }
		</style>
		<script>
			jQuery( document ).ready( function() { jQuery( "#woocommerce_extensions" ).css( "display", "none" ) } );
		</script>
		<h2>Wholesale Plus Settings</h2>

		<p><?php _e( 'Configure the settings to control how your wholesale aspects work in your store.', 'ignitewoo_wholesale' ) ?></p>

		<div>
		    <table class="form-table" style="width:99%;margin:0;padding:0">
		    <tr>
		    <td width="80%">


			<h3 style="font-size:1.3em">Settings</h3>
			<table>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Wholesale Only', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<input type="checkbox" value="yes" name="woocommerce_wholesale_pricing_plus_display_when_no_retail" <?php if ( '' != $this->display_when_no_retail ) echo 'checked="checked"' ?>/> <?php _e('Enable wholesale-only products', 'ignitewoo_wholesale' )?>
					<p class="description"><?php _e( 'Normally, if a product has no retail price set then no buy button appears when viewing the product.', 'ignitewoo_wholesale' )?></p>
					<p class="description"><?php _e( 'When this feature is enabled products that have no retail price but do have a wholesale price will be visible to, and purchasable by, wholesale buyers.', 'ignitewoo_wholesale' ) ?></p>
					<p class="description"><?php _e( 'This feature effectively allows you to sell "wholesale only" products that are not purchasable by regular customers.', 'ignitewoo_wholesale' ) ?></p>
					<p class="description" style="font-weight:bold"><?php _e( 'When using this feature do not set the product regular price to 0 (zero) otherwise it will appear in your store as free! Instead leave the regular price field blank. ', 'ignitewoo_wholesale' ) ?></p>

				</td>
			</tr>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Wholesale Filter', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<input type="checkbox" value="yes" name="woocommerce_wholesale_pricing_plus_filter_products" <?php if ( '' != $this->filter_products ) echo 'checked="checked"' ?>/> <?php _e('Enable filtering for wholesale buyers', 'ignitewoo_wholesale' )?>
					<p class="description"><?php _e( 'When enabled, wholesale buyers will only see products that have wholesale pricing set. No other products will appear in your store.', 'ignitewoo_wholesale' ) ?></p>
				</td>
			</tr>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Retail Filter', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<input type="checkbox" value="yes" name="woocommerce_wholesale_pricing_plus_retail_filter_products" <?php if ( '' != $this->retail_filter_products ) echo 'checked="checked"' ?>/> <?php _e('Enable filtering for retail buyers', 'ignitewoo_wholesale' )?>
					<p class="description"><?php _e( 'When enabled, retail buyers will only see products that have retail pricing set. Wholesale-only product will not appear.', 'ignitewoo_wholesale' ) ?></p>
				</td>
			</tr>
			
			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Alternative Filtering', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<input type="checkbox" value="yes" name="woocommerce_wholesale_pricing_plus_alt_filter" <?php if ( '' != $this->alt_filter ) echo 'checked="checked"' ?>/> <?php _e('Enable alternative filtering', 'ignitewoo_wholesale' )?>
					<p class="description"><?php _e( 'When enabled, if either Retail or Wholesale filters are enabled then an alternative filtering method will be used. Helpful with themes whose menus do not appear correctly when filtering is enabled', 'ignitewoo_wholesale' ) ?></p>
				</td>
			</tr>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Disable Coupons', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<input type="checkbox" value="yes" name="woocommerce_wholesale_pricing_plus_disable_coupons" <?php if ( '' != $this->disable_coupons ) echo 'checked="checked"' ?>/> <?php _e('Disable coupons for wholesale buyers', 'ignitewoo_wholesale' )?>
					<p class="description"><?php _e( 'When this feature is enabled wholesale buyers cannot use coupon codes when ordering.', 'ignitewoo_wholesale' ) ?></p>

					 <?php _e('Message', 'ignitewoo_wholesale' )?> <input style="width: 400px" type="text" value="<?php echo $this->disable_coupons_error_msg ?>" name="woocommerce_wholesale_pricing_plus_disable_coupons_msg"/>
					<p class="description"><?php _e( 'Enter the message displayed to wholesale buyers when they try to use a coupon and coupons are disabled for wholesale buyers. This field cannot be blank.', 'ignitewoo_wholesale' ) ?></p>

				</td>
			</tr>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Tax Setting', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<select name="woocommerce_wholesale_pricing_plus_disable_tax">
						<option value="yes" <?php selected( $this->disable_tax, 'yes', true ) ?>><?php _e( 'Disable all taxes', 'ignitewoo_wholesale' )?></option>
						<option value="no" <?php selected( $this->disable_tax, 'no', true ) ?>><?php _e( 'Charge taxes', 'ignitewoo_wholesale' )?></option>
						<option value="eu" <?php selected( $this->disable_tax, 'eu', true ) ?>><?php _e( 'Display price without tax, charge tax at checkout', 'ignitewoo_wholesale' )?></option>
					</select>
					
					<p class="description"><?php _e( 'Choose how to handle taxes for wholesale buyers. If you display price without tax and charge tax at checkout BE CERTAIN to enter your product wholesale prices INCLUDING VAT', 'ignitewoo_wholesale' ) ?></p>
				</td>
			</tr>

			<tr>
				<th style="width: 120px; vertical-align:top">
					<h4 style="margin:0"><label for="logo_image"><?php _e( 'Sitewide Discounts', 'ignitewoo_wholesale' ) ?></label></h4>
				</th>
				<td>
					<p class="description">
						<?php _e( 'You can enable sitewide discounts for wholesale buyers.', 'ignitewoo_wholesale' ) ?><br/>
						<?php _e( 'These rules will be overridden by the wholesale discount rules of individual products, if such rules are configured. ', 'ignitewoo_wholesale' ) ?><br/>
						<?php _e( 'You can rearrange rulesets by dragging and dropping them into a new order.', 'ignitewoo_wholesale' ) ?><br/>
					</p>


					<?php $this->sitewide_discount_rule_box() ?>
				</td>
			</tr>

			</table>

		    </td>

		    <td style="width:20%; vertical-align:top" valign="top">
			    <div style="width:250px; border: 3px solid #00cc00; padding: 12px 0; font-weight:bold; font-style:italic; margin-top: 15px; text-align:center; border-radius:7px;-webkit-border-radius:7px">
				    <a title=" More Extensions + Custom WooCommerce Site Development " href="http://ignitewoo.com" target="_blank" style="color:#0000cc; text-decoration:none">
					    <img style="height:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAABWCAYAAAB1s6tmAAAgAElEQVR4nO2de3xcV3Xvv2ce0uj9smVJli2/bYwSJ9iQmITQxPmEkKTNbdq0F27b3JIQ8rmUe00L96alcG9KA4HehA95XMItNB8gBZoCBZqSB05IQiC5TkyC67cdSdbbsiWNpJFmNI9z7h97ztF57DNz5iFZCef3+cgz3rPXWvvMOXvNWmuvvbbCeUZra2tIUZR6oDYQCDRWV1dvCoVC2wOBwEZFUdYqitKpKEozUA1EgMD5HbEPH285qEACiGmaNqZp2rCmaf2qqp5MpVJHZmdnTwBzQAyInTlzJn2+BqqcL8HAOmBdKBTaEg6HdwaDwYsCgUC3oijV53FMPnz4sEHTtAlVVQ9lMpmD8/PzB1RVPQWcAkaXeixLrbDqgYuAdwHvBbYDa4HQEo/Dhw8fxSEJ9AC/Al4C9gOHEBbYomOpFFY1cANwI0Jhrcu2+fDh482LaaAPobT+BfjJYgtcTIUVQFhO1wKfRFhT9fjWlA8fbzUkEcrrBeAB4JdAGhEbKysWQ2EFgGbgcuCjwBVAxSLI8eHDx/JDDHgS+ArwKkKRlQ2LobCuAP4Y+B2gdRH4+/DhY/mjH/ge8PfAsXIxDZaLEcKquhm4C7gOqCkjbx8+fLy50ADsBjYBs8AgwnUsCeVSWJuAvwH+S/a9Dx8+fABsAC4DmoDDCJexaJSqsEKIFIUvIVYBG0rk58OHj7cWFIRe2AVsQcS1JotlVorCqkCkKTyQHUw53UsfPny8tRAC3gZciVBa54BMoUyKVTK1wB8C9wJdRfLw4cPHbx5WIbyyYUQOV0HbfIpZJawF/jMit2ptEfQ+fPh4E+DDbbCzVv7ZrAr/cg5enIbL6+F3V8BUGv6m3xNrFTgC3A38ELGP0RMKtbAiwAeBvwQ6Ob97EX348LGI2BCBSAA+1AbNYXh5BmYy8Cer4D0NMK/Cvkn4i064owPaK+D/jHhirQArgLcjtvn04DHJtBCFVQH8LkIrrsFXVj58vKVxfA5+MS0U1qFZ+HiPsKj+ZJX4PACkNLimCWqDEFc9KywQ+qMFuAR4BRjwQlRIqZYrgM8iLCsfPny8xZFBKCQ00LLvU9n3/fMwlISPrRav/fNFiQgg0qC+ikh/8ETgBVuAL+DnWPnw4QOYTMP+GXhbtXidLK1CVjdCv+SNiXvZiLwCYVm9o6QhLSEiAagMQFqD2YIXTn34WFxEIhEikQiapqFpGtPTZd1uJ0VdXR2BgLBPUqkU8XgcTdOK5jevwnNReGAIno/C1Y0lD/E6xHaeu8ix/9CLwvogcE3JwzEhEoArGiAkiYLNqsJfHk8VxnNzBN7bCO+qg45KISOtwUQKjsyJL/fVGCQK2D9eG4DuWmh2+ZYUBQ7MwGieDQdtFbCzDmTPx3gKDs4K/9+MSAAuyiH7tRiMJJ00u+qgvswZcRpwbA5OJ6yR0UgA3lkHdWWSpyhwdFYEds/a7v+ePXuorKyU0g0ODnLs2DGSSeeNaG1tZdeuXVK6VCrFsWPHGBiQh0+6u7vp6uqSTuyZmRkOHjzI1NRUnqsSaGlp4YorruCaa66hq6uLyspKQ2GNjo6yf/9+fvrTn/LGG2+QShX48EsQDAZZs2YNe/bs4bLLLqOzs9OisAYHB3n22Wd57rnnGBnxHngy42gcvjAIc+WpyVAN/D7wC+AHbp1yBc4DCK13L8IlLBu6KuHoOyEcQswG02hOxmHvCXjaQy5spSJM0g+1wR+tgvqwuCD7RWlARoUDMfjfA2JlY8qD5fX2anhwK1zeYBunjgB8sRc+1Zebz80r4dsXIE2T+/kU3HYMemwLu2sr4R+2w3tlsgNwy2H49piT5kfd0F3nMt5ioAhen+qF+wetCn9NJTx9IWyqKZO8IHzqFByJweMT1o9ee+01uru7pWSPP/44H//4x+nr63N8dvvtt/PQQw9J6ZLJJHfffTdf+MIXyGSsNycUCvHd736XG2+8UUr70ksvcfvtt3PsmPu+3kAgwOrVq7n55pu57bbb2LhxI6FQCEVxTjtVVTl37hw//vGPeeCBBzhy5IhjTF6gKAqbN2/mIx/5CDfffDMdHR0oiuKQqWkaqqoyNDTEN7/5TR599FFOnTqFqjq1T0gRP7b6aPRpax5dEPGolKF28svAxxAFAh2DyWVhdQJ/SpmVFWQDeCpUmYajaYACiuZtfbMuCL/bAh9fAxfUQkATtBZ+WQQU8WVeUg9f2wr/MApfHYZT8dyytOw/QQ1LR0VZ4P/uemgI5laAqgZBVfAw06KIcbvNdUWn0xbkCoaCp5uskLZAYxJlvS6vbdn7IbMONSCtZsdYigy9TRVyZJd28OBBduzYYZl4uuWzatUqGhrku8K6uroIBoUJaKeNRCKsWrWK6upqZmZmLHTNzc20trZKaVVVJRqNMjmZ+1d1586dfPKTn+T666+nurrakKsoivGqtwWDQVpbW/nwhz/Me97zHu68806eeeYZYjHvW+8ikQiXXnop99xzD5dccolDhlmu/tfV1cWdd97J7t27+exnP8sLL7zg4Ju23RCZUipj5OVSRLWXE0hcQ7egewRReO+3yjcOOTTEQ6oo3vMkqgPwR63wv9bDjhqhUJQsMwu/7B8mZdgYhNvb4TPrYEu1B5kmZSHjt74aLvRQl0LBRuvxenU6gxZ3BWcfs6aZLM4ytOUc4yLLOHLkiEPh6JNuxYoV1NfXO2iCwSBtbW0OC8NM297eLlV2jY2NrFy5UkqbSqUYGBhwKDkzuru7ue+++/i93/s9h7ICHIrE3LZ161buu+8+PvjBDxq0+RAOh7nhhhv48pe/zCWXXJJXhrktHA5z9dVXc9999xm05xl/ALxb9oGbwloB3IIoGbOoMCZyAbhxBfxlF6yvdE56Oz/Zr3pNAP7DCti7GpryRfFsisXOryEM76j1ttwqG0u+vrL/56VX3PsV1Vbg/SlFrht6enossR2zEqmvr6e5udmI0eiora2lo6PDKdc0eVeuXClVdi0tLdTUOH+JFEUhmUwyNDREPB6XjnXdunXcfffdXH755ZZxmpVFvrZ169axd+9errnmGsPKy4Xdu3fziU98gre//e2eZdjbdu7cyT333MOOHTvyyltktAH/TfaB2zz7A8R+nyWD1xBIZwXc3QVrTDVMDVoXRaVJ2qoV+ONVYkuBl4G58asFLm2Aeg/LF7KxOP8jFW/QelnY0bQFF9vMp9g2TC5mTrklyMgnYnR0lGg0agSqzdAVUyhkvQn19fV0di6kDcpoW1paqKurc8hra2ujomLhITPTplIpRkdHpcH4SCTCHXfcwZ49ewCrYtXhpS0QCLBlyxY+8pGPsHnzZkd/M9auXcsdd9zBO97xDqkLW4jc3bt382d/9mc0Npa+7FcirkJ4eRbIFFY98BcsQe11y+TNEcvRUaHAnWthXY2ENkuvAjENxjNixVEjGzcy9QPhzlUF4dNroSNPAWcHraktqMC2GthW5e1azbQyRStrMss1rsUFZhfSrAQsbXZ+mks/C1Nv1+aFX642NwwODjI4OGi4aGYFUlVVxerVqy0KBkQcqq6uzugno21sbKSpqckxcdesWWOkHthp4/E4PT09jjEqisJVV13FTTfdRHV1tUFrftU0jUQiwcTEBBMTE8zPz7v2CwaD7Nmzx+AnQ0VFBe973/u4/vrrCYfDlmszv87OzjI+Pk40GiWdTrv2q6io4Nprr+X973+/w2JdYlQg9itbdjPKlNIfAU47erGgZR9UD/7Bpiq4ukkoG2PSmWhnVXh1RuSF9M/Dxgj8VpOIMVUHFiaERpZGha5qeF8TPHLGRajuXtlptYW2jgp4e41IoHML4isutG6wuI/KAm2uryqpwvE4qIrcEuuogFVhoWQBNGVBycQzcCwBGTtddrxjSXfrTh+PmV9KE/egkITCQBDGUjAtieDG43HDqrHHlRRFoa2tjXA4bL3ejg4qKioccRszbUNDA6tXryYUCllczs7OTlfaubk5BgcHHWOsqakxUgjc4kfHjh1j3759HD58GE3T2LFjBzfccIOFxkwbDoe57rrr+Na3vsXcnPMkraamJq6//nrDrbXLTafTHDp0iMcff5y+vj7q6+vZvXs3V111FS0tLdL4VltbG1deeSX79u3j7Nmz8pu1NLgQkanwmN5gV1grgA8s5YjITi4vcax31sGKCiyzWadNaPCjcfhiv8i7Smki7eGfz8Gfd8JNK4TSypJZlMe1zfDNMclklQ93gTaLxqBQig2h3BNURpurr5Q2BybS8LenoTro7KwBt7bBLe0gMwaHkvCxkyIh0C5bAwaTkPTqtwMzqliJ/VnUe6xKUYSSi0sUViKRoL/fvRRAZ2enI09r9erVrrlbOmpqali1apVFYVVVVRnKToazZ88yPDzsaG9tbeXCCy+kqmrhGzavzPX29rJ3715efPFFZmdnDfkHDhzgr//6r+nq6nIoYk3T6O7uZsOGDdJ8sfb2dnbs2GFRPDqtqqo8++yzfP7zn+cXv/gFqVSKQCDAP/3TP3HLLbfwmc98xrAizbShUIju7m46OjrOt8JqBK4Hngai4FRYV7AIaQw5oS24B/ke7O5qqDHHH020p+LwudNCWenzal6D12Pw0BBsr4aLaxcmvjkmtKUaVoThTI4EUDONQZt9H1Zge42wXnIpLBltPjjk5iBManA4x3GW709mlbJJ0etWU1wVyajFJgHa+aWB3oRI1i0HEokEPT09ZDIZS5xGn2xr1qxxuE1mhWVe1jfT6ukE4XDYCKI3NTXR3NwsTQkAsQCQSDgrojQ3N7N69WqLDB2pVIovfelLPPXUUxaa2dlZHnvsMTo7O/mrv/orw0o009bV1XHRRRfx/PPPO2Ru3LiR1tZWY5xm2r6+Ph588EF+/vOfGzldqqoyPDzMQw89xCWXXGLkmdlp29vbaW1ttVz3eUAIscPmQsQRYpYYVgB4P0uwMqhDQbgRQF7zoVKBlRULGlZRTLQBeHrCqqx0aIiE0f83DbrBr9heG4K541iKaXyKS9vbqmF9JLcSktF6hUGreLdYZDwUk7Ky8wsUyViPE9r5FTtOGZLJJH19fczPz1viSfoE6+josKz2hUIh2tvbjbiO2e2x03Z1dRGJRAzaFStWGApLRnvs2DFpgmV9fT0tLS0WGTr6+/t58sknpdc2MzPDc889Z1iQdlpFUQylZEdLS4vUotM0jV//+tccPHhQmoA6PT3Nv/7rvzpWXs2xvRUrVpzvOBaI/cvvIqurzKO5KPu3pAed6pM338NdExTpCMZk16y0A0l3HZDRxHaPjCnKaw48BxFWkhRmRSWh1ds6KkRialWe+6vZFJ9ZhqxJJrdcv3fLnZ8ZqqoyMjJibCMxT2hN06itrWXdunVGm55HZU4JsAeZ9ffr16+3KCxZqoOZ9tSpU3nHa5c1MjIijUHpmJycZGxszHWcXmGmnZmZkVqCOmZmZkinF1wCM61sRfE8IQK8B1gHVoXVzRJXENVdsnyrUA4oNlry50EpjjdYZlbeR8M+Rvu4Fbi03lt6g8MflFy74vqf0i0X+7NY6rPpiLct0rMejUY5c+ZMVoY1eB4MBtm2bZvx/+bmZlpaWggEAo7JZ6dtb2+ntnZhMaqhoYGamhpHP4B0Oi3dAmSHOZvcC/StMsXQyuTa37v1LWXMS4gLyeomfZ5XAxcggu5LBiX7jwaF/SybVhYL/g2SrC7mhNlltdOa2xB7DzvzpEhYaIsZcxmx3PnZMTU1xejoqESumGTr16832pqbm2loaHAEomW0NTU1Rr5WMBhk5cqVjniYTjs5OZlzs3Apk91NWXjlWarsZYxOYBtQoSustmzDkjusiib+CrWw0Ey0BQks0mWRKUhzmwZtlfAuZ9K0AQdtAbLNtKW6XCY9uyz5uSEWizE2NmbJIzK7TWvXLjgIjY2N1NTUSPvZ2yKRCBs2iPpxlZWVtLe3G3lUdtqzZ88aVl4umGkLVQbF0JoXCAp1Je205zHI7oYQsBNRQAUQeVfb3PsvHjR90hdqcSgmWg8kxm23uYF5PVLN5H7aaW1tQQX2eEgQNmg9QGZ9luwS2mKGJfOjvPzcMDc3x8DAAPF4XOq+dHR0EIlEUBSxv7C2tlbaz94WCATYtEnUpqyoqKC1tdXgY6cdGRlhfj5/ec1yuHWluoRLKXcJsAuoDSGsqg7OQ+ljfTLqq31edZZmeqN5/G5Hk2IVUeaxjSZhLsd2c7P3p2Bawre3AVc1wsqws56TG631giTX6EJbCsxK0JyKsFz4uSGRSDAwMMDs7Kwl5iTkKnR2dtLW1sbw8DBtbW2GhWXvJ2vTLayamhpjX6Ks36FDh3KOURYwL0QBeBmvV9pCsAytKju2kC0PV4GIwEdydl8EGKGcQieiIqwEgzZPdxX47ll4JioJOCOSJXvdFlOy4zKPz3hv4qW3NYbhtxpEwqqUnYRfrqC7woJSLtsPn1KEG56f5eKZViacO3eOmZkZVq0SJyHYs9ZbWlo4e/YsbW1tljhUvtiQnihaX1/PypUrXfudOHEi5/hkgexClIEsV8yLwrPnihUCWY7aMkQ1sCWEUFQbz9co9F/kXAmRDmT76laHl1s0lBR/RY0Rk9WQHWNKhcF5WF9l66eIons/OOdeI8jg5+F6DeulnMrlTaqsAIaHh5mYENX97BMrEAiwZs0a+vv7aWlpIRQKuU54e1tnZyeNjY3U1dXR0tIi7ZdKpaR7CN1QiMIplVaWIV+IrGJplxjbAgiFdd5OwjHHkLzeViVrmhWTEVEsFF1uFhngpRmI2bSSosHuBnFGm53ewS+HPLN+lFmF5cRirBZqOAu/lQPDw8OMj4+7yFXYtGkTtbW1NDc3G22yfna0trbS1NRksbDs/SYmJjxvVTGnFhSKctAWg1LkLhE26y7h0m12tkHRrJPTO6GV1o4NEZHI6VaZ04zZDPxyWuzFc4PdKKlQRPb8ughcZipjrGnQUQU7amFwQsLIhZ8dSo5+JesBG8Ny6xUNqFFE6Z4NVe7XqSgwPC+sUXtNezdMTU3R399PKpVy7PXTNI3t27fzzDPP0NbWZrTZJ6CsLRQK0dXVRX19vVFuxt6vp6eHc+dcfH0bSkm+fDPSLhHW6QprybbjmKHHchT9PwUQaoqJVoIrG+Er20DLVy1AgYEE/MejMCHb92ay5Ay5iG0scyr8ZBze3ZBVnlklWh8QdeCfmrCWk9Uw9fN4qRa5Jle4LFhEfrUBUW/sj3P1V+CVaXhq0rvCSqfTnDp1ikQi4VBYiqKwbt06mpqaWLFihdFmDC+Hq6MoClu3bkVRFIOvffLmKtpnl3G+cqLewrlYAG0BxCphjuyhxYOhqPQ0Bf2DPMrLrEDc+gYRxfPDgYW/ioD1/+Y21310pui3Lkp/DSmwLwqTKdNqpQIRRZRubjcVCjBoJfzywUFbIowtNOXip7+a+Mm26TjaNG8WsEWWpnH69GkSiYR0Ra6pqYmNGzdaqoXqn2cyGaampshkMlLaLVu2sHr1akuZGnO/4eFh6ck8ZsgUZKlbbArpXy7aZYpGXWF5KxxdZmiIh1zLxqSKpnX5HGzxsayCk7XlEm+2AM2xp5AiAu+vzVotRQVYVw1bqpx8FBs/rzDLLfWR0udU2fjZXnXL2UtbMRgaGjI2QcOCktA0jaamJnbu3GlYSWY3J5lMcuTIEWKxmJR206ZNdHV1GRt+zbSpVIqRkZG8CkuHrM6UF5piac3XUailVArtEqNWV1h5NpQsDowJrNkUT77vTLHR5uiqYbMoFFtbvkHKaLMfBRSxqfqV6YVaWvp1tIVFSRvz2YuOseQXa6RPmGlLhb4qq1mElMAPJz+ZDEdbkeJHR0eZmJiQZmY3Nzezc+dOh5WkaeLwiBdffJGRkREp7aZNm+ju7pZmxc/OztLb25tXYZVi6ZhX6MyvXujN/Qp1S2W0yxSR8147Qma55IPdHczlzSlYf9llbfmF2WhNH89kRJE6/TBV/fPGILyzAVaEco8lj1jnmMvwPC0FP69txWBkZISBgQFpZnZNTQ0XXHCBo+SKoijMz8/zyiuvMDo6KqVdv34927ZtcyzzK4rC9PQ0Z86cyXtWYCkpAvZk02KVR6lxrOWstAKIvMoiM5TKBPv36+X7svshNgRMM11mzXi+J5J+9qbeBPy7rRaXgtgMvdpW8FIq14MMg7YcFrv+vWgL78vB08LP/N0HrH/mtnCgcPF6xrsO8wQLBAJUVlZK6zhFo1FLiRo7bTAYJBgMSifsmTNnPJ3yXMpkX+bu2HJAIoRQWHOcB7fQHHy13CsP983sasi6n02JtAM1I3ivrRBHxgeytJ6fDVs/2faYkXn41TS811QPSwPWV8LGqoV8JFe5kjaHDjfRluWxXmR+8xocnoWRXKeuK9A3J2rRFwrzicuyeI+sbWxsjOnpad54442Cac+cOUM0Gs07LjttOZI4i0keLRSy61+GiOkKaxpRP3nJYc50L/R7Mmglnz0XhVNHFz7buxr+0ypRuVSPCRXimRjjU5w0s6ooxXw2A2tMFkN9SJxZeEBPlzDL9Xi9hlGl05bRylpMfjMq/N8R+Gnuw5FJqM7kWy84evSo8d5rrtXY2BhTU1McOXKkYNrR0VFPFlapcBtPsbSlyF2GiIYQ7uAES1y8DxYmbjETRldWbrQT6YVEUAU4l1o40aao22KzRsyPkIaopT4QhzXhhX5BBXbWwcA8TkuwUOVcJJ0MFk9wkfipwHgKetwLXpaEEydOGPXd7akE9tiUPuEnJiaIxWIcP37ctZ+sLZVKcfbs2ZzVO2UoVAmY+5e6Ulgo7HKXaRxrNIBQWM4jQJYQpYRRvNDa+2geAvYLna393O7jibhYLZw3uX9o8M7ahaPsC5Gri7HIzf4V+yhp9jc6v3I9mzZ+i/l7PTEx4djXJ5tkels8HmdoaIhEIsHY2BiTk5Oeaaempujt7fVUVsZOW6hLZ1+x86o87LSFwE3uMkRfAEgAzkPWlgAaWJ7qQuLgdtpCZWp4n6jm+S1zCUHEqZ6fginV1E+D+qDIetetQbNchwIxwaJgzXJLeI4U22up/Ox8y8XPC+bm5oxYlKyUi71tdnbWUFjRaNQ4V9ALrV44MN8KoRu/QpArplYMbSlylyFO6grrjXw9FwMK2ZhHEb/wRrXRYmj1P4/3RrG9dyM7EIP+OacMPYHUVa6Eof2ySrFCHbC5t2VmuyTIZDKGwvKywXlubo7x8XFUVWVubs5yQk0+2unpac97CPPxWipaKH7FchkrrWO6S9iHUFxLDsPTKeS7NVsrFKizzL6WF/cqe+80D4KG58XJ00ah6ayMoExuHl4Wq8VGW1bFVcRix5Lxy4Oenh7LcVu5lM/s7KzhBmqaRm9vr7SfvU3TNKanp10rRLjJK1c8qVha+/vFlrsEmANO6HlYw5wnt1BRsn9LSGvcjgJoZcF2OzLAExOgqXIZZZFbjiC5+SLKFHQvJz+vOHnypOPoLLdYVDQaNY7RAjh16pTjbEEZbSaTYXh42ELrFcXEgmRxpEL3BJa6d3GZBt1PABO6LTAMHMvReUngeRKXEscxaQBPt8TUz4vcZ6LQn8BhwcnkGuzyWVtmrVVC0H2B4QKvsvBjEfh5wMmTJy2pBm7KStM0xsfHLUrn+PHjllU/N9pMJuM5adRO68bXK00piqcYLPOg+6tATFdYowiFVeRB5cVBd3ccQWgXmFfMHLR5iI3AedalKqZSgUGbo8+cCk9MCiVjr15gl+t5kcFMW0b3bVnz84DZ2VmGh4dzrvCBsJL0/Yc6BgcHmZmZyUs7Pz/P6dOnpSc9y2DnV0jA3H6GYrFpDW5j8Uq7DJVVGjiASWHNAf8OFBdZLBJK9h89ITPX15TWFjLGDTdQWbA+qnPsitQQm5DNAW8j4K/zyTFIu6x8t/PZSZhLm2QoErlmmXkYWuSWwXzRr9vgVyJPxfxaBn5ekUgk6O3tzTvJk8kkg4ODllW+6elpYz9iLlr7NqB8cJv4DQ0NhELup+xWVVVRX19vKBlZHlk+mGkVRdT1Mp98bUcoFLJUpsg1/vOMQYRBlTRP80NA/1KOQg+2O4LukvsTy8BUJls/SXPSvqvefSv3irCoQBo2u0ImUYkMTOUp9GfOg8qHf5/NHmphD9ib5Xqc1EbQ3dS/LI+RmV85GJabnwfE43FLLpZb4Hx+ft5IY9AxPz9vrBTmotWVohek02mjmoOd3/r169m6dauULhQKsXXrVrq6uhyJqzovtyoR5vwpO+3mzZuNyqsy7Nq1y6hqYadNpVLMz88vl1jWQbK6yTzFX8/+5avRWTboFo/Dk5A88CrQl4CEyToxaDW4vBFubxOWlpm8NggfXAmXNWYv1iwsSx9Nw1CenEDdyvKiLc4kRU6WZhqrTK4XyGhLhlLeID648FPEj0Qhf4EChqRbTvbJbJ9kiUTCopz0NtmR83baWCzmuY6726nQmqZRU1PD5z//ebq6uiyWVigUYteuXezdu9dxdJlOm0wmXU/rOXnyJJOTk1JXdMeOHdx22210dnYalpSiKEQiEa677jo+9KEPSY8zA7GN6cyZM55d4UVEAvg5IpMBs42qAk8AvwO0LtlwspPay1P6WkzsUavT1axOCzQH4X90iSqfT0yIrTiNIbi+BW5pg5U2a9wgDcDLMxBzuy+OCHl+I2smAy9MwgdWQlPISWsooQJRrt86fR+jwa9ExhpOftUKvL9JbDj3DAV+GYWJFPR6SCpPp9MMDg4yPj5usSTsq1zxeNxxWnMymWRkZIR0Om1xm+y0vb29jqx4N4yOjnL8+HF2795tURA6z4svvphvfetbfO1rX+P48eMAbNu2jb1793LRRRcBVoWp0w4NDfH6669LZfb09NDb20tLS4uDNhQKceutt9La2sojjzzCyMgINTU17N69m49+9KNG7XrZquCpU6cYGhrydN2LjJHVqdIAAAmkSURBVFPAfrLxdbtT/QJi+XBJFJZ9wlhWwyT4VQyOxqCjCTTV2lcB2sPw553w+ythLCUONF1XueAKmpWFLiquwo9ypdiY3Dozba45ngGOxeFIHN5dJzpb5GoLkzwfNCQxr1Jhj8WVibGZX40Cf9oGfypT1ri0BeHOU3A45k1hqarK2NgY4+PjtLe3C36SWMz4+Lgl4A4Lym5qaso41ktGOzAwQCwmK/bvRDQaZf/+/fz2b/82ra2t0lW3yy+/nIsvvthwUdeuXWvU7pKlMmiaxvPPP+9QuDomJyf52c9+xsUXX2woXjNtOBzmpptu4rLLLuPcuXNUVVUZJ2S7pVDEYjH279/vKnMJkQZ+hXAJAatLCCLo/p2lHJFie6BFo7zvWAq+MYqxBcRBq4nTbDZGYHc9bIosVPzUTP2MlUUFfjYJL0/nHqOMNh+GknAolj2bULHJtfHNB4vccgTdixlEgfwsAXhVvCqm925tqlbYkOxJnbJYlPksQx3pdNqRriCjLWSFMJVK8cwzz/Daa6+RyWQMy8VswSiKQk1NDdu2bWPr1q1UV1e79tM0jcHBQR5//HGmp+UP6ezsLE888YSxodtMa+a3atUqtm/fzoYNG6isrHTtB6J0z5NPPunIcTsPiAL/ln0FnAoL4FGWcjO06QH38kP/g3Pw9AQWM8egNQXVFZWF7TumPua+I/Nw74BIRcgFB60HTKREyZnpjJO2EF6yvmVxDc3KvhwMZfyKaCsUU1NTnDlzxnXjr6ZpjIyMSE+7mZ6etmS/y2i9Btx19Pb28vWvf12aGZ8vhcLelkwm+eEPf8jzzz9POi0PLauqyiuvvMJ3vvMdpqenC5Zhb5uamuLRRx/l4MGDjs/PAw4CPzE3yBTWNHAvSxl8L+CBnVVh7xvwiylI61aWjd5iedmD1QqoCpyeh0/1Cj6FwGv+lgrsn4E3Eibjo4TAuZm2bK6hzq+cDGX8vLYVgWg0Sl9fH6lUynWlz1ywz047OjrqmiiZSqU4fPhwQeNRVZXvf//73HXXXUxPT1usGHPaRL62eDzO9773Pe699968+xhjsRgPP/ww//iP/2i4r8XIjcfjPPjggzz88MOkUrkqLy4JksDfARZ/3C176TFEoGtRoVs6BWQMAHByDu7sEVnlc9oCrR7rsfPTXSpNES7aiTh8rg/++exCOZhckPHzglNxODwj8sccYzHzkTC09C9QrlcsBb9S2rwgFosxODjoqFVltiDcjpfXSybbrQ39/7FYTLqSmA+qqvLwww/zxS9+UVoVQjZGc1ssFuOxxx7j05/+NKdPn/Yk89y5c9xzzz088sgjnrL/7RgZGeGrX/0qn/vc5zyfDLTIeBZ40t7olsl2DvgGsI1FOGRVIRtbCmAJSOuHo+Y7GSODiDv99zfg1na4cQWs1Wuna1ZFaLiKiijFuy8KXx+Gp6PCWss3ToWsJRCw8tO0/OOcyZ4o/TuroMmcbmGOq7lYGrpcJWAK+Os0uc5RzAG9zr1urZkXD4JFWG76fdRM99E+FQpqCxSW1qBjZGSEqakpY9ULsMRl3CysmZkZRkZGjHiTnXZwcNBzwN0OVVW5//77GR4e5pZbbmHXrl1UVy+cpierX5XJZDh58iTf//73+cpXvsLwcGGRmYGBAe666y5GR0f5wAc+wNve9jZHjXq73EQiweuvv843vvENHnvssbwHxS4RRoEvyz5wU1gJhHZ7H3BTuUcTV8VJM5GQNX6iKDCcFJUq8yEDHJyFvz0NT4zDH66C3XXQXgE1pgk9r8K5NByag6fG4d8moCfuzd+NZUQcKqNZD/zUJ1x/npUsDXhpGp48B6sqnLEiJXsNshhaQoVfz4CqOukCCox6ryVnoCch0i3CdhcakeOWLtDMSaji+oZS5YmDhQLCVT9bYDDixIkT7Nu3j7Vr1zomZyaTcbVSUqkUr7/+Ok8//bRl1QzEYRYvv/xyUdehY2Zmhm9/+9scOHCAK6+8kmuvvZYLL7yQ5uZmKioqjPFNTk7S29vLc889x5NPPsmrr77KzMxMUTInJia4//77efHFF7n66qu56qqr2LRpE83NzYTDYVRVJZlMEo1GOXr0KD/5yU949tlnOXLkSMEVVRcRjwG/lH2Q68csAFyHiGdtKedoAojs8+wPs2UwaWA6DckCJkAAaAiJNIYNkexhE9kri2XgZFyUfommvbmAOkKIuuwVitxlmcnkD9iHFJEPFkJuVSQ1kWVvLw0XQNDJZCuIa0kUqCRqAiKRVoa0JkpKF8IygMgzC7t8P4VCUWAmDSmtsPsfDoepr68nHA67JkG6ZWxXVVVRV1cnzS7Xi/2VA5WVlTQ0NNDe3s7mzZupq6sjEAiQTCbp6emhr6+PqakpZmdny5JdrigK1dXV1NfXs2bNGjZt2kRVVRV6uZy+vj4GBweJRqPLKaMd4GXgY4h0Bsfs8mJ9/1fgLs7TIRU+fPj4jUE/8HHgB24dvByk+m3g6XKNyIcPHz4kmAO+B+zL1cl9K7eV0UHgMqC99HH58OHDhwM/Av4GyFkp0YvCAhgHeoB3swirhj58+PiNxiHgVrIbnHPBq8ICUZNmFLgUqC9qWD58+PCxABVxAM6tmPYL5kIhCiuD2Dk9BewA6ihz0rUPHz5+Y6AiivJ9Angej9WOC1FYILIOjiPiWhcADQXS+/Dhw4cKHAH+FrFX0HNqfaEKiyzzI4jg2KWAs+qYDx8+fLjj18D/BB6nwOMFi1FYsKC0eoCLgJYi+fjw4eM3CweB24AXgYL3axSrsCC7jxixSXoDsLpEfj58+HjrIgH8GBFgP4xzc4cnlKpgVMTq4S8R8ax2fBfRhw8fC1CBAeDvgc9k3xeNcllEEwgTrx9YD7gf1eHDh4/fJDyFCK5/A6EnSkI5Xbg4Iph2FGHudQI1ZeTvw4ePNw/6ga8Dn0MYM2UpsrUYeVQBRDb85cBHgSuAQs5O8eHDx5sXMURpqq8gjpfPc2JCYVjMxM8AoqrKtcAnge2IDHn34299+PDxZkQSoZheAB5AxLTTeEwGLQRLlaleDdwA3IhIg1iXbfPhw8ebF9OI/X/7gX/BdmDEYmCpt9bUIxTWu4D3IqyutfhWlw8fbxYkEfmXvwJeQiirQ4jdL4uO87kXcF32bwuwE6HIuvEtLx8+lhsmEErpIHAAsaf4FKIYwpJiOWxeDiEsr9pAINAYiUQ2hUKh7YFAYKOiKGsVRelEBPGrgYiiKF6KDvrw4cMjNE1TEYmdMWBM07RhTdP6VVU9mUqljsTj8RMICyqW/VuyIwDt+P/9JGiKxnQFFwAAAABJRU5ErkJggg%3D%3D">
				    </a>
				    <br>
				    Get more custom plugins <br/> for WooCommerce <br/> and/or custom development
				    <br><br>
				    <a title=" More Extensions + Custom WooCommerce Site Development " href="http://ignitewoo.com" target="_blank" style="color:#0000cc; text-decoration:none">Contact us at<br>IgniteWoo.com</a>
			    </div>
		    </td>

		    </tr>
		    </table>
		</div>


		<?php
	}


	function save_settings() {

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_filter_products'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_filter_products', $_POST['woocommerce_wholesale_pricing_plus_filter_products'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_filter_products', '' );

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_retail_filter_products'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_retail_filter_products', $_POST['woocommerce_wholesale_pricing_plus_retail_filter_products'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_retail_filter_products', '' );

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_alt_filter'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_alt_filter', $_POST['woocommerce_wholesale_pricing_plus_alt_filter'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_alt_filter', '' );
			
		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_disable_coupons'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_disable_coupons', $_POST['woocommerce_wholesale_pricing_plus_disable_coupons'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_disable_coupons', '' );

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_disable_coupons_msg'] ) && '' != trim( $_POST['woocommerce_wholesale_pricing_plus_disable_coupons_msg'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_disable_coupons_msg', $_POST['woocommerce_wholesale_pricing_plus_disable_coupons_msg'] );
		else { 
			$this->disable_coupons_error_msg = __( 'As a wholesale buyer you may not use coupon codes.', 'ignitewoo_wholesale' );
			update_option( 'woocommerce_wholesale_pricing_plus_disable_coupons_msg', $this->disable_coupons_error_msg );
		}

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_display_when_no_retail'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_display_when_no_retail', $_POST['woocommerce_wholesale_pricing_plus_display_when_no_retail'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_display_when_no_retail', '' );

		if ( isset( $_POST['woocommerce_wholesale_pricing_plus_disable_tax'] ) )
			update_option( 'woocommerce_wholesale_pricing_plus_disable_tax', $_POST['woocommerce_wholesale_pricing_plus_disable_tax'] );
		else
			update_option( 'woocommerce_wholesale_pricing_plus_disable_tax', '' );


		if ( isset( $_POST['pricing_rules'] ) ) {
		
			$pricing_rule_sets = $_POST['pricing_rules'];

			foreach ( $pricing_rule_sets as $key => $rule_set ) {

				// Convert 1,2,3 to array 
				if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { 
					foreach( $rule_set['conditions'] as $index => $vals ) { 
						if ( !empty( $vals['args']['users'] ) ) {
							$vals['args']['users'] = explode( ',', $vals['args']['users'] );
							$rule_set['conditions'][ $index ] = $vals;
						}
					}
				}
				
				$valid = true;

				foreach ( $rule_set['rules'] as $rule ) {

				    if (isset( $rule['to'] ) && isset( $rule['from'] ) && isset( $rule['amount'] ) )
					    $valid = $valid & true;
				    else
					    $valid = $valid & false;

				}

				if ( $valid )
				    $valid_rules[$key] = $rule_set;
				
			}

			update_option( '_woocommerce_wholesale_sitewide_rules', $valid_rules );
			
		} else {
		
			update_option( '_woocommerce_wholesale_sitewide_rules', '' );
		}
		
			
		$this->load_settings();
		
	}


	function disable_coupons( $valid, $obj ) { 

		// are retail coupons disabled for wholesale buyers? 
		if ( 'yes' != $this->disable_coupons ) 
			return $valid;

		if ( current_user_can( 'wholesale_buyer' ) ) 
			return false;

		return $valid;

	}


	function disable_coupons_error_msg( $error, $obj ) { 

		if ( 'yes' != $this->disable_coupons ) 
			return $error;

		if ( !current_user_can( 'wholesale_buyer' ) ) 
			return $error;

		$error = $this->disable_coupons_error_msg;

		return $error;

	}
	
	// Intercept WooCommerce's function hook and disabled variations that don't have a wholesale price
	// if the filter_products option is turned on.
	function woocommerce_variable_add_to_cart() {
		global $product;

		if ( !current_user_can( 'wholesale_buyer' ) || !is_user_logged_in() ) {
			add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			return;
		}
		
		// Enqueue variation scripts
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		$variations = $product->get_available_variations();

		$attrs = $product->get_variation_attributes();

		$sattrs = $product->get_variation_default_attributes();
		 
		foreach( $variations as $k => $v ) { 
			
			$wprice = get_post_meta( $v['variation_id'], 'wholesale_price', true );
				
			if ( empty( $wprice ) ) { 
			
				$variations[ $k ]['is_in_stock'] = false;
				$variations[ $k ]['price_html'] = '<span class="price">' . __( 'Unavailable', 'ignitewoo_wholesale' ) . '</span>';
 			
			}
		}
		
		
		// Load the template
		woocommerce_get_template( 'single-product/add-to-cart/variable.php', array(
				'available_variations'  => $variations,
				'attributes'   		=> $attrs,
				'selected_attributes' 	=> $sattrs
			) );
	}
	

	function is_purchasable( $purchasable, $obj ) { 

		if ( 'yes' != $this->display_when_no_retail ) 
			return $purchasable;

		if ( !current_user_can( 'wholesale_buyer' ) ) 
			return $purchasable;

		if ( '' == trim( get_post_meta( $obj->id, 'wholesale_price', true ) ) ) 
			return $purchasable;

		return true; 

	}


	function empty_price_html( $foobar, $obj ) { 

		if ( !class_exists( 'woocommerce_wholesale_pricing' ) )
			return;
	 
		remove_filter( 'woocommerce_empty_price_html', array( &$this, 'empty_price_html' ), 999999999 );

		$out = woocommerce_wholesale_pricing::maybe_return_wholesale_price( '', $obj );

		add_filter( 'woocommerce_empty_price_html', array( &$this, 'empty_price_html' ), 999999999, 2 );

		return $out;
	}


	function sitewide_discount_rule_box() {

		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) { 
		?>
		
		<script>
		jQuery( document ).ready( function() { 
			
			jQuery( '#woocommerce-wholesale-sitewide-pricing-rules-wrap' ).bind( 'DOMSubtreeModified', function() { 
				setTimeout( function() { 
					jQuery( '.chosen' ).chosen();	
					}, 500 );
			})
		});
		</script>
		
		<?php } else {  ?>
		
		<script>
		jQuery( document ).ready( function($) { 
		
			function wholesale_users_format_string() {
				var formatString = {
					formatMatches: function( matches ) {
						if ( 1 === matches ) {
							return wc_enhanced_select_params.i18n_matches_1;
						}

						return wc_enhanced_select_params.i18n_matches_n.replace( '%qty%', matches );
					},
					formatNoMatches: function() {
						return wc_enhanced_select_params.i18n_no_matches;
					},
					formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
						return wc_enhanced_select_params.i18n_ajax_error;
					},
					formatInputTooShort: function( input, min ) {
						var number = min - input.length;

						if ( 1 === number ) {
							return wc_enhanced_select_params.i18n_input_too_short_1;
						}

						return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', number );
					},
					formatInputTooLong: function( input, max ) {
						var number = input.length - max;

						if ( 1 === number ) {
							return wc_enhanced_select_params.i18n_input_too_long_1;
						}

						return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', number );
					},
					formatSelectionTooBig: function( limit ) {
						if ( 1 === limit ) {
							return wc_enhanced_select_params.i18n_selection_too_long_1;
						}

						return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', limit );
					},
					formatLoadMore: function( pageNumber ) {
						return wc_enhanced_select_params.i18n_load_more;
					},
					formatSearching: function() {
						return wc_enhanced_select_params.i18n_searching;
					}
				};

				return formatString;
			}
			
			function wc_wholesale_pricing_cost_users_select() { 

				$( 'input.wholesale_plus_rule_users' ).filter( ':not(.enhanced)' ).each( function() {
					var ign_select2_args = {
						allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url: wc_enhanced_select_params.ajax_url,
							dataType: 'json',
							quietMillis: 250,
							data: function( term, page ) {
								return {
									term: term,
									action: 'woocommerce_json_search_customers',
									security: wc_enhanced_select_params.search_customers_nonce
								};
							},
							results: function( data, page ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push( { id: id, text: text } );
									});
								}
								return { results: terms };
							},
							cache: true
						}
					};
					
					if ( $( this ).data( 'multiple' ) === true ) {
						ign_select2_args.multiple = true;
						ign_select2_args.initSelection = function( element, callback ) {
							var data     = $.parseJSON( element.attr( 'data-selected' ) );
							var selected = [];

							$( element.val().split( "," ) ).each( function( i, val ) {
								selected.push( { id: val, text: data[ val ] } );
							});

							return callback( selected );
						};

						ign_select2_args.formatSelection = function( data ) {
							return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
						};
					} 
					
					ign_select2_args = $.extend( ign_select2_args, wholesale_users_format_string() );
					
					$( this ).select2( ign_select2_args ).addClass( 'enhanced' );
				
				})
			}
			
			
			
			jQuery( '#woocommerce-wholesale-sitewide-pricing-rules-wrap' ).bind( 'DOMSubtreeModified', function() { 
				setTimeout( function() { 
					jQuery( 'select.chosen' ).not( '.enhanced' ).addClass('enhanced').select2({});
					wc_wholesale_pricing_cost_users_select();
					}, 500 );
			})
		});
		</script>
		
		<?php } ?>
		
		<div id="woocommerce-pricing-category">

			<?php //settings_fields('_woocommerce_wholesale_sitewide_rules'); ?>

			<?php $pricing_rule_sets = get_option('_woocommerce_wholesale_sitewide_rules', array()); ?>

			<div id="woocommerce-wholesale-sitewide-pricing-rules-wrap" class="inside" data-setindex="<?php echo count($pricing_rule_sets) - 1; ?>">

				<?php $this->meta_box_javascript(); ?>

				<?php $this->meta_box_css(); ?>  

				<?php if ($pricing_rule_sets && is_array($pricing_rule_sets) && sizeof($pricing_rule_sets) > 0) : ?>
					<?php $this->create_rulesets($pricing_rule_sets); ?>
				<?php endif; ?>        

			</div>

			<button id="woocommerce-wholesale-pricing-add-ruleset" type="button" class="button button-secondary"> <?php _e( 'Add New Ruleset', 'ignitewoo_wholesale' )?></button>

		</div>
		<?php
        }

	function create_rulesets($pricing_rule_sets, $index_offset = 0 ) {

		$i = $index_offset;

		foreach ( $pricing_rule_sets as $set_name => $pricing_rule_set ) {

			$name = 'set_' . $i; 
			
			$index_offset++;
			
			$pricing_rules = isset($pricing_rule_set['rules']) ? $pricing_rule_set['rules'] : null;
			$pricing_conditions = isset($pricing_rule_set['conditions']) ? $pricing_rule_set['conditions'] : null;
			$collector = isset($pricing_rule_set['collector']) ? $pricing_rule_set['collector'] : null;

			$invalid = isset($pricing_rule_set['invalid']);
			$validation_class = $invalid ? 'invalid' : '';

			?>
			<div id="woocommerce-wholesale-sitewide-pricing-ruleset-<?php echo $name; ?>" class="woocommerce_wholesale_sitewide_pricing_ruleset <?php echo $validation_class; ?>">

				<h4 class="first">Sitewide Wholesale Ruleset<a href="#" data-name="<?php echo $name; ?>" class="delete_pricing_ruleset" ><img  src="<?php echo $this->plugin_url; ?>/assets/images/delete.png" title="delete this set" alt="delete this set" style="cursor:pointer; margin:0 3px;float:right;" /></a></h4>    


				<div id="woocommerce-pricing-collector-<?php echo $name; ?>" class="section" style="" >
					<?php
					if (is_array($collector) && count($collector) > 0) {
						$this->create_collector($collector, $name);
					} else {
						$product_cats = array();
						$this->create_collector(array('type' => 'cat', 'args' => array('cats' => $product_cats)), $name);
					}
					?>
				</div>


				<div id="woocommerce-pricing-conditions-<?php echo $name; ?>" class="section">
				<?php
				if ( empty( $index_offset ) )
					$condition_index = 0;
				else 
					$condition_index = $index_offset;
					
				if (is_array($pricing_conditions) && sizeof($pricing_conditions) > 0):
					?>
					<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" />
					<?php

					foreach ($pricing_conditions as $condition) :
					$condition_index++;
					
					$this->create_condition($condition, $name, $condition_index);
					endforeach;
				else :
					?>
					<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" />
					<?php
					$this->create_condition(array('type' => 'apply_to', 'args' => array('applies_to' => 'everyone', 'roles' => array('wholesale_buyer'))), $name, 1 );
				endif;
				?>
				</div>

				<div class="section">
				<table id="woocommerce-wholesale-sitewide-pricing-rules-table-<?php echo $name; ?>" data-lastindex="<?php echo (is_array($pricing_rules) && sizeof($pricing_rules) > 0) ? count($pricing_rules) : '1'; ?>">
					<thead>
					<th>
					<?php _e('Minimum Quantity', 'ignitewoo_wholesale' ); ?>
					</th>
					<th>
					<?php _e('Max Quantity', 'ignitewoo_wholesale'); ?>
					</th>
					<th>
					<?php _e('Type', 'ignitewoo_wholesale'); ?>
					</th>
					<th>
					<?php _e('Amount', 'ignitewoo_wholesale'); ?>
					</th>
					<th>&nbsp;</th>
					</thead>
					<tbody>
					<?php
					$index = 0;
					if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0) {
						foreach ( $pricing_rules as $rule ) {
							$index++;
							$this->get_row( $rule, $name, $index );
						}
					} else {
						$this->get_row( array( 'to' => '', 'from' => '', 'amount' => '', 'type' => '' ), $name, 1 );
					}
					?>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
				</div>
			</div><?php
			
			$i++;
		    }
        }

	function create_new_ruleset() {

		$set_index = $_POST['set_index'];

		$pricing_rule_sets = array();

		$pricing_rule_sets['set_' . $set_index] = array();

		$pricing_rule_sets['set_' . $set_index]['title'] = 'Rule Set ' . $set_index;

		$pricing_rule_sets['set_' . $set_index]['rules'] = array();

		$this->create_rulesets( $pricing_rule_sets, $set_index );

		die;
	}


	function create_condition($condition, $name, $condition_index) {
		global $wp_roles;

		switch ( $condition['type'] ) {

		    case 'apply_to':
			$this->create_condition_apply_to( $condition, $name, $condition_index );
			break;

		    default:
			break;
		}
	}


	function create_condition_apply_to( $condition, $name, $condition_index ) {
		global $wpdb;

		if ( !isset( $wp_roles ) )
		    $wp_roles = new WP_Roles();

		$all_roles = $wp_roles->roles;
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
		
			$sql = '
				SELECT DISTINCT ID, user_login, user_email, m1.meta_value AS fname, m2.meta_value AS lname
				FROM ' . $wpdb->users . '
				LEFT JOIN ' . $wpdb->usermeta . ' m1 ON ID = m1.user_id
				LEFT JOIN ' . $wpdb->usermeta . ' m2 ON ID = m2.user_id
				WHERE 
				m1.meta_key = "billing_first_name"
				AND
				m2.meta_key = "billing_last_name"
				ORDER BY m2.meta_value ASC 
			';

			$all_users = $wpdb->get_results( $sql );
		}

		$div_style = ( $condition['args']['applies_to'] != 'roles' ) ? 'display:none;' : '';
		
		?>
		<style>
		.chosen-container-multi { width: 200px !important; }
		</style>
		<div>
			<label for="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>"><?php _e( 'Applies To:', 'ignitewoo_wholesale'); ?></label>

			<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][type]" value="apply_to" />
			
			<?php /*
			<input type="hidden" value="roles" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][applies_to]" >
			
			<input type="hidden" id="<?php echo $name; ?>_role_<?php echo $role_id; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" value="wholesale_buyer" />
			*/ ?>
			
			<select class="chosen pricing_rule_apply_to" id="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][applies_to]" style="width: 200px !important">
				<option <?php selected('everyone', $condition['args']['applies_to']); ?> value="everyone">Everyone</option>
				<option <?php selected('roles', $condition['args']['applies_to']); ?> value="roles"><?php _e( 'Specific Roles', 'ignitewoo_wholesale');?></option>
				<option <?php selected( 'users', $condition['args']['applies_to'] ); ?> value="users"><?php _e( 'Specific Users', 'ignitewoo_wholesale'); ?></option>
			</select>

			<div class="roles" style="<?php echo $div_style; ?> margin-top: 10px">
				<?php $chunks = array_chunk($all_roles, ceil(count($all_roles) / 3), true); ?>

				<p class="description"><?php _e( 'Click in the box and start typing a role name to select roles. Add as many as you need.', 'ignitewoo_wholesale' )?></p>
				
				<select class="chosen wholesale_plus_rule_roles" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" style="width: 200px !important">
				
				<?php foreach ($chunks as $chunk) : ?>

					<?php foreach ($chunk as $role_id => $role) : ?>
					
					<?php $role_checked = (isset($condition['args']['roles']) && is_array($condition['args']['roles']) && in_array($role_id, $condition['args']['roles'])) ? 'selected="selected"' : ''; ?>
					
					<option <?php echo $role_checked ?> value="<?php echo $role_id; ?>">
							<?php echo $role['name']; ?>
					</option>

					<?php endforeach; ?>
				
				<?php endforeach; ?>
				</select>
			</div>
			
			<div style="clear:both;"></div>

			<?php $div_style = ( $condition['args']['applies_to'] != 'users' ) ? 'display:none;' : ''; ?>
			
			<div class="users" style="clear: both; z-index: 999999; <?php echo $div_style; ?> ">

				<p class="description"><?php _e( 'Click in the box and start typing a name to select users. Add as many as you need.', 'ignitewoo_wholesale' )?></p>

				<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) { ?>
				
					<select class="chosen wholesale_plus_rule_users" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][users][]" style="min-width:380px">

						<?php foreach( $all_users as $key => $data ) { ?>

							<?php $user_selected = (isset( $condition['args']['users'] ) && is_array( $condition['args']['users'] ) && in_array( $data->ID, $condition['args']['users'] )) ? 'selected="selected"' : ''; ?>

							<option <?php echo $user_selected ?> value="<?php echo $data->ID ?>">
								<?php echo $data->lname . ', ' . $data->fname . ' &mdash; ' . $data->user_email; ?>
							</option>

						<?php } ?>

					</select>
				
				<?php } else if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) {

					$ru =  isset( $condition['args']['users'] ) ? $condition['args']['users'] : array();

					$user_strings = array();
					$user_id = '';

					if ( isset( $ru ) && !empty( $ru ) ) {

						foreach( $ru as $pu ) { 
							if ( empty( $pu ) )
								continue; 
							$user = get_user_by( 'id', $pu);
							
							$user_strings[ $pu ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
						}
					}

					if ( empty( $ru ) )
						$ru = array();
					?>
					<br/>
					<input type="hidden" class="chosen wholesale_plus_rule_users" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][users]" data-placeholder="<?php _e( 'Search for users&hellip;', 'ignitewoo-bookings' ); ?>" data-selected='<?php echo esc_attr( json_encode( $user_strings ) ) ?>' value="<?php echo implode( ',', $ru ) ?>" data-multiple="true" data-allow_clear="true" data-action="woocommerce_json_search_customers" style="width:380px;min-height:32px"/>
				
				<?php } ?>

			</div>
		
			<div class="clear"></div>

		
		</div>
		<?php
	}


        function create_collector($collector, $name) {
		$terms = (array) get_terms('product_cat', array('get' => 'all'));
		?>
		<label for="pricing_rule_when_<?php echo $name; ?>"><?php _e('Quantities based on:', 'ignitewoo_wholesale'); ?></label>
		
		<select title="Choose how to calculate the quantity.  This tallied amount is used in determining the min and max quantities used below in the Quantity Pricing section." class="chosen wholesale_pricing_rule_when" id="pricing_rule_when_<?php echo $name; ?>" name="pricing_rules[<?php echo $name; ?>][collector][type]">
		
			<option title="Calculate quantity based on cart item quantity" <?php selected('cat_product', $collector['type']); ?> value="cart_item"><?php _e('Cart Line Item Quantity', 'ignitewoo_wholesale'); ?></option>
			
			<option title="Calculate quantity based on total sum of the categories in the cart" <?php selected('cat', $collector['type']); ?> value="cat"><?php _e('Sum of of Category', 'ignitewoo_wholesale'); ?></option>
			
		</select>
		
		<div class="cats">   
			<label style="margin-top:10px;">Categories:</label>

			<?php $chunks = array_chunk($terms, ceil(count($terms) / 3)); ?>
				
			<select class="chosen wholesale_plus_rule_cats" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][collector][args][cats][]" >
						
			<?php foreach ($chunks as $chunk) : ?>

				<?php foreach ($chunk as $term) : ?>
				
					<?php $term_checked = (isset($collector['args']['cats']) && is_array($collector['args']['cats']) && in_array($term->term_id, $collector['args']['cats'])) ? 'selected="selected"' : ''; ?> 
					
					<option value="<?php echo $term->term_id; ?>" <?php echo $term_checked; ?>><?php echo $term->name; ?> </option>
					
					
				<?php endforeach; ?>

				
			<?php endforeach; ?>
			
			</select>
			
			<div style="clear:both;"></div>
		</div>
		<?php
        }


        function get_row($rule, $name, $index ) {
		?>
		<tr id="pricing_rule_row_<?php echo $name . '_' . $index; ?>">
		    <td>
				<input class="int_pricing_rule" id="pricing_rule_from_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][from]" value="<?php echo $rule['from']; ?>" />
		    </td>
		    <td>
			    <input class="int_pricing_rule" id="pricing_rule_to_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][to]" value="<?php echo $rule['to']; ?>" />
		    </td>
		    <td>
			    <select id="pricing_rule_type_value_<?php echo $name . '_' . $index; ?>" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][type]" class="chosen">
				    <option <?php selected('price_discount', $rule['type']); ?> value="price_discount">Price Discount</option>
				    <option <?php selected('percentage_discount', $rule['type']); ?> value="percentage_discount">Percentage Discount</option>
				    <option <?php selected('fixed_price', $rule['type']); ?> value="fixed_price">Fixed Price</option>
			    </select>
		    </td>
		    <td>
			<input class="float_rule_pricing" id="pricing_rule_amount_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][amount]" value="<?php echo $rule['amount']; ?>" /> 
		    </td>
		    <td><a class="add_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img 
				src="<?php echo $this->plugin_url . '/assets/images/add.png'; ?>" 
				title="add another rule" alt="add another rule" 
				style="cursor:pointer; margin:0 3px;" /></a><a <?php echo ($index > 1) ? '' : 'style="display:none;"'; ?> class="delete_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img 
				src="<?php echo $this->plugin_url . '/assets/images/remove.png'; ?>" 
				title="add another rule" alt="add another rule" 
				style="cursor:pointer; margin:0 3px;" /></a>
		    </td>
		</tr>
		<?php
        }


	function meta_box_javascript() {
		?>
		<script type="text/javascript">
																																
		    jQuery(document).ready(function($) {
		    
			var set_index = 0;
			var rule_indexes = new Array();

			$('.woocommerce_wholesale_sitewide_pricing_ruleset').each(function(){
				var length = $('table tbody tr', $(this)).length;
				if (length==1) {
					$('.delete_pricing_rule', $(this)).hide(); 
				}
			});

			$("#woocommerce-wholesale-pricing-add-ruleset").click(function(event) {
				event.preventDefault();

				var set_index = $("#woocommerce-wholesale-sitewide-pricing-rules-wrap").data('setindex') + 1;

				$("#woocommerce-wholesale-sitewide-pricing-rules-wrap").data('setindex', set_index );
																																
				var data = {
					set_index:set_index,
					post:<?php echo isset($_GET['post']) ? $_GET['post'] : 0; ?>,
					action:'ignitewoo_wholesale_create_new_ruleset'
				}

				$.post(ajaxurl, data, function(response) { 
					$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').append(response);
				}); 
				
			});

			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.pricing_rule_apply_to', 'change', function(event) {  
				var value = $(this).val();

				if (value != 'roles' && value != 'users' ) {
				    $( '.users', $(this).parent()).hide();
				    $( '.users input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );
				    $( '.roles', $(this).parent()).hide();
				    $( '.roles input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );
				} else if ( value == 'roles' ) {
				    $( '.users', $(this).parent()).hide();
				    $( '.users input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );
				    $( '.roles', $(this).parent()).fadeIn();
				} else if ( value == 'users' ) { 
				    $( '.roles', $(this).parent()).hide();
				    $( '.roles input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );
				    $( '.users', $(this).parent()).fadeIn();                               
				}                                                             
			});

			$( '#woocommerce-wholesale-sitewide-pricing-rules-wrap' ).delegate( '.wholesale_pricing_rule_when', 'change', function(event) {  

			    var value = $(this).val();

			    if (value != 'cat' ) {
				$( '.cats', $(this).closest( 'div' )).fadeOut();
				$( '.cats input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );

			    } else {                                                            
				$( '.cats', $(this).closest( 'div' )).fadeIn();
			    }                                                              
			});
			

			$('.wholesale_pricing_rule_when').change();
			
			//Remove Pricing Set
			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.delete_pricing_ruleset', 'click', function(event) {  
				event.preventDefault();
				DeleteRuleSet( $(this).data('name') );
			});

			//Add Button
			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.add_pricing_rule', 'click', function(event) {  
				event.preventDefault();
				InsertRule( $(this).data('index'), $(this).data('name') );
			});

			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.delete_pricing_rule', 'click', function(event) {  
				event.preventDefault();
				DeleteRule($(this).data('index'), $(this).data('name'));
			});

			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.int_pricing_rule', 'keydown', function(event) {  
				// Allow only backspace, delete and tab
				if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 ) {
				    // let it happen, don't do anything
				}
				else {
				    if (event.shiftKey && event.keyCode == 56){
					if ( $(this).val().length > 0) {
					    event.preventDefault();
					} else {
					    return true;    
					}
				    }else if (event.shiftKey){
					event.preventDefault();
				    } else if ( (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 ) ) {
					event.preventDefault(); 
				    } else {
					if ( $(this).val() == "*") {
						event.preventDefault();
					}
				    }
				}
			});

			$('#woocommerce-wholesale-sitewide-pricing-rules-wrap').delegate('.float_rule_pricing', 'keydown', function(event) {  
				// Allow only backspace, delete and tab
				if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190 ) {
				    // let it happen, don't do anything
				}
				else {
				    if (event.shiftKey && event.keyCode == 56){
					if ( $(this).val().length > 0) {
					    event.preventDefault();
					} else {
					    return true;    
					}
				    }else if (event.shiftKey){
					event.preventDefault();
				    } else if ( (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 ) ) {
					event.preventDefault(); 
				    } else {
					if ( $(this).val() == "*") {
						event.preventDefault();
					}
				    }
				}
			});

			$("#woocommerce-wholesale-sitewide-pricing-rules-wrap").sortable(
			{ 
			    handle: 'h4.first',
			    containment: 'parent',
			    axis:'y'
			});

			function InsertRule(previousRowIndex, name) {

				var $index = $("#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).data('lastindex') + 1;

				$("#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).data('lastindex', $index );

				var html = '';
				html += '<tr id="pricing_rule_row_' + name + '_' + $index + '">';
				html += '<td>';
				html += '<input class="int_pricing_rule" id="pricing_rule_from_input_'  + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][from]" value="" /> ';
				html += '</td>';
				html += '<td>';
				html += '<input class="int_pricing_rule" id="pricing_rule_to_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][to]" value="" /> ';
				html += '</td>';
				html += '<td>';
				html += '<select id="pricing_rule_type_value_' + name + '_' + $index + '" name="pricing_rules[' + name + '][rules][' + $index + '][type]" class="chosen">';
				html += '<option value="price_discount">Price Discount</option>';
				html += '<option value="percentage_discount">Percentage Discount</option>';
				html += '<option value="fixed_price">Fixed Price</option>';
				html += '</select>';
				html += '</td>';
				html += '<td>';
				html += '<input class="float_pricing_rule" id="pricing_rule_amount_input_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][amount]" value="" /> ';
				html += '</td>';
				html += '<td>';
				html += '<a data-index="' + $index + '" data-name="' + name + '" class="add_pricing_rule"><img  src="<?php echo $this->plugin_url . '/assets/images/add.png'; ?>" title="add another rule" alt="add another rule" style="cursor:pointer; margin:0 3px;" /></a>';         
				html += '<a data-index="' + $index + '" data-name="' + name + '" class="delete_pricing_rule"><img data-index="' + $index + '" src="<?php echo $this->plugin_url . '/assets/images/remove.png'; ?>" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;" /></a>';         
				html += '</td>';
				html += '</tr>';
																																
				$('#pricing_rule_row_' + name + '_' + previousRowIndex).after(html);
				$('.delete_pricing_rule', "#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).show();

			} 

			function DeleteRule(index, name) {
				if (confirm("Are you sure you would like to remove this price adjustment?")) {
					$('#pricing_rule_row_' + name + '_' + index).remove();
																																
					var $index = $('tbody tr', "#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).length;
					if ($index > 1) {
					    $('.delete_pricing_rule', "#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).show();
					} else {
					    $('.delete_pricing_rule', "#woocommerce-wholesale-sitewide-pricing-rules-table-" + name).hide();
					}
				}
			}

			function DeleteRuleSet(name) {
				if (confirm('Are you sure you would like to remove this dynamic price set?')){
					$('#woocommerce-wholesale-sitewide-pricing-ruleset-' + name ).slideUp().remove();  
				}
			}
																																
		    });
																																
		</script>
		<?php
        }


        function meta_box_css() {
		?>
		<style>
		    #woocommerce-pricing-category div.section {
			margin-bottom: 10px;
		    }

		    #woocommerce-pricing-category label {
			display:block;
			font-weight: bold;
			margin-bottom:5px;
		    }

		    #woocommerce-pricing-category .list-column {
			float:left;
			margin-right:25px;
			margin-top:0px;
			margin-bottom: 0px;
		    }

		    #woocommerce-pricing-category .list-column label {
			margin-bottom:0px;
		    }

		    #woocommerce-wholesale-sitewide-pricing-rules-wrap {
			margin:10px;
		    }

		    #woocommerce-wholesale-sitewide-pricing-rules-wrap h4 {
			border-bottom: 1px solid #E5E5E5;
			padding-bottom: 6px;
			font-size: 1em;
			margin: 1em 0 1em;
		    }

		    #woocommerce-wholesale-sitewide-pricing-rules-wrap h4.first {
			margin-top:0px;
			cursor:move;
		    }

		    .woocommerce_wholesale_sitewide_pricing_ruleset {

			border-color:#dfdfdf;
			border-width:1px;
			border-style:solid;
			-moz-border-radius:3px;
			-khtml-border-radius:3px;
			-webkit-border-radius:3px;
			border-radius:3px;
			padding: 10px;
			border-style:solid;
			border-spacing:0;
			background-color:#F9F9F9;
			margin-bottom: 25px;
		    }

		    .woocommerce_wholesale_sitewide_pricing_ruleset.invalid {
			border-color:#EACBCC;
			background-color:#FFDFDF;
		    }

		    .woocommerce_wholesale_sitewide_pricing_ruleset th {
			background-color: #efefef;
		    }

		    .woocommerce_wholesale_sitewide_pricing_ruleset .selectit {
			font-weight: normal !important;
		    }
		    .woocommerce_wholesale_sitewide_pricing_ruleset .chzn-choices, .woocommerce_wholesale_sitewide_pricing_ruleset .chzn-drop, .chzn-choices .search-field input {
			min-width: 300px !important;
		    }
		    
		</style>
		<?php	
        }


        function selected($value, $compare, $arg=true) {
		if (!$arg) {
		    echo '';
		} else if ((string) $value == (string) $compare) {
		    echo 'selected="selected"';
		}
        }


}


add_action( 'plugins_loaded', 'ignite_wpp_init', -99 ); 

function ignite_wpp_init() { 

	global $ignitewoo_wholesale_pricing_plus, $wc_wholesale_filter_products; 

	$ignitewoo_wholesale_pricing_plus = new IgniteWoo_Wholesale_Pricing_Plus();

	$wc_wholesale_filter_products = new WC_Wholesale_Filter_Products();

}


class WC_Wholesale_Filter_Products { 

	/** 
	    Filter products so that only those with wholesale prices appear in the store
	    if the logged in user is a wholesale buyer
	*/

	var $doing_shortcode = false;
	
	function __construct() { 
		global $ignitewoo_wholesale_pricing_plus; 

		
		add_shortcode( 'recent_products_plus', array( $this, 'recent_products' ) );
		
		add_shortcode( 'featured_products_plus', array( $this, 'featured_products' ) );
		
		add_shortcode( 'sale_products_plus', array( $this, 'sale_products' ) );
		
		add_shortcode( 'best_selling_products_plus', array( $this, 'best_selling_products' ) );
		
		add_shortcode( 'top_rated_products_plus', array( $this, 'top_rated_products' ) );
		
		add_shortcode( 'related_products_plus', array( $this, 'related_products' ) );
		
		if ( is_admin() ) 
			return;

		if ( 'yes' != $ignitewoo_wholesale_pricing_plus->filter_products && 'yes' != $ignitewoo_wholesale_pricing_plus->retail_filter_products )
			return;


		//if ( current_user_can( 'administrator' ) )
		//	return;

		if ( !empty( $ignitewoo_wholesale_pricing_plus->alt_filter ) && 'yes' == $ignitewoo_wholesale_pricing_plus->alt_filter ) { 

			add_filter( 'loop_shop_post_in', array( $this, 'product_filter' ) );
			add_filter( 'the_posts', array( $this, 'the_posts' ), 11, 2 );
		
		} else { 
			add_filter( 'posts_join', array( &$this, 'woocom_wholesale_join' )  );
			add_filter( 'posts_where', array( &$this, 'posts_where' ) );
		}
		
	}


	function recent_products( $atts = array() ) { 

		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}		

		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[recent_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
	
	
	function featured_products( $atts = array() ) { 
	
		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}	
		
		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[featured_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
	
	
	function sale_products( $atts = array() ) { 
	
		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}	
		
		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[sale_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
	
	
	function best_selling_products( $atts = array() ) { 
	
		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}	
		
		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[best_selling_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
	
	
	function top_rated_products( $atts = array() ) { 
	
		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}	
		
		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[top_rated_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
		
	
	function related_products( $atts = array() ) { 
	
		$attribs = '';
		
		if ( count( $atts ) > 0 ) { 
		
			foreach( $atts as $name => $val ) 
				$attribs[] = $name . '= "' . $val . '"'; 
				
			$attribs = implode( ' ', $attribs );
		
		}	
		
		$this->doing_shortcode = true;
		
		$out = do_shortcode( '[related_products ' . $attribs . ']' );
		
		$this->doing_shortcode = false;
		
		return $out;
		
	}
	
	// Filter search results
	public function the_posts( $posts, $query = false ) {
	
		// Abort if there's no query
		if ( ! $query )
			return $posts;

		$post__in = array_unique( apply_filters( 'loop_shop_post_in', array() ) );

		// Abort if we're not filtering posts
		if ( empty( $post__in ) )
			return $posts;

		// Abort if this query has already been done
		if ( ! empty( $query->wc_query ) )
			return $posts;

		// Abort if this isn't a search query
		if ( empty( $query->query_vars["s"] ) )
			return $posts;

		// Abort if we're not on a post type archive/product taxonomy
		//if 	( ! $query->is_post_type_archive( 'product' ) && ! $query->is_tax( get_object_taxonomies( 'product' //) ) )
		//	return $posts;

		$filtered_posts = array();
		
		$queried_post_ids = array();

		foreach ( $posts as $post ) {
			if ( in_array( $post->ID, $post__in ) ) {
				$filtered_posts[] = $post;
				$queried_post_ids[] = $post->ID;
			}
		}

		$query->posts = $filtered_posts;
		
		$query->post_count = count( $filtered_posts );

		// Ensure filters are set
		$this->unfiltered_product_ids = $queried_post_ids;
		
		$this->filtered_product_ids = $queried_post_ids;
		
		/*
		$this->layered_nav_product_ids = null;

		if ( sizeof( $this->layered_nav_post__in ) > 0 ) {
			$this->layered_nav_product_ids = array_intersect( $this->unfiltered_product_ids, $this->layered_nav_post__in );
		} else {
			$this->layered_nav_product_ids = $this->unfiltered_product_ids;
		}
		*/
		
		return $filtered_posts;
	}

	// Filter products in the shop - does not affect admins and shop managers
	// Runs via a WooCommerce hook "loop_shop_post_in"
	function product_filter( $matched_products = null ) {
		global $wpdb, $ignitewoo_wholesale_pricing_plus, $current_user;
		
		if ( empty( $current_user ) )
			$current_user = get_currentuserinfo();
			
		$wholesaler = false;
		
		$tiered_role = false;
		
		if ( !empty( $current_user->roles ) ) {
			foreach( $current_user->roles as $role ) {
				if ( 'wholesale_' == substr( $role, 0, 10 ) )
					$wholesaler = true;
				if ( 'ignite_level_' == substr( $role, 0, 13 ) )
					$tiered_role = $role;
			}
		}
	
		if ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) {
		
			return $matched_products;
		
		} else if ( $wholesaler && 'yes' == $ignitewoo_wholesale_pricing_plus->filter_products ) { 
		
			$sql = "SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta ON ID = post_id
				WHERE post_type IN ( 'product', 'product_variation' ) 
				AND post_status = 'publish' 
				AND ( meta_key = 'wholesale_price' AND meta_value != '' )
			";
		
		} else if ( $tiered && 'yes' == $ignitewoo_wholesale_pricing_plus->filter_products ) { 
		
			$sql = "SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta ON ID = post_id
				WHERE post_type IN ( 'product', 'product_variation' ) 
				AND post_status = 'publish' 
				AND ( meta_key = '_' . $tiered_role . '_price' AND meta_value != '' )
			";
			
		} else if ( 'yes' == $ignitewoo_wholesale_pricing_plus->retail_filter_products ) { 
		
			$sql = "SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta m1 ON ID = m1.post_id
				INNER JOIN $wpdb->postmeta m2 ON ID = m2.post_id
				WHERE post_type IN ( 'product', 'product_variation' ) 
				AND post_status = 'publish' 
				AND ( 
					( m1.meta_key = '_price' AND m1.meta_value != '' ) 
					OR 
					( m2.meta_key = '_sale_price' AND m2.meta_value != '' ) )
			";
		} else { 
		
			return $matched_products;
			
		}
		
		$matched_products_query = $wpdb->get_results( $sql, OBJECT_K );
				
		 if ( $matched_products_query ) {
		 
			foreach ( $matched_products_query as $product ) {
			
				if ( $product->post_type == 'product' )
					$matched_products[] = $product->ID;
					
				if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
					$matched_products[] = $product->post_parent;
			}
	        }
	        
	        return $matched_products;
	}

	function woocom_wholesale_join( $join ) {
		global $wp_query, $wpdb, $ignitewoo_wholesale_pricing_plus;

		if ( is_single() )
                        return $join;

		if ( 'product' != $wp_query->query_vars['post_type'] && empty( $wp_query->query_vars['product_cat'] ) && empty( $wp_query->query_vars['product_tag'] ) && empty( $wp_query->query_vars['product_brands'] ) && false == $this->doing_shortcode )
			return $join;

		//if ( !current_user_can( 'wholesale_buyer' ) )
		//	return $join;

		$join .= " LEFT JOIN $wpdb->postmeta wwjm ON $wpdb->posts.ID = wwjm.post_id ";

		return $join;
	}


	function posts_where( $where ) {
		global $wpdb, $wp_query, $ignitewoo_wholesale_pricing_plus, $wp_roles;
					
		if ( is_single() )
                        return $where;
                        
		$roles = array( 'wholesale_buyer' );
		
		if ( isset( $wp_roles->roles ) ) {
			foreach( $wp_roles->roles as $role => $data ) { 
			
				if ( 'ignite_level_' != substr( $role, 0, 13 ) )
					continue; 
					
				$roles[] = $role; 
			}
		}

		if ( 'product' != $wp_query->query_vars['post_type'] && empty( $wp_query->query_vars['product_cat'] ) && empty( $wp_query->query_vars['product_tag'] ) && empty( $wp_query->query_vars['product_brands'] ) && false == $this->doing_shortcode )
			return $where;
		
		if ( 'yes' == $ignitewoo_wholesale_pricing_plus->filter_products && $this->has_filterable_role( $roles ) ) 
			$where .= " AND ( wwjm.meta_key = 'wholesale_price' AND wwjm.meta_value != '' ) ";

		if ( 'yes' == $ignitewoo_wholesale_pricing_plus->retail_filter_products && !$this->has_filterable_role( $roles ) )
			$where .= " AND ( wwjm.meta_key = '_price' AND wwjm.meta_value != '' ) ";

		return $where;

	}
	
	// Support for Tiered Pricing because $roles is loaded with any roles created by Tiered Pricing plus wholesale_buyer
	function has_filterable_role( $roles ) { 
	
		$current_user_can = false;
		
 		foreach( $roles as $r ) { 
			if ( current_user_can( $r ) )
				$current_user_can = true;
		}
		
		return $current_user_can;
	
	}

}


add_filter( 'woocommerce_cart_shipping_method_full_label', 'remove_unwanted_free_label', 1, 2 );

function remove_unwanted_free_label( $label, $method ) { 

	if ( 'free_wholesale_shipping' == $method->id ) 
		return str_replace( '(Free)', '', $label ); 
		
	return $label;


} 

add_action( 'init', 'woocommerce_add_free_wholesale_shipping_method' );

function woocommerce_add_free_wholesale_shipping_method() { 
	global $ignitewoo_wholesale_pricing_plus;

	if ( !class_exists( 'Woocommerce' ) ) 
		return;

	if ( !$ignitewoo_wholesale_pricing_plus->compatible )
		return;

	/** 
		Add a wholesale shipping module that enforces free wholesale shipping 
		if the logged in customer is a wholesale buyer; also removes all other
		shipping options so that the buyer cannot chose from any that are enabled.
	*/

	class WC_Free_Wholesale_Shipping extends WC_Shipping_Method {
		
		function __construct() { 
			$this->id = 'free_wholesale_shipping';
			$this->method_title = __('Wholesale Shipping', 'ignitewoo_wholesale');
			$this->init();
		} 
		
		function init() {

			$this->init_form_fields();
			
			$this->init_settings();
			
			$this->enabled = $this->settings['enabled'];

			$this->title = $this->settings['title'];
			
			add_action( 'woocommerce_update_options_shipping_'.$this->id, array( &$this, 'process_admin_options'));

		}

		
		static function remove_shipping_methods( $methods = array() ) {
			global $woocommerce;

			$settings = get_option( 'woocommerce_free_wholesale_shipping_settings', false );

			if ( 'yes' != $settings['enabled'] ) 
				return $methods;

			if ( ( is_admin() && !defined('DOING_AJAX') ) || !current_user_can( 'wholesale_buyer' ) ) { 

				unset( $methods['WC_Free_Wholesale_Shipping'] );

				return $methods;

			}

			$methods = array(); 

			$methods[] = 'WC_Free_Wholesale_Shipping'; 

			return $methods;

		}

		function maybe_remove_shipping_too() { 
			global $woocommerce;

			$settings = get_option( 'woocommerce_free_wholesale_shipping_settings', false );

			if ( 'yes' != $settings['enabled'] ) 
				return;

			if ( !current_user_can( 'wholesale_buyer' ) ) {

				unset( $woocommerce->payment_gateways->payment_gateways[ 'wholesale_invoice' ] );

				return;

			}



			foreach( $woocommerce->payment_gateways->payment_gateways as $key => $gw ) { 
				if ( 'wholesale_invoice' != $gw->id ) 
					unset( $woocommerce->payment_gateways->payment_gateways[ $key ] );

			}

			global $wp_filter;
			unset( $wp_filter['woocommerce_available_payment_gateways'] );

		}



		function init_form_fields() {

		    $this->form_fields = array(
				    'enabled' => array(
							'title' 		=> __( 'Enable/Disable', 'ignitewoo_wholesale' ), 
							'type' 			=> 'checkbox', 
							'label' 		=> __( 'Bypass shipping for wholesale buyers during checkout.', 'ignitewoo_wholesale' ), 
							'default' 		=> 'yes'
							    ), 
				    'title' => array(
							'title' 		=> __( 'Method Title', 'ignitewoo_wholesale' ), 
							'type' 			=> 'text', 
							'description' 	=> __( '<br/>This controls the title which the user sees during checkout. If you invoice customers and bill for shipping on the invoice then consider setting the label to "Standard Shipping Terms" or similar.', 'ignitewoo_wholesale' ), 
							'default'		=> __( 'Standard Shipping Terms', 'ignitewoo_wholesale' )
							    )
				    );
		
		}


		function admin_options() {

			?>
			<h3><?php _e('Wholesale Shipping', 'ignitewoo_wholesale'); ?></h3>
			<p><?php _e('When enabled this module bypasses the requirement for wholesale buyers to select a shipping method during checkout', 'ignitewoo_wholesale'); ?></p>
			<table class="form-table">
			<?php
				$this->generate_settings_html();
			?>
				</table>
			<?php
		}
	    

		function is_available( $package ) {
			global $woocommerce;

			if ( 'no' == $this->enabled ) 
				return false;

			if ( !current_user_can( 'wholesale_buyer' ) )
				$is_available = false;
			else
				$is_available = true;

			return $is_available; 
			//return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available );

		} 
		

		function calculate_shipping() {

			$args = array(
				'id' 	=> $this->id,
				'label' => $this->title,
				'cost' 	=> 0,
				'taxes' => false
			);

			$this->add_rate( $args );  	
		}
		
	}

	function add_free_wholesale_shipping_method( $methods = array() ) {

		add_action( 'wp_ajax_woocommerce_update_order_review', array( 'WC_Free_Wholesale_Shipping', 'maybe_remove_shipping_too' ), 999 );

		add_action( 'wp_ajax_nopriv_woocommerce_update_order_review', array( 'WC_Free_Wholesale_Shipping', 'maybe_remove_shipping_too' ), 999 );

		$methods[] = 'WC_Free_Wholesale_Shipping'; 

		return $methods;

	}

	add_filter( 'woocommerce_shipping_methods', 'add_free_wholesale_shipping_method', 20, 1);

	add_filter( 'woocommerce_shipping_methods', array( 'WC_Free_Wholesale_Shipping', 'remove_shipping_methods' ), 9999999, 1 );

}


add_action( 'plugins_loaded', 'woocommerce_wholesale_payment_init' ); 

function woocommerce_wholesale_payment_init() { 
	global $ignitewoo_wholesale_pricing_plus;


	if ( !class_exists( 'Woocommerce' ) ) 
		return;

	if ( !$ignitewoo_wholesale_pricing_plus->compatible )
		return;

	/** 
		Add a wholesale payment module that removes any requirement to pay for an 
		order during checkout. 
	*/
	
	//if ( !class_exists( 'WC_Payment_Gateway' ) ) 
	//	return;

	class WC_Wholesale_Skip_Payment_At_Checkout extends WC_Payment_Gateway {
			
		public function __construct() { 

			$this->id = 'wholesale_invoice';

			$this->has_fields = false;

			$this->method_title  = __( 'Wholesale Checkout', 'ignitewoo_wholesale' );

			$this->init_form_fields();

			// Fix for multisite wackiness during Ajax on the checkout page
			if ( defined( 'DOING_AJAX' ) ) { 
			
				$opts = get_option( 'woocommerce_wholesale_invoice_settings' );
				
				if ( !isset( $opts['enabled'] ) || 'yes' != $opts['enabled'] )
					return; 
				
			}
			
			$this->init_settings();

			$this->title = $this->settings['title'];

			$this->description = $this->settings['description'];
			
			add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );

			add_action( 'woocommerce_thankyou_wholesale_invoice', array( &$this, 'thankyou_page' ) );
			
			add_action( 'woocommerce_email_before_order_table', array( &$this, 'email_instructions' ), 10, 2);

			if ( 'yes' == $this->settings['enabled'] ) 
				add_filter( 'woocommerce_available_payment_gateways', array( &$this, 'maybe_remove_gateways' ), 999, 1 );

		} 
	    

		function maybe_remove_gateways( $gateways ) { 

			if ( !current_user_can( 'wholesale_buyer' ) ) { 

				unset( $gateways[ 'wholesale_invoice' ] );

				return $gateways;

			}

			$settings = get_option( 'woocommerce_wholesale_invoice_settings' );
			
			// Should we unset other payment gateways? It not then return
			if ( isset( $settings['leave_gateways'] ) && 'yes' == $settings['leave_gateways'] )
				return $gateways;

			foreach ( $gateways as $key => $vals ) { 

				if ( 'wholesale_invoice' != $key  ) {

					unset( $gateways[ $key ] );

				}

			}

			return $gateways;

		}
		
		function get_title() { 
			return $this->settings['title'];
		}
		
		function is_available() { 
		
			if ( !current_user_can( 'wholesale_buyer' ) )
				return false;
				
			if ( 'yes' !== $this->settings['enabled'] )
				return false;
				
			return true;
		
		}


		public static function maybe_remove_gateways_too() { 
			global $woocommerce;
 
			$enabled = false;

			foreach( $woocommerce->payment_gateways->payment_gateways as $g ) { 

				if ( 'wholesale_invoice' == $g->id ) {
					$g->init_form_fields();
					$g->init_settings();
					$enabled = $g->enabled; 
					if ( empty( $enabled ) )
						$enabled = ( 'yes' == $g->settings['enabled'] ) ? true : false;
					if ( 'yes' == $enabled ) $enabled = true; else $enabled = false;
				}
			}

			if ( !$enabled  ) 
				return;

			if ( !current_user_can( 'wholesale_buyer' ) ) {

				unset( $woocommerce->payment_gateways->payment_gateways[ 'wholesale_invoice' ] );

				return;

			}

			// Should we unset other payment gateways? It not then return
			foreach( $woocommerce->payment_gateways->payment_gateways as $g ) { 
				if ( 'wholesale_invoice' != $g->id )
					continue;
					
				if ( isset( $g->settings['leave_gateways'] ) && 'yes' == $g->settings['leave_gateways'] )
					return;
			}

			foreach( $woocommerce->payment_gateways->payment_gateways as $key => $gw ) { 
				if ( 'wholesale_invoice' != $gw->id ) 
					unset( $woocommerce->payment_gateways->payment_gateways[ $key ] );

			}

			global $wp_filter;
			
			unset( $wp_filter['woocommerce_available_payment_gateways'] );

		}


		function init_form_fields() {
		
			$this->form_fields = array(
					'enabled' => array(
									'title' => __( 'Enable/Disable', 'ignitewoo_wholesale' ), 
									'type' => 'checkbox', 
									'label' => __( 'Bypass payment for wholesale buyers during checkout.', 'ignitewoo_wholesale' ), 
									'default' => 'yes'
								), 
					'title' => array(
									'title' => __( 'Title', 'ignitewoo_wholesale' ), 
									'type' => 'text', 
									'description' => __( '<br/>This controls the title which the user sees during checkout.', 'ignitewoo_wholesale' ), 
									'default' => __( 'Standard Payment Terms', 'ignitewoo_wholesale' )
								),
					'description' => array(
									'title' => __( 'Customer Message', 'ignitewoo_wholesale' ), 
									'type' => 'textarea', 
									'description' => __( 'Let the customer know about how you intend to collect payment.', 'ignitewoo_wholesale' ), 
									'default' => __( 'We will invoice you using your standard payment terms.', 'ignitewoo_wholesale' )    
								),
					'leave_gateways' => array(
									'title' => __( 'Allow other payment methods', 'ignitewoo_wholesale' ), 
									'type' => 'checkbox', 
									'label' => __( 'When this setting is on then your other enabled payment gateways remain available for wholesale buyers to use at checkout.', 'ignitewoo_wholesale' ), 
									'default' => 'no'
								), 
					);

		}
		

		function admin_options() {

			?>
			<h3><?php _e('Wholesale Payments', 'ignitewoo_wholesale'); ?></h3>
			<p><?php _e('Allow wholesale buyers to skip payment during checkout.', 'ignitewoo_wholesale'); ?></p>
			<table class="form-table">
			<?php
				$this->generate_settings_html();
			?>
				</table>
			<?php
		}


		function payment_fields() {

			if ( $this->description ) 
				echo wpautop( wptexturize( $this->description ) );

		}
		

		function thankyou_page() {

			if ( $this->description ) 
				echo wpautop( wptexturize( $this->description ) );

		}


		function email_instructions( $order, $sent_to_admin ) {

			if ( $sent_to_admin ) return;
			
			if ( $order->status !== 'on-hold') return;
			
			if ( $order->payment_method !== 'wholesale_invoice') return;
		
			if ( $this->description) echo wpautop( wptexturize( $this->description ) );
		}
		

		function process_payment( $order_id ) {

			global $woocommerce;

			$order = new WC_Order( $order_id );

			$order->update_status( 'on-hold', __( 'Awaiting payment', 'ignitewoo_wholesale' ) );
			
			$order->reduce_order_stock();
			
			$woocommerce->cart->empty_cart();
			
			unset( $_SESSION['order_awaiting_payment'] );
				
			$url = $this->get_return_url( $order );
			
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) { 
			
				return array(
					'result'  => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			
			} else { 
				
				return array(
					'result' 	=> 'success',
					'redirect'	=> add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order_id, get_permalink( woocommerce_get_page_id( 'thanks' ) ) ) )
				);
			}
		}
		
	}

	function add_wholesale_payment_gateway( $methods = array() ) {

		$methods[] = 'WC_Wholesale_Skip_Payment_At_Checkout'; 

		return $methods;
	}

	
	add_action( 'wp_ajax_woocommerce_update_order_review', array( 'WC_Wholesale_Skip_Payment_At_Checkout', 'maybe_remove_gateways_too' ), -1 );

	add_action( 'wp_ajax_nopriv_woocommerce_update_order_review', array( 'WC_Wholesale_Skip_Payment_At_Checkout', 'maybe_remove_gateways_too' ), -1 );

		
	add_filter( 'woocommerce_payment_gateways', 'add_wholesale_payment_gateway', -1, 1 );

}

add_action( 'init', 'ignitewoo_wholesale_loader', 1 );

function ignitewoo_wholesale_loader() { 

	global $ignitewoo_wholesale_pricing_plus;

	if ( !class_exists( 'Woocommerce' ) ) 
		return;

	if ( !$ignitewoo_wholesale_pricing_plus->compatible )
		return;

	if ( is_admin() ) { 

		require_once( dirname( __FILE__ ) . '/woocommerce-wholesale-plus-product-edit.php' );

		global $ignitewoo_wholesale_pricing_plus_product_edit;

		$ignitewoo_wholesale_pricing_plus_product_edit = new IgniteWoo_Wholesale_Pricing_Plus_Rules_Admin();
	}

	require_once( dirname( __FILE__ ) . '/woocommerce-wholesale-plus-product-process.php' );

	//global $ignitewoo_wholesale_pricing_plus_product_process;

	//$ignitewoo_wholesale_pricing_plus_product_process = new IgniteWoo_Wholesale_Process_Product(); 
}
