<?php
/**
 * Template Name: Архив отзывов
 */

get_header(); ?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title">Отзывы наших клиентов</h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="testimonials-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('testimonial-item'); ?>>
                    
                    <!-- Изображение автора -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="testimonial-avatar">
                            <?php the_post_thumbnail('thumbnail', ['class' => 'testimonial-image']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Текст отзыва -->
                    <div class="testimonial-content">
                        <div class="testimonial-text">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Информация об авторе -->
                        <div class="testimonial-author">
                            <strong class="author-name">
                                <?php 
                                $author_name = get_post_meta(get_the_ID(), '_tombstone_testimonial_author_name', true);
                                echo $author_name ? esc_html($author_name) : get_the_title();
                                ?>
                            </strong>
                            
                            <?php $author_position = get_post_meta(get_the_ID(), '_tombstone_testimonial_author_position', true); ?>
                            <?php if ($author_position) : ?>
                                <span class="author-position">, <?php echo esc_html($author_position); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Дата отзыва -->
                        <div class="testimonial-date">
                            <?php
                            $testimonial_date = get_post_meta(get_the_ID(), '_tombstone_testimonial_date', true);
                            if ($testimonial_date) {
                                echo date('d.m.Y', strtotime($testimonial_date));
                            } else {
                                echo get_the_date('d.m.Y');
                            }
                            ?>
                        </div>
                    </div>
                    
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Пагинация -->
        <div class="testimonials-pagination">
            <?php
            the_posts_pagination([
                'prev_text' => '&laquo; Назад',
                'next_text' => 'Вперед &raquo;',
            ]);
            ?>
        </div>

    <?php else : ?>
        <div class="no-testimonials">
            <p>Отзывы пока не добавлены.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>