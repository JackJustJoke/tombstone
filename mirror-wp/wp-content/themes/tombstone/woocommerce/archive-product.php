<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/** 
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>



<?php
// === Фильтр по цене ===
function get_wc_price_range_fast() {
    global $wpdb;

    $sql = "
        SELECT MIN(meta_value+0) as min_price, MAX(meta_value+0) as max_price
        FROM {$wpdb->postmeta}
        INNER JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
        WHERE meta_key = '_price'
        AND post_type IN ('product', 'product_variation')
        AND post_status = 'publish'
    ";

    return $wpdb->get_row($sql, ARRAY_A);
}

$range = get_wc_price_range_fast();

?>
<form method="get" class="product-filter ptb-20">
	<div class="range-price grid gtc-2 gg-12">
		<div class="wrap-input min-price">
			<label for="min-price" class="fs-14 uppercase">Цена от</label>
			<input id="min-price" type="number" name="min_price" placeholder="Цена от" value="<?php echo esc_attr($_GET['min_price'] ?? $range['min_price']); ?>" min="<? echo esc_attr($_GET['min_price'] ?? $range['min_price']); ?>">
		</div>
		<div class="wrap-input max-price">
			<label for="max-price" class="fs-14 uppercase">Цена до</label>
			<input id="max-price" type="number" name="max_price" placeholder="Цена до" value="<?php echo esc_attr($_GET['max_price'] ?? $range['max_price']); ?>" max="<? echo esc_attr($_GET['max_price'] ?? $range['max_price']); ?>">
		</div>
	</div>
  	<button type="submit" class="btn-1">Фильтровать</button>
</form>




<?php
/** <h2 class="cercle cercle-orange txt-white">Порядок работы</h2>
 * Hook: woocommerce_shop_loop_header.
 *
 * @since 8.6.0
 *
 * @hooked woocommerce_product_taxonomy_archive_header - 10
 */
do_action( 'woocommerce_shop_loop_header' );

if ( woocommerce_product_loop() ) {



	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
