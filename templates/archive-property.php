<?php
//todo: get_header()/get_footer() doesn't work with FSE theme
get_header();

?>

<div class="container property-archive ">
    <?php
        $template_loader =  AleProperty::get_template_loader();

        $template_loader->get_template_part( 'template-parts/filters'); // load filters template

        if ( have_posts() ) : ?>
            <div class="property-archive__grid"> <?php

                 while ( have_posts() ) :
                    the_post();

                    $template_loader->get_template_part( 'template-parts/content', 'property' );

                 endwhile; ?>

            </div> <!-- /.property-archive__grid -->

            <?php
            // Previous/next page navigation.
            posts_nav_link();


        else : ?>
            <p><?php esc_html_e('No Properties', 'ale-property'); ?></p>



        <?php endif;

    ?>
</div>


<?php
get_footer();
