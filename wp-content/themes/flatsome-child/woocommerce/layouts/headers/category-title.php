<?php
if(is_product_category()){
    $cat_id = $wp_query->get_queried_object()->term_id;
    $term = get_term_by ( 'term_taxonomy_id' , $cat_id );
    $term_thumbnail_id = get_woocommerce_term_meta( $cat_id, 'thumbnail_id', true );
    //if(empty($term_thumbnail_id)){
        $term_thumbnail_id = "527";
    //}
    //print_r($term);
    $category_description = $term->description;

    $content = '[ux_banner height="190px" bg="'.$term_thumbnail_id.'"]

    [text_box position_x="50" position_y="50"]

    <br />
    <h1 class="text-black"><strong>'.get_cat_name( $cat_id ).'</strong></h1>
    <p class="text-black">'.$category_description.'</p>

    [/text_box]

    [/ux_banner]';

    echo do_shortcode($content);
}
?>
<div class="shop-page-title category-page-title page-title <?php flatsome_header_title_classes() ?>">

	<div class="page-title-inner flex-row  medium-flex-wrap container">
	  <div class="flex-col flex-grow medium-text-center">
	  	 	 <?php do_action('flatsome_category_title') ;?>
	  </div><!-- .flex-left -->
	  
	   <div class="flex-col medium-text-center">
	  	 	<?php do_action('flatsome_category_title_alt') ;?>
	   </div><!-- .flex-right -->
	   
	</div><!-- flex-row -->
</div><!-- .page-title -->
