<?php

// Copyright: © 2011 Lucas Stark.
// Copyright: © 2012 - IgniteWoo.com

class IgniteWoo_Wholesale_Pricing_Plus_Rules_Admin {
    
	public function __construct() {
	    
		if ( !class_exists( 'woocommerce_wholesale_pricing' ) && !class_exists( 'woocommerce_tiered_pricing' ) ) 
			return;

		add_action( 'woocommerce_product_write_panel_tabs', array(&$this, 'on_product_write_panel_tabs' ), 99);

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) )
			add_action( 'woocommerce_product_data_panels', array(&$this, 'product_data_panel' ), 99);
		else 
			add_action( 'woocommerce_product_write_panels', array(&$this, 'product_data_panel' ), 99);

		add_action( 'woocommerce_process_product_meta', array(&$this, 'process_meta_box' ), 1, 2);

		add_action( 'wp_ajax_ignitewoo_wholesale_product_create_empty_ruleset', array(&$this, 'create_empty_ruleset' ) );

		add_action ( 'admin_head', array( &$this, 'admin_head' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts') );

	}

	function admin_scripts() { 
		global $woocommerce;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
		
			wp_register_script( 'chosen', $woocommerce->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery'), '1.0' );

			wp_enqueue_script( 'chosen' );
		}
		
	}

	function admin_head() { 
		?>

		<style>
		#dynamic_wholesale_pricing_data ul.product_data_tabs li.dynamic_pricing_options a{background-position:9px 9px;}
		#woocommerce-product-data ul.product_data_tabs li.dynamic_pricing_options a {
			<?php if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) { ?>
			padding:5px 5px 5px 9px;
			<?php } else if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) { ?>
			padding:5px 5px 5px 28px;
			<?php } else { ?>
			padding:9px 9px 9px 34px;
			<?php } ?>
			line-height:16px;
			border-bottom:1px solid #d5d5d5;
			text-shadow:0 1px 1px #fff;
			/*color:#555;*/
			background-position: 9px -695px;
			background: url("<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/wc-tab-icons.png") no-repeat scroll 9px -55px #F1F1F1
		}
		#woocommerce-product-data ul.product_data_tabs li.active a{background-color:#f8f8f8;border-bottom:1px solid #f8f8f8;}
		#dynamic_wholesale_pricing_data button { float:right; color: #333; font-weight: bold; }
		#dynamic_wholesale_pricing_data .section { display: block; margin-bottom: 10px; }
		#dynamic_wholesale_pricing_data .list-column { float:left; margin-bottom: 0; }
		#dynamic_wholesale_pricing_data.woocommerce_options_panel label,#dynamic_wholesale_pricing_data.woocommerce_options_panel input, #dynamic_wholesale_pricing_data.woocommerce_options_panel select {
			float:none;
		}
		#dynamic_wholesale_pricing_data.woocommerce_options_panel div.section {
			zoom: 1;
			margin: 9px 0 0;
			font-size: 12px;
			padding: 5px 9px;
		}
		#dynamic_wholesale_pricing_data.woocommerce_options_panel label {
			display:block;
			line-height: 24px;
			float: left;
			width: 250px;
			padding: 0;
			margin: 4px 0 0 5px;
		}
		#dynamic_wholesale_pricing_data table{ width:100%;position:relative;}
		#dynamic_wholesale_pricing_data table thead th{ background:#ececec;padding:7px 9px;font-size:11px;text-align:left;}
		#dynamic_wholesale_pricing_data table td{ padding:2px 9px;text-align:left;vertical-align:middle;border-bottom:1px dotted #ececec;}
		#dynamic_wholesale_pricing_data table td input, #dynamic_wholesale_pricing_data table td textarea{ width:100%;margin:0;display:block;}
		#dynamic_wholesale_pricing_data table td select{width:100%;}
		#dynamic_wholesale_pricing_data table td input, #dynamic_wholesale_pricing_data table td textarea{ font-size:14px;padding:4px;color:#555;}
		#dynamic_wholesale_pricing_data table td input, #dynamic_wholesale_pricing_data .list-column input{ font-size:14px;padding:4px;color:#555;width:100%}
		#dynamic_wholesale_pricing_data .list-column input  { width: 17px; }
		#dynamic_wholesale_pricing_data .rule_user_list { 
			border: 1px solid #ccc;
			margin-top: 2px;
			padding: 0px 10px;
			overflow: auto;
			width: 600px;
			height: 150px;
		}
		.woocommerce_wholesale_sitewide_pricing_ruleset .chosen-container, #dynamic_wholesale_pricing_data .chosen-container {
			min-height:30px !important;
			width: 300px !important;
		}
		.woocommerce_wholesale_sitewide_pricing_ruleset .chosen-container-multi .chosen-choices, #dynamic_wholesale_pricing_data .chosen-container-multi .chosen-choices {
			min-height:30px !important;
		}
		.woocommerce_wholesale_sitewide_pricing_ruleset .chosen-container-multi .chosen-choices li.search-field input, #dynamic_wholesale_pricing_data .chosen-container-multi .chosen-choices li.search-field input {
			min-height:30px !important;
		}
		</style>

	<?php
	}

	public function on_product_write_panel_tabs() {

		?>
		<li class="pricing_tab dynamic_pricing_options"><a href="#dynamic_wholesale_pricing_data"><span><?php _e( 'Wholesale Discounts', 'ignitewoo_wholesale' ); ?></span></a></li>
		<?php
	}

	public function product_data_panel() {
		global $post;

		$pricing_rule_sets = get_post_meta( $post->ID, '_wholesale_pricing_rules', true);

		?>
		<div id="dynamic_wholesale_pricing_data" class="panel woocommerce_options_panel">
			<p class="description"><?php _e( 'Here you can add groups of rules that will be applied to wholesale buyers.', 'ignitewoo_wholesale' ) ?></p>
			<p class="description"><?php _e( 'Rules are applied in the order seen, where the first matching is applied.', 'ignitewoo_wholesale' ) ?></p>
			<p class="description" style="margin-bottom: 7px"><?php _e( 'You can also rearrange the order of groups by dragging and dropping them.', 'ignitewoo_wholesale' ) ?></p>

			<div id="woocommerce-wholesale-pricing-rules-wrap" data-setindex="<?php echo count( $pricing_rule_sets); ?>">
				<?php $this->meta_box_javascript(); ?>
				<?php $this->meta_box_css(); ?>  
				<?php if ( $pricing_rule_sets && is_array( $pricing_rule_sets) && sizeof( $pricing_rule_sets) > 0) : ?>
					<?php $this->create_rulesets( $pricing_rule_sets); ?>
				<?php endif; ?>        
			</div>   
			<button title="<?php _e( 'Adds add another wholesale discount rule group.', 'ignitewoo_wholesale' )?> " id="woocommerce-pricing-add-ruleset" type="button" class="button">Add Discount Group</button>
			<div class="clear"></div>
		</div>
		<?php
	}


	function get_product( $product_id, $args = array() ) {

		$product = null;

		if ( version_compare( WOOCOMMERCE_VERSION, "3.0" ) >= 0 ) {

			// WC 2.0
			$product = wc_get_product( $product_id, $args );
			
		} else if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {

			// WC 2.0
			$product = get_product( $product_id, $args );

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

	
	public function create_rulesets( $pricing_rule_sets ) {
		global $ignitewoo_wholesale_pricing_plus;

		foreach ( $pricing_rule_sets as $pricing_rule_set) {

			$name = uniqid( 'set_' );

			$pricing_rules = isset( $pricing_rule_set['rules'] ) ? $pricing_rule_set['rules'] : null;

			$pricing_conditions = isset( $pricing_rule_set['conditions'] ) ? $pricing_rule_set['conditions'] : null;

			$collector = isset( $pricing_rule_set['collector'] ) ? $pricing_rule_set['collector'] : null;

			$variation_rules = isset( $pricing_rule_set['variation_rules'] ) ? $pricing_rule_set['variation_rules'] : null;

			?>
			<div id="woocommerce-pricing-ruleset-<?php echo $name; ?>" class="woocommerce_wholesale_pricing_ruleset">
				<div id="woocommerce-pricing-conditions-<?php echo $name; ?>" class="section    ">
					<h4 class="first"><?php _e( 'Wholesale Discount Rule Group', 'ignitewoo_wholesale' )?><a href="#" data-name="<?php echo $name; ?>" class="wholesale_delete_pricing_ruleset" ><img  src="<?php echo $ignitewoo_wholesale_pricing_plus->plugin_url; ?>/assets/images/delete.png" title="<?php _e( 'Delete this rule group', 'ignitewoo_wholesale') ?>" alt="<?php _e( 'Delete this rule group', 'ignitewoo_wholesale') ?>" style="cursor:pointer; margin:0 3px;float:right;" /></a></h4>
					<?php
					$condition_index = 0;

					if ( is_array( $pricing_conditions) && sizeof( $pricing_conditions) > 0 ) {
						?>
						<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" /> 

						<?php
						foreach ( $pricing_conditions as $condition ) { 

							$condition_index++;

							$this->create_condition( $condition, $name, $condition_index);

						} 

					} else {

						?>

						<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" /> 

						<?php

						$this->create_condition(array( 'type' => 'apply_to', 'args' => array( 'applies_to' => 'everyone', 'roles' => array())), $name, 1);

					}

					?>
				</div>

				<div id="woocommerce-pricing-collector-<?php echo $name; ?>" class="section" style="position:relative; z-index: 99">
					<?php
					if (is_array( $collector) && count( $collector) > 0) {

						$this->create_collector( $collector, $name);

					} else {

						$product_cats = array();

						$this->create_collector(array( 'type' => 'product', 'args' => array( 'cats' => $product_cats)), $name);
					}
					?>
				</div>

				<div id="woocommerce-pricing-variations-<?php echo $name; ?>" class="section">
					<?php
					$variation_index = 0;

					if (is_array( $variation_rules) && count( $variation_rules) > 0) {

						$this->create_variation_selector( $variation_rules, $name);

					} else {

						$product_cats = array();

						$this->create_variation_selector(null, $name);

					}
					?>
				</div>
				<div class="clear"></div>
				<div class="section">
					<table id="woocommerce-pricing-rules-table-<?php echo $name; ?>" data-lastindex="<?php echo (is_array( $pricing_rules) && sizeof( $pricing_rules) > 0) ? count( $pricing_rules) : '1'; ?>">
						<thead>
						<th>
							<?php _e( 'Minimum Quantity', 'ignitewoo_wholesale' ); ?>
						</th>
						<th>
							<?php _e( 'Maximum Quantity', 'ignitewoo_wholesale' ); ?>
						</th>
						<th>
							<?php _e( 'Discount Type', 'ignitewoo_wholesale' ); ?>
						</th>
						<th>
							<?php _e( 'Discount Amount', 'ignitewoo_wholesale' ); ?>
						</th>
						<th>&nbsp;</th>
						</thead>
						<tbody>
							<?php
							$index = 0;

							if (is_array( $pricing_rules) && sizeof( $pricing_rules) > 0 ) {

								foreach ( $pricing_rules as $rule ) {

									$index++;

									$this->get_row( $rule, $name, $index );

								}

							} else {

								$this->get_row(array( 'to' => '', 'from' => '', 'amount' => '', 'type' => '' ), $name, 1 );

							}
							?>
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
			<?php
		}
	}

	public function create_empty_ruleset( $set_index ) {

		$pricing_rule_sets = array();

		$pricing_rule_sets['set_' . $set_index] = array();

		$pricing_rule_sets['set_' . $set_index]['title'] = 'Rule Set ' . $set_index;

		$pricing_rule_sets['set_' . $set_index]['rules'] = array();

		$this->create_rulesets( $pricing_rule_sets);

		die;
	}

	private function create_condition( $condition, $name, $condition_index ) {
		global $wp_roles;

		switch ( $condition['type'] ) {

		    case 'apply_to':
			$this->create_condition_apply_to( $condition, $name, $condition_index);
			break;

		    default:
			break;

		}
	}

	private function create_condition_apply_to( $condition, $name, $condition_index ) {
		global $wpdb;

		if ( !isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		$all_roles = $wp_roles->roles;

		/*
		foreach( $all_roles as $key => $data ) {

			if ( false === strpos( $key, 'wholesale_' ) )
				unset( $all_roles[ $key ] );

		}
		*/
		
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

			<label for="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>">Applies To:</label>

			<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][type]" value="apply_to" />

			<select title="<?php _e( 'Choose if this rule should apply to everyone, or to specific roles.  Useful if you only give discounts to existing customers, or if you have tiered pricing based on the users role.', 'ignitewoo_wholesale' )?>" class="wc-enhanced-select wholesale_pricing_rule_apply_to" id="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][applies_to]">
				<option <?php selected( 'everyone', $condition['args']['applies_to'] ); ?> value="everyone">Everyone</option>
				<option <?php selected( 'roles', $condition['args']['applies_to'] ); ?> value="roles">Specific Roles</option>
				<option <?php selected( 'users', $condition['args']['applies_to'] ); ?> value="users">Specific Users</option>
			</select>

			<div class="roles" style="clear:both;margin-top: 10px; min-width:380px; margin-left: 157px; position:relative;<?php echo $div_style; ?>">

				<?php /* $chunks = array_chunk( $all_roles, ceil(count( $all_roles) / 3), true); ?>

				<?php foreach ( $chunks as $chunk) { ?>

						<ul class="list-column">        

							<?php foreach ( $chunk as $role_id => $role) { ?>
							<?php $role_checked = (isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) && in_array( $role_id, $condition['args']['roles'] )) ? 'checked="checked"' : ''; ?>
							<li>
								<label for="role_<?php echo $role_id; ?>" class="selectit"> 
									<input <?php echo $role_checked; ?> type="checkbox" id="role_<?php echo $role_id; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" value="<?php echo $role_id; ?>" /> <?php echo $role['name']; ?>
								</label>
							</li>
							<?php } ?>

						</ul>

				<?php } */ ?>
				
				<p class="description"><?php _e( 'Click in the box and start typing a name to select roles. Add as many as you need.', 'ignitewoo_wholesale' )?></p>
				
				<select class="wholesale_plus_rule_roles" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" style="min-width:380px;">

					<?php foreach( $all_roles as $role_id => $role) { ?>

						<?php $role_checked = (isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) && in_array( $role_id, $condition['args']['roles'] )) ? 'selected="selected"' : ''; ?>
						
						<option <?php echo $role_checked ?> value="<?php echo $role_id ?>">
							<?php echo $role['name']; ?>
						</option>

					<?php } ?>

				</select>

			</div>

			<div class="clear"></div>


			<div class="users" style="clear: both; margin-left: 157px; position:relative;  ">

				<p class="description"><?php _e( 'Click in the box and start typing a name to select users. Add as many as you need.', 'ignitewoo_wholesale' )?></p>

				<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) { ?>
				
					<select class="wholesale_plus_rule_users" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][users][]" style="min-width:380px">

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
						//$pricing_users = implode( ',' , $ru );
	
						foreach( $ru as $pu ) { 
							if ( empty( $pu ) )
								continue;
							$user = get_user_by( 'id', $pu );
							
							if ( !empty( $user ) && !is_wp_error( $user ) )
								$user_strings[ $pu ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
						}
					}

					?>
					<br/>
					<input type="hidden" class="wholesale_plus_rule_users" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][users]" data-placeholder="<?php _e( 'Search for users&hellip;', 'ignitewoo-bookings' ); ?>" data-selected='<?php echo esc_attr( json_encode( $user_strings ) ) ?>' value="<?php if ( is_array( $ru ) ) echo implode( ',', $ru ) ?>" data-multiple="true" data-allow_clear="true" data-action="woocommerce_json_search_customers" style="width:380px;min-height:32px"/>
				
				<?php } ?>

			</div>

			<div class="clear"></div>

		<?php
	}

	private function create_variation_selector( $condition, $name ) {
		global $post;

		$post_id = isset( $_POST['post'] ) ? intval( $_POST['post'] ) : $post->ID;

		//$product = new WC_Product( $post_id);
		
		$product = $this->get_product( $post_id );

		if ( !method_exists( $product, 'has_child' ) ) 
			return;
			
		if ( !$product->has_child() )
			return;

		$all_variations = $product->get_children();

		if ( version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) && isset( $all_variations['all'] ) )
			$all_variations = $all_variations['all'];
			
		$div_style = ( $condition['args']['type'] != 'variations' ) ? 'display:none;' : '';


		$this->z = 0 ;
		
		?>

		<div>
			<label for="pricing_rule_variations_<?php echo $name; ?>"><?php _e( 'Product / Variations:', 'ignitewoo_wholesale' ) ?></label>
			
			<select title="<?php _e( 'Choose what you would like to apply this pricing rule set to', 'ignitewoo_wholesale' )?>" class="wholesale_pricing_rule_variations" id="pricing_rule_variations_<?php echo $name; ?>" name="pricing_rules[<?php echo $name; ?>][variation_rules][args][type]" class="wc-enhanced-select">
			
				<option <?php selected( 'product', $condition['args']['type'] ); ?> value="product"><?php _e( 'All Variations', 'ignitewoo_wholesale' )?></option>
				<option <?php selected( 'variations', $condition['args']['type'] ); ?> value="variations"><?php _e( 'Specific Variations', 'ignitewoo_wholesale' ) ?></option>
			</select>

			<div class="variations" style="<?php echo $div_style; ?>">
			    <?php $chunks = array_chunk( $all_variations, ceil(count( $all_variations) / 3), true); ?>

			    <?php foreach ( $chunks as $chunk) : ?>

			        <?php $this->z++ ?>
			        
				<ul class="list-column">        
				    <?php foreach ( $chunk as $variation_id) : ?>

					<?php $variation_object = $this->get_product( $variation_id ); ?>
					
					<?php //$variation_object = new WC_Product_Variation( $variation_id); ?>
					
					<?php $variation_checked = (isset( $condition['args']['variations'] ) && is_array( $condition['args']['variations'] ) && in_array( $variation_id, $condition['args']['variations'] )) ? 'checked="checked"' : ''; ?>
					
					<li>
						<label for="variation_<?php echo $variation_id; ?>" class="selectit"> 
							<input <?php echo $variation_checked; ?> type="checkbox" id="variation_<?php echo $variation_id . $this->z; ?>" name="pricing_rules[<?php echo $name; ?>][variation_rules][args][variations][]" value="<?php echo $variation_id; ?>" /> 
							<?php 
								//echo get_the_title( $variation_id ); 
								echo '#' . $variation_id . ' - ';
								$attrs = $variation_object->get_variation_attributes();
								if ( !empty( $attrs ) ) { 
									$a = array();
									foreach( $attrs as $key => $val ) { 
										$a[] = $variation_object->get_attribute( str_replace( 'attribute_', '', $key ) );
									}
									echo implode( ' &ndash; ', $a );
								}
							?>
						</label>
					</li>
					
				    <?php endforeach; ?>
				    
				</ul>
				
			    <?php endforeach; ?>

			</div>
			<div class="clear"></div>
		</div>
		<?php
	}


	private function create_collector( $collector, $name ) {

		$terms = (array) get_terms( 'product_cat', array( 'get' => 'all' ) );

		$div_style = ( $collector['type'] != 'cat' ) ? 'display:none;' : '';

		?>

		<label for="pricing_rule_when_<?php echo $name; ?>"><?php _e( 'Quantities based on:', 'ignitewoo_wholesale' ); ?></label>
		
		<select title="<?php _e( 'Choose how to calculate the quantity.  This tallied amount is used in determining the min and max quantities used below in the Quantity Pricing section.', 'ignitewoo_wholesale' )?>" class="wc-enhanced-select wholesale_pricing_rule_when" id="pricing_rule_when_<?php echo $name; ?>" name="pricing_rules[<?php echo $name; ?>][collector][type]">
		
			<option title="<?php _e( 'Calculate quantity based on the Product ID', 'ignitewoo_wholesale' )?>" <?php selected( 'product', $collector['type'] ); ?> value="product"><?php _e( 'Product Quantity', 'ignitewoo_wholesale' ); ?></option>
			
			<option title="<?php _e( 'Calculate quantity based on the Variation ID', 'ignitewoo_wholesale' )?>" <?php selected( 'variation', $collector['type'] ); ?> value="variation"><?php _e( 'Variation Quantity', 'ignitewoo_wholesale' ); ?></option>
			
			<option title="<?php _e( 'Calculate quantity based on the Cart Line Item', 'ignitewoo_wholesale' )?>" <?php selected( 'cart_item', $collector['type'] ); ?> value="cart_item"><?php _e( 'Cart Line Item Quantity', 'ignitewoo_wholesale' ); ?></option>
			
			<option title="<?php _e( 'Calculate quantity based on total amount of a category in the cart', 'ignitewoo_wholesale' )?>" <?php selected( 'cat', $collector['type'] ); ?> value="cat"><?php _e( 'Quantity of Category', 'ignitewoo_wholesale' ); ?></option>
		</select>
		<br />
		<div class="cats" style="clear:both; <?php echo $div_style; ?>">

		    <?php $chunks = array_chunk( $terms, ceil(count( $terms) / 3 ) ); ?>

		    <?php foreach ( $chunks as $chunk) : ?>

			<ul class="list-column">        
			    <?php foreach ( $chunk as $term) : ?>
				<?php $term_checked = (isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) && in_array( $term->term_id, $collector['args']['cats'] )) ? 'checked="checked"' : ''; ?> 
				<li>
					<label for="<?php echo $name; ?>_term_<?php echo $term->term_id; ?>" class="selectit"> 
						<input <?php echo $term_checked; ?> type="checkbox" id="<?php echo $name; ?>_term_<?php echo $term->term_id; ?>" name="pricing_rules[<?php echo $name; ?>][collector][args][cats][]" value="<?php echo $term->term_id; ?>" /> <?php echo $term->name; ?>
					</label>
				</li>
			    <?php endforeach; ?>
			</ul>

		    <?php endforeach; ?>

		    <div class="clear"></div>
		</div>

		<?php
	}

	private function get_row( $rule, $name, $index ) {
		global $ignitewoo_wholesale_pricing_plus
		?>
		<tr id="wholesale_pricing_rule_row_<?php echo $name . '_' . $index; ?>">
			<td>
				<input title="<?php _e( 'Apply this adjustment when the quantity in the cart starts at this value.  Use * for any.', 'ignitewoo_wholesale' )?>" class="wholesale_int_pricing_rule" id="pricing_rule_from_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][from]" value="<?php echo $rule['from']; ?>" />
			</td>
			<td>
			    <input title="<?php _e( 'Apply this adjustment when the quantity in the cart is less than this value.  Use * for any.', 'ignitewoo_wholesale' )?>" class="wholesale_int_pricing_rule" id="pricing_rule_to_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][to]" value="<?php echo $rule['to']; ?>" />
			</td>
			<td>
			    <select title="<?php _e( 'The type of adjustment to apply', 'ignitewoo_wholesale' )?>" id="pricing_rule_type_value_<?php echo $name . '_' . $index; ?>" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][type]" class="wc-enhanced-select">
				    <option <?php selected( 'price_discount', $rule['type'] ); ?> value="price_discount">Price Discount</option>
				    <option <?php selected( 'percentage_discount', $rule['type'] ); ?> value="percentage_discount">Percentage Discount</option>
				    <option <?php selected( 'fixed_price', $rule['type'] ); ?> value="fixed_price">Fixed Price</option>
			    </select>
			</td>
			<td>
			    <input title="<?php _e( 'The value of the adjustment. Currency and percentage symbols are not required', 'ignitewoo_wholesale' )?>" class="wholesale_float_pricing_rule" id="pricing_rule_amount_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][amount]" value="<?php echo $rule['amount']; ?>" /> 
			</td>
			<td width="48"><a class="wholesale_add_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img 
				    src="<?php echo $ignitewoo_wholesale_pricing_plus->plugin_url . '/assets/images/add.png'; ?>" 
				    title="<?php _e( 'Add another rule','ignitewoo_wholesale' )?>" alt="<?php _e( 'Add another rule', 'ignitewoo_wholesale' ) ?> "
				    style="cursor:pointer; margin:0 3px;" /></a><a <?php echo ( $index > 1) ? '' : 'style="display:none;"'; ?> class="wholesale_delete_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img 
				    src="<?php echo $ignitewoo_wholesale_pricing_plus->plugin_url . '/assets/images/remove.png'; ?>" 
				    title="<?php _e( 'Remove this rule','ignitewoo_wholesale' )?>" alt=" <?php _e( 'Remove this rule', 'ignitewoo_wholesale' ) ?> "
				    style="cursor:pointer; margin:0 3px;" /></a>
			</td>
		</tr>
		<?php
	}

	private function meta_box_javascript() {
		global $ignitewoo_wholesale_pricing_plus;

		?>
		<script type="text/javascript">

		    jQuery(document).ready(function( $ 	 ) {
			var set_index = 0;
			var rule_indexes = new Array();

			$( '.woocommerce_wholesale_pricing_ruleset' ).each(function(){
				var length = $( 'table tbody tr', $(this) ).length;
				if (length==1) {
				    $( '.delete_pricing_rule', $(this)).hide(); 
				}
			});

			$("#woocommerce-pricing-add-ruleset").click(function(event) {
				event.preventDefault();

				var set_index = $("#woocommerce-wholesale-pricing-rules-wrap").data( 'setindex' ) + 1;

				$("#woocommerce-wholesale-pricing-rules-wrap").data( 'setindex', set_index );

				var data = {
					set_index:set_index,
					post:<?php echo isset( $_GET['post'] ) ? $_GET['post'] : 0; ?>,
					action:'ignitewoo_wholesale_product_create_empty_ruleset'
				}

				$.post(ajaxurl, data, function(response) { 
					$( '#woocommerce-wholesale-pricing-rules-wrap' ).append( response );
					$( '.wholesale_pricing_rule_apply_to').each( function() { 
						$(this).change();
						<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
							//$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_users").not( '.enhanced' ).select2({});
							wc_wholesale_pricing_cost_users_select();
							$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_roles").not( '.enhanced' ).select2({});
							$("#dynamic_wholesale_pricing_data select.wc-enhanced-select").not( '.enhanced' ).select2({});
						<?php } else { ?>
							$("#dynamic_wholesale_pricing_data .wholesale_plus_rule_users, #dynamic_wholesale_pricing_data .wholesale_plus_rule_roles").chosen();
						<?php } ?>
					});

				});                                                                                                    

			});

			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_pricing_rule_apply_to', 'change', function(event) {  
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

			$( '.wholesale_pricing_rule_apply_to').each( function() { 
				$(this).change();
				<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
					//$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_users").addClass( 'enhanced' ).select2({});
					
					$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_roles").addClass( '.enhanced' ).select2({});
				<?php } else { ?>
					$("#dynamic_wholesale_pricing_data .wholesale_plus_rule_users, #dynamic_wholesale_pricing_data .wholesale_plus_rule_roles").chosen();
				<?php } ?>
			});

			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_pricing_rule_variations', 'change', function(event) {  
				var value = $(this).val();
				if (value != 'variations' ) {
				    $( '.variations', $(this).parent()).fadeOut();
				    $( '.variations input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );
				} else {                                                            
				    $( '.variations', $(this).parent()).fadeIn();
				}                                                              
			});

			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_pricing_rule_when', 'change', function(event) {  
			    var value = $(this).val();
			    if (value != 'cat' ) {
				$( '.cats', $(this).closest( 'div' )).fadeOut();
				$( '.cats input[type=checkbox]', $(this).closest( 'div' ) ).removeAttr( 'checked' );

			    } else {                                                            
				$( '.cats', $(this).closest( 'div' )).fadeIn();
			    }                                                              
			});

			//Remove Pricing Set
			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_delete_pricing_ruleset', 'click', function(event) {  
			    event.preventDefault();
			    DeleteRuleSet( $(this).data( 'name' ) );
			});

			//Add Button
			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_add_pricing_rule', 'click', function(event) {  
			    event.preventDefault();
			    InsertRule( $(this).data( 'index' ), $(this).data( 'name' ) );
			    $("#dynamic_wholesale_pricing_data select.wc-enhanced-select").not( '.enhanced' ).select2({});
			});

			//Remove Button                
			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_delete_pricing_rule', 'click', function(event) {  
			    event.preventDefault();
			    DeleteRule( $(this).data( 'index' ), $(this).data( 'name' ) );
			});
			
			<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
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
			
			wc_wholesale_pricing_cost_users_select()
			
			<?php } ?>
			
			/*
			//Validation
			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_int_pricing_rule', 'keydown', function(event) {  
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


			$( '#woocommerce-wholesale-pricing-rules-wrap' ).delegate( '.wholesale_float_pricing_rule', 'keydown', function(event) {  
				// Allow only backspace, delete, tab, and decimal
				if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190 ) {

					    var v = jQuery( this ).val();

					    v = v.replace( /[^0-9\.]/g, "" );

					    jQuery( this ).val( v );

				} else if ( ( event.keyCode >= 48 ) && ( event.keyCode <= 57 ) )  {

					    var v = jQuery( this ).val();

					    v = v.replace( /[^0-9\.]/g, "" );

					    jQuery( this ).val( v );

				} else if ( event.keyCode < 48 || event.keyCode > 57 ) {

					    event.preventDefault(); 

					    var v = jQuery( this ).val();

					    v = v.replace( /[^0-9\.]/g, "" );

					    jQuery( this ).val( v );

				} else if ( event.keyCode < 96 || event.keyCode > 105 ) { 

					    event.preventDefault(); 

					    var v = jQuery( this ).val();

					    v = v.replace( /[^0-9\.]/g, "" );

					    jQuery( this ).val( v );
				}
			});
			*/
			
			$("#woocommerce-wholesale-pricing-rules-wrap").sortable(
			{ 
			    handle: 'h4.first',
			    containment: 'parent',
			    axis:'y'
			});
			
			function InsertRule(previousRowIndex, name) {

			    var $index = $("#woocommerce-pricing-rules-table-" + name).data( 'lastindex' ) + 1;
			    $("#woocommerce-pricing-rules-table-" + name).data( 'lastindex', $index );

			    var html = '';
			    html += '<tr id="wholesale_pricing_rule_row_' + name + '_' + $index + '">';
			    html += '<td>';
			    html += '<input class="wholesale_int_pricing_rule" id="pricing_rule_from_input_'  + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][from]" value="" /> ';
			    html += '</td>';
			    html += '<td>';
			    html += '<input class="wholesale_int_pricing_rule" id="pricing_rule_to_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][to]" value="" /> ';
			    html += '</td>';
			    html += '<td>';
			    html += '<select id="pricing_rule_type_value_' + name + '_' + $index + '" name="pricing_rules[' + name + '][rules][' + $index + '][type]" class="wc-enhanced-select">';
			    html += '<option value="price_discount"><?php _e( 'Price Discount','ignitewoo_wholesale' )?></option>';
			    html += '<option value="percentage_discount"><?php _e( 'Percentage Discount','ignitewoo_wholesale' )?></option>';
			    html += '<option value="fixed_price"><?php _e( 'Fixed Price','ignitewoo_wholesale' )?></option>';
			    html += '</select>';
			    html += '</td>';
			    html += '<td>';
			    html += '<input class="wholesale_float_pricing_rule" id="pricing_rule_amount_input_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][amount]" value="" /> ';
			    html += '</td>';
			    html += '<td width="48">';
			    html += '<a data-index="' + $index + '" data-name="' + name + '" class="wholesale_add_pricing_rule"><img  src="<?php echo $ignitewoo_wholesale_pricing_plus->plugin_url . '/assets/images/add.png'; ?>" title="<?php _e( 'Add another rule','ignitewoo_wholesale' )?>" alt="<?php _e( 'Add another rule','ignitewoo_wholesale' )?>" style="cursor:pointer; margin:0 3px;" /></a>';
			    html += '<a data-index="' + $index + '" data-name="' + name + '" class="wholesale_delete_pricing_rule"><img data-index="' + $index + '" src="<?php echo $ignitewoo_wholesale_pricing_plus->plugin_url . '/assets/images/remove.png'; ?>" title="<?php _e( 'Remove rule','ignitewoo_wholesale' )?>" alt="<?php _e( 'Remove rule','ignitewoo_wholesale' )?>" style="cursor:pointer; margin:0 3px;" /></a>';
			    html += '</td>';
			    html += '</tr>';
																																
			    $( '#wholesale_pricing_rule_row_' + name + '_' + previousRowIndex).after(html);
			    $( '.wholesale_delete_pricing_ruleset', "#woocommerce-pricing-rules-table-" + name).show();

			} 

			function DeleteRule(index, name) {
			    if (confirm( "<?php _e('Are you sure you would like to remove this price adjustment?','ignitewoo_wholesale' ) ?>")) {
				$( '#wholesale_pricing_rule_row_' + name + '_' + index).remove();
																																
				var $index = $( 'tbody tr', "#woocommerce-pricing-rules-table-" + name).length;
				if ( $index > 1) {
				    $( '.wholesale_delete_pricing_ruleset', "#woocommerce-pricing-rules-table-" + name).show();
				} else {
				    $( '.wholesale_delete_pricing_ruleset', "#woocommerce-pricing-rules-table-" + name).hide();
				}
			    }
			}

			function DeleteRuleSet(name) {
			    if ( confirm( "<?php _e('Are you sure you would like to remove this dynamic price set?','ignitewoo_wholesale' )?>" )){
				$( '#woocommerce-pricing-ruleset-' + name ).slideUp().remove();  
			    }
			}



		    });

		</script>
		<?php
	}

	public function meta_box_css() {
	    ?>
		<style>
		#woocommerce-pricing-product div.section { margin-bottom: 10px; }
		#woocommerce-pricing-product label { display:block; font-weight: bold; }
		#woocommerce-pricing-product .list-column { float:left; margin-right:25px; }
		#dynamic_wholesale_pricing_data { padding: 12px; margin: 0; overflow: hidden; zoom: 1; }
		#woocommerce-wholesale-pricing-rules-wrap h4 {  margin-bottom: 10px; border-bottom: 1px solid #E5E5E5; padding-bottom: 6px;  font-size: 1.2em; margin: 1em 0 1em; text-transform: none; }
		#woocommerce-wholesale-pricing-rules-wrap h4.first { margin-top:0px; cursor:move; }
		#woocommerce-wholesale-pricing-rules-wrap select { width:250px; }
		.woocommerce_wholesale_pricing_ruleset {
			border-color:#dfdfdf;
			border-width:1px;
			border-style:solid;
			-moz-border-radius:3px;
			-khtml-border-radius:3px;
			-webkit-border-radius:3px;
			border-radius:3px;
			padding: 0;
			border-style:solid;
			border-spacing:0;
			background-color:#fff;
			margin-bottom: 12px;
			margin-left: 20px;
		}
		</style>
	    <?php
	}

	public function process_meta_box( $post_id, $post ) {

		$pricing_rules = array();

		$valid_rules = array();

		if ( isset( $_POST['pricing_rules'] ) ) {

			$pricing_rule_sets = $_POST['pricing_rules'];

			// pricing_rules[ $name; ][conditions][ $condition_index; ][args][users]

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

			update_post_meta( $post_id, '_wholesale_pricing_rules', $valid_rules);

		} else {

			delete_post_meta( $post_id, '_wholesale_pricing_rules' );

		}
	}

}
?>
