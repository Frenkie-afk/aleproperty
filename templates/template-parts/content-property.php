
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="property-archive__post-permalink" aria-label="<?php esc_html_e('Link to', 'ale-property'); echo ' ' . esc_html__( get_the_title() ); ?>"></a>

    <?php the_post_thumbnail('medium_large', ['class' => 'property-thumbnail', 'title' => 'Feature image']); ?>

    <h2 style="margin-block: 1rem"><?php the_title(); ?></h2>

    <div class="description" style="margin-bottom: 1rem"><?php the_excerpt(); ?></div>

    <div class="post-meta">
        <p>
            <?php
            $terms = wp_get_post_terms( get_the_ID(), 'location', array("fields" => "names") );
            if (! is_wp_error($terms) && ! empty($terms)) {
	            esc_html_e('Location:', 'ale-property');

	            echo ' ' . esc_html( implode(', ', $terms) );
            }
            ?>
        </p>
        <p>
            <?php $property_types = wp_get_post_terms( get_the_ID(), 'property-type', array("fields" => "names") );
            if (! is_wp_error($property_types) && ! empty($property_types)) {
	            esc_html_e('Type:', 'ale-property');

	            echo ' ' . esc_html( implode(', ', $property_types) );
            }
            ?>
        </p>
        <p><?php esc_html_e('Price:', 'ale-property'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), 'aleproperty_price', true)); ?></p>
        <p><?php esc_html_e('Offer:', 'ale-property'); ?> <?php echo esc_html(get_post_meta(get_the_ID(), 'aleproperty_type', true)); ?></p>
        <p>
            <?php
            esc_html_e('Agent:', 'ale-property');

            $agent_id = get_post_meta(get_the_ID(), 'aleproperty_agent', true);
            $agent = get_post($agent_id);

            echo  ' ' . esc_html($agent->post_title);

            ?>
        </p>
    </div>

	<?php AlepropertyWishlist::render_add_wishlist_button(); ?>

</article><!-- #post-<?php the_ID(); ?> -->
