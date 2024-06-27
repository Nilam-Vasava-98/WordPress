<?php
/*
Plugin Name: Custom Book Post Type
Description: Adds a custom post type for Books with custom fields and a shortcode to display them.
Version: 1.0
Author: Nilam
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function cbpt_register_book_post_type() {

    $labels = array(
        'name'                  => _x('Books', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Book', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Books', 'text_domain'),
        'name_admin_bar'        => __('Book', 'text_domain'),
        'archives'              => __('Book Archives', 'text_domain'),
        'attributes'            => __('Book Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Book:', 'text_domain'),
        'all_items'             => __('All Books', 'text_domain'),
        'add_new_item'          => __('Add New Book', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Book', 'text_domain'),
        'edit_item'             => __('Edit Book', 'text_domain'),
        'update_item'           => __('Update Book', 'text_domain'),
    );
    $args = array(
        'label'                 => __('Book', 'text_domain'),
        'description'           => __('Custom Post Type for Books', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail',),
        'taxonomies'            => array('category',),
        'public'                => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'capability_type'       => 'post',
    );
    register_post_type('book', $args);

}
add_action('init', 'cbpt_register_book_post_type', 0);

// Add custom fields
function cbpt_add_custom_meta_boxes() {
    add_meta_box(
        'cbpt_book_details',
        __('Book Details', 'text_domain'),
        'cbpt_render_book_details_meta_box',
        'book',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'cbpt_add_custom_meta_boxes');

function cbpt_render_book_details_meta_box($post) {
    wp_nonce_field('cbpt_save_book_details', 'cbpt_book_details_nonce');

    $author = get_post_meta($post->ID, '_cbpt_author', true);
    $year = get_post_meta($post->ID, '_cbpt_year', true);
    $genre = get_post_meta($post->ID, '_cbpt_genre', true);
    ?>
    <p>
        <label for="cbpt_author"><?php _e('Author', 'text_domain'); ?></label>
        <input type="text" id="cbpt_author" name="cbpt_author" value="<?php echo esc_attr($author); ?>" />
    </p>
    <p>
        <label for="cbpt_year"><?php _e('Publication Year', 'text_domain'); ?></label>
        <input type="number" id="cbpt_year" name="cbpt_year" value="<?php echo esc_attr($year); ?>" />
    </p>
    <p>
        <label for="cbpt_genre"><?php _e('Genre', 'text_domain'); ?></label>
        <input type="text" id="cbpt_genre" name="cbpt_genre" value="<?php echo esc_attr($genre); ?>" />
    </p>
    <?php
}

function cbpt_save_book_details($post_id) {
    if (!isset($_POST['cbpt_book_details_nonce']) || !wp_verify_nonce($_POST['cbpt_book_details_nonce'], 'cbpt_save_book_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'book' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['cbpt_author'])) {
        update_post_meta($post_id, '_cbpt_author', sanitize_text_field($_POST['cbpt_author']));
    }

    if (isset($_POST['cbpt_year'])) {
        update_post_meta($post_id, '_cbpt_year', sanitize_text_field($_POST['cbpt_year']));
    }

    if (isset($_POST['cbpt_genre'])) {
        update_post_meta($post_id, '_cbpt_genre', sanitize_text_field($_POST['cbpt_genre']));
    }
}
add_action('save_post', 'cbpt_save_book_details');

// Shortcode to display Books
function cbpt_books_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'count' => 5,
        ), $atts, 'book_list'
    );

    $query = new WP_Query(array(
        'post_type' => 'book',
        'posts_per_page' => $atts['count'],
    ));

    $output = '<div class="cbpt-books">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $author = get_post_meta(get_the_ID(), '_cbpt_author', true);
            $year = get_post_meta(get_the_ID(), '_cbpt_year', true);
            $genre = get_post_meta(get_the_ID(), '_cbpt_genre', true);

            $output .= '<div class="cbpt-book">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<p><strong>Author:</strong> ' . esc_html($author) . '</p>';
            $output .= '<p><strong>Publication Year:</strong> ' . esc_html($year) . '</p>';
            $output .= '<p><strong>Genre:</strong> ' . esc_html($genre) . '</p>';
            $output .= '<div class="cbpt-book-content">' . get_the_excerpt() . '</div>';
            $output .= '<p><a href="' . get_permalink() . '" class="cbpt-read-more">Read More</a></p>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No books found</p>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('book_list', 'cbpt_books_shortcode');

// Add styles for the shortcode
function cbpt_books_shortcode_styles() {
    echo '
    <style>
    .cbpt-books {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .cbpt-book {
        border: 1px solid #ddd;
        padding: 15px;
        width: calc(33.333% - 20px);
        box-sizing: border-box;
    }
    .cbpt-book h2 {
        margin-top: 0;
    }
    .cbpt-read-more {
        color: #0073aa;
        text-decoration: none;
        font-weight: bold;
    }
    .cbpt-read-more:hover {
        text-decoration: underline;
    }
    @media (max-width: 1024px) {
        .cbpt-book {
            width: calc(50% - 20px);
        }
    }
    @media (max-width: 768px) {
        .cbpt-book {
            width: 100%;
        }
    }
    </style>
    ';
}
add_action('wp_head', 'cbpt_books_shortcode_styles');
