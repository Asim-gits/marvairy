<?php
/*
 * Copyright © 2014 IgniteWoo.com - All Rights Reserved
*/
class IgniteWoo_Wholesale_Pro_Pricing {

	public $variation_counts = array();

	public $product_counts = array();

	public $category_counts = array();

	public $_discounted;

	public $_discounted_cart;

	public $pricing_by_product;

	public $sitewide_discounts;

	public function __construct() {

		if ( !session_id()  )
			session_start();

		$this->_discounted = array();

		$this->_discounted_cart = array();

		$this->pricing_by_product = new IgniteWoo_WSP_Process_Product( 1 );

		$this->sitewide_discounts = new IgniteWoo_WSP_Sitewide_Discounts( 2 );

		add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'update_counts' ), 1, 2 );

		add_action( 'woocommerce_after_cart_item_quantity_update', array( &$this, 'on_update_cart_item_quantity' ), 1, 2 );

		// These were formerly prio 10, changed to 15 so wholesale pricing can operate before this one
		if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 )
			add_filter( 'woocommerce_cart_item_price', array( &$this, 'on_display_cart_item_price_html' ), 15, 3 );
		else
			add_filter( 'woocommerce_cart_item_price_html', array( &$this, 'on_display_cart_item_price_html' ), 15, 3 );
	
		//add_filter( 'woocommerce_cart_item_price_html', array( &$this, 'on_display_cart_item_price_html' ), 15, 3 );
		
		add_filter( 'woocommerce_grouped_price_html', array( &$this, 'on_price_html' ), 15, 2 );
		
		add_filter( 'woocommerce_variable_price_html', array( &$this, 'on_price_html' ), 15, 2 );
		
		add_filter( 'woocommerce_sale_price_html', array( &$this, 'on_price_html' ), 15, 2 );
		
		add_filter( 'woocommerce_price_html', array( &$this, 'on_price_html' ), 15, 2 );
		
		add_filter( 'woocommerce_empty_price_html', array( &$this, 'on_price_html' ), 15, 2 );


		// Must run AFTER tiered pricing runs this same hook
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			add_filter( 'woocommerce_get_product_price', array( &$this, 'woocommerce_get_price' ), 1000, 2 );
			add_filter( 'woocommerce_product_get_price', array( &$this, 'woocommerce_get_price' ), 1000, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( &$this, 'woocommerce_get_price' ), 1000, 2 );
		} else { 
			add_filter( 'woocommerce_get_price', array( &$this, 'woocommerce_get_price' ), 1000, 2 ); // 99999999, 2
		}

	}


	function woocommerce_get_price( $_price, $_product ) {
		global $woocommerce;

		if ( !defined( 'DOING_AJAX' ) && is_admin() )
			return $_price;

		if ( !isset( $woocommerce->cart ) || !isset( $woocommerce->cart->cart_contents ) )
			return;
			
		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$product_id = $_product->get_parent_id();
			if ( !empty( $product_id ) ) {
				$variation_id = $_product->get_id();
			} else {
				$product_id = $_product->get_id();
				$variation_id = false;
			}
		} else {
			$product_id = $_product->id;
			$variation_id = $_product->variation_id;
		}

		foreach( $woocommerce->cart->cart_contents as $key => $item ) {

			if ( isset( $item['variation_id'] ) && !empty( $item['variation_id'] ) ) {

				if ( $item['product_id'] == $product_id && $item['variation_id'] == $variation_id ) {

					if ( isset( $item['d_discounts'] ) )
						if ( isset( $item['d_discounts']['discounted'] ) )
							return $item['d_discounts']['discounted'];
				}

			} else {

				if ( $item['product_id'] == $product_id ) {

					if ( isset( $item['d_discounts'] ) )
						if ( isset( $item['d_discounts']['discounted'] ) )
							return $item['d_discounts']['discounted'];
				}

			}

		}

		return $_price;

	}

	
	public function on_display_cart_item_price_html( $html, $cart_item, $cart_item_key ) {
		global $ignitewoo_wsp_discounts;

		if ( $this->is_cart_item_discounted( $cart_item_key ) ) {

			remove_all_filters( 'woocommerce_get_price' );
			remove_all_filters( 'woocommerce_get_product_price' );
			remove_all_filters( 'woocommerce_product_get_price' );
			remove_all_filters( 'woocommerce_product_variation_get_price' );
			
			remove_all_filters( 'woocommerce_get_price_excluding_tax' );
		
			if ( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' ) {

				$price = $this->_discounted_cart[$cart_item_key]['d_discounts']['price_excluding_tax'];

				if ( is_callable( 'wc_get_price_excluding_tax' ) )
					$discounted_price = wc_get_price_excluding_tax( $cart_item['data'] );
				else 
					$discounted_price = $cart_item['data']->get_price_excluding_tax();

			} else { 

				$price = $this->_discounted_cart[$cart_item_key]['d_discounts']['price'];

				$discounted_price = $cart_item['data']->get_price();
			}

			$html = '<del>' . wc_price( $price ) . '</del><ins> ' . wc_price( $discounted_price ) . '</ins>';

			
			//if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
			//	$html .= '<br /><strong>WP_DEBUG ENABLED - Discounts Applied: ' . $this->_discounted_cart[$cart_item_key]['d_discounts']['by'] . '</strong><br /><pre>' . print_r( $this->_discounted_cart[$cart_item_key]['d_discounts'], true ) . '</pre>';
			
		}

		return $html;
	}


	public function on_price_html( $html, $_product  ) {

		return $html;
	}


	public function get_product_ids( $variations = false  ) {

		if ( $variations )
		    return array_merge( array_keys( $this->product_counts ), array_keys( $this->variation_counts ) );

		else
		    return array_keys( $this->product_counts );

	}


	public function reset_counts() {
		global $ignitewoo_wsp_discounts;

		$this->variation_counts = array();

		$this->product_counts = array();

		$this->category_counts = array();
	}


	public function reset_products() {
		global $woocommerce;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {

			if (isset( $cart_item['d_discounts'] ) ) {

				if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '>=' ) )
					$cart_item['data']->set_price( $cart_item['d_discounts']['price'] );
				else 
					$cart_item['data']->price = $cart_item['d_discounts']['price'];

			    unset( $cart_item['d_discounts'] );

			}

		}
	}


	public function update_counts( $cart_item, $value  ) {
		global $woocommerce;
		$_product = $cart_item['data'];
		
		
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
		
		//Gather product id counts
		$this->product_counts[ $product_id ] = isset( $this->product_counts[ $product_id ] ) ? $this->product_counts[ $product_id ] + $cart_item['quantity'] : $cart_item['quantity'];

		//Gather product variation id counts
		if ( isset( $variation_id ) && !empty( $variation_id ) )
		    $this->variation_counts[ $variation_id ] = isset( $this->variation_counts[ $variation_id ] ) ? $this->variation_counts[ $variation_id ] + $cart_item['quantity'] : $cart_item['quantity'];

		//Gather product category counts
		$product_categories = wp_get_post_terms( $product_id, 'product_cat' );

		foreach ( $product_categories as $category )
		    $this->category_counts[$category->term_id] = isset( $this->category_counts[$category->term_id] ) ? $this->category_counts[$category->term_id] + $cart_item['quantity'] : $cart_item['quantity'];

		return $cart_item;
	}


	public function on_update_cart_item_quantity( $cart_item, $quantity  ) {
		global $woocommerce;

		$this->reset_counts();

		$this->reset_products();

		if (sizeof( $woocommerce->cart->get_cart() ) > 0 ) {

			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values )
				$this->update_counts( $values, null );

		}

	}


	public function reset_totals() {

		$this->_discounted = array();

		$this->_discounted_cart = array();
	}


	public function is_cart_item_discounted( $cart_item_key ) {

		return isset( $this->_discounted_cart[ $cart_item_key ] ) && isset( $this->_discounted_cart[ $cart_item_key ]['d_discounts'] );

	}


	public function add_discounted_cart_item( &$cart_item_key, $cart_item, $track_variation = false  ) {

		$this->_discounted[ $cart_item['product_id'] ] = $cart_item;

		if ( $track_variation )
			$this->_discounted[ $cart_item['variation_id'] ] = $cart_item;

		$this->_discounted_cart[ $cart_item_key ] = $cart_item;
	}


	public function remove_discounted_cart_item( $cart_item_key, $cart_item, $track_variation = false  ) {

		unset( $this->_discounted[ $cart_item['product_id'] ] );

		if ( $track_variation )
			unset( $this->_discounted[ $cart_item['variation_id'] ] );

		unset( $this->_discounted_cart[ $cart_item_key ] );
	}

}


