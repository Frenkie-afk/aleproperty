<?php
//todo: get_header()/get_footer() doesn't work with FSE theme
get_header(); ?>

    <div class="container property-archive ">
        <?php

        if ( have_posts() ) : ?>

            <div class="property-archive__grid">

                <?php
                // Load posts loop.
                while ( have_posts() ) :
                    the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="property-archive__post-permalink" aria-label="<?php esc_html_e('Link to', 'ale-property'); echo ' ' . esc_html__( get_the_title() ); ?>"></a>

                        <?php the_post_thumbnail('medium_large', ['class' => 'agent-thumbnail', 'title' => 'Feature image']); ?>

                        <h2><?php the_title(); ?></h2>

                        <div class="description" style="margin-bottom: 1rem"><?php the_excerpt(); ?></div>


                    </article><!-- #post-<?php the_ID(); ?> -->
                <?php endwhile; ?>

            </div> <!-- /.property-archive__grid -->

            <?php
            // Previous/next page navigation.
            posts_nav_link();

        else : ?>
            <p><?php esc_html_e('No Properties', 'ale-property'); ?></p>

        <?php

        endif;

        ?>
    </div>


<?php
get_footer();
