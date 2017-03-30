<?php

/**
 * Plugin Name: ODM Tabular Pages
 * Plugin URI: http://github.com/OpenDevelopmentMekong/wp-odm_tabular_pages
 * Description: Internal ODM Wordpress plugin for exposing a page template for tabular pages
 * Version: 2.1.24
 * Author: Alex Corbi (mail@lifeformapps.com)
 * Author URI: http://www.lifeformapps.com
 * License: GPLv3.
 */

// Require utils
require_once plugin_dir_path(__FILE__).'utils/utils.php';

// Require post types
require_once plugin_dir_path(__FILE__).'post-types/tabular-pages.php';

if (!class_exists('Odm_Tabular_Pages_Plugin')) {

    class Odm_Tabular_Pages_Plugin
    {
        private static $instance;

        private static $post_type;

        public static function get_instance()
        {
            if (null == self::$instance) {
                self::$instance = new self();
            }

            if (null == self::$post_type) {
              self::$post_type = new Odm_Tabular_Pages_Post_Type();
            }

            return self::$instance;
        }

        private function __construct()
        {
            add_action('init', array($this, 'register_styles'));
            add_action('init', array($this, 'load_text_domain'));
            add_action('admin_notices', array($this, 'check_requirements'));
        }

        public function register_styles()
        {
            wp_enqueue_style('tabular-style', plugin_dir_url(__FILE__).'css/tabular-pages.css');
						wp_enqueue_style('select_css', plugin_dir_url(__FILE__).'bower_components/select2/dist/css/select2.min.css');
						wp_enqueue_script('select_js', plugin_dir_url(__FILE__).'bower_components/select2/dist/js/select2.min.js');
        }

        public function check_requirements()
        {
            if (!check_requirements_tabular_pages()):
        echo '<div class="error"><p>ODM Tabular pages: WPCKAN plugin is missing, deactivated or missconfigured. Please check.</p></div>';
            endif;
        }

				public function load_text_domain()
				{
          $locale = apply_filters( 'plugin_locale', get_locale(), 'odi' );
          load_textdomain( 'odi', trailingslashit( WP_LANG_DIR ) . '-' . $locale . '.mo' );
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

if (class_exists('Odm_Tabular_Pages_Plugin')) {
  register_activation_hook(__FILE__, array('Odm_Tabular_Pages_Plugin', 'activate'));
  register_deactivation_hook(__FILE__, array('Odm_Tabular_Pages_Plugin', 'deactivate'));
}

add_action('plugins_loaded', array('Odm_Tabular_Pages_Plugin', 'get_instance'));
