<?php
/**
 * Product quantity inputs
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 * @editor: Faisal Sarfraz
 * @date: Mar 3, 2017
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$defaults = array(
	'max_value'   => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
	'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
	'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
);

if ( ! empty( $defaults['min_value'] ) )
	$min = $defaults['min_value'];
else $min = 1;

if ( ! empty( $defaults['max_value'] ) )
	$max = $defaults['max_value'];
else $max = 10;

if ( ! empty( $defaults['step'] ) )
	$step = $defaults['step'];
else $step = 1;

?>
<div class="pull-left quantity_select" style="width: 50%;">
	<select name="<?php echo esc_attr( $input_name ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="qty">
	<?php
        $values = array();
        if(is_user_logged_in() && current_user_can("buy_wholesale")){
            $values = array("10","50","100","200","300","500");
        }
        else {
            $values = array("1","2","3","4","5");
        }
        foreach ($values as $value):
            if ( $value == $input_value )
		$selected = ' selected';
            else $selected = '';
            echo '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
        endforeach;
        
//	for ( $count = $min; $count <= $max; $count = $count+$step ) {
//		if ( $count == $input_value )
//			$selected = ' selected';
//		else $selected = '';
//		echo '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';
//	}
	?>
	</select>
</div>
<div class="clearfix"></div>