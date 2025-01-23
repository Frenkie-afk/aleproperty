<?php

class AlepropertyBookingForm
{
	public function __construct()
	{
		self::init();
	}

	public static function init(): void
	{
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);

		add_action( 'wp_ajax_booking_form', [__CLASS__, 'form_handler'] );
		add_action( 'wp_ajax_nopriv_booking_form', [__CLASS__, 'form_handler']);
	}

	public static function enqueue_scripts(): void
	{
		wp_enqueue_script('aleproperty-booking-scripts', ALE_PROPERTY_URL . '/assets/js/front/bookingform.js', ['jquery'], ALE_PROPERTY_VERSION, ['strategy' => 'defer', 'in_footer'=> true, ]);
	}

    public static function form_handler(): void
    {
        check_ajax_referer( 'aleproperty_nonce', 'security' ); // check ajax nonce

	    //safely get $_POST data with sanitization
	    $booking_property = (string) filter_input( INPUT_POST, 'property', FILTER_SANITIZE_SPECIAL_CHARS );
	    $booking_name   = (string) filter_input( INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS );
	    $booking_email  = (string) filter_input( INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS );
	    $booking_phone  = (string) filter_input( INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS );

	    if ($booking_property && $booking_name && $booking_email && $booking_phone) {
            $message = esc_html__('Thank you for your reservation. We will contact you soon', 'ale-property');
		    $data_message   = "Property: " . esc_html($booking_property) . "\n";
		    $data_message  .= "Name: " . esc_html($booking_name) . "\n";
		    $data_message  .= "Email: " . esc_html($booking_email) . "\n";
		    $data_message  .= "Phone: " . esc_html($booking_phone) . "\n";

            // send confirmation email to user
//            wp_mail($booking_email, esc_html__('Aleproperty Booking', 'ale-property'), $message); //optional

            //send email to the admin
            wp_mail(get_option('admin_email'), esc_html__('New Booking form submission', 'ale-property'), $data_message);

		    wp_send_json_success($message, 200);
        }

        wp_send_json_error(['error' => esc_html__('Sorry, something went wrong. Please try again later', 'ale-property')], 400);

        wp_die();
    }

	public static function render_form(): void
	{ ?>
		<?php if ( get_option('aleproperty_settings')['aleproperty_settings_title'] ): ?>
            <h3 style="text-align: center; margin-bottom: 1rem"><?php echo get_option('aleproperty_settings')['aleproperty_settings_title']; ?></h3>
	    <?php endif; ?>

        <form  method="post" id="aleproperty-booking-form">
            <div class="aleproperty-form-group">
                <input type="text" name="name" id="aleproperty-booking-name" placeholder="<?php esc_html_e('Name', 'ale-property'); ?>" required>
            </div>

            <div class="aleproperty-form-group">
                <input type="email" name="email" id="aleproperty-booking-email" placeholder="<?php esc_html_e('Email', 'ale-property'); ?>" required>
            </div>

            <div class="aleproperty-form-group">
                <input type="tel" name="phone" id="aleproperty-booking-phone" placeholder="<?php esc_html_e('Phone', 'ale-property'); ?>" required>
            </div>

            <button type="submit" name="submit" id="aleproperty-booking-submit"><?php esc_html_e('Submit', 'ale-property'); ?></button>
        </form>

        <div id="aleproperty-booking-result"></div>
	<?php }

}
