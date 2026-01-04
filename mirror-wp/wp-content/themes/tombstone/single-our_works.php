<?php
/**
 * Template Name: Страница работы
 * Description: Шаблон для отображения отдельной работы
 */

get_header(); ?>
<style>
    p {padding: 8px 0!important}
    h1, p {line-height:120%}
    .work-price-large {min-width: max-content}
    img {
        object-fit: cover;
        width: 100%;
    }
</style>
<div class="container pt-40">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('work-single'); ?>>
            
            <!-- Заголовок -->
            <header class="work-header">
                
            </header>

            <div class="work-content-wrapper grid gtc-2 gg-40">
                <!-- Основное изображение -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="work-featured-image">
                        <?php the_post_thumbnail('large', ['class' => 'work-main-image']); ?>
                    </div>
                <?php endif; ?>

                <!-- Описание работы -->
                <div class="work-content">

                    <h1 class="work-title"><?php the_title(); ?></h1>
                    <div class="grid gtc-2 ai-c">
                        <?php $price = get_post_meta(get_the_ID(), '_tombstone_work_price', true); ?>
                        <?php if ($price) : ?>
                            <div class="work-price-large ff-inter-800 fs-28">
                                <span class="price-label">Стоимость:</span>
                                <span class="price-value"><?php echo esc_html($price); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="work-meta">
                            <?php $post_date = get_the_date('d.m.Y'); ?>
                            <div class="work-date ta-r"><?php echo $post_date; ?></div>
                        </div>

                    </div>

                    <?php the_content(); ?>
                </div>


                <!-- Галерея (если нужно добавить дополнительные изображения) -->
                <?php
                $gallery = get_post_meta(get_the_ID(), '_tombstone_work_gallery', true);
                if ($gallery) : ?>
                    <div class="work-gallery">
                        <h3>Галерея работы</h3>
                        <div class="gallery-images">
                            <!-- Здесь можно вывести галерею -->
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Навигация между работами -->
            <nav class="work-navigation">
                <div class="nav-previous">
                    <?php 
                    $prev_work = get_previous_post(true, '', 'our_works');
                    if ($prev_work) : ?>
                        <a href="<?php echo get_permalink($prev_work); ?>" class="nav-link prev-link">
                            &larr; Предыдущая работа
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="nav-next">
                    <?php 
                    $next_work = get_next_post(true, '', 'our_works');
                    if ($next_work) : ?>
                        <a href="<?php echo get_permalink($next_work); ?>" class="nav-link next-link">
                            Следующая работа &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </nav>

        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>