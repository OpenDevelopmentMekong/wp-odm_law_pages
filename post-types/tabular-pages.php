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
            'name' => __('Database', 'post type general name', 'wp-odm_tabular_pages'),
            'singular_name' => __('Database', 'post type singular name', 'wp-odm_tabular_pages'),
            'menu_name' => __('Database', 'admin menu for tabular pages', 'wp-odm_tabular_pages'),
            'name_admin_bar' => __('Database', 'add new on admin bar', 'wp-odm_tabular_pages'),
            'add_new' => __('Add new', 'tabular page', 'wp-odm_tabular_pages'),
            'add_new_item' => __('Add new database', 'wp-odm_tabular_pages'),
            'new_item' => __('New database', 'wp-odm_tabular_pages'),
            'edit_item' => __('Edit database', 'wp-odm_tabular_pages'),
            'view_item' => __('View database', 'wp-odm_tabular_pages'),
            'all_items' => __('All databases', 'wp-odm_tabular_pages'),
            'search_items' => __('Search databases', 'wp-odm_tabular_pages'),
            'parent_item_colon' => __('Parent databases:', 'wp-odm_tabular_pages'),
            'not_found' => __('No database found.', 'wp-odm_tabular_pages'),
            'not_found_in_trash' => __('No database found in trash.', 'wp-odm_tabular_pages'),
            );

            $args = array(
              'labels'             => $labels,
              'public'             => true,
              'publicly_queryable' => true,
              'show_ui'            => true,
              'show_in_menu'       => true,
  			      'menu_icon'          => '',
              'query_var'          => true,
              'rewrite'            => array( 'slug' => 'database' ),
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
           __('Option for database', 'wp-odm_tabular_pages'),
           array($this, 'tabular_options_box'),
           'tabular',
           'advanced',
           'high'
          );
          // Profile settings
          add_meta_box(
           'tabular_filters',
           __('Filters for database', 'wp-odm_tabular_pages'),
           array($this, 'tabular_filters_box'),
           'tabular',
           'advanced',
           'high'
          );
				}

				public function tabular_options_box($post = false)
	      {
	          $dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true);
						$column_list = get_post_meta($post->ID, '_attributes_column_list', true);
            $column_list_localization = get_post_meta($post->ID, '_attributes_column_list_localization', true);
						$link_to_detail_column = get_post_meta($post->ID,'_attributes_link_to_detail_column', true);
            $link_to_detail_column_localization = get_post_meta($post->ID,'_attributes_link_to_detail_column_localization', true);
						$values_mapping = get_post_meta($post->ID,'_attributes_values_mapping', true);
            $values_mapping_localization = get_post_meta($post->ID,'_attributes_values_mapping_localization', true);
            $group_data_by_column_index = get_post_meta($post->ID,'_attributes_group_data_by_column_index', true);
            $group_data_by_column_index_localization = get_post_meta($post->ID,'_attributes_group_data_by_column_index_localization', true);
						?>
            <div id="multiple-site">
              <input type="radio" id="en" class="en" name="language_site" value="en" checked />
              <label for="en"><?php _e('ENGLISH', 'wp-odm_tabular_pages');
                  ?></label> &nbsp;
              <?php if (odm_language_manager()->get_the_language_by_site() != "English"): ?>
                <input type="radio" id="localization" class="localization" name="language_site" value="localization" />
                <label for="localization"><?php _e(odm_language_manager()->get_the_language_by_site(), 'wp-odm_tabular_pages');?></label>
              <?php endif; ?>
            </div>
	          <div id="tabular_options_box">
	            <h4><?php _e('Choose dataset type', 'wp-odm_tabular_pages');?></h4>
	            <select id="_attributes_dataset_type" name="_attributes_dataset_type">
                <option value="all" <?php if ($dataset_type == "all"): echo "selected"; endif; ?>><?php _e('All', 'wp-odm_tabular_pages');?></option>
	              <option value="dataset" <?php if ($dataset_type == "dataset"): echo "selected"; endif; ?>><?php _e('Dataset', 'wp-odm_tabular_pages');?></option>
	              <option value="library_record" <?php if ($dataset_type == "library_record"): echo "selected"; endif; ?>><?php _e('Publication', 'wp-odm_tabular_pages');?></option>
	              <option value="laws_record" <?php if ($dataset_type == "laws_record"): echo "selected"; endif; ?>><?php _e('Laws record', 'wp-odm_tabular_pages');?></option>
	            </select>

              <div class="language_settings language-en">
                <h4><?php _e('The attributes of the Dataset that would like to display', 'wp-odm_tabular_pages'); ?></h4>
  							<textarea name="_attributes_column_list" style="width:100%;height: 200px;" placeholder="title_translated  =>  Title"><?php echo $column_list;  ?></textarea>
  							<p class="description"><?php _e('Please specify the attributes plus their labels, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

  							<h4><?php _e('Id to Label mapping for values', 'wp-odm_tabular_pages'); ?></h4>
  							<textarea name="_attributes_values_mapping" style="width:100%;height: 200px;" placeholder="anukretsub-decree  =>  Anukret Sub-decree"><?php echo $values_mapping;  ?></textarea>
  							<p class="description"><?php _e('Please specify the ids plus their labels, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

  						  <h4><?php _e('Column ids linking to detail page', 'wp-odm_tabular_pages');?></h4>
  						  <input class="full-width" type="text" id="_attributes_link_to_detail_column" name="_attributes_link_to_detail_column" placeholder="name,title" value="<?php echo $link_to_detail_column; ?>" />
  			        <p class="description"><?php _e('Please add the ids of the columns that will feature a link to the entry\'s detail page. Format: Comma-separated values. <br/>eg. name,company,developer,block', 'wp-odm_tabular_pages'); ?></p>

                <h4><?php _e('Group Data in Column', 'wp-odm_tabular_pages');?></h4>
  						  <input class="full-width" type="text" id="_attributes_group_data_by_column_index" name="_attributes_group_data_by_column_index" placeholder="1" value="<?php echo $group_data_by_column_index; ?>" />
  			        <p class="description"><?php _e('Eg. To group records by a certain column. please specify the index, not the name.', 'wp-odm_tabular_pages'); ?></p>
              </div>

              <?php if (odm_language_manager()->get_the_language_by_site() != "English") { ?>
                <div class="language_settings language-localization">
                  <h4><?php _e('The attributes of the Dataset that would like to display', 'wp-odm_tabular_pages'); ?></h4>
    							<textarea name="_attributes_column_list_localization" style="width:100%;height: 200px;" placeholder="title_translated  =>  Title"><?php echo $column_list_localization;  ?></textarea>
    							<p class="description"><?php _e('Please specify the attributes plus their labels, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

    							<h4><?php _e('Id to Label mapping for values', 'wp-odm_tabular_pages'); ?></h4>
    							<textarea id="_attributes_values_mapping_localization" name="_attributes_values_mapping_localization" style="width:100%;height: 200px;" placeholder="anukretsub-decree  =>  Anukret Sub-decree"><?php echo $values_mapping_localization;  ?></textarea>
    							<p class="description"><?php _e('Please specify the ids plus their labels, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

    						  <h4><?php _e('Column ids linking to detail page', 'wp-odm_tabular_pages');?></h4>
    						  <input class="full-width" type="text" id="_attributes_link_to_detail_column_localization" name="_attributes_link_to_detail_column_localization" placeholder="name,title" value="<?php echo $link_to_detail_column_localization; ?>" />
    			        <p class="description"><?php _e('Please add the ids of the columns that will feature a link to the entry\'s detail page. Format: Comma-separated values. <br/>eg. name,company,developer,block', 'wp-odm_tabular_pages'); ?></p>

                  <h4><?php _e('Group Data in Column', 'wp-odm_tabular_pages');?></h4>
    						  <input class="full-width" type="text" id="_attributes_group_data_by_column_index_localization" name="_attributes_group_data_by_column_index_localization" placeholder="1" value="<?php echo $group_data_by_column_index_localization; ?>" />
    			        <p class="description"><?php _e('Eg. To group records by a certain column. please specify the index, not the name.', 'wp-odm_tabular_pages'); ?></p>
                </div>
              <?php } ?>

            </div>
            <script type="text/javascript">
        		 jQuery(document).ready(function($) {
        			var $container = $('#multiple-site');
        			var $languageSelection = $('input[type="radio"]');
        			var $forms = $('.language_settings');
        			var showForms = function() {
        				  $forms.hide();
        					var selected = $('input[type="radio"][name=language_site]').filter(':checked').val();
        					$('.language-' + selected).show();
        			}
        			$languageSelection.on('change', function() {
        					$('.' + this.className).prop('checked', this.checked);
        			 	showForms();
        			});

        			showForms();
             });
            </script>
	      <?php
	      }

        public function tabular_filters_box($post = false)
	      {
          $filters_list = get_post_meta($post->ID, '_attributes_filters_list', true);
          $filters_datatables_list = get_post_meta($post->ID, '_attributes_filters_datatables_list', true);

          $country_filter_enabled = get_post_meta($post->ID, '_attributes_country_filter_enabled', true) == "true" ? true : false;
          $language_filter_enabled = get_post_meta($post->ID, '_attributes_language_filter_enabled', true) == "true" ? true : false;
          $taxonomy_filter_enabled = get_post_meta($post->ID, '_attributes_taxonomy_filter_enabled', true) == "true" ? true : false;

          $filtered_by_column_index = get_post_meta($post->ID, '_filtered_by_column_index', true); ?>

          <div id="tabular_filters_box">

            <h4><?php _e('List of default filters', 'wp-odm_tabular_pages'); ?></h4>
            <input type="checkbox" id="_attributes_country_filter_enabled" name="_attributes_country_filter_enabled"  value="true" <?php if ($country_filter_enabled): echo 'checked="yes"'; endif;?>/> Country (odm_spatial_range) <br />
            <input type="checkbox" id="_attributes_language_filter_enabled" name="_attributes_language_filter_enabled"  value="true" <?php if ($language_filter_enabled): echo 'checked="yes"'; endif;?>/> Language (odm_language)<br />
            <input type="checkbox" id="_attributes_taxonomy_filter_enabled" name="_attributes_taxonomy_filter_enabled" value="true" <?php if ($taxonomy_filter_enabled): echo 'checked="yes"'; endif;?>/> Taxonomy (taxonomy)<br/>
            <p class="description"><?php _e('Please specify which of the default filters are displayed', 'wp-odm_tabular_pages'); ?></p>

            <h4><?php _e('List of additional filters with types', 'wp-odm_tabular_pages'); ?></h4>
            <textarea id="_attributes_filters_list" name="_attributes_filters_list" style="width:100%;height: 200px;" placeholder="odm_promulgation_date  => date"><?php echo $filters_list; ?></textarea>
            <p class="description"><?php _e('Please specify the field names and the corresponding field type, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

            <h4><?php _e('List of additional filters associated with data tables', 'wp-odm_tabular_pages'); ?></h4>
            <textarea id="_attributes_filters_datatables_list" name="_attributes_filters_datatables_list" style="width:100%;height: 200px;" placeholder="odm_document_type  =>  09f75141-0885-44f7-bcfc-8cd1e3779ff5"><?php echo $filters_datatables_list;  ?></textarea>
            <p class="description"><?php _e('Please specify the field names and the corresponding resource ids, separated by => and line breaks', 'wp-odm_tabular_pages'); ?></p>

            <h4><?php _e('Create Select Filter by Column Index', 'wp-odm_tabular_pages'); ?></h4>
            <input id="_filtered_by_column_index" type="text" placeholder="2, 5" size="40" name="_filtered_by_column_index" value="<?php echo $filtered_by_column_index; ?>" />
            <p class="description"><?php _e('Filter selectors will create automatically by adding the column index and separated by comma.', 'wp-odm_tabular_pages'); ?></p>

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

                if (isset($_POST['_attributes_column_list_localization'])) {
                    update_post_meta($post_id, '_attributes_column_list_localization', $_POST['_attributes_column_list_localization']);
                }

                if (isset($_POST['_attributes_group_data_by_column_index'])) {
                    update_post_meta($post_id, '_attributes_group_data_by_column_index', $_POST['_attributes_group_data_by_column_index']);
                }

                if (isset($_POST['_attributes_group_data_by_column_index_localization'])) {
                    update_post_meta($post_id, '_attributes_group_data_by_column_index_localization', $_POST['_attributes_group_data_by_column_index_localization']);
                }

								if (isset($_POST['_attributes_link_to_detail_column'])) {
                    update_post_meta($post_id, '_attributes_link_to_detail_column', $_POST['_attributes_link_to_detail_column']);
                }

                if (isset($_POST['_attributes_link_to_detail_column_localization'])) {
                    update_post_meta($post_id, '_attributes_link_to_detail_column_localization', $_POST['_attributes_link_to_detail_column_localization']);
                }

								if (isset($_POST['_attributes_values_mapping'])) {
                    update_post_meta($post_id, '_attributes_values_mapping', $_POST['_attributes_values_mapping']);
                }

                if (isset($_POST['_attributes_values_mapping_localization'])) {
                    update_post_meta($post_id, '_attributes_values_mapping_localization', $_POST['_attributes_values_mapping_localization']);
                }

                if (isset($_POST['_attributes_filters_list'])) {
                    update_post_meta($post_id, '_attributes_filters_list', $_POST['_attributes_filters_list']);
                }

                if (isset($_POST['_attributes_filters_datatables_list'])) {
                    update_post_meta($post_id, '_attributes_filters_datatables_list', $_POST['_attributes_filters_datatables_list']);
                }

                if (isset($_POST['_filtered_by_column_index'])) {
                    update_post_meta($post_id, '_filtered_by_column_index', $_POST['_filtered_by_column_index']);
                }

                update_post_meta($post_id, '_attributes_country_filter_enabled', $_POST['_attributes_country_filter_enabled']);
                update_post_meta($post_id, '_attributes_language_filter_enabled', $_POST['_attributes_language_filter_enabled']);
                update_post_meta($post_id, '_attributes_taxonomy_filter_enabled', $_POST['_attributes_taxonomy_filter_enabled']);

                if (!current_user_can('edit_post')) {
                    return;
                }

            }

          }

    }
}

?>
