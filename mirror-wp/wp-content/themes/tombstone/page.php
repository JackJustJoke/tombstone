<?php
/**
 * Шаблон страницы (page.php)
 * Используется для всех стандартных страниц WordPress, включая корзину и оформление заказа
 */

get_header(); // подключаем header.php
?>

<h1 class="h2 cercle bg-gray mb-60"><? the_title(); ?></h1>

<?php the_content(); ?>

<?php
get_footer(); // подключаем footer.php
