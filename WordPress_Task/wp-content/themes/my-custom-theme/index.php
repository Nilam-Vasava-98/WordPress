<?php get_header(); ?>

<div id="content">
    <?php
    
    if (is_page('all-books')) { 
        // echo do_shortcode('[book_list count="10"]');
        the_content();
    } else {
        //home page data
        $latest_post_query = new WP_Query(array(
            'posts_per_page' => 3
        ));

        if ($latest_post_query->have_posts()) :
            while ($latest_post_query->have_posts()) : $latest_post_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <span>Posted by <?php the_author(); ?></span> | 
                        <span><?php the_time('F j, Y'); ?></span>
                    </div>
                    <div class="entry-content">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail();
                        }
                        the_content();
                        ?>
                    </div>
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>No content found</p>';
        endif;
    }
    ?>
</div>

<?php get_footer(); ?>
