<?php get_header(); ?>

<?php	if (have_posts()) : ?>


  <?php
		global $post;
		$valid_config = true;

		$dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true);
		$column_list = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_column_list_localization', true) : get_post_meta($post->ID, '_attributes_column_list', true);
		$values_mapping = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_values_mapping_localization', true) : get_post_meta($post->ID, '_attributes_values_mapping', true);
    $column_list_array = parse_mapping_pairs($column_list);
    $values_mapping_array = parse_mapping_pairs($values_mapping);

		$link_to_detail_columns = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_link_to_detail_column_localization', true) : get_post_meta($post->ID, '_attributes_link_to_detail_column', true);
		$link_to_detail_columns_array = explode(",",$link_to_detail_columns);

    $group_data_by_column_index = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID,'_attributes_group_data_by_column_index_localization', true) : get_post_meta($post->ID,'_attributes_group_data_by_column_index', true);

    $filters_list = get_post_meta($post->ID, '_attributes_filters_list', true);
    $filters_list_array = parse_mapping_pairs($filters_list);
    $filters_datatables_list = get_post_meta($post->ID, '_attributes_filters_datatables_list', true);
    $filters_datatables_list_array = parse_mapping_pairs($filters_datatables_list);

    $country_filter_enabled = get_post_meta($post->ID, '_attributes_country_filter_enabled', true) == "true" ? true : false;
    $language_filter_enabled = get_post_meta($post->ID, '_attributes_language_filter_enabled', true) == "true" ? true : false;
    $taxonomy_filter_enabled = get_post_meta($post->ID, '_attributes_taxonomy_filter_enabled', true) == "true" ? true : false;

    $num_filters = count($filters_datatables_list_array) + count($filters_list_array) + 1;
    if ($country_filter_enabled): $num_filters++; endif;
    if ($language_filter_enabled): $num_filters++; endif;
    if ($taxonomy_filter_enabled): $num_filters++; endif;
    if (isset($dataset_type) && $dataset_type == 'all'): $num_filters++; endif;
    $filters_specified = $num_filters > 1;

    $max_columns = 12;
    $num_filters = ($num_filters > 3) ? round($num_filters/2) : $num_filters;
    $num_columns = 12;
    if ($filters_specified):
      $num_columns = integer_to_text(round($max_columns / $num_filters));
    endif;

		$param_country = odm_country_manager()->get_current_country() == 'mekong' && isset($_GET['country']) ? $_GET['country'] : odm_country_manager()->get_current_country();
	  $param_query = !empty($_GET['query']) ? $_GET['query'] : null;
    $param_type = !empty($_GET['type']) ? $_GET['type'] : null;
	  $param_taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : null;
	  $param_language = isset($_GET['language']) ? $_GET['language'] : null;
	  $active_filters = !empty($param_query) || !empty($param_taxonomy) || !empty($param_language) || !empty($param_query);

		$countries = odm_country_manager()->get_country_codes();

    $datasets = array();
		$filter_fields = array();
    $attrs = array(
      'type' => '(dataset OR library_record OR laws_record)'
    );

    if (isset($dataset_type) && $dataset_type !== 'all'){
      $attrs['type'] = $dataset_type;
    }
    if (isset($param_type) && $param_type !== 'all'){
      $attrs['type'] = $param_type;
    }
		if (!empty($param_country) && $param_country != 'mekong' && $param_country !== "all") {
			array_push($filter_fields,'"extras_odm_spatial_range":"'. $countries[$param_country]['iso2'] .'"');
		}
		if ($active_filters):
			if (!empty($param_query)) {
	      $attrs['query'] = $param_query;
	    }
			if (!empty($param_taxonomy) && $param_taxonomy !== "all") {
	      array_push($filter_fields,'"extras_taxonomy":"'.$param_taxonomy.'"');
	    }
			if (!empty($param_language)  && $param_language !== "all") {
	      array_push($filter_fields,'"extras_odm_language":"'.$param_language.'"');
	    }
		endif;

    foreach ($filters_list_array as $key => $type):
      $selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
      if (isset($selected_param)  && $selected_param !== "all") {
	      array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
	    }
    endforeach;

    foreach ($filters_datatables_list_array as $key => $resource_id):
      $selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
      if (isset($selected_param)  && $selected_param !== "all") {
	      array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
	    }
    endforeach;
		$attrs['filter_fields'] = '{' . implode($filter_fields,",") . '}';

    $datasets = wpckan_api_package_search(wpckan_get_ckan_domain(),$attrs);

  ?>

  <section class="container">
		<header class="row">
			<div class="sixteen columns">
        <h1><?php the_title(); ?></h1>
			</div>
		</header>
	</section>

	<?php
	if (!$valid_config): ?>
	<section class="container">
		<div class="row">
			<h3 class="error"><?php _e('Error in configuration, please check.','wp-odm_tabular_pages'); ?></h3>
		</div>
	</section>
	<?php
	endif;
	?>

	<div class="container">
    <div class="row">

      <form class="advanced-nav-filters">

        <div class="sixteen columns panel">

          <?php
            $num_columns_text_search = ($filters_specified) ? "four" : "twelve"
            ?>
          <div class="<?php echo $num_columns_text_search; ?> columns">
            <div class="adv-nav-input">
              <p class="label"><label for="s"><?php _e('Text search', 'wp-odm_tabular_pages'); ?></label></p>
              <input type="text" id="query" name="query" placeholder="<?php _e('Search for title or other attributes', 'wp-odm_tabular_pages'); ?>" value="<?php echo $param_query; ?>" />
            </div>
          </div>

          <?php
            if (isset($dataset_type) && $dataset_type == 'all'):
          ?>
	        <div class="<?php echo $num_columns?> columns">
	          <div class="adv-nav-input">
	            <p class="label"><label for="type"><?php _e('Type', 'wp-odm_tabular_pages'); ?></label></p>
              <select id="type" name="type" data-placeholder="<?php _e('Select type', 'wp-odm_tabular_pages'); ?>">
                <option value="all" <?php if ($param_type == "all"): echo "selected"; endif; ?>>All</option>
                <option value="dataset" <?php if ($param_type == "dataset"): echo "selected"; endif; ?>>Dataset</option>
                <option value="library_record" <?php if ($param_type == "library_record"): echo "selected"; endif; ?>>Library record</option>
                <option value="laws_record" <?php if ($param_type == "laws_record"): echo "selected"; endif; ?>>Laws record</option>
              </select>
	          </div>
	        </div>
          <?php
            endif; ?>

          <?php
            $countries = odm_country_manager()->get_country_codes();
            if ($country_filter_enabled):
          ?>
	        <div class="<?php echo $num_columns?> columns">
	          <div class="adv-nav-input">
	            <p class="label"><label for="country"><?php _e('Country', 'wp-odm_tabular_pages'); ?></label></p>
	            <select id="country" name="country" data-placeholder="<?php _e('Select country', 'wp-odm_tabular_pages'); ?>">
	              <?php
	                if (odm_country_manager()->get_current_country() == 'mekong'): ?>
	                  <option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
	              <?php
	                endif; ?>
	              <?php
	                foreach($countries as $key => $value):
	                  if ($key != 'mekong'): ?>
	                    <option value="<?php echo $key; ?>" <?php if($key == $param_country) echo 'selected'; ?> <?php if (odm_country_manager()->get_current_country() != 'mekong' && $key != odm_country_manager()->get_current_country()) echo 'disabled'; ?>><?php echo odm_country_manager()->get_country_name($key); ?></option>
	                <?php
	                  endif; ?>
	                  <?php
	                endforeach; ?>
	            </select>
	          </div>
	        </div>
          <?php
            endif; ?>

          <?php
            $languages = odm_language_manager()->get_supported_languages_by_site();
            if ($language_filter_enabled):
          ?>
          <div class="<?php echo $num_columns?> columns">
            <div class="adv-nav-input">
              <p class="label"><label for="language"><?php _e('Language', 'wp-odm_tabular_pages'); ?></label></p>
              <select id="language" name="language" data-placeholder="<?php _e('Select language', 'wp-odm_tabular_pages'); ?>">
                <option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
                <?php
                  foreach($languages as $key => $value): ?>
                  <option value="<?php echo $key; ?>" <?php if($key == $param_language) echo 'selected'; ?>><?php echo $value; ?></option>
                <?php
                  endforeach; ?>
              </select>
            </div>
          </div>
          <?php
            endif; ?>

          <?php
            $taxonomy_list = odm_taxonomy_manager()->get_taxonomy_list();
            if ($taxonomy_filter_enabled):
          ?>
          <div class="<?php echo $num_columns?> columns">
            <div class="adv-nav-input">
              <p class="label"><label for="taxonomy"><?php _e('Taxonomy', 'wp-odm_tabular_pages'); ?></label></p>
              <select id="taxonomy" name="taxonomy" data-placeholder="<?php _e('Select term', 'wp-odm_tabular_pages'); ?>">
                <option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
                <?php
                  foreach($taxonomy_list as $value):
                    $val = apply_filters('translate_term', $value, odm_language_manager()->get_current_language());
                  ?>
                  <option value="<?php echo $value; ?>" <?php if($value == $param_taxonomy) echo 'selected'; ?>><?php echo $val; ?></option>
                <?php
                  endforeach; ?>
              </select>
            </div>
          </div>
          <?php
            endif; ?>

          <?php
          foreach ($filters_list_array as $key => $type):
            $mapped_key = in_array($key,array_keys($values_mapping_array)) ?  $values_mapping_array[$key] : $key;
            $selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
            $selected_param_array = explode(",",$selected_param); ?>

            <div class="<?php echo $num_columns?> columns">
              <div class="adv-nav-input">
                <p class="label"><label for="<?php echo $key; ?>"><?php _e($mapped_key, 'wp-odm_tabular_pages'); ?></label></p>
                <?php
                  if ($type == "date"): ?>
                    <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $selected_param; ?>" class="datepicker"></input>
                <?php
                  else: ?>
                    <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $selected_param; ?>"></input>
                <?php
                  endif; ?>
              </div>
            </div>
        <?php
          endforeach; ?>

          <?php
            foreach ($filters_datatables_list_array as $key => $resource_id):
              $mapped_key = in_array($key,array_keys($values_mapping_array)) ?  $values_mapping_array[$key] : $key;
              $options = wpckan_get_datastore_resource(wpckan_get_ckan_domain(),$resource_id);
              $selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
              $selected_param_array = explode(",",$selected_param);

              if (!empty($options)): ?>

              <div class="<?php echo $num_columns?> columns">
                <div class="adv-nav-input">
                  <p class="label"><label for="<?php echo $key; ?>"><?php _e($mapped_key, 'wp-odm_tabular_pages'); ?></label></p>
                  <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="odm_spatial_range-specific" data-current_country="<?php echo odm_country_manager()->get_current_country_code() ?>">
                    <option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
                    <?php
                      foreach($options as $option): ?>
                      <option
												value="<?php echo $option['id']; ?>"
												data-country_codes="<?php echo $option['country_codes']; ?>"
												<?php if(in_array($option['id'],$selected_param_array)) echo 'selected'; ?>><?php _e($option['name'],'wp-odm_tabular_pages'); ?></option>
                    <?php
                      endforeach; ?>
                  </select>
                </div>
              </div>

          <?php
              endif;
            endforeach;

            $num_columns_button = $filters_specified ? integer_to_text($max_columns - (round($max_columns / $num_filters) * ($num_filters -1))) : "four";
            ?>

            <div class="<?php echo $num_columns_button ?> columns">
              <input class="button" type="submit" value="<?php _e('Search', 'wp-odm_tabular_pages'); ?>"/>
              <?php
                if ($active_filters):
                  ?>
                  <a href="?clear"><?php _e('Clear','wp-odm_tabular_pages') ?></a>
              <?php
                endif;
               ?>
            </div>

        </div>

      </form>

    </div>
  </div>

  <section class="container">
    <div class="row">
		  <div class="sixteen columns">
        <?php the_content();?>
        <table id="datasets_table" class="data-table">
          <thead>
            <tr>
							<?php
								foreach ($column_list_array as $key => $value): ?>
									<th><?php _e($value, 'wp-odm_tabular_pages'); ?></th>
							<?php
								endforeach;
							 ?>
              <th><?php _e('Download', 'wp-odm_tabular_pages');?></th>
            </tr>
          </thead>
          <tbody>
            <?php
							if (in_array('results',array_keys($datasets))):
								foreach ($datasets['results'] as $dataset): ?>
							<tr>
							<?php
									foreach ($column_list_array as $key => $value):
                    $metadata_key = isset($dataset[$key]) ? $dataset[$key] : $dataset[str_replace("_translated","",$key)];
										echo "<td>";
										if (isset($metadata_key)):
											$single_value = getMultilingualValueOrFallback($metadata_key, odm_language_manager()->get_current_language(),$metadata_key);
											if (is_array($single_value) && isset($single_value["en"])):
                        $single_value = $single_value["en"];
											endif;
                    	$mapped_value = in_array($single_value,array_keys($values_mapping_array)) ?  $values_mapping_array[$single_value] : $single_value;
                      if (strlen($mapped_value) > 300):
                        $mapped_value = substr($mapped_value, 0, 300) . ' ...';
                      endif;
                      if (in_array($key,$link_to_detail_columns_array)): ?>
												<a target="_blank" href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo __($mapped_value, 'wp-odm_tabular_pages');?></a>
                      <?php
                      else:
                        echo $mapped_value == '' || empty($mapped_value) ? __('Not found', 'wp-odm_tabular_pages') : __($mapped_value, 'wp-odm_tabular_pages');
	                    endif;
                    endif;
										echo "</td>";
									endforeach;
							 ?>
                <td class="download_buttons">
                  <?php if (isset($dataset['resources'])) :?>
                    <?php foreach ($dataset['resources'] as $resource) :?>
                      <?php if (isset($resource['odm_language']) && !empty($resource['odm_language'])): ?>
                        <span>
                          <?php
                            if (is_array($resource['odm_language'])):
                              foreach ($resource['odm_language'] as $language) :?>
                                <a href="<?php echo $resource['url'];?>">
                                <i class="fa fa-download"></i> <?php _e(odm_language_manager()->get_the_language_by_language_code($language),'wp-odm_tabular_pages'); ?></a>
                              <?php
                              endforeach;
                            endif;
                            ?>
												</span>
                      <?php else: ?>
                        <span>
                          <a href="<?php echo $resource['url'];?>">
                          <i class="fa fa-download"></i> <?php _e('Download','wp-odm_tabular_pages'); ?></a>
                        </span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </td>
              </tr>
    				<?php
							endforeach;
						endif;?>
  				</tbody>
  			</table>
		  </div>
    </div>
	</section>
