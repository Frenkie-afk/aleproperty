<?php
//todo: get_header()/get_footer() doesn't work with FSE theme
get_header(); ?>

    <div class="container agent-single">

        <?php
        // Load posts loop.
        while ( have_posts() ) :
            the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php the_post_thumbnail('medium_large', ['class' => 'property-thumbnail', 'title' => 'Feature image']); ?>

                <h2 style="margin-block: 1rem"><?php the_title(); ?></h2>

                <div class="description" style="margin-bottom: 1rem"><?php the_content(); ?></div>

            </article><!-- #post-<?php the_ID(); ?> -->
        <?php endwhile; ?>

    </div>


<?php
get_footer();
