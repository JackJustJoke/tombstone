<?php   

/* Functions and definitions for Tombstone theme */
/* Подключение кастомных типов записей           */
require get_template_directory() . '/inc/our-works-cpt.php';    // Подключение функционала "Наши работы" cpt - content post type
require get_template_directory() . '/inc/testimonials-cpt.php'; // Подключение функционала "Отзывы"





/**
 * Полностью убрать адрес и доставку и оставить только ФИО и телефон
 */
add_filter('woocommerce_checkout_fields', function($fields) {

    // Убираем все поля доставки
    if (isset($fields['shipping'])) {
        unset($fields['shipping']);
    }

    // Убираем все лишние поля billing
    if (isset($fields['billing'])) {
        $remove_billing_fields = [
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_postcode',
            'billing_country',
            'billing_state',
            'billing_company',
            'billing_email', // оставим своё поле Email ниже
        ];

        foreach ($remove_billing_fields as $key) {
            if (isset($fields['billing'][$key])) {
                unset($fields['billing'][$key]);
            }
        }

        // Оставляем только нужные поля
        $fields['billing']['billing_first_name'] = [
            'label'       => 'Фамилия',
            'required'    => true,
            'class'       => ['form-row-first'],
            'clear'       => false,
        ];
        $fields['billing']['billing_last_name'] = [
            'label'       => 'Имя',
            'required'    => true,
            'class'       => ['form-row-last'],
            'clear'       => true,
        ];
        $fields['billing']['billing_phone'] = [
            'label'       => 'Телефон',
            'required'    => true,
            'class'       => ['form-row-wide'],
            'clear'       => true,
        ];
        $fields['billing']['billing_email'] = [
            'label'       => 'Email',
            'required'    => true,
            'class'       => ['form-row-wide'],
            'clear'       => true,
        ];
    }

    return $fields;
}, 9999);

// Принудительно ставим страну, чтобы блоковая касса не ругалась
add_filter('default_checkout_billing_country', fn() => 'RU');






// add_filter( 'show_admin_bar', '__return_false');

add_theme_support('custom-logo');// Поддержка логотипа

register_nav_menus([
    'header_menu' => 'Меню в шапке',
]);




add_action( 'wp_enqueue_scripts', 'true_enqueue_js_and_css' );
function true_enqueue_js_and_css() {
    // CSS
	wp_enqueue_style('reset_css', get_stylesheet_directory_uri() . '/assets/css/reset.css');
    wp_enqueue_style('fonts_css', get_stylesheet_directory_uri() . '/assets/css/fonts.css');
    wp_enqueue_style('root_css', get_stylesheet_directory_uri() . '/assets/css/root.css');
    wp_enqueue_style('grid_css', get_stylesheet_directory_uri() . '/assets/css/grid.css');
    wp_enqueue_style('flex_css', get_stylesheet_directory_uri() . '/assets/css/flex.css');
    wp_enqueue_style('gap_css', get_stylesheet_directory_uri() . '/assets/css/gap.css');
    wp_enqueue_style('color_css', get_stylesheet_directory_uri() . '/assets/css/color.css');
    wp_enqueue_style('mytheme-style', get_stylesheet_uri());
 
	// JavaScript
    // wp_dequeue_script( 'wp-emoji-release.min.js' )
	wp_enqueue_script( 
		'main_js', // идентификатор скрипта
		get_stylesheet_directory_uri() . '/assets/js/main.js', // URL скрипта
		array(), // зависимости от других скриптов
		filemtime( get_stylesheet_directory() . '/assets/js/main.js'),
		true // true - в футере, false – в хедере
	);
 
}

add_action( 'init', 'remove_wp_emoji_action' ); //код удаляет соответствующие хуки и фильтры, которые отвечают за загрузку скриптов и стилей для эмодзи
function remove_wp_emoji_action() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

add_theme_support('woocommerce');












// Отключаю стили woocommerce в /shop/
add_action('wp_enqueue_scripts', function() {
    if (is_shop()) {
        // wp_dequeue_style('woocommerce-general');      // основной стиль WooCommerce
        // wp_dequeue_style('woocommerce-layout');       // layout.css
        // wp_dequeue_style('woocommerce-smallscreen');  // smallscreen.css
        // wp_dequeue_style('woocommerce-inline');       // inline-стили
    }
}, 100);





remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 ); // Отключаем стандартный вызов
add_action( 'woocommerce_archive_description', 'woocommerce_breadcrumb', 20 ); // Добавляем после заголовка категории







