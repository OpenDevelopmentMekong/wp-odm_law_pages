<?php

require_once 'tabular-pages-config.php';

if (!class_exists('Odm_Tabular_Pages_Post_Type')) {

    class Odm_Tabular_Pages_Post_Type
    {
        public function __construct()
        {
          add_action('init', array($this, 'register_post_type'));
          add_action('save_post', array($this, 'save_post_data'));
          add_filter('single_template', array($this, 'get_tabular_pages_template'));
        }

        public function get_tabular_pages_template($single_template)
        {
            global $post;

            if ($post->post_type == 'tabular') {
                $single_template = plugin_dir_path(__FILE__).'templates/single-tabular.php';
            }

            return $single_template;
        }

        public function register_post_type()
        {
            $labels = array(
            'name' => __('Tabular Pages', 'post type general name', 'wp-odm_tabular_pages'),
            'singular_name' => __('Tabular Page', 'post type singular name', 'wp-odm_tabular_pages'),
            'menu_name' => __('Tabular Pages', 'admin menu for tabular pages', 'wp-odm_tabular_pages'),
            'name_admin_bar' => __('Tabular Pages', 'add new on admin bar', 'wp-odm_tabular_pages'),
            'add_new' => __('Add new', 'tabular page', 'wp-odm_tabular_pages'),
            'add_new_item' => __('Add new tabular page', 'wp-odm_tabular_pages'),
            'new_item' => __('New tabular pages', 'wp-odm_tabular_pages'),
            'edit_item' => __('Edit tabular pages', 'wp-odm_tabular_pages'),
            'view_item' => __('View tabular pages', 'wp-odm_tabular_pages'),
            'all_items' => __('All tabular pages', 'wp-odm_tabular_pages'),
            'search_items' => __('Search tabular pages', 'wp-odm_tabular_pages'),
            'parent_item_colon' => __('Parent tabular pages:', 'wp-odm_tabular_pages'),
            'not_found' => __('No tabular page found.', 'wp-odm_tabular_pages'),
            'not_found_in_trash' => __('No tabular page found in trash.', 'wp-odm_tabular_pages'),
            );

            $args = array(
              'labels'             => $labels,
              'public'             => true,
              'publicly_queryable' => true,
              'show_ui'            => true,
              'show_in_menu'       => true,
  			      'menu_icon'          => '',
              'query_var'          => true,
              'rewrite'            => array( 'slug' => 'tabular' ),
              'capability_type'    => 'page',
              'has_archive'        => true,
              'hierarchical'       => true,
              'menu_position'      => 5,
              //'taxonomies'         => array('category', 'language', 'post_tag'),
              'supports' => array('title', 'editor', 'page-attributes', 'revisions', 'author', 'thumbnail')
            );

            register_post_type('tabular', $args);
        }

        public function save_post_data($post_id)
        {
            global $post;
            if (isset($post->ID) && get_post_type($post->ID) == 'tabular') {

                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                    return;
                }

                if (defined('DOING_AJAX') && DOING_AJAX) {
                    return;
                }

                if (false !== wp_is_post_revision($post_id)) {
                    return;
                }

                if (!current_user_can('edit_post')) {
                    return;
                }

            }

          }

    }
}

?>
