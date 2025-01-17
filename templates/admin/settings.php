<div class="wrap">
    <!-- Print the page title -->
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <form action="options.php" method="post">
		<?php
		settings_errors(); // call this function to see messages on submitting the form
		settings_fields('aleproperty_settings'); // for passing wp security

		// display previously registered sections for settings page with selected slug
		do_settings_sections('aleproperty-settings-page');

		submit_button();
		?>
    </form>
</div>
