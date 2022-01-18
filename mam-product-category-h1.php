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
    $items = array();
    $item = array();
    if (is_shop()) {
        $item['slug'] = '';
        $item['title'] = get_the_title(get_option('woocommerce_shop_page_id'));
        $item['url'] = get_the_permalink(get_option('woocommerce_shop_page_id'));
        //$items[] = $item;
    }
    if (is_tax()) {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $array_parts = explode('/', $actual_link);
        array_pop($array_parts);
        $item['slug'] = [array_pop($array_parts), 'pa_' . array_pop($array_parts)];
        $term = get_term_by('slug', $item['slug'][0], $item['slug'][1]);
        $item['title'] = $term->name;
        $item['url'] = site_url() . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $items[] = $item;
    }
    foreach ($_GET as $key => $value) {
        $last_item = end($items);
        $item = array();
        if (strpos($key, 'pa_') !== false) {
            $item['slug'] = [$value, $key];
            $term = get_term_by('slug', $item['slug'][0], $item['slug'][1]);
            $item['title'] = $term->name;
        } else if (strpos($key, 'woof_text') !== false) {
            $item['slug'] = [$value, $key];
            $item['title'] = '"' . $value . '"';
        } else {
            continue;
        }
        $item['url-before'] = $last_item['url'];
        if (strpos($last_item['url'], '?') == false) {
            $item['url'] = $last_item['url'] . '?' . $item['slug'][1] . '=' . $item['slug'][0];
        } else {
            $item['url'] = $last_item['url'] . '&' . $item['slug'][1] . '=' . $item['slug'][0];
        }
        $check = true;
        foreach ($items as $_item) {
            if ($_item['slug'][1] == $item['slug'][1]) {
                $check = false;
            }
        }
        if ($check) {
            $items[] = $item;
        }
    }
    ob_start();
    ?>
    <h1 class="mam-h1-category">
        <?php
        $count = count($items);
        foreach ($items as $item) {
            $count--;
            if (!$item['title']) {
                continue;
            }
            echo '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
            if ($count) {
                ?>
                <span class="mam-separator"> > </span>
                <?
            }
        }
        ?>
    </h1>
    <script>
        // Update selected filters
        jQuery(document).ready(function ($) {
            <?php
            foreach ($items as $item) {

            if (!$item['slug'][0] || !$item['slug'][1]) {
                continue;
            }
            ?>
            $('input[data-slug="<?php echo $item['slug'][0]; ?>"][name="<?php echo $item['slug'][1]; ?>"]')
                .parent().find('.woof_radio_label').addClass('woof_checkbox_label_selected')
                .click(function (e) {
                    e.preventDefault();
                    window.location.href = '<?php echo $item['url-before']; ?>';
                });
            <?php
            if (strpos($item['slug'][1], 'woof_text') !== false) {
            ?>
            $('input.woof_show_text_search').val('<?php echo $item['slug'][0]; ?>');
            <?php
            }
            }
            ?>

        });
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('mam-product-category-h1', 'mam_product_category_h1');

function mam_get_material_links($atts)
{
    $a = shortcode_atts(array('term' => 'pa_stone-types'), $atts);
    $url = get_the_permalink(get_option('woocommerce_shop_page_id'));
    $terms = get_terms($a['term']);
    $html = '<ul>';
    foreach ($terms as $term) {
        $term_link = get_term_link($term);
        if (is_wp_error($term_link)) {
            continue;
        }
        $html .= '<li><a href="' . esc_url($url) . '?' . $a['term'] . '=' . $term->slug . '">' . $term->name . '</a></li>';
    }

    $html .= '</ul>';
    return $html;
}

add_shortcode('mam-get-links', 'mam_get_material_links');


function sv_add_sku_sorting($args)
{
    $args['orderby'] = 'meta_value';
    $args['order'] = 'asc'; // lists SKUs alphabetically 0-9, a-z; change to desc for reverse alphabetical
    $args['meta_key'] = '_sku';
    return $args;
}

add_filter('woocommerce_get_catalog_ordering_args', 'sv_add_sku_sorting');
