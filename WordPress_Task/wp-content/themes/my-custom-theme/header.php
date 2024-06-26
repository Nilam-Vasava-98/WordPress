<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <div class="header-container">
            <div class="header-logo-nav">
                <!-- Logo -->
                <div class="logo">
                    <?php $uploads = wp_upload_dir(); ?>
                    <a href="<?php echo home_url(); ?>">
                        <img src="<?php echo esc_url($uploads['url'] . '/images.png'); ?>" alt="<?php bloginfo('name'); ?>">
                    </a>
                </div>
                <!-- Mobile Menu Toggle -->
                <div class="mobile-menu-toggle">
                    <span>&#9776;</span>
                </div>
                <!-- Navigation Menu -->
                <nav class="main-navigation">
                    <?php wp_nav_menu(array('theme_location' => 'main-menu')); ?>
                </nav>
            </div>
            <div class="header-description">
                <p><?php bloginfo('description'); ?></p>
            </div>
        </div>
    </header>
    <div id="content">
