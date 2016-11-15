<?php

if (!class_exists('Odm_Tabular_Pages_Post_Type')) {

    class Odm_Tabular_Pages_Post_Type
    {
        public function __construct()
        {
          add_action('init', array($this, 'register_post_type'));
          add_action('save_post', array($this, 'save_post_data'));
          add_action('add_meta_boxes', array($this, 'add_meta_box'));
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

				public function add_meta_box()
        {
          // Profile settings
          add_meta_box(
           'tabular_options',
           __('Option for tabular pages', 'wp-odm_tabular_pages'),
           array($this, 'tabular_options_box'),
           'tabular',
           'advanced',
           'high'
          );
				}

				public function tabular_options_box($post = false)
	      {
	          $dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true);
						$column_list = get_post_meta($post->ID, '_attributes_column_list', true);
						$link_to_detail_column = get_post_meta($post->ID,'_attributes_link_to_detail_column', true); ?>
	          <div id="tabular_options_box">
	            <h4><?php _e('Choose dataset type', 'wp-odm_tabular_pages');?></h4>
	            <select id="_attributes_dataset_type" name="_attributes_dataset_type">
	              <option value="dataset" <?php if ($dataset_type == "dataset"): echo "selected"; endif; ?>>Dataset</option>
	              <option value="library_record" <?php if ($dataset_type == "library_record"): echo "selected"; endif; ?>>Library record</option>
	              <option value="laws_record" <?php if ($dataset_type == "laws_record"): echo "selected"; endif; ?>>Laws record</option>
	            </select>

						  <h4><?php _e('Specify list of columns to present on the table (comma-separated)', 'wp-odm_tabular_pages');?></h4>
 	            <input class="full-width" type="text" id="_attributes_column_list" name="_attributes_column_list" value="<?php echo $column_list; ?>"></input>
              <p class="description"><?php _e('Please add the ids of the columns that will feature a link to the entry\'s detail page. Format: Comma-separated values. <br/>eg. name,company,developer,block', 'wp-odm_tabular_pages'); ?></p>

						  <h4><?php _e('Column ids linking to detail page', 'wp-odm_tabular_pages');?></h4>
						  <input class="full-width" type="text" id="_attributes_link_to_detail_column" name="_attributes_link_to_detail_column" placeholder="name,title" value="<?php echo $link_to_detail_column; ?>" />
			        <p class="description"><?php _e('Please add the ids of the columns that will feature a link to the entry\'s detail page. Format: Comma-separated values. <br/>eg. name,company,developer,block', 'wp-odm_tabular_pages'); ?></p>
	          </div>
	      <?php
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

								if (isset($_POST['_attributes_dataset_type'])) {
                    update_post_meta($post_id, '_attributes_dataset_type', $_POST['_attributes_dataset_type']);
                }

								if (isset($_POST['_attributes_column_list'])) {
                    update_post_meta($post_id, '_attributes_column_list', $_POST['_attributes_column_list']);
                }

								if (isset($_POST['_attributes_link_to_detail_column'])) {
                    update_post_meta($post_id, '_attributes_link_to_detail_column', $_POST['_attributes_link_to_detail_column']);
                }

                if (!current_user_can('edit_post')) {
                    return;
                }

            }

          }

    }
}

?>
