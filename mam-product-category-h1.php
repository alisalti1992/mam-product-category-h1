<?php

/**
 * Plugin Name: MAM Product Category H1
 * Plugin URI: https://github.com/moveaheadmedia/mam-product-category-h1/
 * Description: Use shortcode [mam-product-category-h1] to create custom H1 with Woof plugin.
 * Version: 1.0.0
 * Author: Ali
 * Author URI: https://github.com/moveaheadmedia/
 * License: GPL
 */

//[mam-product-category-h1]
function mam_product_category_h1( $atts ){
    return "test";
}
add_shortcode( 'mam-product-category-h1', 'mam_product_category_h1' );