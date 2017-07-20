<?php

/* 
 * 
 * Custom functions file to override woocommerce functions in child theme
 * @author: Faisal
 * 
 */
function woocommerce_template_loop_product_title() {
        echo '<h3 class="name product-title font26"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h3>'
                . '<center><hr class="after-title"></center>';
    }
