<?php
/**
 * Кастомные типы записей для темы Tombstone
 */

// Регистрация типа записи "Наши работы"
function tombstone_register_our_works_cpt() {
    $labels = [
        'name'               => 'Наши работы',
        'singular_name'      => 'Работа',
        'menu_name'          => 'Наши работы',
        'name_admin_bar'     => 'Работу',
        'add_new'            => 'Добавить новую',
        'add_new_item'       => 'Добавить новую работу',
        'new_item'           => 'Новая работа',
        'edit_item'          => 'Редактировать работу',
        'view_item'          => 'Просмотреть работу',
        'all_items'          => 'Все работы',
        'search_items'       => 'Поиск работ',
        'parent_item_colon'  => 'Родительская работа:',
        'not_found'          => 'Работ не найдено.',
        'not_found_in_trash' => 'В корзине работ не найдено.'
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'our-works'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'       => true // для поддержки Gutenberg
    ];

    register_post_type('our_works', $args);
}
add_action('init', 'tombstone_register_our_works_cpt');

// Метабокс для цены работы
function tombstone_add_work_price_metabox() {
    add_meta_box(
        'tombstone_work_price',
        'Цена работы',
        'tombstone_render_work_price_metabox',
        'our_works',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'tombstone_add_work_price_metabox');

// Отображение метабокса цены
function tombstone_render_work_price_metabox($post) {
    // Добавляем nonce для безопасности
    wp_nonce_field('tombstone_work_price_nonce', 'tombstone_work_price_nonce');
    
    $price = get_post_meta($post->ID, '_tombstone_work_price', true);
    
    echo '<label for="tombstone_work_price">Стоимость работы:</label>';
    echo '<input type="text" id="tombstone_work_price" name="tombstone_work_price" value="' . esc_attr($price) . '" placeholder="например: 5000 руб." style="width:100%; margin-top:5px;">';
    echo '<p class="description">Укажите стоимость работы в произвольном формате</p>';
}

// Сохранение метаполя цены
function tombstone_save_work_price_metabox($post_id) {
    // Проверяем nonce
    if (!isset($_POST['tombstone_work_price_nonce']) || 
        !wp_verify_nonce($_POST['tombstone_work_price_nonce'], 'tombstone_work_price_nonce')) {
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
    if (isset($_POST['tombstone_work_price'])) {
        update_post_meta(
            $post_id,
            '_tombstone_work_price',
            sanitize_text_field($_POST['tombstone_work_price'])
        );
    }
}
add_action('save_post', 'tombstone_save_work_price_metabox');

// Добавляем колонку "Цена" в админ-списке работ
function tombstone_add_work_price_column($columns) {
    $new_columns = [];
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['work_price'] = 'Цена';
        }
    }
    
    return $new_columns;
}
add_filter('manage_our_works_posts_columns', 'tombstone_add_work_price_column');

// Заполняем колонку "Цена" данными
function tombstone_display_work_price_column($column, $post_id) {
    if ($column === 'work_price') {
        $price = get_post_meta($post_id, '_tombstone_work_price', true);
        echo $price ? esc_html($price) : '—';
    }
}
add_action('manage_our_works_posts_custom_column', 'tombstone_display_work_price_column', 10, 2);

// Делаем колонку "Цена" сортируемой
function tombstone_make_work_price_column_sortable($columns) {
    $columns['work_price'] = 'work_price';
    return $columns;
}
add_filter('manage_edit-our_works_sortable_columns', 'tombstone_make_work_price_column_sortable');