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

		$param_country = odm_country_manager()->get_current_country() == 'mekong' && isset($_GET['country']) ? $_GET['country'] : odm_country_manager()->get_current_country();
	  $param_query = !empty($_GET['query']) ? $_GET['query'] : null;
	  $param_taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : null;
	  $param_language = isset($_GET['language']) ? $_GET['language'] : null;
	  $active_filters = !empty($param_taxonomy) || !empty($param_language) || !empty($param_query);

		$countries = odm_country_manager()->get_country_codes();

    $datasets = array();
		$filter_fields = array();
    $attrs = array(
      'type' => $dataset_type
    );
		if (!empty($param_country) && $param_country != 'mekong' && $param_country !== "all") {
			array_push($filter_fields,'"extras_odm_spatial_range":"'. $countries[$param_country]['iso2'] .'"');
		}
		if ($active_filters):
			if (!empty($param_query)) {
	      array_push($filter_fields,'"title":"'.$param_query.'"}');
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
        <a href="<?php get_page_link(); ?>"><h1><?php the_title(); ?></h1></a>
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

        <div class="four columns panel">
          <div class="sixteen columns">
            <div class="adv-nav-input">
              <p class="label"><label for="s"><?php _e('Text search', 'wp-odm_tabular_pages'); ?></label></p>
              <input type="text" id="query" name="query" placeholder="<?php _e('Type your search here', 'wp-odm_tabular_pages'); ?>" value="<?php echo $param_query; ?>" />
            </div>
          </div>
        </div>

        <div class="twelve columns panel">
          <?php
            $languages = odm_language_manager()->get_supported_languages_by_site();
            $num_columns = ($param_country === 'mekong') ? "four" : "six";
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
            $countries = odm_country_manager()->get_country_codes();
          ?>
  				<?php if ($param_country === 'mekong'): ?>
  	        <div class="four columns">
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
  				<?php endif; ?>

          <?php
            $taxonomy_list = odm_taxonomy_manager()->get_taxonomy_list();
            $num_columns = ($param_country === 'mekong') ? "four" : "six";
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

          <div class="four columns">
            <input class="button" type="submit" value="<?php _e('Search Filter', 'wp-odm_tabular_pages'); ?>"/>
            <?php
              if ($active_filters):
                ?>
                <a href="?clear"><?php _e('Clear','wp-odm_tabular_pages') ?></a>
            <?php
              endif;
             ?>
          </div>

          <?php
          foreach ($filters_list_array as $key => $type):
            $mapped_key = in_array($key,array_keys($values_mapping_array)) ?  $values_mapping_array[$key] : $key;
            $selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
            $selected_param_array = explode(",",$selected_param);
            $num_columns = integer_to_text(round(16 / (count($filters_datatables_list_array) + count($filters_list_array)))); ?>

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
              $num_columns = integer_to_text(round(16 / (count($filters_datatables_list_array) + count($filters_list_array))));
              if (!empty($options)): ?>

              <div class="<?php echo $num_columns?> columns">
                <div class="adv-nav-input">
                  <p class="label"><label for="<?php echo $key; ?>"><?php _e($mapped_key, 'wp-odm_tabular_pages'); ?></label></p>
                  <select id="<?php echo $key; ?>" name="<?php echo $key; ?>">
                    <option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
                    <?php
                      foreach($options as $option): ?>
                      <option value="<?php echo $option['id']; ?>" <?php if(in_array($option['id'],$selected_param_array)) echo 'selected'; ?>><?php echo $option['name']; ?></option>
                    <?php
                      endforeach; ?>
                  </select>
                </div>
              </div>

          <?php
              endif;
            endforeach; ?>
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
										echo "<td>";
										if (isset($dataset[$key])):
											$single_value = getMultilingualValueOrFallback($dataset[$key], odm_language_manager()->get_current_language(),$dataset[$key]);
											if (is_array($single_value) && isset($single_value["en"])):
                        $single_value = $single_value["en"];
											endif;
                    	$mapped_value = in_array($single_value,array_keys($values_mapping_array)) ?  $values_mapping_array[$single_value] : $single_value;
                      if (in_array($key,$link_to_detail_columns_array)): ?>
												<a target="_blank" href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo __($mapped_value, 'wp-odm_tabular_pages');?></a>
                      <?php
                      else:
                        echo $mapped_value == '' ? __('Not found', 'wp-odm_tabular_pages') : __($mapped_value, 'wp-odm_tabular_pages');
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
                          <a href="<?php echo $resource['url'];?>">
                          <i class="fa fa-download"></i> <?php echo strtoupper(implode(", ",$resource['odm_language'])); ?></a>
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

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
    "aoColumns": [{ "sWidth": "40%" }, { "sWidth": "20%" }, { "sWidth": "14%" }, { "sWidth": "12%" }, { "sWidth": "17%" }],
		<?php if (odm_language_manager()->get_current_language() == 'km'): ?>
		,"oLanguage": {
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

  $("#query").keyup(function () {
    console.log("filtering page " + this.value);
    oTable.fnFilterAll(this.value);
  });

  $('select').select2();
  $('.datepicker').datepicker();

});

</script>
