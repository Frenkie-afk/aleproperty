<?php
/**
 * Property wishlist class
 *
 * see: https://stackoverflow.com/questions/34001707/add-values-to-user-meta-field-within-an-array
 *
 * */
class AlepropertyWishlist
{
	const ALEPROPERTY_WISHLIST_META_KEY = 'aleproperty_wishlist';

    private int $user_id;
    private array $wishlist_items;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
        $this->wishlist_items = self::get_wishlist( $user_id );
    }

	public static function init(): void
	{
		add_action( 'wp_ajax_update-wishlist', [__CLASS__, 'wishlist_handler'] );
	}

	public static function wishlist_handler(): void
	{
		check_ajax_referer( 'aleproperty_nonce', 'security' ); // check ajax nonce

		$user_id = get_current_user_id();
		$property_id = (int) filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);

		if (!$user_id) {
			wp_send_json_error(['error' => esc_html__('User not authenticated', 'ale-property')], 401);
			wp_die();
		}

		if ( !$property_id || get_post_type($property_id) !== 'property') {
			wp_send_json_error(['error' => esc_html__('Invalid property ID', 'ale-property')], 400);
			wp_die();
		}

		//instantiate wishlist
		$wishlist = new AlepropertyWishlist($user_id);

		//check if item already exist in array
		if (in_array($property_id, $wishlist->wishlist_items, true)) {
			$wishlist->remove_property( $property_id);
		}

		//add property to the wishlist
		$wishlist->add_property($property_id);
	}

    public function add_property(int $property_id): void
    {
	    $this->wishlist_items[] = $property_id;
	    $update_result = update_user_meta($this->user_id, self::ALEPROPERTY_WISHLIST_META_KEY, $this->wishlist_items);

	    if ($update_result) {
		    wp_send_json_success(['message' => esc_html__('Property successfully added to the wishlist', 'ale-property')], 200);
	    } else {
		    error_log(sprintf('Failed to update wishlist for user ID %d with property ID %d', $this->user_id, $property_id));
		    wp_send_json_error(['error' => esc_html__('Could not update wishlist. Please try again later', 'ale-property')], 500);
	    }
	    wp_die();
    }

    public function remove_property(int $property_id): void
    {
	    $index_to_remove = array_search($property_id, $this->wishlist_items, true);

        if ($index_to_remove !== false) {
            unset($this->wishlist_items[$index_to_remove]);
        }

	    $update_result = update_user_meta($this->user_id, self::ALEPROPERTY_WISHLIST_META_KEY, $this->wishlist_items);

	    if ($update_result) {
		    wp_send_json_success(['message' => esc_html__('Property successfully removed from the wishlist', 'ale-property')], 200);
	    } else {
		    error_log(sprintf('Failed to update wishlist for user ID %d with property ID %d', $this->user_id, $property_id));
		    wp_send_json_error(['error' => esc_html__('Could not update wishlist. Please try again later', 'ale-property')], 500);
	    }
        wp_die();
    }

	public static function get_wishlist(int $user_id): array {
		$wishlist = get_user_meta($user_id, self::ALEPROPERTY_WISHLIST_META_KEY, true);
		return is_array($wishlist) ? $wishlist : [];
	}

	public static function render_add_wishlist_button(): void
	{
		if (!is_user_logged_in()) {
			return;
		}

		$wishlist = AlepropertyWishlist::get_wishlist( get_current_user_id() );
		$is_property_added = in_array( get_the_ID(), $wishlist, true );
        ?>

		<button class="aleproperty-wishlist-btn <?php echo $is_property_added ? 'item-added' : ''; ?>" type="button" data-property-id="<?php echo esc_attr( get_the_ID() ); ?>">
            <svg width="20" height="17" stroke="coral" stroke-width="40" fill="transparent" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"></path>
            </svg>
        </button>

	<?php
    }

}
