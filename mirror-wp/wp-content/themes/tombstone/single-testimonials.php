<?php
/**
 * Template Name: Страница отзыва
 */

get_header(); ?>

<div class="container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('testimonial-single'); ?>>
            
            <div class="testimonial-single-header">
                <!-- Изображение автора -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="testimonial-single-avatar">
                        <?php the_post_thumbnail('medium', ['class' => 'testimonial-single-image']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="testimonial-single-info">
                    <!-- Имя автора -->
                    <h1 class="testimonial-single-name">
                        <?php 
                        $author_name = get_post_meta(get_the_ID(), '_tombstone_testimonial_author_name', true);
                        echo $author_name ? esc_html($author_name) : get_the_title();
                        ?>
                    </h1>
                    
                    <!-- Должность/компания -->
                    <?php $author_position = get_post_meta(get_the_ID(), '_tombstone_testimonial_author_position', true); ?>
                    <?php if ($author_position) : ?>
                        <div class="testimonial-single-position">
                            <?php echo esc_html($author_position); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Дата отзыва -->
                    <div class="testimonial-single-date">
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
            </div>

            <!-- Текст отзыва -->
            <div class="testimonial-single-content">
                <?php the_content(); ?>
            </div>

            <!-- Навигация между отзывами -->
            <nav class="testimonial-navigation">
                <div class="nav-previous">
                    <?php 
                    $prev_testimonial = get_previous_post(true, '', 'testimonials');
                    if ($prev_testimonial) : ?>
                        <a href="<?php echo get_permalink($prev_testimonial); ?>" class="nav-link prev-link">
                            &larr; Предыдущий отзыв
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="nav-back">
                    <a href="<?php echo get_post_type_archive_link('testimonials'); ?>" class="nav-link back-link">
                        Все отзывы
                    </a>
                </div>
                
                <div class="nav-next">
                    <?php 
                    $next_testimonial = get_next_post(true, '', 'testimonials');
                    if ($next_testimonial) : ?>
                        <a href="<?php echo get_permalink($next_testimonial); ?>" class="nav-link next-link">
                            Следующий отзыв &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </nav>

        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>