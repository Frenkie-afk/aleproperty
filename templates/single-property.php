<?php
//todo: get_header()/get_footer() doesn't work with FSE theme
get_header(); ?>

    <div class="container property-single ">

        <?php
        // Load posts loop.
        while ( have_posts() ) :
            the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php the_post_thumbnail('medium_large', ['class' => 'property-thumbnail', 'title' => 'Feature image']); ?>

                <h2 id="aleproperty-property-title" style="margin-block: 1rem"><?php the_title(); ?></h2>

                <div class="description" style="margin-bottom: 1rem"><?php the_content(); ?></div>

                <div class="post-meta" style="margin-bottom: 1rem">
                    <p>
                        <?php
                        esc_html_e('Location:', 'ale-property');

                        $terms = wp_get_post_terms( get_the_ID(), 'location', array("fields" => "names") );
                        echo ' ' . esc_html( implode(', ', $terms) );
                        ?>
                    </p>
                    <p>
                        <?php esc_html_e('Type:', 'ale-property');

                        $property_types = wp_get_post_terms( get_the_ID(), 'property-type', array("fields" => "names") );
                        echo ' ' . esc_html( implode(', ', $property_types) );
                        ?>
                    </p>
                    <p><?php esc_html_e('Price:', 'ale-property'); ?> &#36;<?php echo esc_html(get_post_meta(get_the_ID(), 'aleproperty_price', true)); ?></p>
                    <p><?php esc_html_e('Offer:', 'ale-property'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), 'aleproperty_type', true)); ?></p>
                    <p>
                        <?php
                        esc_html_e('Agent:', 'ale-property');

                        $agent_id = get_post_meta(get_the_ID(), 'aleproperty_agent', true);
                        $agent = get_post($agent_id);

                        echo esc_html($agent->post_title);

                        ?>
                    </p>
                </div>

	            <?php AlepropertyWishlist::render_add_wishlist_button(); // render wishlist button ?>
                <?php AlepropertyBookingForm::render_form(); // render booking form ?>

            </article><!-- #post-<?php the_ID(); ?> -->
        <?php endwhile; ?>

    </div>


<?php
get_footer();
