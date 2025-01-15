
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="property-archive__post-permalink" aria-label="<?php esc_html_e('Link to', 'ale-property'); echo ' ' . esc_html__( get_the_title() ); ?>"></a>

    <?php the_post_thumbnail('medium_large', ['class' => 'property-thumbnail', 'title' => 'Feature image']); ?>

    <h2 style="margin-block: 1rem"><?php the_title(); ?></h2>

    <div class="description" style="margin-bottom: 1rem"><?php the_excerpt(); ?></div>

    <div class="post-meta">
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

</article><!-- #post-<?php the_ID(); ?> -->
