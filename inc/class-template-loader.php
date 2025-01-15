<?php
/**
 * Template Loader for themes and child themes to override plugin templates
 * Works similar as Woo
 *
 * See: http://github.com/GaryJones/Gamajo-Template-Loader
 */
class AlepropertyTemplateLoader extends Gamajo_Template_Loader
{
    /**
     * Prefix for filter names.
     */
    protected $filter_prefix = 'aleproperty';

    /**
     * Directory name where custom templates for this plugin should be found in the theme.
     *
     * For example: 'your-plugin-templates'.
     */
    protected $theme_template_directory = 'aleproperty';

    /**
     * Reference to the root directory path of this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * e.g. YOUR_PLUGIN_TEMPLATE or plugin_dir_path( dirname( __FILE__ ) ); etc.
     *
     */
    protected $plugin_directory = ALE_PROPERTY_PATH;

    /**
     * Directory name where templates are found in this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * e.g. 'templates' or 'includes/templates', etc.
     */
    protected $plugin_template_directory = 'templates';

    public function __construct()
    {
        add_filter('template_include', [__CLASS__, 'property_templates']);
    }


    public static function property_templates($template): string
    {
        //todo: make it throw loop or something
        if (is_post_type_archive('property'))
        {
            $theme_files = ['archive-property.php', 'aleproperty/archive-property.php'];
            $exist = locate_template($theme_files);
            return $exist ? $exist : ALE_PROPERTY_TEMPLATES_PATH . '/archive-property.php';
        } elseif (is_post_type_archive('agent'))
        {
            $theme_files = ['archive-agent.php', 'aleproperty/archive-agent.php'];
            $exist = locate_template($theme_files);
            return $exist ? $exist : ALE_PROPERTY_TEMPLATES_PATH . '/archive-agent.php';
        }
        elseif (is_singular('property'))
        {
            $theme_files = ['single-property.php', 'aleproperty/single-property.php'];
            $exist = locate_template($theme_files);
            return $exist ? $exist : ALE_PROPERTY_TEMPLATES_PATH . '/single-property.php';
        }
        elseif (is_singular('agent'))
        {
            $theme_files = ['single-agent.php', 'aleproperty/single-agent.php'];
            $exist = locate_template($theme_files);
            return $exist ? $exist : ALE_PROPERTY_TEMPLATES_PATH . '/single-agent.php';
        }

        return $template;
    }
}
