<?php

/**
 * Plugin Name: ODM Law Pages
 * Plugin URI: http://github.com/OpenDevelopmentMekong/wp-odm_dataset_pages
 * Description: Internal ODM Wordpress plugin for exposing a page template for law pages
 * Version: 0.9.0
 * Author: Alex Corbi (mail@lifeformapps.com)
 * Author URI: http://www.lifeformapps.com
 * License: GPLv3.
 */

// Require dependencies isntalled via composer
require_once plugin_dir_path(__FILE__).'vendor/autoload.php';

// Require utils
require_once plugin_dir_path(__FILE__).'utils/utils.php';

// Require post types
require_once plugin_dir_path(__FILE__).'post-types/profile-pages.php';

if (!class_exists('OpenDev_Dataset_Pages')) {
    class OpenDev_Law_Pages
    {
        /**
     * A Unique Identifier.
     */
    protected $plugin_slug;

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        add_action('init', array($this, 'register_styles'));
        add_action('admin_notices', array($this, 'check_requirements'));
    }

        public function register_styles()
        {
            wp_register_style('style',  plugin_dir_url(__FILE__).'css/law-pages.css');
            wp_enqueue_style('style');

            locate_template(array(
          'page-laws.php',
      ), true);
        }

        public function check_requirements()
        {
            if (!check_requirements_law_pages()):
        echo '<div class="error"><p>ODM Laws pages: WPCKAN plugin is missing, deactivated or missconfigured. Please check.</p></div>';
            endif;
        }

        public static function activate()
        {
            // Do nothing
        }

        public static function deactivate()
        {
            // Do nothing
        }
    }
}

add_action('plugins_loaded', array('OpenDev_Law_Pages', 'get_instance'));
