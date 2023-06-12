<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', [] );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 20 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );



//---------Display brand listing page--------

add_shortcode('Display_Brand','display_brand_function');
function display_brand_function(){
	$output = '' ;
	$output .= '<div class="demo fusion-builder-row fusion-row fusion-flex-align-items-flex-start" >
		<div class="fusion-layout-column fusion_builder_column fusion-builder-column-12 fusion_builder_column_1_1 1_1 fusion-flex-column custom_product_section">
		<div class="fusion-column-wrapper fusion-flex-justify-content-flex-start fusion-content-layout-column">
		<div class="woocommerce columns-4">
		<ul class="products clearfix products-4">';

        $brand = get_terms( 
            array(
                'taxonomy' => 'berocket_brand',
                'hide_empty' => false,
            ) 
        );
        foreach($brand as $brands){
			 $permalink = get_term_link($brands->term_id);
             $thumnail =  get_term_meta( $brands->term_id, 'brand_image_url', true );
             $title = $brands->name ;
    $output .= '<li class="product-category product first product-grid-view">
            <div class="fusion-product-wrapper">
			<a href="'.$permalink.'">
			<img class=" ls-is-cached lazyloaded" src="'.$thumnail.'">		
			   <h2>
			    '.$title.'</h2></a>
			    </div></li>';
        }
$output .= '</ul></div></div></div></div>' ;
return $output ;

}

//---------Display designer listing page--------
add_shortcode('Display_Designers','designers_list');

function designers_list(){
    $output = '';
    $output .= '<div class="demo fusion-builder-row fusion-row fusion-flex-align-items-flex-start" >
		<div class="fusion-layout-column fusion_builder_column fusion-builder-column-12 fusion_builder_column_1_1 1_1 fusion-flex-column custom_product_section">
		<div class="fusion-column-wrapper fusion-flex-justify-content-flex-start fusion-content-layout-column">
		<div class="woocommerce columns-4">
		<ul class="products clearfix products-4">';
    $designer = get_terms( 
            array(
                'taxonomy' => 'product_cat',
                'parent' => '140',
                'hide_empty' => false,
            ) 
        );
        foreach($designer as $designers){
             $permalink = get_term_link($designers->term_id);
             $thumbnail_id = get_term_meta( $designers->term_id, 'thumbnail_id', true ); 
             $thumnail = wp_get_attachment_url( $thumbnail_id ); 
             $title = $designers->name ;
    $output .= '<li class="product-category product first product-grid-view">
            <div class="fusion-product-wrapper">
			<a href="'.$permalink.'">
			<img class=" ls-is-cached lazyloaded" src="'.$thumnail.'">		
			   <h2>
			    '.$title.'</h2></a>
			</div></li>'; 
        }
    $output .= '</ul></div></div></div></div>' ;    
    return $output ; 
}

/*----------------remove in stock msg from product page--------------------------*/
function my_wc_hide_in_stock_message( $html, $text, $product ) {
	$availability = $product->get_availability();
	if ( isset( $availability['class'] ) && 'in-stock' === $availability['class'] ) {
		return '';
	}
	return $html;
}
add_filter( 'woocommerce_stock_html', 'my_wc_hide_in_stock_message', 10, 3 );

//----------Remove product-category from page url--------------//

add_filter('request', function( $vars ) {
	global $wpdb;
	if( ! empty( $vars['pagename'] ) || ! empty( $vars['category_name'] ) || ! empty( $vars['name'] ) || ! empty( $vars['attachment'] ) ) {
		$slug = ! empty( $vars['pagename'] ) ? $vars['pagename'] : ( ! empty( $vars['name'] ) ? $vars['name'] : ( !empty( $vars['category_name'] ) ? $vars['category_name'] : $vars['attachment'] ) );
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT t.term_id FROM $wpdb->terms t LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND t.slug = %s" ,array( $slug )));
		if( $exists ){
			$old_vars = $vars;
			$vars = array('product_cat' => $slug );
			if ( !empty( $old_vars['paged'] ) || !empty( $old_vars['page'] ) )
				$vars['paged'] = ! empty( $old_vars['paged'] ) ? $old_vars['paged'] : $old_vars['page'];
			if ( !empty( $old_vars['orderby'] ) )
	 	        	$vars['orderby'] = $old_vars['orderby'];
      			if ( !empty( $old_vars['order'] ) )
 			        $vars['order'] = $old_vars['order'];	
		}
	}
	return $vars;
});