add_action('init', 'add_image_field_to_attribute_terms');
function add_image_field_to_attribute_terms() {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    if (empty($attribute_taxonomies) || !is_array($attribute_taxonomies)) return;

    foreach ($attribute_taxonomies as $tax) {
        $taxonomy = wc_attribute_taxonomy_name($tax->attribute_name);

        // Форма создания
        add_action("{$taxonomy}_add_form_fields", 'wc_attribute_add_image_field');
        // Форма редактирования
        add_action("{$taxonomy}_edit_form_fields", 'wc_attribute_edit_image_field', 10, 2);
        // Сохранение
        add_action("created_{$taxonomy}", 'wc_attribute_save_image_field');
        add_action("edited_{$taxonomy}", 'wc_attribute_save_image_field');
        // Колонки
        add_filter("manage_edit-{$taxonomy}_columns", 'wc_attribute_image_column');
        add_action("manage_{$taxonomy}_custom_column", 'wc_attribute_image_column_content', 10, 3);
    }
}

// --- Добавление поля при создании ---
function wc_attribute_add_image_field() {
    ?>
    <div class="form-field term-image-wrap">
        <label><?php _e('Image', 'woocommerce'); ?></label>
        <input type="hidden" name="attribute_image" class="attribute-image-id" value="">
        <div class="attribute-image-preview"></div>
        <p>
            <button type="button" class="button select-attribute-image"><?php _e('Add image', 'woocommerce'); ?></button>
            <button type="button" class="button remove-attribute-image"><?php _e('Remove', 'woocommerce'); ?></button>
        </p>
    </div>
    <?php
}

// --- Поле при редактировании ---
function wc_attribute_edit_image_field($term, $taxonomy) {
    $image_id = get_term_meta($term->term_id, 'attribute_image', true);
    $image = $image_id ? wp_get_attachment_image($image_id, 'thumbnail') : '';
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label><?php _e('Image', 'woocommerce'); ?></label></th>
        <td>
            <input type="hidden" name="attribute_image" class="attribute-image-id" value="<?php echo esc_attr($image_id); ?>">
            <div class="attribute-image-preview"><?php echo $image; ?></div>
            <p>
                <button type="button" class="button select-attribute-image"><?php _e('Add image', 'woocommerce'); ?></button>
                <button type="button" class="button remove-attribute-image"><?php _e('Remove', 'woocommerce'); ?></button>
            </p>
        </td>
    </tr>
    <?php
}

// --- Сохранение данных ---
function wc_attribute_save_image_field($term_id) {
    if (isset($_POST['attribute_image'])) {
        update_term_meta($term_id, 'attribute_image', absint($_POST['attribute_image']));
    }
}

// --- Колонка изображения ---
function wc_attribute_image_column($columns) {
    $new = [];
    foreach ($columns as $key => $val) {
        $new[$key] = $val;
        if ($key === 'name') {
            $new['attribute_image'] = __('Image', 'woocommerce');
        }
    }
    return $new;
}

function wc_attribute_image_column_content($content, $column_name, $term_id) {
    if ($column_name === 'attribute_image') {
        $image_id = get_term_meta($term_id, 'attribute_image', true);
        $content = $image_id ? wp_get_attachment_image($image_id, 'thumbnail', false, ['style' => 'max-width:60px;height:auto;']) : '—';
    }
    return $content;
}

// =============================
// 2. Скрипты и медиа
// =============================

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_media();
    wp_add_inline_script('jquery-core', <<<JS
    jQuery(document).ready(function($){
        let frame;
        $('body').on('click', '.select-attribute-image', function(e){
            e.preventDefault();
            const field = $(this).closest('.term-image-wrap');
            if (frame) frame.close();
            frame = wp.media({
                title: 'Select image',
                button: { text: 'Use this image' },
                multiple: false
            });
            frame.on('select', function(){
                const attachment = frame.state().get('selection').first().toJSON();
                field.find('.attribute-image-id').val(attachment.id);
                field.find('.attribute-image-preview').html('<img src="'+attachment.url+'" style="max-height:100px;">');
            });
            frame.open();
        });

        $('body').on('click', '.remove-attribute-image', function(e){
            e.preventDefault();
            const field = $(this).closest('.term-image-wrap');
            field.find('.attribute-image-id').val('');
            field.find('.attribute-image-preview').html('');
        });
    });
    JS);
});

