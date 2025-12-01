<?php
/**
 * Template Name: Архив наших работ
 * Description: Шаблон для отображения архива работ
 */

get_header(); ?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title h2 mt-200">Наши работы</h1>
        <?php 
        $description = get_the_archive_description();
        if ($description) : ?>
            <div class="archive-description"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
    </header>

    <?php if (have_posts()) : ?>
        <div class="works-grid mt-30 grid gtc-3">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('work-item'); ?>>
                    
                    <!-- Изображение работы -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="work-thumbnail">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <?php the_post_thumbnail('medium', ['class' => 'work-image']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Название работы -->
                    <h2 class="work-title fs-28">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    
                    <!-- Краткое описание -->
                    <div class="work-excerpt">
                        <?php 
                        if (has_excerpt()) {
                            the_excerpt();
                        } else {
                            echo wp_trim_words(get_the_content(), 20, '...');
                        }
                        ?>
                    </div>
                    
                    <!-- Цена работы -->
                    <?php $price = get_post_meta(get_the_ID(), '_tombstone_work_price', true); ?>
                    <?php if ($price) : ?>
                        <div class="work-price ff-inter-700 fs-42">
                            <span class="price-label">Стоимость:</span>
                            <span class="price-value"><?php echo esc_html($price); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Кнопка подробнее -->
                    <div class="work-read-more">
                        <a href="<?php the_permalink(); ?>" class="button">Подробнее</a>
                    </div>
                    
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Пагинация -->
        <div class="works-pagination">
            <?php
            the_posts_pagination([
                'prev_text' => '&laquo; Назад',
                'next_text' => 'Вперед &raquo;',
                'mid_size'  => 2
            ]);
            ?>
        </div>

    <?php else : ?>
        <!-- Если работ нет -->
        <div class="no-works">
            <p>К сожалению, работы пока не добавлены.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>