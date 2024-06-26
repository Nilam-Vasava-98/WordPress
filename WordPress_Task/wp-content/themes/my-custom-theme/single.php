<?php get_header(); ?>

<div id="content" class="site-content">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <?php
            while (have_posts()) : the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-meta">
                            <span>Posted by <?php the_author(); ?></span> | 
                            <span><?php the_time('F j, Y'); ?></span>
                        </div>
                    </header>
                    <div class="entry-content">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail();
                        }

                        if (get_post_type() == 'book') {
                            $author = get_post_meta(get_the_ID(), '_cbpt_author', true);
                            $year = get_post_meta(get_the_ID(), '_cbpt_year', true);
                            $genre = get_post_meta(get_the_ID(), '_cbpt_genre', true);
                            ?>
                            <p><strong>Author:</strong> <?php echo esc_html($author); ?></p>
                            <p><strong>Publication Year:</strong> <?php echo esc_html($year); ?></p>
                            <p><strong>Genre:</strong> <?php echo esc_html($genre); ?></p>
                            <?php
                        }

                        the_content();
                        ?>
                    </div>
                    <footer class="entry-footer">
                        <span><?php the_tags(); ?></span>
                    </footer>
                </article>
                <?php
                // Display comments template if comments are open or there are comments
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
            endwhile;
            ?>
        </main><!-- .site-main -->
    </div><!-- .content-area -->
</div><!-- .site-content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>