// =============================
// 3. Автоматическое создание атрибута "Гранит" (если отсутствует)
// =============================

add_action('init', function() {
    $slug = 'material';
    if (!taxonomy_exists('pa_' . $slug)) {
        $exists = wc_attribute_taxonomy_id_by_name($slug);
        if (!$exists) {
            wc_create_attribute([
                'name' => 'Гранит',
                'slug' => $slug,
                'type' => 'select',
                'orderby' => 'menu_order',
                'has_archives' => false,
            ]);
            delete_transient('wc_attribute_taxonomies');
            flush_rewrite_rules();
        }
    }
});

// =============================
// 4. Визуальный выбор атрибута "Гранит"
// =============================

add_filter('woocommerce_dropdown_variation_attribute_options_html', function($html, $args) {
    if ($args['attribute'] !== 'pa_material') return $html;

    $options = $args['options'];
    $product = $args['product'];
    $attribute = $args['attribute'];
    $name = $args['name'] ?: 'attribute_' . sanitize_title($attribute);
    $id = $args['id'] ?: sanitize_title($attribute);

    if (empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options = $attributes[$attribute] ?? [];
    }

    if (empty($options)) return $html;

    ob_start();
    ?>
    <div class="material-visual-selector" data-attribute="<?php echo esc_attr($attribute); ?>">
        <div class="material-options">
            <?php foreach ($options as $option):
                $term = get_term_by('slug', $option, $attribute);
                if (!$term) continue;
                $img_id = get_term_meta($term->term_id, 'attribute_image', true);
                $img_url = $img_id ? wp_get_attachment_url($img_id) : wc_placeholder_img_src();
                $selected = sanitize_title($args['selected']) === $term->slug ? 'selected' : '';
            ?>
                <div class="material-option <?php echo $selected; ?>" data-value="<?php echo esc_attr($term->slug); ?>">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
                    <span class="material-name"><?php echo esc_html($term->name); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="mb-10">Другие цвета либо спец. заказы оговариваются отдельно.</p>
        <?php echo $html; // стандартный WooCommerce select ?>
    </div>
    <?php
    return ob_get_clean();
}, 10, 2);


// =============================
// 5. Стили и JS для визуального выбора
// =============================

add_action('wp_head', function() {
    ?>
    <style>
    .material-visual-selector { margin:4px 0 0; }
    .material-options {
        display:grid; grid-template-columns:  repeat(auto-fill, minmax(90px, min-content)); flex-wrap:wrap; gap:2px; margin-bottom:10px;
    }
    .material-option {
        border:2px solid #ddd; border-radius:6px;
        padding:2px; width:auto; cursor:pointer; text-align:center;
        transition:all .2s ease;
    }
    .material-option img {
        /* width:60px; 
        height:60px;  */
        object-fit:cover; border-radius:4px;
    }
    .material-option:hover { border-color:#999; }
    .material-option.selected {
        border-color:#007cba; background:#f0f8ff;
    }
    .material-name {
        display: block;
        margin-bottom: 4px;
        top: 5px;
        font-size: 12px;
        line-height: 90%;
        font-weight: 600;
    }
    select[name^="attribute_pa_material"] { display:none !important; }
    </style>
    <?php
});

add_action('wp_footer', function() {
    ?>
    <script>
    jQuery(function($){
        $('body').on('click', '.material-option', function(e){
            e.preventDefault();
            var $opt = $(this);
            var $container = $opt.closest('.material-visual-selector');
            var val = $opt.data('value');

            $container.find('.material-option').removeClass('selected');
            $opt.addClass('selected');

            // Меняем значение скрытого select WooCommerce
            var $select = $container.find('select');
            if ($select.length) {
                $select.val(val).trigger('change'); // Триггерит обновление вариаций
            }
        });
    });
    </script>
    <?php
});


















// Обработка фильтра
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('product')) {

        // Цена
        if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
            $meta_query = $query->get('meta_query') ?: [];

            $meta_query[] = [
                'key'       => '_price',
                'value'     => [
                    $_GET['min_price'] ?? 0,
                    $_GET['max_price'] ?? 999999
                ],
                'compare'   => 'BETWEEN',
                'type'      => 'NUMERIC',
            ];

            $query->set('meta_query', $meta_query);
        }
    }
});












// Регистрация шорткодов
foreach (glob(get_template_directory() . '/shortcodes/*.php') as $file) {
    if (basename($file) !== 'memorial-calc.php') continue;
    require_once $file;
}





