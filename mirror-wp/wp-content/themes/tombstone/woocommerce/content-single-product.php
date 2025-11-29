<?php
defined( 'ABSPATH' ) || exit;

global $product;
?>

<h1 class="h2 mt-190 cercle"><?php the_title(); ?></h1>
<section class="grid gtc-2 gg-40 mt-40">
    <div>
        <div class=" product-gallery">
            <?php woocommerce_show_product_images(); ?>
            <div class="product-meta"><?php woocommerce_template_single_meta(); ?></div>
            <!-- <a href="#" class="btn-1 btn-bg-orange button-with-arrow ff-inter-700">Быстрый заказ</a> -->
        </div>
    </div>
    <div>
        <?php echo do_shortcode( '[memorial_calc]' ); ?>
        <div class="product-summary">
            <!-- <div class="product-add-to-cart"><?php woocommerce_template_single_add_to_cart(); ?></div>
            <div class="product-description"><?php woocommerce_template_single_excerpt(); ?></div> -->
            
        </div>
        <a href="#" class="btn-1 button-with-arrow ff-inter-700">Заказать памятник</a>
    </div>
</section>

<section class="product-card container">
</section>
