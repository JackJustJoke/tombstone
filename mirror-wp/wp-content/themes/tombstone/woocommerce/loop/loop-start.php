<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="grid gg-40 catalog-sidebar-and-products">

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

<?php
global $wpdb;
$max_price = $wpdb->get_var("
	SELECT MAX(CAST(meta_value AS UNSIGNED))
	FROM {$wpdb->postmeta}
	WHERE meta_key = '_price'
");
$min_price = $wpdb->get_var("
	SELECT MIN(CAST(meta_value AS UNSIGNED))
	FROM {$wpdb->postmeta}
	WHERE meta_key = '_price'
");
?>

<div class="sidebar-catalog">
	<form method="get" class="product-filter ptb-20">
		<p class="product-card__title mb-40">Цена, ₽</p>
		<div class="range-price grid gtc-2 mb-20">
			<div class="wrap-input min-price">
				<label for="min-price" class="fs-14 uppercase d-none">Цена от</label>
				<input id="min-price" type="number" name="min_price" placeholder="Цена от" value="<?php echo esc_attr($_GET['min_price'] ?? $range['min_price']); ?>" min="<? echo esc_attr($_GET['min_price'] ?? $range['min_price']); ?>">
			</div>
			<div class="wrap-input max-price">
				<label for="max-price" class="fs-14 uppercase d-none">Цена до</label>
				<input id="max-price" type="number" name="max_price" placeholder="Цена до" value="<? echo $max_price ?>" max="<? echo $max_price ?>">
			</div>
		</div>
		<div class="range-slider-container mb-40">
			<input id="rangeMinPrice" class="range-slider" type="range" min="0" max="<? echo $max_price ?>" step="1" value="0">
			<input id="rangeMaxPrice" class="range-slider" type="range" min="0" max="<? echo $max_price ?>" step="1" value="<? echo $max_price ?>">
		<div id="sliderTrack" class="slider-track"></div>
</div>

		<script>
			let updateSlider = (fillColor = '#ff6200', emptyColor = '#bcbcbc') => {
				let [min, max] = [parseInt(rangeMinPrice.value), parseInt(rangeMaxPrice.value)]
				if (min >= max) rangeMinPrice.value = max - 1;  
				if (max <= min) rangeMaxPrice.value = min + 1;
				let percentForMin = parseInt((rangeMinPrice.value / rangeMinPrice.max) * 100);
				let percentForMax = parseInt((rangeMaxPrice.value / rangeMaxPrice.max) * 100);
				sliderTrack.style.background = `linear-gradient(to right, 
					${emptyColor} ${percentForMin}%, 
					${fillColor}  ${percentForMin}%, 
					${fillColor}  ${percentForMax}%, 
					${emptyColor} ${percentForMax}%)`;
				document.querySelector('#min-price').value = min;
				document.querySelector('#max-price').value = max;
				
			}
			rangeMinPrice.addEventListener('input', () => updateSlider());
			rangeMaxPrice.addEventListener('input', () => updateSlider());
			updateSlider();
		</script>

		<button type="submit" class="btn-1">Применить фильтры</button>
	</form>
</div>

<div class="products mt-30">
<!-- <ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>"> -->

