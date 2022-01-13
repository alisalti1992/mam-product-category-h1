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

// [mam-product-category-h1]
function mam_product_category_h1($atts)
{
    if(!is_shop()){
        return false;
    }
    if (!is_tax()) {
        return false;
    }
    $slugs = array();
    $titles = array();
    if(is_tax()){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $array_parts = explode('/', $actual_link);
        array_pop($array_parts);
        $slugs[] = [array_pop($array_parts), 'pa_' . array_pop($array_parts)];
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'pa_') !== false) {
                $slugs[] = [$value, $key];
            }
        }
    }
    if(is_shop()){
        $titles[] = get_the_title(get_option( 'woocommerce_shop_page_id' ));
    }
    foreach ($slugs as $slug) {
        $term = get_term_by('slug', $slug[0], $slug[1]);
        $titles[] = $term->name;
    }
    ob_start();
    ?>
    <h1>
        <?php
        $count = count($titles);
        foreach ($titles as $title) {
            $count--;
            if(!$title){
                continue;
            }
            echo $title;
            if ($count) {
                ?>
                <span class="mam-separator"> > </span>
                <?
            }
        }
        ?>
    </h1>
    <?php
    return ob_get_clean();
}

add_shortcode('mam-product-category-h1', 'mam_product_category_h1');