global $ignitewoo_wsp_discounts_pricing;

$ignitewoo_wsp_discounts_pricing = new IgniteWoo_Wholesale_Pro_Pricing();


class IgniteWoo_WSP_Base_Product {

	protected $discounter = 'base';

	protected $discount_data = array();


	public function __construct( $name ) {
		$this->discounter = $name;
	}


	public function add_adjustment( &$cart_item, $price_adjusted, $applied_rule  ) {

		if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '>=' ) )
			$cart_item['data']->set_price( $price_adjusted );
		else 
			$cart_item['data']->price = $price_adjusted;
		//$cart_item['data']->regular_price = $price_adjusted;
	}


	protected function add_discount_info(&$cart_item, $original_price, $original_price_ex_tax, $adjusted_price  ) {

		$cart_item['d_discounts'] = array( 'price' => $original_price, 'price_excluding_tax' => $original_price_ex_tax, 'discounted' => $adjusted_price, 'by' => $this->discounter, 'data' => $this->discount_data );

	}


	protected function remove_discount_info( &$cart_item  ) {

		if (isset( $cart_item['d_discounts'] ) && isset( $cart_item['d_discounts']['by'] ) && $cart_item['d_discounts']['by'] == $this->discounter )
			unset( $cart_item['d_discounts'] );
	}


	protected function reset_cart_item_price( &$cart_item  ) {

		if ( isset( $cart_item['d_discounts'] ) && isset( $cart_item['d_discounts']['by'] ) && $cart_item['d_discounts']['by'] == $this->discounter ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '>=' ) )
				$cart_item['data']->set_price( $cart_item['d_discounts']['price'] );
			else
				$cart_item['data']->price = $cart_item['d_discounts']['price'];
		}
	}


	protected function track_cart_item( &$cart_item_key, $cart_item  ) {
		global $ignitewoo_wsp_discounts_pricing;

		$tracking_variation = isset( $cart_item['variation_id'] );

		$ignitewoo_wsp_discounts_pricing->add_discounted_cart_item( $cart_item_key, $cart_item, $tracking_variation );
	}


	protected function is_item_discounted( $cart_item  ) {

		return isset( $cart_item['d_discounts'] );
	}

}


