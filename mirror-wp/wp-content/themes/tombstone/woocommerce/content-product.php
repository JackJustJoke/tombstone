<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>


<div <?php wc_product_class('product-card grid-item bg-white', $product); ?>>
    <a class="wrap-img" href="<?php the_permalink(); ?>">
        <img src="<?php echo esc_url( wp_get_attachment_url( $product->get_image_id() ) ); ?>" alt="<?php the_title(); ?>">
    </a>
    <div class="product-card__info p-20">
        <div class="product-card__title"><?php the_title(); ?></div>
		<div class="product-card__bottom">
			<div class="product-card__price price pt-20">
				<?php
					if ($product->is_type('variable')) {
						$prices = $product->get_variation_prices(true);

						// Минимальные цены
						$min_regular_price = current($prices['regular_price']);
						$min_sale_price    = current($prices['sale_price']);

						// Если есть скидка
						if ($min_sale_price && $min_sale_price < $min_regular_price) {
							echo '<span class="price flex jc-sb ai-b">';
							echo '<div class="price-new"><span class="fs-14">от</span>&nbsp;' . wc_price($min_sale_price) . '</div>';
							echo '<del class="price-old">' . wc_price($min_regular_price) . '</del>';
							echo '</span>';
						} else {
							// Без скидки
							echo '<span class="price">' . wc_price($min_regular_price) . '</span>';
						}

					} else {
						// Для простых товаров
						if ($product->is_on_sale()) {
							echo '<span class="price flex jc-sb ai-b">';
							echo '<div class="price-new"><span class="fs-14">от</span>&nbsp;' . wc_price($product->get_sale_price()) . '</div>';
							echo '<del class="price-old">' . wc_price($product->get_regular_price()) . '</del> ';
							echo '</span>';
						} else {
							echo '<span class="price flex jc-sb ai-b">';
							echo '<div class="price-new"><span class="fs-14">от</span>&nbsp;' . wc_price($product->get_regular_price()) . '</div>';
							echo '<del class="price-old"></del> ';
							echo '</span>';
						}
					}
				?>
			</div>
		</div>
	</div>
	<div><a class="btn-1" href="<?php the_permalink(); ?>">Подробнее</a></div>
</div>
