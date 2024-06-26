<?php
function my_custom_theme_setup() {
    // Register navigation menu
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'my-custom-theme'),
    ));
}

add_action('after_setup_theme', 'my_custom_theme_setup');

// Enqueue styles and scripts
function my_custom_theme_enqueue_styles_scripts() {
    wp_enqueue_style('main-style', get_stylesheet_uri());

    wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array(), '1.0', false); // Enqueue jQuery
    wp_enqueue_script('mytheme-script', get_template_directory_uri() . '/js/main.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'my_custom_theme_enqueue_styles_scripts');

// admin widget style

function enqueue_custom_admin_style() {
    wp_enqueue_style('custom-admin-style', get_template_directory_uri() . '/css/custom-admin-style.css');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_style');


// Add a custom dashboard widget
function my_custom_dashboard_widget() {
    wp_add_dashboard_widget(
        'custom_dashboard_widget', 
        'Welcome to Your Site!',   
        'custom_dashboard_widget_content' // custom Display function.
    );
}

function custom_dashboard_widget_content() {
    echo '<p>Welcome to your site! Here are some important links:</p>';
    echo '<ul>';
    echo '<li><a href="' . admin_url('post-new.php') . '">Add New Post</a></li>';
    echo '<li><a href="' . admin_url('edit.php') . '">View All Posts</a></li>';
    echo '<li><a href="' . admin_url('customize.php') . '">Customize Your Site</a></li>';
    echo '<li><a href="' . admin_url('options-general.php') . '">Site Settings</a></li>';
    echo '</ul>';
}

add_action('wp_dashboard_setup', 'my_custom_dashboard_widget');

// Customize the admin menu
function customize_admin_menu() {
    // Remove Comments from admin menu (default items)
    remove_menu_page('edit-comments.php'); 

    // Move 'Pages' to the top (just below 'Dashboard')
    global $menu;
    $menu[5] = $menu[20]; 
}

add_action('admin_menu', 'customize_admin_menu');

// Add custom columns to the "Book" post type list table
function set_custom_book_columns($columns) {
    unset($columns['date']); // Remove the default 'Date' column
    // unset($columns['author']);
    $columns['bookauthor'] = __('Author');
    $columns['year'] = __('Publication Year');
    $columns['genre'] = __('Genre');
    $columns['date'] = __('Date');
    return $columns;
}

function custom_book_column($column, $post_id) {
    switch ($column) {
        case 'bookauthor':
            $author = get_post_meta($post_id, '_cbpt_author', true);
            // echo '<pre>';
            // var_dump($author); 
            // echo '</pre>';
            if (empty($author)) {
                echo '<span style="color: red;">No Author Found</span>';
            } else {
                echo esc_html($author);
            }
            break;
        case 'year':
            $year = get_post_meta($post_id, '_cbpt_year', true);
            // echo '<pre>';
            // var_dump($year); 
            // echo '</pre>';
            if (empty($year)) {
                echo '<span style="color: red;">No Year Found</span>';
            } else {
                echo esc_html($year);
            }
            break;
        case 'genre':
            $genre = get_post_meta($post_id, '_cbpt_genre', true);
            // echo '<pre>';
            // var_dump($genre); 
            // echo '</pre>';
            if (empty($genre)) {
                echo '<span style="color: red;">No Genre Found</span>';
            } else {
                echo esc_html($genre);
            }
            break;
    }
}


add_filter('manage_book_posts_columns', 'set_custom_book_columns');
add_action('manage_book_posts_custom_column', 'custom_book_column', 10, 2);


// REST API initialization for book posts
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/books', array(
        'methods' => 'GET',
        'callback' => 'get_books',
    ));
});


// handle the GET request
function get_books() {
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1, 
    );
    $query = new WP_Query($args);

    $books = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $books[] = array(
                'title' => get_the_title(),
                'author' => get_post_meta(get_the_ID(), '_cbpt_author', true),
                'year' => get_post_meta(get_the_ID(), '_cbpt_year', true),
                'genre' => get_post_meta(get_the_ID(), '_cbpt_genre', true),
            );
        }
        wp_reset_postdata();
    }
    return new WP_REST_Response($books, 200);
}


