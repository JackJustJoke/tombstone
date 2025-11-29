<?php
/**
 * Функционал отзывов для темы Tombstone
 */

// Регистрация типа записи "Отзывы"
function tombstone_register_testimonials_cpt() {
    $labels = [
        'name'               => 'Отзывы',
        'singular_name'      => 'Отзыв',
        'menu_name'          => 'Отзывы',
        'name_admin_bar'     => 'Отзыв',
        'add_new'            => 'Добавить новый',
        'add_new_item'       => 'Добавить новый отзыв',
        'new_item'           => 'Новый отзыв',
        'edit_item'          => 'Редактировать отзыв',
        'view_item'          => 'Просмотреть отзыв',
        'all_items'          => 'Все отзывы',
        'search_items'       => 'Поиск отзывов',
        'parent_item_colon'  => 'Родительский отзыв:',
        'not_found'          => 'Отзывов не найдено.',
        'not_found_in_trash' => 'В корзине отзывов не найдено.'
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'testimonials'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-testimonial',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'show_in_rest'       => true
    ];

    register_post_type('testimonials', $args);
}
add_action('init', 'tombstone_register_testimonials_cpt');

// Метабокс для информации об авторе отзыва
function tombstone_add_testimonial_author_metabox() {
    add_meta_box(
        'tombstone_testimonial_author',
        'Информация об авторе отзыва',
        'tombstone_render_testimonial_author_metabox',
        'testimonials',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'tombstone_add_testimonial_author_metabox');

// Отображение метабокса автора отзыва
function tombstone_render_testimonial_author_metabox($post) {
    // Добавляем nonce для безопасности
    wp_nonce_field('tombstone_testimonial_author_nonce', 'tombstone_testimonial_author_nonce');
    
    $author_name = get_post_meta($post->ID, '_tombstone_testimonial_author_name', true);
    $testimonial_date = get_post_meta($post->ID, '_tombstone_testimonial_date', true);
    $testimonial_link = get_post_meta($post->ID, '_tombstone_testimonial_link', true);
    
    ?>
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <div>
            <label for="tombstone_testimonial_author_name"><strong>Имя автора:</strong></label>
            <input type="text" id="tombstone_testimonial_author_name" name="tombstone_testimonial_author_name" 
                   value="<?php echo esc_attr($author_name); ?>" placeholder="Например: Иван Иванов" 
                   style="width:100%; margin-top:5px;">
        </div>
        
        <div>
            <label for="tombstone_testimonial_date"><strong>Дата отзыва:</strong></label>
            <input type="date" id="tombstone_testimonial_date" name="tombstone_testimonial_date" 
                   value="<?php echo esc_attr($testimonial_date); ?>" 
                   style="width:100%; margin-top:5px;">
            <p class="description">Если не указана, будет использована дата публикации</p>
        </div>
        <div>
            <label for="tombstone_testimonial_link"><strong>Ссылка на отзыв:</strong></label>
            <input type="url" id="tombstone_testimonial_link" name="tombstone_testimonial_link"
                   value="<?php echo esc_attr($testimonial_link); ?>" placeholder="https://example.com"
                   style="width:100%; margin-top:5px;">
        </div>
    </div>
    <?php
}

// Сохранение метаполей отзыва
function tombstone_save_testimonial_author_metabox($post_id) {
    // Проверяем nonce
    if (!isset($_POST['tombstone_testimonial_author_nonce']) || 
        !wp_verify_nonce($_POST['tombstone_testimonial_author_nonce'], 'tombstone_testimonial_author_nonce')) {
        return;
    }
    
    // Проверяем автосохранение
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Проверяем права пользователя
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Сохраняем данные
    if (isset($_POST['tombstone_testimonial_author_name'])) {
        update_post_meta(
            $post_id,
            '_tombstone_testimonial_author_name',
            sanitize_text_field($_POST['tombstone_testimonial_author_name'])
        );
    }
    
    if (isset($_POST['tombstone_testimonial_date'])) {
        update_post_meta(
            $post_id,
            '_tombstone_testimonial_date',
            sanitize_text_field($_POST['tombstone_testimonial_date'])
        );
    }

    if (isset($_POST['tombstone_testimonial_link'])) {
        update_post_meta(
            $post_id,
            '_tombstone_testimonial_link',
            esc_url_raw($_POST['tombstone_testimonial_link'])
        );
    }
}
add_action('save_post', 'tombstone_save_testimonial_author_metabox');

// Добавляем кастомные колонки в админ-списке отзывов
function tombstone_add_testimonial_columns($columns) {
    $new_columns = [];
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['author_name'] = 'Имя автора';
            $new_columns['testimonial_date'] = 'Дата отзыва';
            $new_columns['testimonial_link'] = 'Ссылка на отзыв';
        }
    }
    
    return $new_columns;
}
add_filter('manage_testimonials_posts_columns', 'tombstone_add_testimonial_columns');

// Заполняем кастомные колонки данными
function tombstone_display_testimonial_columns($column, $post_id) {
    switch ($column) {
        case 'author_name':
            $author_name = get_post_meta($post_id, '_tombstone_testimonial_author_name', true);
            echo $author_name ? esc_html($author_name) : '—';
            break;
            
        case 'testimonial_date':
            $testimonial_date = get_post_meta($post_id, '_tombstone_testimonial_date', true);
            if ($testimonial_date) {
                echo date('d.m.Y', strtotime($testimonial_date));
            } else {
                echo get_the_date('d.m.Y', $post_id);
            }
            break;

        case 'testimonial_link':
            $testimonial_link = get_post_meta($post_id, '_tombstone_testimonial_link', true);
            if ($testimonial_link) {
                echo '<a href="' . esc_url($testimonial_link) . '" target="_blank" rel="noopener noreferrer">' . esc_html($testimonial_link) . '</a>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_testimonials_posts_custom_column', 'tombstone_display_testimonial_columns', 10, 2);

// Делаем колонки сортируемыми
function tombstone_make_testimonial_columns_sortable($columns) {
    $columns['author_name'] = 'author_name';
    $columns['testimonial_date'] = 'testimonial_date';
    return $columns;
}
add_filter('manage_edit-testimonials_sortable_columns', 'tombstone_make_testimonial_columns_sortable');