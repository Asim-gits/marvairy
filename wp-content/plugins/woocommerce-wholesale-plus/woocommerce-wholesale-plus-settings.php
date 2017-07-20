<?php

class IgniteWoo_Wholesale_Plus_Settings { 

	function sitewide_discount_rule_box() {

		/*
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
		jQuery( document ).ready( function() { 
			jQuery( '#woocommerce-wholesale-sitewide-pricing-rules-wrap' ).bind( 'DOMSubtreeModified', function() { 
				setTimeout( function() { 
					jQuery( '.chosen' ).not( '.enhanced' ).addClass('enhanced').select2();	
					}, 500 );
			})
		});
		</script>
		<?php }*/ ?>
		
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
				
				<p class="description"><?php _e( 'Click in the box and start typing a name to select roles. Add as many as you need.', 'ignitewoo_wholesale' )?></p>
				
				<select class="chosen wholesale_plus_rule_roles" multiple="multiple" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" style="min-width:380px;">

					<?php foreach( $all_roles as $role_id => $role) { ?>

						<?php $role_checked = (isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) && in_array( $role_id, $condition['args']['roles'] )) ? 'selected="selected"' : ''; ?>
						
						<option <?php echo $role_checked ?> value="<?php echo $role_id ?>">
							<?php echo $role['name']; ?>
						</option>

					<?php } ?>

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
			

			<?php /* if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
				//$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_users").not( '.enhanced' ).select2({});
				wc_wholesale_pricing_cost_users_select();
				$(".woocommerce_wholesale_sitewide_pricing_ruleset select.wholesale_plus_rule_roles").not( '.enhanced' ).select2({});
			<?php } else { ?>
				$(".woocommerce_wholesale_sitewide_pricing_ruleset .wholesale_plus_rule_users, #dynamic_wholesale_pricing_data .wholesale_plus_rule_roles").chosen();
			<?php } */ ?>

			$( '.pricing_rule_apply_to').each( function() { 
				$(this).change();

				<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
					//$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_users").addClass( 'enhanced' ).select2({});
					
					$("woocommerce_wholesale_sitewide_pricing_ruleset select.wholesale_plus_rule_roles").addClass( '.enhanced' ).select2({});
				<?php } else { ?>
					$("woocommerce_wholesale_sitewide_pricing_ruleset select.wholesale_plus_rule_users, #dynamic_wholesale_pricing_data select.wholesale_plus_rule_roles").chosen();
				<?php } ?>
			});
			
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
					$( '.wholesale_pricing_rule_apply_to').each( function() { 
						$(this).change();
						<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '>=' ) ) { ?>
							//$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_users").not( '.enhanced' ).select2({});
							wc_wholesale_pricing_cost_users_select();
							$("#dynamic_wholesale_pricing_data select.wholesale_plus_rule_roles").not( '.enhanced' ).select2({});
						<?php } else { ?>
							$("#dynamic_wholesale_pricing_data .wholesale_plus_rule_users, #dynamic_wholesale_pricing_data select.wholesale_plus_rule_roles")chosen();
						<?php } ?>
					});
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

			/*
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
			*/
			
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

$ignitewoo_wholesale_pricing_plus_settings = new IgniteWoo_Wholesale_Plus_Settings();


}

