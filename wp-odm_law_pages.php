<?php

/**
 * Plugin Name: ODM Law Pages
 * Plugin URI: http://github.com/OpenDevelopmentMekong/wp-odm_law_pages
 * Description: Internal ODM Wordpress plugin for exposing a page template for law pages
 * Version: 0.9.0
 * Author: Alex Corbi (mail@lifeformapps.com)
 * Author URI: http://www.lifeformapps.com
 * License: GPLv3.
 */

// Require dependencies isntalled via composer
require_once plugin_dir_path( __FILE__ ).'vendor/autoload.php';

if (!class_exists('OpenDev_Law_Pages')) {

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
     * The array of templates that this plugin tracks.
     */
    protected $templates;

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

      $this->templates = array();

      // Add a filter to the attributes metabox to inject template into the cache.
      add_filter(
          'page_attributes_dropdown_pages_args',
           array($this, 'register_project_templates')
      );

      // Add a filter to the save post to inject out template into the page cache
      add_filter(
          'wp_insert_post_data',
          array($this, 'register_project_templates')
      );

      // Add a filter to the template include to determine if the page has our
      // template assigned and return it's path
      add_filter(
          'template_include',
          array($this, 'view_project_template')
      );

      add_action('init', array($this, 'register_styles'));

      // Add your templates to this array.
      $this->templates = array('templates/page-laws.php' => 'Law pages');
    }

    function register_styles()
    {
      wp_register_style('style',  plugin_dir_url( __FILE__ ).'css/law-pages.css');
      wp_enqueue_style('style');
    }

    public static function activate()
    {
      // Do nothing
    }

    public static function deactivate()
    {
      // Do nothing
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates($atts)
    {
      // Create the key used for the themes cache
      $cache_key = 'page_templates-'.md5(get_theme_root().'/'.get_stylesheet());

      // Retrieve the cache list.
      // If it doesn't exist, or it's empty prepare an array
      $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

      // New cache, therefore remove the old one
      wp_cache_delete($cache_key, 'themes');

      // Now add our template to the list of templates by merging our templates
      // with the existing templates array from the cache.
      $templates = array_merge($templates, $this->templates);

      // Add the modified cache to allow WordPress to pick it up for listing
      // available templates
      wp_cache_add($cache_key, $templates, 'themes', 1800);

      return $atts;
    }

    /**
     * Checks if the template is assigned to the page.
     */
    public function view_project_template($template)
    {
      global $post;

      if (!isset($post->ID)) {
          return;
      }

      if (!isset($this->templates[get_post_meta(
              $post->ID, '_wp_page_template', true
          )])) {
          return $template;
      }

      $file = plugin_dir_path(__FILE__).get_post_meta(
          $post->ID, '_wp_page_template', true
      );

      // Just to be safe, we check if the file exist first
      if (file_exists($file)) {
          return $file;
      } else {
          echo $file;
      }

      return $template;
    }
  }
}

add_action('plugins_loaded', array('OpenDev_Law_Pages', 'get_instance'));

?>
