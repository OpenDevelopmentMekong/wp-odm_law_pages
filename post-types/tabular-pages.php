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
            'name' => __('Tabular Pages', 'post type general name', 'tabular'),
            'singular_name' => __('Tabular Page', 'post type singular name', 'tabular'),
            'menu_name' => __('Tabular Pages', 'admin menu for tabular pages', 'tabular'),
            'name_admin_bar' => __('Tabular Pages', 'add new on admin bar', 'tabular'),
            'add_new' => __('Add new', 'tabular page', 'tabular'),
            'add_new_item' => __('Add new tabular page', 'tabular'),
            'new_item' => __('New tabular pages', 'tabular'),
            'edit_item' => __('Edit tabular pages', 'tabular'),
            'view_item' => __('View tabular pages', 'tabular'),
            'all_items' => __('All tabular pages', 'tabular'),
            'search_items' => __('Search tabular pages', 'tabular'),
            'parent_item_colon' => __('Parent tabular pages:', 'tabular'),
            'not_found' => __('No tabular page found.', 'tabular'),
            'not_found_in_trash' => __('No tabular page found in trash.', 'tabular'),
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
