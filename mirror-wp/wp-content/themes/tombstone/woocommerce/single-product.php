<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); // или просто get_header();
?>

<main class="single-product-page">
    <?php
    while ( have_posts() ) :
        the_post();

        wc_get_template_part( 'content', 'single-product' );

    endwhile; // end of the loop.
    ?>
</main>

<?php
get_footer( 'shop' ); // или просто get_footer();
