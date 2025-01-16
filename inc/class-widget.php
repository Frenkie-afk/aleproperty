<?php
/**
 * WP widget example using OOP and extending WP_Widget
 *
 * see: https://developer.wordpress.org/reference/classes/wp_widget/
 * see: https://wp-kama.ru/function/register_widget#example_34633
 */

class AlepropertyWidget extends WP_Widget
{

    public function __construct()
    {

        // init parent class
        parent::__construct(
            'aleproperty_widget',
            esc_html__('Aleproperty Widget', 'ale-property'),
            [
                'classname' => 'aleproperty-widget',
                'description' => esc_html__('Widget playground', 'ale-property')
            ]
        );
    }

    /**
     * Front-end display of widget.
     *
     */
    public function widget($args, $instance):void
    {
        $widget_title = apply_filters('widget_title', !empty($instance['title']) ? $instance['title'] : '');
	    $widget_button = $instance['button'] ?? '';

        echo $args['before_widget'];

        if ($widget_title) {
            echo $args['before_title'] . esc_html($widget_title) . $args['after_title'];
        }

        ob_start(); //widget body start ?>

        <p>Hello! It's a widget body</p>

        <?php if ($widget_button): ?>
            <button class="button"><?php esc_html_e('Filter Button', 'ale-property'); ?></button>
        <?php endif; ?>

        <?php echo ob_get_clean(); // echo widget body

        echo $args['after_widget'];
    }

    /**
     * Outputs the settings update form.
     *
     */
    public function form($instance):void
    {
        $widget_title = $instance['title'] ?? '';
	    $widget_button = $instance['button'] ?? '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'ale-property'); ?></label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($widget_title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button')); ?>"><?php esc_html_e('Show button', 'ale-property'); ?></label>
            <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('button')); ?>" name="<?php echo esc_attr($this->get_field_name('button')); ?>" value="1" <?php checked($widget_button, 1); ?>>
        </p>
    <?php }

    /**
     * Updates a particular instance of a widget.
     *
     * This function should check that `$new_instance` is set correctly. The newly-calculated
     * value of `$instance` should be returned. If false is returned, the instance won't be
     * saved/updated.
     *
     */
    public function update($new_instance, $old_instance)
    {
	    $instance  =  array();

	    $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
	    $instance['button'] = !empty($new_instance['button']) ? strip_tags($new_instance['button']) : '';

        return $instance;
    }

}
