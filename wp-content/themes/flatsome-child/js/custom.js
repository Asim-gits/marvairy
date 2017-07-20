/* 
 * 
 * Custom Script file
 * @author: Faisal
 * @date: May 18, 2017
 * 
 */

/* Custom JS for prices update */
jQuery(document).ready(function(e){
    if(jQuery('body').hasClass('single-product')){
        var $unit_price = jQuery('body.single-product .product-page-price .amount').clone().children().remove().end().text();
        $unit_price_num = $unit_price.replace(/\,/g, '.');
        $qty  = jQuery('body.single-product select.qty').val();
        $np = Math.round(($qty * $unit_price_num)*100)/100;
        /*$np = $np.toString();*/
        $np = $np.toFixed(2);
        $np = $np.replace(/\./g, ',');
        jQuery('body.single-product .product-page-price .amount').contents().filter(function(){ 
            return this.nodeType == 3; 
        })[0].nodeValue = $np; 
    }
    jQuery('body.single-product select.qty').change(function(e){
        $qty  = jQuery('body.single-product select.qty').val(); 
        //console.log($qty * $unit_price);
        $np = Math.round(($qty * $unit_price_num)*100)/100;
        /*$np = $np.toString();*/
        $np = $np.toFixed(2);
        $np = $np.replace(/\./g, ',');
        jQuery('body.single-product .product-page-price .amount').contents().filter(function(){ 
            return this.nodeType == 3; 
        })[0].nodeValue = $np; 
    });
});
