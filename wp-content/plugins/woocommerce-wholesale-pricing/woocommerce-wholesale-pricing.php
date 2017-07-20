<?php
/*
Plugin Name: WooCommerce Wholesale Pricing
Plugin URI:  http://ignitewoo.com
Description: Allows you to set wholesale prices for products and variations. Users whose role is "Wholesale Buyer" can buy at the wholesale prices that you set. 
Version: 2.6.38
Author: IgniteWoo.com
Author URI: http://ignitewoo.com
Email: support@ignitewoo.com
*/


global $ignitewoo_remove_wholesale_tax, $ignitewoo_wholesale_pricing_base_version;

$ignitewoo_wholesale_pricing_base_version = '2.6.13';


/** Set this to false if you want to charge tax on wholesale purchases */

global $ignitewoo_remove_wholesale_tax;

$ignitewoo_remove_wholesale_tax = false;


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
ignitewoo_queue_update( plugin_basename( __FILE__ ), '0447a1a545a9c0eff94010e0c6c1d23c', '109' );



class woocommerce_wholesale_pricing { 

	function __construct() {

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );

		// Changed from 9999999 in v2.6.24 to ensure hooks get setup before the add to cart functionality
		// in WC tries to do its checking on availability. Note that 9 is too high, so we chose 5
		add_action( 'init', array( &$this, 'init' ), 5 );
		
		add_action( 'wc_init', array( &$this, 'wc_init' ), 5 );