<?php endif; ?>

<?php get_footer(); ?>

<script type="text/javascript">

jQuery(document).ready(function($) {

  console.log("Tabular pages init");

  $.fn.dataTableExt.oApi.fnFilterAll = function (oSettings, sInput, iColumn, bRegex, bSmart) {
   var settings = $.fn.dataTableSettings;
   for (var i = 0; i < settings.length; i++) {
     settings[i].oInstance.fnFilter(sInput, iColumn, bRegex, bSmart);
   }
  };

  var oTable = $("#datasets_table").dataTable({
    scrollX: false,
    responsive: true,
		"bAutoWidth": false,
    dom: '<"top"<"info"i><"pagination"p><"length"l>>rt',
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    order: [[ <?php echo isset($group_data_by_column_index) && $group_data_by_column_index != '' ?  $group_data_by_column_index : 0 ?>, 'asc' ]],
    displayLength: 50,
		<?php if (odm_language_manager()->get_current_language() == 'km'): ?>
		"oLanguage": {
				"sLengthMenu": 'បង្ហាញចំនួន <select>'+
						'<option value="10">10</option>'+
						'<option value="25">25</option>'+
						'<option value="50">50</option>'+
						'<option value="-1">ទាំងអស់</option>'+
					'</select> ក្នុងមួយទំព័រ',
				"sZeroRecords": "ព័ត៌មានពុំអាចរកបាន",
				"sInfo": "បង្ហាញពីទី _START_ ដល់ _END_ នៃទិន្នន័យចំនួន _TOTAL_",
				"sInfoEmpty": "បង្ហាញពីទី 0 ដល់ 0 នៃទិន្នន័យចំនួន 0",
				"sInfoFiltered": "(ទាញចេញពីទិន្នន័យសរុបចំនួន _MAX_)",
				"sSearch":"ស្វែងរក",
				"oPaginate": {
					"sFirst": "ទំព័រដំបូង",
					"sLast": "ចុងក្រោយ",
					"sPrevious": "មុន",
					"sNext": "បន្ទាប់"
				}
		},
	 	<?php endif; ?>
    <?php if (isset($group_data_by_column_index) && $group_data_by_column_index != ''): ?>
    "drawCallback": function ( settings ) {
        var api = this.api();
        var rows = api.rows( {page:'current'} ).nodes();
        var last=null;

        api.column(<?php echo $group_data_by_column_index ?>, {page:'current'} ).data().each( function ( group, i ) {
            if ( last !== group ) {
                $(rows).eq( i ).before(
                    '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                );

                last = group;
            }
        } );
    }
    <?php endif; ?>
  });

  setTimeout(function () {
    oTable.fnAdjustColumnSizing();
  }, 10 );

  $('select').select2();
  $('.datepicker').datepicker();

	$('.odm_spatial_range-specific').each(function(){
		var country = [$(this).data('current_country')];
		$(this).find('option').each(function() {
			var countryCodes = $(this).data('country_codes');
			if (countryCodes){
				var countryCodesArray = countryCodes.split(",");
				var intersection = $(countryCodesArray).filter(country);
				if (intersection.length===0){
					console.log("removing", $(this).val());
					$(this).remove();
				}
			}
		});
	});

});

</script>