class IgniteWoo_WSP_Process_Product extends IgniteWoo_WSP_Base_Product {

	var $rules = '';
	
	public function __construct() {

		parent::__construct( 'advanced_dd_product' );

		add_action( 'woocommerce_after_cart_item_quantity_update', array( &$this, 'on_update_cart_item_quantity' ), 99, 2 );

		add_action( 'woocommerce_before_calculate_totals', array( &$this, 'on_calculate_totals' ), 35, 1 );
 
		add_filter( 'woocommerce_calculate_totals', array( &$this, 'on_calculate_totals_pre' ), 9999, 1 );

		// For the mini-cart widget to display prices properly
		if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 )
			add_filter( 'woocommerce_cart_item_price', array( &$this, 'cart_item_price' ), 20, 3 );
		else
			add_filter( 'woocommerce_cart_item_price_html', array( &$this, 'cart_item_price' ), 20, 3 );

		// Adjust mini cart to show subtotal WITH discount, default widget behavior is not show with discounts applied
		add_filter( 'woocommerce_cart_subtotal', array( &$this, 'mini_cart_subtotal' ), 20, 3 );


	}


        public function is_ajax() {

                if ( defined('DOING_AJAX') ) 
			return true;

                if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) 
			return true;
		else 
			return false;

        }


	public function cart_item_price( $price, $item, $key ) { 
		global $ignitewoo_wsp_discounts;

		if ( function_exists( 'wc_get_page_id' ) )
			$cart_page_id = wc_get_page_id( 'cart' );
		else 
			$cart_page_id = woocommerce_get_page_id( 'cart' );

		// Are we on the cart page? 
		if ( is_page( $cart_page_id ) )
			return $price;

		$this->adjust_cart_item( $key, $item );

		if ( isset( $ignitewoo_wsp_discounts->_discounted_cart[ $key ]['data']->price ) )
			return $ignitewoo_wsp_discounts->_discounted_cart[ $key ]['data']->price;
		else
			return $price;
	}


	public function mini_cart_subtotal( $cart_subtotal, $compound, $cart ) { 

		return wc_price( $cart->cart_contents_total );

	}


	public function on_update_cart_item_quantity( $cart_item, $quantity  ) {
	    
	}


	public function on_calculate_totals_pre( $cart ) { 

		return $this->on_calculate_totals( $cart );

	}

	public function on_calculate_totals( $_cart ) {
		global $woocommerce;

		if ( sizeof( $_cart->cart_contents ) > 0 ) {

			foreach ( $_cart->cart_contents as $cart_item_key => &$values  ) {

				$this->adjust_cart_item( $cart_item_key, $values );

			}

		}

		return $_cart;
	}

	function get_cart_item_discounts( $for_display = false ) {
		global $ignitewoo_wsp_discounts, $product;

		$discounts = array();
				
		$conditions_met = 0;
		
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

		$pricing_rule_sets = get_post_meta( $product_id, '_wholesale_pricing_rules', true );

		if ( !empty( $pricing_rule_sets ) && is_array( $pricing_rule_sets ) && sizeof($pricing_rule_sets) > 0 ) {
		
			foreach ( $pricing_rule_sets as $ruleset ) { 
		
				$conditions_met = 0;
		
				$pricing_conditions = $ruleset['conditions'];

				foreach ( $pricing_conditions as $condition )
					$conditions_met = $this->handle_condition( $condition, '' );

				if ( $conditions_met )
				foreach ( $ruleset['rules'] as $key => $value ) {

					$index = $ruleset['rules'][$key]['from'] . '_' . $ruleset['rules'][$key]['to'];
					
					if ( $for_display )
						$index .=  '_' . uniqid(); 
					
					if ( 'price_discount' == $ruleset['rules'][$key]['type'] )
						$price = wc_price( $ruleset['rules'][$key]['amount'] ) . __( ' off each', 'ignitewoo_wholesale_pro' );
					else if ( 'percentage_discount' == $ruleset['rules'][$key]['type'] )
						$price = $ruleset['rules'][$key]['amount'] . __( '% off each', 'ignitewoo_wholesale_pro' );
					if ( 'fixed_price' == $ruleset['rules'][$key]['type'] )
						$price = wc_price( $ruleset['rules'][$key]['amount'] ) . __( ' each', 'ignitewoo_wholesale_pro' );
					
					if ( isset( $ruleset['variation_rules']['args'] ) )
						$applies_to = array( 
							'type' => $ruleset['collector']['type'],
							'args' => $ruleset['variation_rules']['args']
							);
					
					else 
						$applies_to = array( 
							'type' => $ruleset['collector']['type'],
							'args' => array()
							);
							
					$discounts[ $index ] = array( 
							'from' => $ruleset['rules'][$key]['from'],
							'to' => $ruleset['rules'][$key]['to'],
							'amount' => $ruleset['rules'][$key]['amount'],
							'type' => $ruleset['rules'][$key]['type'],
							'string' => $price,
							'applies_to' => $applies_to
						);
						
				}
			
			}

			return $discounts;
		}
		
		$sitewide_rules = get_option( '_woocommerce_wholesale_sitewide_rules', false );

		if ( isset( $sitewide_rules ) && is_array( $sitewide_rules ) && !empty( $sitewide_rules ) )
		foreach ( $sitewide_rules as $pricing_rule_set ) {

			$pricing_conditions = $pricing_rule_set['conditions'];

			$collector = $this->get_collector( $pricing_rule_set );

			if ( !$collector )
				$collector = array( 'type' => 'product' );
				
			if ( is_array( $pricing_conditions ) && sizeof( $pricing_conditions ) > 0 ) {

				foreach ( $pricing_conditions as $condition )
					$conditions_met = $this->handle_condition( $condition, '' );
					
				if ( $conditions_met )
				foreach( $pricing_rule_set['rules'] as $r ) {
					
					if ( 'price_discount' == $r['type'] )
						$price = wc_price( $r['amount'] ) . __( ' off each', 'ignitewoo_wholesale_pro' );
					else if ( 'percentage_discount' == $r['type'] )
						$price = $r['amount'] . __( '% off each', 'ignitewoo_wholesale_pro' );
					if ( 'fixed_price' == $r['type'] )
						$price = wc_price( $r['amount'] ) . __( ' each', 'ignitewoo_wholesale_pro' );
					
					$discounts[ $r['from'] . '_' . $r['to'] ] = $r;
					
					$discounts[ $r['from'] . '_' . $r['to'] ]['string'] = $price;
										
					if ( 'cat' == $collector['type'] )
						$discounts[ $r['from'] . '_' . $r['to'] ]['applies_to'] = array( 'type' => $collector['type'], 'args' => !empty( $collector['args'] ) ? $collector['args'] : array() );
					else
						$discounts[ $r['from'] . '_' . $r['to'] ]['applies_to'] = $collector['type'];
				}
					
			}
			
		}

		return $discounts;

	}
	
	
	public function adjust_cart_item( $cart_item_key, &$cart_item  ) {
		global $woocommerce, $ignitewoo_wsp_discounts_pricing, $woocommerce_wholesale_pricing;

		// already discounted?
		//if ( $this->is_item_discounted( $cart_item ) )
		//	return false;

		$this->reset_cart_item_price( $cart_item  );

		$original_price = $cart_item['data']->get_price();

		$_product = $cart_item['data'];
	
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

		// Per Product rules take precendence if they exist - per documentation
		
		//if ( empty( $ignitewoo_wsp_discounts_pricing->sitewide_discounts->rules ) )
		$pricing_rule_sets = get_post_meta( $product_id, '_wholesale_pricing_rules', true );

		if ( empty( $pricing_rule_sets ) )
			$pricing_rule_sets = array();
			
		if ( empty( $pricing_rule_sets_global ) )
			$pricing_rule_sets_global = $ignitewoo_wsp_discounts_pricing->sitewide_discounts->rules;

		if ( empty( $pricing_rule_sets_global ) )
			$pricing_rule_sets_global = array();
			
			
		if ( is_array( $pricing_rule_sets ) && is_array( $pricing_rule_sets_global ) )
			$pricing_rule_sets = array_merge( $pricing_rule_sets, $pricing_rule_sets_global );
//var_dump( $pricing_rule_sets ); die;
		if ( is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) {

			foreach ( $pricing_rule_sets as $pricing_rule_set ) {

				$execute_rules = false;

				$conditions_met = 0;

				$variation_rules = isset( $pricing_rule_set['variation_rules'] ) ? $pricing_rule_set['variation_rules'] : '';

				if ( ( $_product->is_type( 'variable' ) || $_product->is_type('variation') ) && $variation_rules ) {

					if ( isset( $variation_id ) && isset( $variation_rules['args']['type'] ) && $variation_rules['args']['type'] == 'variations' ) {

					    if ( !is_array( $variation_rules['args']['variations'] ) || !in_array( $variation_id, $variation_rules['args']['variations'] ) )
						    continue;

					}
				}

//var_dump( $variation_rules );
//echo '<p>';
				$pricing_conditions = $pricing_rule_set['conditions'];

				$collector = $this->get_collector( $pricing_rule_set );

				if ( !$collector )
					$collector = array( 'type' => 'product' );

				if ( is_array( $pricing_conditions ) && sizeof( $pricing_conditions ) > 0 ) {

					foreach ( $pricing_conditions as $condition )
						$conditions_met += $this->handle_condition( $condition, $cart_item );

					if ( $pricing_rule_set['conditions_type'] == 'all' )
						$execute_rules = $conditions_met == count( $pricing_conditions );
						
					elseif ( $pricing_rule_set['conditions_type'] == 'any' )
						$execute_rules = $conditions_met > 0;

				} else {
				
					$execute_rules = true;
					
				}

				if ( $execute_rules ) {

					$pricing_rules = $pricing_rule_set['rules'];

					if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) { 
						$price = $cart_item['data']->get_price( 'ign_read' );
						$price = $woocommerce_wholesale_pricing->maybe_return_price( $price, $cart_item['data'] );
					} else { 
						$price = $cart_item['data']->price;
					}
					
					$original_price = $price;
//var_dump( $price ); die;
					if ( is_callable( 'wc_get_price_excluding_tax' ) )
						$original_price_ex_tax = wc_get_price_excluding_tax( $cart_item['data'] );
					else 
						$original_price_ex_tax = $cart_item['data']->get_price_excluding_tax();
						
// var_dump( $pricing_rules, $original_price, $original_price_ex_tax, $collector );
//echo '<p>';
					$price_adjusted = $this->get_adjusted_price( $pricing_rules, $original_price, $collector, $cart_item );
//var_dump( $pricing_rules, $original_price, $price_adjusted ); die;
//echo '<p>';
					if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {

						$this->add_adjustment( $cart_item, $price_adjusted, $pricing_rule_set );

						$this->add_discount_info( $cart_item, $original_price, $original_price_ex_tax, $price_adjusted );

						$this->track_cart_item( $cart_item_key, $cart_item );

						break;

					} else {

						$this->remove_discount_info( $cart_item );

						$tracking_variation = $collector['type'] == 'variation' && isset( $variation_id );

						$ignitewoo_wsp_discounts_pricing->remove_discounted_cart_item( $cart_item_key, $cart_item, $tracking_variation );
					}
				}
			}
		}
	}


	private function get_adjusted_price( $pricing_rules, $price, $collector, $cart_item  ) {

		$result = false;
//var_dump( $collector ); die;
		if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {

			foreach ( $pricing_rules as $rule ) {

				if ( 'product' == $collector['type'] && !empty( $cart_item['variation_id'] ) && is_numeric( $cart_item['variation_id'] ) )
					$collector['type'] = 'variation';
					
				$q = $this->get_quantity_to_compare( $cart_item, $collector );

				if ( $rule['from'] == '*' ) {
					$rule['from'] = 0;
				}

				if ( $rule['to'] == '*' ) {
					$rule['to'] = $q;
				}

				if ( $q >= $rule['from'] && $q <= $rule['to'] ) {

				    $this->discount_data['rule'] = $rule;

				    switch ( $rule['type'] ) {

					    case 'price_discount':
						    $adjusted = floatval( $price ) - floatval( $rule['amount'] );
						    $result = $adjusted >= 0 ? $adjusted : 0;
						    break;

					    case 'percentage_discount':
						    if ( $rule['amount'] > 1 )
							    $rule['amount'] = $rule['amount'] / 100;
						    $result = round(floatval( $price ) - ( floatval( $rule['amount'] ) * $price ), 2 );
						    break;

					    case 'fixed_price':
						    $result = round( $rule['amount'], 2 );
						    break;

					    default:
						    $result = false;
						    break;

				    }

				    break;
				}
			}
		}

		return $result;
	}


	private function get_collector( $pricing_rule_set  ) {

		if ( !isset( $pricing_rule_set['collector'] ) )
			return;
			
		$this->discount_data['collector'] = $pricing_rule_set['collector'];

		return $pricing_rule_set['collector'];
	}


	private function handle_condition( $condition, $cart_item  ) {
		global $user_ID; 

		$result = 0;

		switch ( $condition['type'] ) {

			case 'apply_to':

				if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {

					if ( $condition['args']['applies_to'] == 'everyone' ) {
						$result = 1;
						
					} elseif ( $condition['args']['applies_to'] == 'unauthenticated' ) {
					
						if ( !is_user_logged_in() ) {
							$result = 1;
						}
						
					} elseif ( $condition['args']['applies_to'] == 'authenticated' ) {
					
						if ( is_user_logged_in() ) {
							$result = 1;
						}
						
					} elseif ( $condition['args']['applies_to'] == 'roles' && isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) ) {
					
						if ( is_user_logged_in() ) {
						
						    foreach ( $condition['args']['roles'] as $role ) {
						    
							    if ( current_user_can( $role ) ) {
								    $result = 1;
								    break;
							    }
						    }
						    
						}
						
					} elseif ( $condition['args']['applies_to'] == 'users' && isset( $condition['args']['users'] ) && is_array( $condition['args']['users'] ) ) {

						if ( is_user_logged_in() ) {
						    foreach ( $condition['args']['users'] as $key => $uid ) {

							    if ( $user_ID == $uid ) {
								    $result = 1;
								    break;
							    }
						    }
						    
						}
					}
				}
				break;

			default:
				break;
		}

		if ( $result )
			$this->discount_data['condition'] = $condition;

		return $result;
	}


	private function get_quantity_to_compare( $cart_item, $collector  ) {
		global $ignitewoo_wsp_discounts, $ignitewoo_wsp_discounts_pricing;

		$quantity = 0;

		if ( is_array( $cart_item ) ) { 
			$product_id = $cart_item['product_id'];
		} else {
			if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
				$product_id = $cart_item->get_parent_id();
				if ( !empty( $product_id ) ) 
					$variation_id =$cart_item->get_id();
				else 
					$product_id = $cart_item->get_id();
			} else {
				$product_id = $cart_item->id;
				$variation_id = $cart_item->variation_id;
			}
			// $product_id = ign_get_product_id( $cart_item, false );
			//$variation_id = ign_get_product_id( $cart_item, true );
		}

		switch ( $collector['type'] ) {

			case 'cart_item':
				$quantity = $cart_item['quantity'];
				break;

			case 'cat' :

				if (isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {
					foreach ( $collector['args']['cats'] as $cat ) {
//var_dump( is_object_in_term( $cart_item['product_id'], 'product_cat', $cat ) ); 
						if ( isset( $ignitewoo_wsp_discounts_pricing->category_counts[$cat] ) && is_object_in_term( $product_id, 'product_cat', $cat ) ) {
						    $quantity += $ignitewoo_wsp_discounts_pricing->category_counts[$cat];
						}
					}
				}
				break;

			case 'product':
				if (isset( $ignitewoo_wsp_discounts_pricing->product_counts[ $cart_item['product_id'] ] ) ) {
				    $quantity += $ignitewoo_wsp_discounts_pricing->product_counts[ $cart_item['product_id'] ];
				}
				break;

			case 'variation':
				if ( isset( $ignitewoo_wsp_discounts_pricing->variation_counts[ $cart_item['variation_id'] ] ) ) {
				    $quantity += $ignitewoo_wsp_discounts_pricing->variation_counts[ $cart_item['variation_id'] ];
				}
				break;
		}

		$this->discount_data['collected_quantity'] = $quantity;

		return $quantity;
	}

}

class IgniteWoo_WSP_Sitewide_Discounts extends IgniteWoo_WSP_Process_Product {

	private $applied_rules = array();

	var $rules = '';

	public function __construct( $priority = '' ) {

		parent::__construct( 'dd_sitewide' );

		$this->rules = get_option( '_woocommerce_wholesale_sitewide_rules', array() );

		add_action( 'woocommerce_after_cart_item_quantity_update', array(&$this, 'on_update_cart_item_quantity' ), 100, 2); // priority 98 makes it run before product rules

		add_action( 'woocommerce_before_calculate_totals', array(&$this, 'on_calculate_totals' ), 36 ); 
		// priority 34 makes it run before product rules

		add_filter( 'woocommerce_calculate_totals', array( &$this, 'on_calculate_totals_pre' ), 99991, 1 );

	}

}