		// this lets the plugin adjust the price so it shows up in the cart on the fly
		// triggers when someone clicks "Add to Cart" from a product page
		add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'add_cart_item' ), -1, 1 );

		// this helps with mini carts such as the one in the ShelfLife theme 
		// gets accurate pricing into the session before theme displays it on the screen
		// helps when "add to cart" does not redirect to cart page immediately
		add_action('woocommerce_before_calculate_totals', array( &$this, 'predisplay_calculate_and_set_session'), 9999999, 1 );

		// ensure sale price is always empty for wholesale buyers so that the price display doesn't include
		// a marked out regular retail price
		add_filter( 'woocommerce_get_price_html', array( &$this, 'maybe_remove_sale_price' ), 999, 2 );
			     
		add_action( 'woocommerce_variable_product_bulk_edit_actions', array( &$this, 'bulk_edit' ), 1 );

		add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'add_variable_attributes'), 1, 3 );
		
		add_action( 'woocommerce_product_options_pricing', array( &$this, 'add_simple_price' ), 1 );
				
		// WC 2.4 and newer: 
		add_action( 'woocommerce_ajax_save_product_variations', array( &$this, 'ajax_process_product_meta_variable' ), 5 );
				
	}

	
	function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_wholesale' );

		load_textdomain( 'ignitewoo_wholesale', WP_LANG_DIR.'/woocommerce/ignitewoo_wholesale-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		load_plugin_textdomain( 'ignitewoo_wholesale', false, $plugin_rel_path );

	}

	// add the new role, same as 'customer' role with a different name actually
	// add actions
	function init() { 
		global $wp_roles;

		@session_start();
				
		if ( class_exists( 'WP_Roles' ) ) 
		    if ( !isset( $wp_roles ) ) 
			$wp_roles = new WP_Roles();   
        
		if ( is_object( $wp_roles ) ) { 

			$caps = array( 
				'read' => true,
				'edit_posts' => false,
				'delete_posts' => false,

			);

			add_role ('wholesale_buyer', 'Wholesale Buyer', $caps );

			$role = get_role( 'wholesale_buyer' ); 
			$role->add_cap( 'buy_wholesale' );

		}

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			add_filter( 'woocommerce_get_product_price', array( &$this, 'maybe_return_price' ), 999, 2 );
			add_filter( 'woocommerce_product_get_price', array( &$this, 'maybe_return_price' ), 999, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( &$this, 'maybe_return_price' ), 999, 2 );
			
		} else {
			add_filter( 'woocommerce_get_price', array( &$this, 'maybe_return_price' ), 999, 2 );
		}

		add_action( 'woocommerce_process_product_meta_simple', array( &$this, 'process_product_meta' ), 1, 1 );

		add_action( 'woocommerce_process_product_meta_variable', array( &$this, 'process_product_meta_variable' ), 999, 1 );


		add_action( 'admin_head', array( &$this, 'variable_product_write_panel_js' ), 9999 );

		// Regular price displays, before variations are selected by a buyer
		add_filter( 'woocommerce_grouped_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );
		add_filter( 'woocommerce_variable_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );

		// Javscript related
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '<' ) ) {
		add_filter( 'woocommerce_variation_sale_price_html', array( &$this, 'maybe_return_variation_price' ), 1, 2 );
		add_filter( 'woocommerce_variation_price_html', array( &$this, 'maybe_return_variation_price' ), 1, 2 );
		add_filter( 'woocommerce_variable_empty_price_html', array( &$this, 'maybe_return_variation_price_empty' ), 999, 2 );

		//add_filter( 'woocommerce_available_variation', array( &$this, 'variation_attributes' ), 99999, 3 ); 


		add_filter( 'woocommerce_available_variation', array( &$this, 'maybe_adjust_variations' ), 1, 3 );
		}
		
		add_filter( 'woocommerce_variation_is_visible', array( &$this, 'p_variation_is_visible' ), 99999, 3 );
		
		add_filter( 'woocommerce_product_is_visible', array( &$this, 'variation_is_visible' ), 99999, 2 );

		add_filter( 'woocommerce_is_purchasable', array( &$this, 'is_purchasable' ), 1, 2 );

		add_filter( 'woocommerce_get_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );
		add_filter( 'woocommerce_sale_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );
		add_filter( 'woocommerce_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );
		add_filter( 'woocommerce_empty_price_html', array( &$this, 'maybe_return_wholesale_price' ), 1, 2 );

		add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'add_cart_item' ), -1, 1 );

		// adjust taxes to zero in cart and checkout
		add_filter( 'woocommerce_get_cart_tax', array( &$this, 'get_cart_tax' ), 1, 1 );
		add_filter( 'woocommerce_calculate_totals', array( &$this, 'calculate_totals' ), 999, 1 );

		//add_filter( 'woocommerce_cart_contents', array( &$this, 'maybe_adjust_shipping' ), 1 );

		//add_filter( 'option_woocommerce_calc_taxes', array( &$this, 'override_tax_setting' ), 9999, 1 );


	}

	
	function wc_init() { 
		global $ign_tax_exempt, $ignitewoo_remove_wholesale_tax;
		
		if ( !class_exists( 'WC_Tax_Exempt' ) || empty( $ign_tax_exempt ) )
			return;
			
		if ( $ign_tax_exempt->is_user_exempt() ) {
		
			$ignitewoo_remove_wholesale_tax = true;
			
		} else {
		
			$ignitewoo_remove_wholesale_tax = false;
			
		}
	
	}
	
	function get_product( $product_id, $args = array() ) {

		$product = null;

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {

			// WC 2.0
			$product = wc_get_product( $product_id, $args );

		} else {

			// old style, get the product or product variation object
			if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {

				$product = new WC_Product_Variation( $product_id, $args['parent_id'] );

			} else {

				// get the regular product, but if it has a parent, return the product variation object
				$product = new WC_Product( $product_id );

				if ( $product->get_parent() ) {
					$product = new WC_Product_Variation( $product->id, $product->get_parent() );
				}
			}
		}

		return $product;
	}
	
	function predisplay_calculate_and_set_session( $stuff = '' ) { 
		global $woocommerce;

		// Don't run this function is the Wholesale Pricing Plus plugin is active
		if ( class_exists( 'IgniteWoo_Wholesale_Pricing_Plus' ) )
			return; 


		if ( !$woocommerce->cart->cart_contents )
			return;

		foreach( $woocommerce->cart->cart_contents as $key => $item ) { 

			$item_data = array();
			$item_data = $item['data'];

			// call our internal function to see if wholesale prices need to be set
			$item_data = $this->add_cart_item( $item );

		}
	
		// Set session data
		/*
		$_SESSION['cart'] = $woocommerce->cart->cart_contents;
		$_SESSION['coupons'] = $woocommerce->cart->applied_coupons;
		$_SESSION['cart_contents_total'] = $woocommerce->cart->cart_contents_total;
		$_SESSION['cart_contents_weight'] = $woocommerce->cart->cart_contents_weight;
		$_SESSION['cart_contents_count'] = $woocommerce->cart->cart_contents_count;
		$_SESSION['cart_contents_tax'] = $woocommerce->cart->cart_contents_tax;
		$_SESSION['total'] = $woocommerce->cart->total;
		$_SESSION['subtotal'] = $woocommerce->cart->subtotal;
		$_SESSION['subtotal_ex_tax'] = $woocommerce->cart->subtotal_ex_tax;
		$_SESSION['tax_total'] = $woocommerce->cart->tax_total;
		$_SESSION['shipping_taxes'] = $woocommerce->cart->shipping_taxes;
		$_SESSION['taxes'] = $woocommerce->cart->taxes;
		$_SESSION['discount_cart'] = $woocommerce->cart->discount_cart;
		$_SESSION['discount_total'] = $woocommerce->cart->discount_total;
		$_SESSION['shipping_total'] = $woocommerce->cart->shipping_total;
		$_SESSION['shipping_tax_total'] = $woocommerce->cart->shipping_tax_total;
		$_SESSION['shipping_label'] = isset( $woocommerce->cart->shipping_label ) ? $woocommerce->cart->shipping_label : '';
		*/
	}

	/*
	function override_tax_setting( $setting = '' ) { 
		global $current_user, $ignitewoo_remove_wholesale_tax;

		if ( !$current_user ) 
			$current_user = get_currentuserdata();

		if ( current_user_can( 'wholesale_buyer' ) && $ignitewoo_remove_wholesale_tax ) 
			return false;

		return $setting; 

	}
	*/
	
	function calculate_totals() { 
		global $current_user, $woocommerce, $ignitewoo_remove_wholesale_tax,$ignitewoo_wholesale_pricing_plus;

		if ( !empty( $ignitewoo_wholesale_pricing_plus ) ) { 
			$disable_tax = get_option( 'woocommerce_wholesale_pricing_plus_disable_tax', false );

			if ( !$current_user ) 
				$current_user = get_currentuserdata();

			if ( current_user_can( 'wholesale_buyer' ) && 'eu' == $disable_tax ) {
			
				$woocommerce->customer->set_is_vat_exempt( true );

				return;
				
			}
		}

		if ( false == $ignitewoo_remove_wholesale_tax ) 
			return;

		if ( !$current_user ) 
			$current_user = get_currentuserdata();

		if ( current_user_can( 'wholesale_buyer' ) ) {

			foreach ( $woocommerce->cart->cart_contents as &$line_item ) { 

				$line_item['line_tax'] = 0;
				$line_item['line_subtotal_tax'] = 0;

			}

			$woocommerce->cart->tax_total = 0;
			$woocommerce->cart->shipping_tax_total = 0;
			$woocommerce->cart->taxes = array();
			$woocommerce->cart->shipping_taxes = array();

			if ( empty( $woocommerce->customer ) )
				return;
			
			$woocommerce->customer->set_is_vat_exempt( true );
		}

	}


	function maybe_adjust_shipping( ) { 
		global $woocommerce, $current_user;

		if ( !$current_user ) 
			$current_user = get_currentuserdata();

		if ( !current_user_can( 'wholesale_buyer' ) )
			return;

		for( $i = 0; $i < count( $woocommerce->shipping->shipping_methods ); $i++ ) { 

			if ( 'free_shipping' == $woocommerce->shipping->shipping_methods[$i]->id ) {

				$woocommerce->shipping->shipping_methods[$i]->min_amount = 0;

				break;

			}

		}

		$woocommerce->cart->calculate_totals();

		return;

	}


	function get_cart_tax( $amount ) { 
		global $current_user, $woocommerce, $ignitewoo_remove_wholesale_tax;

		if ( false == $ignitewoo_remove_wholesale_tax ) 
			return $amount;

		if ( !$current_user ) 
			$current_user = get_currentuserdata();

		if ( current_user_can( 'wholesale_buyer' ) ) 
			return 0;

		return $amount;

	}


	function add_cart_item( $item_data = '' ) { 
		global $current_user, $woocommerce;

		if ( !$current_user ) 
			$current_user = get_currentuserdata();

		if ( current_user_can( 'wholesale_buyer' ) ) {

			$_product = $this->get_product( $item_data['product_id'] ); // new WC_Product( $item_data['product_id'] ) ;

			if ( isset( $item_data['variation_id'] ) && 'variable' == $_product->get_type() ) 
				$wholesale_price = get_post_meta( $item_data['variation_id' ], 'wholesale_price', true );

			else if ( 'simple' == $_product->get_type() || 'external' == $_product->get_type() )
				$wholesale_price = get_post_meta( $item_data['product_id' ], 'wholesale_price', true );

			//else if ( 'grouped' == $_product->get_type() )
			//	$wholesale_price = ''; 

			else // all other product types - possibly incompatible with custom product types added by other plugins\
				$wholesale_price = get_post_meta( $item_data['product_id' ], 'wholesale_price', true );


			if ( $wholesale_price ) { 

				if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) < 0 )
					$item_data['data']->product_custom_fields['regular_price'] = $wholesale_price;
					
				$item_data['data']->price = $wholesale_price;
				$item_data['data']->regular_price = $wholesale_price;

			}

		}

		return $item_data;

	}


	function format_price( $price ) {
	
		$this->currency_pos = get_option( 'woocommerce_currency_pos' );

		if ( !$this->currency_pos )
			$this->currency_pos = 'left';
			
		switch ( $this->currency_pos ) {
			case 'left' :
				$return = get_woocommerce_currency_symbol() . $price;
			break;
			case 'right' :
				$return = $price . get_woocommerce_currency_symbol();
			break;
			case 'left_space' :
				$return =  get_woocommerce_currency_symbol() . '&nbsp;' . $price;
			break;
			case 'right_space' :
				$return = $price . '&nbsp;' . get_woocommerce_currency_symbol();
			break;
		}

		return $return;

	}

	
	public static function maybe_return_wholesale_price( $price, $_product ) { 
		global $current_user, $ignitewoo_wholesale_pricing_plus;

		if ( !isset( $current_user->ID ) ) 
			$current_user = get_currentuserdata(); 

		if ( !current_user_can( 'wholesale_buyer' ) ) 
			return $price;
			
		$wtext = '';
		
		$vtype = 'variable';

		if ( $_product->is_type('grouped') ) { 

                        $min_price = '';
                        $max_price = '';

                        foreach ( $_product->get_children() as $child_id ) { 

				if ( current_user_can( 'wholesale_buyer' ) )
					$child_price = get_post_meta( $child_id, 'wholesale_price', true );
				else 
					$child_price = get_post_meta( $child_id, '_price', true);

				if ( !$child_price ) 
					continue;

                                if ( $child_price < $min_price || $min_price == '' ) $min_price = $child_price;

                                if ( $child_price > $max_price || $max_price == '' ) $max_price = $child_price;

                        }

                        //$price = '<span class="from">' . __('From:', 'ignitewoo_wholesale') . ' </span>' . $this->format_price( $min_price );
                        
			$price = '<span class="from">' . __('From:', 'ignitewoo_wholesale') . ' </span>' . wc_price( $min_price );

		} elseif ( $_product->is_type( $vtype ) ) {

			if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
				$product_id = $_product->get_id();
				//$variation_id = $product->get_id();
			} else {
				$product_id = $_product->id;
				//$variation_id = $product->variation_id;
			}
				
			if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) { 
				$wprice_min = get_post_meta( $product_id, 'min_variation_wholesale_price', true );
				$wprice_max = get_post_meta( $product_id, 'max_variation_wholesale_price', true );
			} else {
				$wprice_min = $_product->product_custom_fields['min_variation_wholesale_price'][0];
				$wprice_max = $_product->product_custom_fields['max_variation_wholesale_price'][0];
			}

			if ( current_user_can( 'wholesale_buyer' ) )  { 

				if ( empty( $wprice_min ) && empty( $wprice_max ) ) { 
				
					$wprice_min = get_post_meta( $product_id, '_min_variation_price', true );
					$wprice_max = get_post_meta( $product_id, '_min_variation__price', true );
				}
				
				
				if ( !empty( $ignitewoo_wholesale_pricing_plus ) && 'eu' == $ignitewoo_wholesale_pricing_plus->disable_tax && !empty( $wprice_min ) && 'yes' == get_option('woocommerce_prices_include_tax') ) {

					$_tax = new WC_Tax();
					$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
					$taxes = $_tax->calc_tax( $wprice_min * 1, $tax_rates, true );
					
					$wprice_min = $_tax->round( $wprice_min * 1 - array_sum( $taxes ) );
					
					$taxes = $_tax->calc_tax( $wprice_max * 1, $tax_rates, true );
					$wprice_max = $_tax->round( $wprice_max * 1 - array_sum( $taxes ) );
				}
				

				if ( !isset( $wprice_min ) || $wprice_min !== $wprice_max )
					$price = '<span class="from">' . __('From:', 'ignitewoo_wholesale') . ' </span>';

				if ( $wprice_min == $wprice_max ) 
					return wc_price( $wprice_min );
				
				else if ( isset( $wprice_min ) )
					//$price .= $this->format_price( $_product->product_custom_fields['min_variation_wholesale_price'][0] );
					$price = '<span class="from">' . __('From:', 'ignitewoo_wholesale') . ' ' . wc_price( $wprice_min ) . ' </span>';;

				
			} else { 

				return $price;

			}



		} else { 

			if ( current_user_can( 'wholesale_buyer' ) )  { 
			
				if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
					$product_id = $_product->get_id();
				} else {
					$product_id = $_product->id;
				}

				if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
					$wprice_min = get_post_meta( $product_id, 'wholesale_price', true );
				} else {
					$wprice_min = $_product->product_custom_fields['wholesale_price'][0];
				}

				if ( isset( $wprice_min ) && $wprice_min > 0 )

					//$price = $this->format_price( $_product->product_custom_fields['wholesale_price'][0] );
					$price = wc_price( $wprice_min );

				elseif ( '' === $wprice_min ) {

					if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) )
					$price = '';

				} elseif ( 0 == $wprice_min ) 

					$price = __( 'Free!', 'ignitewoo_wholesale' );

			} 

			if ( !empty( $wprice_min ) )
				$wtext = get_post_meta( $product_id, 'wholesale_price_text', true );
		}
		
		if ( !empty( $wtext ) && !class_exists( 'IgniteWoo_Wholesale_Pricing_Plus' ) )
			return $price . ' <span class="wholesale_text">' . $wtext . '</span>';
		else 
			return $price;

	}


	function is_purchasable( $purchasable, $_product ) { 
		global $current_user;

		if ( !isset( $current_user->ID ) ) 
			$current_user = get_currentuserdata(); 

		if ( !current_user_can( 'wholesale_buyer' ) )
			return $purchasable;

		//$attrs = $_product->get_attributes(); 

		//$is_variation = false;

		$is_variation = $_product->is_type( 'variation' );

		if ( !$is_variation ) 
			$is_variation = $_product->is_type( 'variable' );
			
		//if ( isset( $attrs ) && count( $attrs ) >= 1 && isset( $attrs['type'] )) 
		//	$is_variation = $attrs['type']['is_variation'];
		//else
		//	$is_variation = false;

		if ( $is_variation  ) { 
			// Variable products
			
			if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
				$product_id = $_product->get_id();
				//$variation_id = $product->get_id();
			} else {
				$product_id = $_product->id;
				//$variation_id = $product->variation_id;
			}
			
			if ( !isset( $_product->variation_id ) )
				    return $purchasable;

			if ( version_compare( WOOCOMMERCE_VERSION, '2.2', '>=' ) )
				$price = get_post_meta( $product_id, 'min_variation_wholesale_price', true );
			else
				$price = $this->get_product_meta( $_product->id, 'min_variation_wholesale_price', true );

			if ( !isset( $price ) )
				return $purchasable;
			else 
				$purchasable = true;

		} else { 
		
			if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
				$product_id = $_product->get_id();
				//$variation_id = $product->get_id();
			} else {
				$product_id = $_product->id;
				//$variation_id = $product->variation_id;
			}
			// Simple products
			$price = $this->get_product_meta( $product_id, 'wholesale_price', false );

			if ( isset( $price ) && '' != $price )
				return true;
		}

		return $purchasable;

	}

	
	function maybe_remove_sale_price( $price_html, $_product ) { 

		if ( !current_user_can( 'wholesale_buyer' ) )
			return $price_html;
			
		$pos = strpos( $price_html, '</del>' );
		
		if ( $pos !== false ) 
			$price_html = trim( substr( $price_html, $pos, strlen( $price_html ) ) );
			
		return $price_html;
	}
	
	
	function maybe_return_price( $price = '', $_product ) { 
		global $current_user;

		// Theme developer " Doing it wrong "  ? If so return
		if ( !isset( $_product ) || !is_object( $_product ) /*|| !is_object( $_product->id )*/ )
			return $price; 
			
		if ( !isset( $current_user->ID ) ) 
			$current_user = get_currentuserdata(); 

		if ( !current_user_can( 'wholesale_buyer' ) )
			return $price;

		//$attrs = $_product->get_attributes(); 

		//$is_variation = false;

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $_product->get_parent_id();
			if ( !empty( $product_id ) ) 
				$variation_id = $_product->get_id();
			else 
				$product_id = $_product->get_id();
		} else {
			$product_id = $_product->id;
			$variation_id = $_product->variation_id;
		}

		// For variable products where variations are not linked and instead there is only one variation set to cover all attributes - e.g. "Any type"
		if ( empty( $variation_id ) && $_product->is_type( 'variable' ) ) {
 
			$price = get_post_meta( $product_id, 'min_variation_wholesale_price', true );
			
			if ( false === $price ) 
				$price = '';
				

 			//elseif ( $_product->price == 0 )
			
			//	$price = __( 'Free!', 'ignitewoo_wholesale' );
				
			return $price; 
		 
		} else if ( !empty( $variation_id ) ) {

			//if ( isset( $_product->variation_id ) ) 
				$wholesale = get_post_meta( $variation_id, 'wholesale_price', true );
			//else 
			//	$wholesale = '';

			if ( intval( $wholesale ) > 0 && version_compare( WOOCOMMERCE_VERSION, '2.2', '<=' ) ) 
				$_product->product_custom_fields['wholesale_price'] = array( $wholesale );
			else if ( intval( $wholesale ) > 0 )
				$price = $wholesale;

			if ( isset( $_product->product_custom_fields['wholesale_price'] ) && is_array( $_product->product_custom_fields['wholesale_price'] ) && $_product->product_custom_fields['wholesale_price'][0] > 0 ) //{
				$price = $_product->product_custom_fields['wholesale_price'][0];

			//} elseif ( $_product->price === '' ) 
			//	$price = '';

			//elseif ($_product->price == 0 ) 
			//	$price = __( 'Free!', 'ignitewoo_wholesale' );

			$wtext = get_post_meta( $variation_id, 'wholesale_price_text', true );
			
			return $price; 

		}

		// Avoids a fatal error in WC 2.1.x
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) { 
			$price = get_post_meta( $product_id, 'wholesale_price', true );
			$wtext = get_post_meta( $product_id, 'wholesale_price_text', true );
			return $price . $wtext; 
			
		}

		$wprice = get_post_meta( $product_id, 'wholesale_price', true );
		
		if ( isset( $wprice ) && !is_null( $wprice ) )
			return $wprice;
		else 
			return $price;
		
		/*
		if ( isset( $_product->product_custom_fields['wholesale_price'][0]) && '' != $_product->product_custom_fields['wholesale_price'][0]  )
			return $_product->product_custom_fields['wholesale_price'][0];
		*/

	}

	// Handles inject price info for form submission
	function maybe_adjust_variations( $variation = '', $obj = '' , $variation_obj  = '') { 
		global $current_user;

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
		
		$vprice = $this->maybe_return_variation_price( '', $variation_obj );

		if ( !empty( $vprice ) && !empty( $wtext ) )
			$vprice .= ' <span class="wholesale_text">' . $wtext . '</span>';
			
		//$variation['price_html'] = $obj->product_custom_fields['min_variation_wholesale_price'][0] != $obj->product_custom_fields['max_variation_wholesale_price'][0] ? '<span class="price">' . $this->maybe_return_variation_price( '', $variation_obj ) . '</span>' : '';
		
		$variation['price_html'] = '<span class="price">' . $vprice . '</span>';

		return $variation;

	}

	function get_product_meta( $product, $field_name, $variation = false ) {

		if ( is_int( $product ) )
			$product = wc_get_product( $product );
			
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $product->get_parent_id();
			if ( !empty( $product_id ) ) 
				$variation_id = $product->get_id();
			else 
				$product_id = $product->get_id();
		} else {
			$product_id = $product->id;
			$variation_id = $product->variation_id;
		}

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
		
			// even in WC >= 2.0 product variations still use the product_custom_fields array apparently
			if ( !empty( $variation_id ) && isset( $product->product_custom_fields[ $prefix . $field_name ][0] ) && $product->product_custom_fields[ $prefix . $field_name ][0] !== '' ) {
				return $product->product_custom_fields[ $prefix . $field_name ][0];
			}

			if ( !$variation ) 
				return maybe_unserialize( get_post_meta( $product_id, $field_name, true ) );
			else
				return maybe_unserialize( get_post_meta( $variation_id, $field_name, true ) );
			
		} else {
			// use product custom fields array

			// variation support: return the value if it's defined at the variation level
			if ( isset( $variation_id ) && $variation_id ) {
				if ( ( $value = get_post_meta( $variation_id, $prefix . $field_name, true ) ) !== '' ) 
					return $value;
				// otherwise return the value from the parent
				return get_post_meta( $product_id, $prefix . $field_name, true );
			}

			// regular product
			return isset( $product->product_custom_fields[ $prefix . $field_name ][0] ) ? $product->product_custom_fields[ $prefix . $field_name ][0] : null;
		}
	}


	// For WooCommerce 2.x flow, to ensure product is visible as long as a wholesale price is set
	function variation_is_visible( $visible, $vid ) {
		global $product;

		if ( !isset( $product->children ) || count( $product->children ) <= 0 )
			return $visible;

		$variation = new dummy_variation();

		$variation->variation_id = $vid;

		$res = $this->maybe_return_variation_price( 'xxxxx', $variation );

		if ( !isset( $res ) || empty( $res ) || '' == $res )
			$res = false;
		else
			$res = true;

		return $res;
	}

	// For WC 2.2.x compat, when variation has no retail price
	function p_variation_is_visible( $visible, $vid, $product_id ) {
		global $product;

		if ( !current_user_can( 'wholesale_buyer' ) )
			return $visible; 
			
		$res = get_post_meta( $vid, 'wholesale_price', true );

		if ( !isset( $res ) || empty( $res ) || '' == $res )
			$res = get_post_meta( $product_id, '_price', true );
		
		if ( !isset( $res ) || empty( $res ) || '' == $res )
			$res = false;
		else
			$res = true;

		return $res;
	}


	// Runs during the woocommerce_variable_empty_price_html filter call, used here in this way for debugging purposes
	// This is used for WooCommerce 2.x compatibility
	function maybe_return_variation_price_empty( $price, $_product ) {
		global $product, $ignitewoo_wholesale_pricing_plus;

		if ( !current_user_can( 'wholesale_buyer' ) )
			return $price;

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $product->get_parent_id();
			$variation_id = $product->get_id();
		} else {
			$product_id = $product->id;
			$variation_id = $product->variation_id;
		}

		$min_variation_wholesale_price = get_post_meta( $variation_id, 'min_variation_wholesale_price', true );
		$max_variation_wholesale_price = get_post_meta( $variation_id, 'max_variation_wholesale_price', true );

		if ( !empty( $min_variation_wholesale_price ) && isset( $ignitewoo_wholesale_pricing_plus->disable_tax ) && 'eu' == $ignitewoo_wholesale_pricing_plus->disable_tax ) { 
		
			$_tax = new WC_Tax();
			$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
			$taxes = $_tax->calc_tax( $min_variation_wholesale_price * 1, $tax_rates, true );
			
			$min_variation_wholesale_price = $_tax->round( $min_variation_wholesale_price * 1 - array_sum( $taxes ) );
			
			$taxes = $_tax->calc_tax( $wprice_max * 1, $tax_rates, true );
			$max_variation_wholesale_price = $_tax->round( $max_variation_wholesale_price * 1 - array_sum( $taxes ) );
		}
			
				
		if ( $min_variation_wholesale_price !== $max_variation_wholesale_price )
			$price = '<span class="from">' . __('From:', 'woocommerce') . ' ' .  wc_price( $min_variation_wholesale_price ) . ' </span>';
		else 
			$price = '<span class="from">' . wc_price( $min_variation_wholesale_price ) . ' </span>';

		return $price;

	}

	
	function variation_attributes( $attrs, $product, $variation ) { 

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $product->get_parent_id();
			$variation_id = $product->get_id();
		} else {
			$product_id = $product->id;
			$variation_id = $product->variation_id;
		}
		
		$has_wholesale_price = get_post_meta( $variation_id, 'wholesale_price', true );
		
		if ( $has_wholesale_price ) {

			$price = $this->maybe_return_price( $variation->price, $variation );

			$attrs['price_html'] = $variation->min_variation_price != $variation->max_variation_price ? '<span class="price"><ins>' . wc_price( $price ) . '</ins></span>' : '';

		} else { 
		
			if ( $variation->is_on_sale() ) 
			
				$attrs['price_html'] = $variation->min_variation_price != $variation->max_variation_price ? '<span class="price"><del>' . $variation->regular_price . '</del> <ins>' . wc_price( $variation->get_price() ) . '</ins></span>' : '';
		
			else 
			
				$attrs['price_html'] = $variation->min_variation_price != $variation->max_variation_price ? '<span class="price"><ins>' . wc_price( $variation->get_price() ) . '</ins></span>' : '';
	
		
		}
		
		return $attrs;
	
	}

	
	// Handles getting prices for variable products
	// Used by woocommerce_variable_add_to_cart() function to generate Javascript vars that are later 
	// automatically injected on the public facing side into a single product page.
	// This price is then displayed when someone selected a variation in a dropdown
	function maybe_return_variation_price( $price, $_product ) {
		global $current_user, $ignitewoo_wholesale_pricing_plus, $product; // parent product object - global

		// Sometimes this hook runs when the price is empty but wholesale price is not, 
		// So check for that and handle returning a price for archive page view
		// $attrs = $_product->get_attributes();

		/*
		$is_variation = $_product->is_type( 'variation' );

		if ( !$is_variation )
			$is_variation = $_product->is_type( 'variable' );
		*/
		
		/*
		if ( isset( $attrs ) && count( $attrs ) >= 1 && isset( $attrs['type'] ) ) 
			$is_variation = $attrs['type']['is_variation'];
		else
			$is_variation = false;
		*/

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $product->get_parent_id();
			$variation_id = $product->get_id();
		} else {
			$product_id = $product->id;
			$variation_id = $product->variation_id;
		}
