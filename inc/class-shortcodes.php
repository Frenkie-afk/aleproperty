<?php
/**
 * Example how to create a shortcode for the plugin
 *
 * see: https://codex.wordpress.org/Shortcode_API
 * see: https://misha.agency/wordpress/shortcodes.html
 * wp_insert_post: https://developer.wordpress.org/reference/functions/wp_insert_post/
 */
class AlepropertyShortcodes
{

    public function __construct()
    {
	    self::register_shortcodes();
    }

    private static function register_shortcodes(): void
    {
        add_shortcode('aleproperty_filter', [__CLASS__, 'filter_shortcode']);
	    add_shortcode('aleproperty_add_property', [__CLASS__, 'add_property_shortcode']);
	    add_shortcode('aleproperty_wishlist', [__CLASS__, 'wishlist_shortcode']);
    }
    public static function filter_shortcode( $atts = [] ): string
    {
        $params = shortcode_atts(
            array(
                'location'  => "true",
                'type'      => "true",
                'offer'     => "true",
                'price'     => "true",
                'agent'     => "true",
            ),
            $atts
        );

        ob_start(); ?>


        <div class="filters-form">
            <form method="GET" style="display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <?php if ($params['price'] === 'true'): ?>
                    <!-- Price filter -->
                    <div>
                        <label for="aleproperty-filter-price" style="display: block"><?php esc_html_e('Maximum price', 'ale-property'); ?></label>

                        <input type="number" name="aleproperty-filter-price" value="<?php echo !empty( $_GET['aleproperty-filter-price'] ) ? $_GET['aleproperty-filter-price'] : ''; ?>" id="aleproperty-filter-price">
                    </div>
                <?php endif; ?>

                <?php if ($params['offer'] === 'true'): ?>
                    <!-- Type filter -->
                    <div>
                        <label for="aleproperty-filter-type" style="display: block"><?php esc_html_e('Select offer type', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-type" id="aleproperty-filter-type">
                            <option value=""><?php esc_html_e('Select offer type', 'ale-property'); ?></option>
                            <option value="sale" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "sale" ? "selected" : ""; ?>><?php esc_html_e('For Sale', 'ale-property'); ?></option>
                            <option value="rent" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "rent" ? "selected" : ""; ?>><?php esc_html_e('For Rent', 'ale-property'); ?></option>
                            <option value="sold" <?php echo isset($_GET['aleproperty-filter-type']) && $_GET['aleproperty-filter-type'] === "sold" ? "selected" : ""; ?>><?php esc_html_e('Sold', 'ale-property'); ?></option>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['agent'] === 'true'): ?>
                    <!-- Agent filter -->
                    <div>
                        <label for="aleproperty-filter-agent" style="display: block"><?php esc_html_e('Select agent', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-agent" id="aleproperty-filter-agent">
                            <option value=""><?php esc_html_e('Select agent', 'ale-property'); ?></option>
                            <?php
                            $agent_posts = get_posts( ['post_type' => 'agent', 'numberposts' => -1 ] );
                            $selected_agent = !empty($_GET['aleproperty-filter-agent']) ? $_GET['aleproperty-filter-agent'] : '';

                            foreach ( $agent_posts as $agent ) { ?>
                                <option
                                        value="<?php echo esc_attr($agent->ID); ?>"
                                    <?php selected($selected_agent, $agent->ID); ?>
                                >
                                    <?php echo esc_html($agent->post_name); ?>
                                </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['location'] === 'true'): ?>
                    <!-- Location filter -->
                    <div>
                        <label for="aleproperty-filter-location" style="display: block"><?php esc_html_e('Select location', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-location" id="aleproperty-filter-location">
                            <option value=""><?php esc_html_e('Select location', 'ale-property'); ?></option>
                            <?php
                            $selected_location = !empty($_GET['aleproperty-filter-location']) ? $_GET['aleproperty-filter-location'] : '';

                            AleProperty::get_terms_hierarchically('location', $selected_location);

                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <?php if ($params['type'] === 'true'): ?>
                    <!-- Property type filter -->
                    <div>
                        <label for="aleproperty-filter-property-type" style="display: block"><?php esc_html_e('Select property type', 'ale-property'); ?></label>
                        <select name="aleproperty-filter-property-type" id="aleproperty-filter-property-type">
                            <option value=""><?php esc_html_e('Select property type', 'ale-property'); ?></option>
                            <?php
                            $selected_property_type = !empty($_GET['aleproperty-filter-property-type']) ? $_GET['aleproperty-filter-property-type'] : '';

                            AleProperty::get_terms_hierarchically('property-type', $selected_property_type);

                            ?>
                        </select>
                    </div>
                <?php endif; ?>

            </form>
        </div>

        <?php return ob_get_clean();
    }

    public static function add_property_shortcode( $atts = [] ): string  {

        if ( !is_user_logged_in() ) {
            return "<p>" . esc_html__( 'Please login to access this page.', 'ale-property' ) . "</p>";
        }

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aleproperty-submit']) && wp_verify_nonce( $_POST['_aleproperty_nonce_field'], 'aleproperty-fileds' ) ) {
            echo "<p style='text-align: center;'>" . AlepropertyShortcodes::add_property_form_handler() . "</p>";
        }

	    $agents = get_posts(['post_type' => 'agent', 'numberposts' => -1]);
        $allowed_mime_types = implode(', ', CommonHelper::IMG_ALLOWED_TYPES);

        ob_start(); ?>
        <div class="aleproperty-add-property-shortcode">
            <form method="post" id="aleproperty_add_aproperty" enctype="multipart/form-data">
                <div class="aleproperty-form-group">
                    <label for="property-title"><?php esc_html_e('Title', 'ale-property'); ?></label>
                    <input type="text" name="property-title" id="property-title" value="" placeholder="<?php esc_html_e('Add the title', 'ale-property'); ?>" required>
                </div>

                <div class="aleproperty-form-group">
                    <label for="property-description"><?php esc_html_e('Description', 'ale-property'); ?></label>
                    <textarea name="property-description" id="property-description" placeholder="<?php esc_html_e('Add the description', 'ale-property'); ?>" required></textarea>
                </div>
                
                <div class="aleproperty-form-group">
                    <label for="location"><?php esc_html_e('Location', 'ale-property'); ?></label>
                    <select name="location" id="location" required style="display: block;">
                        <option value=""><?php esc_html_e('Select location', 'ale-property'); ?></option>

                        <?php  AleProperty::get_terms_hierarchically('location'); ?>
                    </select>
                </div>

                <div class="aleproperty-form-group">
                    <label for="property-type"><?php esc_html_e('Property type', 'ale-property'); ?></label>
                    <select name="property-type" id="property-type" required>
                        <option value=""><?php esc_html_e('Select property type', 'ale-property'); ?></option>

			            <?php  AleProperty::get_terms_hierarchically('property-type'); ?>
                    </select>
                </div>

                <div class="aleproperty-form-group">
                    <label for="aleproperty_price"><?php esc_html_e('Property price', 'ale-property'); ?></label>
                    <input type="number" name="aleproperty_price" id="aleproperty_price" value="" placeholder="<?php esc_html_e('Add price', 'ale-property'); ?>" required>
                </div>

                <div class="aleproperty-form-group">
                    <label for="aleproperty_type"><?php esc_html_e('Offer type', 'ale-property'); ?></label>
                    <select name="aleproperty_type" id="aleproperty_type" required>
                        <option value=""><?php esc_html_e('Select property offer type', 'ale-property'); ?></option>
                        <option value="sale" ><?php esc_html_e('For Sale', 'ale-property'); ?></option>
                        <option value="rent" ><?php esc_html_e('For Rent', 'ale-property'); ?></option>
                        <option value="sold" ><?php esc_html_e('Sold', 'ale-property'); ?></option>
                    </select>
                </div>

	            <?php if ($agents): ?>
                    <div class="aleproperty-form-group">
                        <label for="aleproperty_agent"><?php esc_html_e('Agent', 'ale-property'); ?></label>
                        <select name="aleproperty_agent" id="aleproperty_agent">
                            <option value=""><?php esc_html_e('Select Agent', 'ale-property'); ?></option>

				            <?php foreach ($agents as $agent): ?>
                                <option value="<?php echo esc_attr($agent->ID); ?>">
						            <?php echo esc_html($agent->post_title); ?>
                                </option>
				            <?php endforeach; ?>
                        </select>
                    </div>
	            <?php endif; ?>

                <div class="aleproperty-form-group">
                    <label for="property-thumbnail"><?php esc_html_e('Thumbnail', 'ale-property'); ?></label>
                    <input type="file" name="property-thumbnail" id="property-thumbnail" accept="<?php echo esc_attr($allowed_mime_types); ?>" required>
                </div>

                <button type="submit" name="aleproperty-submit" id="aleproperty-submit"><?php esc_html_e('Submit', 'ale-property'); ?></button>

                <input type="hidden" name="action" value="aleproperty_add_property">
                <?php wp_nonce_field('aleproperty-fileds', '_aleproperty_nonce_field'); ?>
            </form>
        </div>

	    <?php return ob_get_clean();
    }

    public static function wishlist_shortcode( array $atts = [] ): string {

	    if (!is_user_logged_in()) {
		    return "<div class='aleproperty-wishlist'><p style='text-align: center'>" . esc_html__( 'Please login to access your wishlist.', 'ale-property' ) . "</p></div>";
	    }

        $wishlist_items = AlepropertyWishlist::get_wishlist( get_current_user_id() );

        if ( empty( $wishlist_items ) ) {
	        return "<div class='aleproperty-wishlist'><p style='text-align: center;'>" . esc_html__('Your wishlist is empty.', 'ale-property') . "</p></div>";
        }

	    $args = [
          'post_type'   => 'property',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'post__in' => $wishlist_items,
          'orderby' => 'post__in', //order by wishlist array order
        ];

        $wishlist = new WP_Query( $args );

        if ( $wishlist->have_posts() ) :
            ob_start();

            echo "<div class='aleproperty-wishlist'>";
            while ( $wishlist->have_posts() ) :
                $wishlist->the_post();

                self::render_wishlist_item();

            endwhile;
	        echo "</div>";

            wp_reset_postdata();
            return ob_get_clean();
        else:
            return "<div class='aleproperty-wishlist'><p style='text-align: center'>" . esc_html__('Your wishlist is empty.', 'ale-property') . "</p></div>";
        endif;


    }

    protected static function add_property_form_handler(): string
    {
	    if ( ! function_exists( 'post_exists' ) ) {
		    require_once ABSPATH . 'wp-admin/includes/post.php';
	    }

	    $aleproperty_post_data = [
		    'post_type'     => 'property',
		    'post_status'   => 'pending',
		    'post_author'   => sanitize_text_field(get_current_user_id()),
		    'post_title'    => sanitize_text_field($_POST['property-title']),
		    'post_content'  =>  sanitize_text_field($_POST['property-description']),
		    'tax_input'     => [
			    'property-type' => (int) sanitize_text_field($_POST['property-type']),
                'location'      => [ (int) sanitize_text_field($_POST['location']) ]
		    ],
		    'meta_input'    => [
			    'aleproperty_price'  => (int) sanitize_text_field($_POST['aleproperty_price']),
			    'aleproperty_type'   => sanitize_text_field($_POST['aleproperty_type']),
                'aleproperty_agent'  => (int) sanitize_text_field($_POST['aleproperty_agent'])
		    ]
	    ];

	    //todo: maybe try to do something more efficient
	    //prevent post duplicate when refreshing the page after form submission
	    $is_post_exist = post_exists(
		    $aleproperty_post_data['post_title'],
		    $aleproperty_post_data['post_content'],
		    '',
		    $aleproperty_post_data['post_type'],
		    $aleproperty_post_data['post_status']
	    );

	    if ( $is_post_exist ) {
		    $message = __( '<strong>Duplicate Post Error:</strong> it seems like you are trying to add the identical post.', 'ale-property' );
		    wp_die( new WP_Error( 'duplicate_post', $message, 409 ), '', ['back_link' => true] );
	    }

        //validation and upload thumbnail
        if ( !empty($_FILES['property-thumbnail']['name']) ) {
	        $aleproperty_post_data['_thumbnail_id'] = AlepropertyShortcodes::add_property_insert_thumbnail(); // add thumbnail id meta to wp_insert_post
        }

	    $aleproperty_result = wp_insert_post( $aleproperty_post_data ); //try to insert post

	    if ( is_wp_error( $aleproperty_result ) || $aleproperty_result == 0 )  {
		    return is_wp_error( $aleproperty_result )
			    ? $aleproperty_result->get_error_message()
			    : esc_html__('Something went wrong. Please try again.', 'ale-property');
	    }

	    return esc_html__('Property has been added successfully.', 'ale-property');
    }

    protected static function add_property_insert_thumbnail(): int {
	    require_once( ABSPATH . 'wp-admin/includes/image.php' );
	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	    require_once( ABSPATH . 'wp-admin/includes/media.php' );

	    $image_validation = CommonHelper::validate_image('property-thumbnail');

	    if ( !$image_validation['size'] ) {
		    $message = __( '<strong>Thumbnail Error:</strong> the attached image is too large.', 'ale-property' );
		    wp_die( new WP_Error( 'attached_image_too_large', $message, 400  ), '', ['back_link' => true] );
	    }

	    if ( !$image_validation['ext'] ) {
		    $message = sprintf( __( '<strong>Thumbnail Error:</strong> accepted file types are %s.', 'ale-property' ), implode(', ', CommonHelper::IMG_ALLOWED_EXT ) );
		    wp_die( new WP_Error( 'attached_image_wrong_ext', $message, 400  ), '', ['back_link' => true] );
	    }

	    $attachmentId = media_handle_upload( 'property-thumbnail', 0); //try to upload thumbnail

	    if ( is_wp_error( $attachmentId ) ) {
		    wp_die( new WP_Error( 'attached_image_error', $attachmentId->get_error_message(), 400  ), '', ['back_link' => true] );
	    }

        return $attachmentId;
    }

    protected static function render_wishlist_item (): void
    { ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('aleproperty-wishlist-item'); ?>>
            <figure>
                <a href="<?php echo esc_url( get_the_permalink() ); ?>">
	                <?php the_post_thumbnail('medium_large', ['class' => 'property-thumbnail', 'title' => 'Feature image']); ?>
                </a>
                <figcaption>
                    <?php the_title('<h3 style="margin-bottom: .5rem">', '</h3>'); ?>

                    <h5 style="margin-bottom: .5rem"><?php esc_html_e('Price:', 'ale-property') ?> &#36;<?php echo esc_html(get_post_meta(get_the_ID(), 'aleproperty_price', true)); ?></h5>

                    <a href="<?php echo esc_url( get_the_permalink() ); ?>" style="color: coral;font-size: 0.875rem"><?php esc_html_e('View details', 'ale-property'); ?></a>
                </figcaption>
            </figure>

            <button class="aleproperty-wishlist-remove-item" type="button" data-property-id="<?php echo esc_attr( get_the_ID() ); ?>" title="<?php esc_attr_e('Remove item from the wishlist', 'ale-property'); ?>">
                <svg style="display: block;" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 15L10 12" stroke="coral" stroke-width="2" stroke-linecap="round"/>
                    <path d="M14 15L14 12" stroke="coral" stroke-width="2" stroke-linecap="round"/>
                    <path d="M3 7H21V7C20.0681 7 19.6022 7 19.2346 7.15224C18.7446 7.35523 18.3552 7.74458 18.1522 8.23463C18 8.60218 18 9.06812 18 10V16C18 17.8856 18 18.8284 17.4142 19.4142C16.8284 20 15.8856 20 14 20H10C8.11438 20 7.17157 20 6.58579 19.4142C6 18.8284 6 17.8856 6 16V10C6 9.06812 6 8.60218 5.84776 8.23463C5.64477 7.74458 5.25542 7.35523 4.76537 7.15224C4.39782 7 3.93188 7 3 7V7Z" stroke="coral" stroke-width="2" stroke-linecap="round"/>
                    <path d="M10.0681 3.37059C10.1821 3.26427 10.4332 3.17033 10.7825 3.10332C11.1318 3.03632 11.5597 3 12 3C12.4403 3 12.8682 3.03632 13.2175 3.10332C13.5668 3.17033 13.8179 3.26427 13.9319 3.37059" stroke="coral" stroke-width="2" stroke-linecap="round"/>
                </svg>

            </button>
        </article>
    <?php }
}