//var_dump( $variation_id, $is_variation ); 	
		//if ( !isset( $variation_id ) && !$is_variation ) 
		//	return $price;
		if ( !isset( $variation_id ) ) 
			return $price;
		
		if ( !isset( $current_user->ID ) ) 
			$current_user = get_currentuserdata(); 

		if ( /*$is_variation &&*/ current_user_can( 'wholesale_buyer' ) ) { 

			// variation product objects
			//$price =  __( 'From:', 'woocommerce' ) . ' ' . $this->format_price( $_product->product_custom_fields['min_variation_wholesale_price'][0] );
			
			//$price = wc_price( $_product->product_custom_fields['min_variation_wholesale_price'][0] );
			$w_price = get_post_meta( $variation_id, 'wholesale_price', true );

			if ( !empty( $ignitewoo_wholesale_pricing_plus ) && 'eu' == $ignitewoo_wholesale_pricing_plus->disable_tax && !empty( $w_price ) && 'yes' == get_option('woocommerce_prices_include_tax') ) {

				$_tax = new WC_Tax();
				$tax_rates = $_tax->get_shop_base_rate( $_product->tax_class );
				$taxes = $_tax->calc_tax( $w_price * 1, $tax_rates, true );
				$w_price = $_tax->round( $w_price * 1 - array_sum( $taxes ) );

				return wc_price( $w_price );
			}

			
			if ( !empty( $w_price ) )
				return wc_price( $w_price );
			else 
				return wc_price( $_product->get_price() );
				
			//$price = wc_price( get_post_meta( $_product->variation_id, 'wholesale_price', true ) );

			// main product object
			//$price =  __( 'From:', 'woocommerce' ) . ' ' . get_woocommerce_currency_symbol() . $_product->product_custom_fields['wholesale_price'][0];

			//return $price;

		}
		
		return $price;

		if ( current_user_can( 'wholesale_buyer' ) )  { 

				$wholesale = get_post_meta( $_product->variation_id, 'wholesale_price', true );

				if ( intval( $wholesale ) > 0 && version_compare( WOOCOMMERCE_VERSION, '2.2', '<=' ) ) 
					$product->product_custom_fields['wholesale_price'] = array( $wholesale );
				else if ( intval( $wholesale ) )
					$price = $wholesale;


				if ( is_array( $product->product_custom_fields['wholesale_price'] ) && $product->product_custom_fields['wholesale_price'][0] > 0 ) {

					//$price = $this->format_price( $product->product_custom_fields['wholesale_price'][0] );
					$price = wc_price( $product->product_custom_fields['wholesale_price'][0] );

				} elseif ( $product->price === '' ) 

					$price = '';

				elseif ($product->price == 0 ) 

					$price = __( 'Free!', 'ignitewoo_wholesale' );



		} 

		return $price;

	}


	// process simple product meta
	function process_product_meta( $post_id, $post = '' ) {

		if ( '' !==  stripslashes( $_POST['_wholesale_price'] ) )
			update_post_meta( $post_id, 'wholesale_price', stripslashes( $_POST['_wholesale_price'] ) );
		else
			delete_post_meta( $post_id, 'wholesale_price' );
			
		if ( '' !==  stripslashes( $_POST['_wholesale_price_text'] ) )
			update_post_meta( $post_id, 'wholesale_price_text', stripslashes( $_POST['_wholesale_price_text'] ) );
		else
			delete_post_meta( $post_id, 'wholesale_price_text' );
	}
	

	// process variable product meta
	function process_product_meta_variable( $post_id ) {

		if ( isset( $_POST['variable_sku'] ) ) {

			$variable_post_ids = $_POST['variable_post_id'];
			$variable_skus = $_POST['variable_sku'];

			$max_loop = max( array_keys( $_POST['variable_post_id'] ) );

			for ( $i = 0; $i <= $max_loop; $i++ ) {

				$variation_id = absint( $variable_post_ids[ $i ] );

				if ( ! isset( $variable_post_ids[ $i ] ) ) {
					continue;
				}
 
				update_post_meta( $variation_id, 'wholesale_price', strip_tags( stripslashes( $_POST[ '_wholesale' ][ $i ] ) ) );
				
				update_post_meta( $variation_id, 'wholesale_price_text', strip_tags( stripslashes( $_POST[ '_wholesale_text' ][ $i ] ) ) );

			}

		}

		$post_parent = $post_id;
		
		$children = get_posts( array(
				    'post_parent' 	=> $post_parent,
				    'posts_per_page'=> -1,
				    'post_type' 	=> 'product_variation',
				    'fields' 		=> 'ids'
			    ) );

		$lowest_price = '';

		$highest_price = '';

		if ( $children ) {

			foreach ( $children as $child ) {

				$child_price = get_post_meta( $child, 'wholesale_price', true );

			
				if ( !$child_price ) continue;
	
				// Low price
				if ( !is_numeric( $lowest_price ) || $child_price < $lowest_price ) $lowest_price = $child_price;

				
				// High price
				if ( $child_price > $highest_price )
					$highest_price = $child_price;

			}


		}

		update_post_meta( $post_parent, 'wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'wholesale_price_text', $wtext );
		update_post_meta( $post_parent, 'min_variation_wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'max_variation_wholesale_price', $highest_price );

		
	}
	
	function ajax_process_product_meta_variable() {

		$product_id = absint( $_POST['product_id'] );

		$this->process_product_meta_variable( $product_id );
 
		
	}


	function variable_product_write_panel_js() {

		if ( !isset( $_GET['post'] ) )
			return;

		?>
		<script>

		jQuery( document ).ready( function() { 

			jQuery( ".toolbar" ).find( ".delete_variations" ).after('<a class="button set_all_wholesale_prices" href="#"><?php _e( 'Set Wholesale Price', 'ignitewoo_wholesale' ) ?></a>')

			
			jQuery( 'a.set_all_wholesale_prices' ).click( function() {

				var value = prompt( "<?php _e( 'Enter a wholesale price', 'ignitewoo_wholesale' ); ?>" );

				jQuery( '.variable_wholesale_price' ).val( value );

				return false;

			});
			
			jQuery( '.wc-metaboxes-wrapper' ).on( 'click', 'a.bulk_edit', function ( event ) {
			
				var value = prompt( "<?php _e( 'Enter a wholesale price', 'ignitewoo_wholesale' ); ?>" );

				jQuery( '.variable_wholesale_price' ).val( value );

				return false;
			})

		});

		</script>
		<?php
	}
	
	function bulk_edit() { 

		?>
		<option value="_wholesale"><?php _e( 'Wholesale Price', 'woocommerce' ); ?></option>
		<?php
	}
	
	function add_variable_attributes( $loop, $variation_data, $variation = null ) { 

		if ( empty( $variation_data['variation_post_id'] ) && !empty( $variation->ID ) )
			$id = $variation->ID;
		else 
			$id = $variation_data['variation_post_id'];
			
		$wprice = get_post_meta( $id, 'wholesale_price', true );
		$wtext = get_post_meta( $id, 'wholesale_price_text', true );
		if ( !$wprice )
			$wprice = '';
	
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<=' ) ) { 
			?>
			<tr>
				<td>
			<?php } ?>
				<p class="form-row form-row-full">
						
				<label><?php _e( 'Wholesale:', 'ignitewoo_wholesale' ); echo ' ('.get_woocommerce_currency_symbol().')'; ?> <a class="tips" data-tip="<?php _e( 'Enter the wholesale price, or leave blank.', 'ignitewoo_wholesale' ); ?>" href="#">[?]</a></label>
				<input class="variable_wholesale_price" type="text" name="_wholesale[<?php echo $loop; ?>]" value="<?php echo $wprice ?>" placeholder="<?php _e( 'Wholesale price ( optional )', 'ignitewoo_wholesale' ) ?>"/>
				</p>
				
				<p class="form-row form-row-full">
				<label><?php _e( 'Wholesale Text:', 'ignitewoo_wholesale' ); ?> <a class="tips" data-tip="<?php _e( 'Optional text to display after the price', 'ignitewoo_wholesale' ); ?>" href="#">[?]</a></label>
				<input class="variable_wholesale_price_text" type="text" size="99" name="_wholesale_text[<?php echo $loop; ?>]" value="<?php echo $wtext ?>"/>
				</p>
				
		<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<=' ) ) { ?>
				</td>
			</tr>
			<?php }

	}
	
	function add_simple_price() { 
		global $thepostid;
		
		$wprice = get_post_meta( $thepostid, 'wholesale_price', true );
		
		$wtext = get_post_meta( $thepostid, 'wholesale_price_text', true );
	
		woocommerce_wp_text_input( array( 'id' => '_wholesale_price', 'class' => 'wc_input_price short', 'label' => __( 'Wholesale:', 'ignitewoo_wholesale' ) . ' ('.get_woocommerce_currency_symbol().')', 'description' => '', 'type' => 'text', 'value' => $wprice ) );
				
		woocommerce_wp_text_input( array( 'id' => '_wholesale_price_text', 'class' => 'wc_input_short', 'label' => __( 'Wholesale Text:', 'ignitewoo_wholesale' ), 'description' => __( 'Optional text to display after the price', 'ignitewoo_wholesale' ), 'type' => 'text', 'value' => $wtext ) );
	}

}


$woocommerce_wholesale_pricing = new woocommerce_wholesale_pricing();

class dummy_variation {

	function is_type() {
		return true;
	}
	
	function get_price() { 
		global $product;
		return $product->get_price();
	}

}

// Remove cookies for Wholesalers
add_action( 'wp_logout', 'maybe_remove_cookies', 1 );
// Helps prevent tax exempt status issues, which apparently are stored in the session or cookie
function maybe_remove_cookies() { 

	if ( !current_user_can('wholesale_buyer' ) )
		return;

	foreach( $_COOKIE as $k => $v ) {

			if ( false !== strpos( $k, 'woocommerce_' ) )
				setcookie( $k, ' ', time() - 999999999, COOKIE_DOMAIN );
	}

}
