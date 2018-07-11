<?php get_header(); ?>

<?php	if (have_posts()) : ?>

	<?php
		global $post;
		$valid_config = true;
		$values_mapping = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_values_mapping_localization', true) : get_post_meta($post->ID, '_attributes_values_mapping', true);
		$dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true)?get_post_meta($post->ID, '_attributes_dataset_type', true) : "all" ;
		$column_list = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_column_list_localization', true) : get_post_meta($post->ID, '_attributes_column_list', true);
		$column_list_array = parse_mapping_pairs($column_list);
		if(!empty($column_list_array)):
			$column_fieldname = '';
			foreach($column_list_array as $key => $value) {
				$column_field_to_display[$value] = explode(",", $key);
				$column_fieldname .= $key .",";
			}
		endif;
		$values_mapping_array = parse_mapping_pairs($values_mapping);

		$link_to_detail_columns = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_link_to_detail_column_localization', true) : get_post_meta($post->ID, '_attributes_link_to_detail_column', true);
		$link_to_detail_columns_array = explode(",",$link_to_detail_columns);

		$group_data_by_column_index = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID,'_attributes_group_data_by_column_index_localization', true) : get_post_meta($post->ID,'_attributes_group_data_by_column_index', true);

		$filters_list_by_type  = get_post_meta($post->ID, '_attributes_filters_list', true);
		$filters_list_by_type_array = parse_mapping_pairs($filters_list_by_type);

		$additional_filters_by = get_post_meta($post->ID, '_attributes_additional_filters_by', true);
		$country_filter_enabled = get_post_meta($post->ID, '_attributes_country_filter_enabled', true) == "true" ? true : false;
		$language_filter_enabled = get_post_meta($post->ID, '_attributes_language_filter_enabled', true) == "true" ? true : false;
		$taxonomy_filter_enabled = get_post_meta($post->ID, '_attributes_taxonomy_filter_enabled', true) == "true" ? true : false;

		//create filter from resource id of ckan, selected fieldnames, or selected fieldnames as group.
		$additional_filters_option = get_post_meta($post->ID, '_additional_filters_list', true);
		if($additional_filters_option =="filters-list-from-resource-id"){
			$filters_datatables_list = get_post_meta($post->ID, '_attributes_filters_datatables_list', true);
			$filters_datatables_list_array = parse_mapping_pairs($filters_datatables_list);
		}elseif($additional_filters_option =="filters-list-from-selected-fieldnames"){
			$filters_from_selected_fieldnames = get_post_meta($post->ID, '_attributes_custom_filter_fieldname', true);
			if (isset($filters_from_selected_fieldnames)){
				$filters_from_selected_fieldnames_arr = explode(",", trim($filters_from_selected_fieldnames));
			}
			$value_filters_from_selected_fieldnames = get_post_meta($post->ID, '_attributes_custom_filters_list', true);
		}elseif($additional_filters_option =="filters-list-from-selected-fieldnames-as-group"){
			$group_filter_label = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_group_filter_label_localization', true) : 	get_post_meta($post->ID, '_attributes_group_filter_label', true);

			$sub_group_filter_label = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_sub_group_filter_label_localization', true) : get_post_meta($post->ID, '_attributes_sub_group_filter_label', true);

			$group_filter_list = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_filters_group_list_localization', true) : get_post_meta($post->ID, '_attributes_filters_group_list', true);
			$group_filter_list_array = parse_mapping_pairs($group_filter_list);
		}
		///AAA $group_filter_enabled = get_post_meta($post->ID, '_attributes_group_filter_enabled', true) == "true" ? true : false;

		$filtered_by_column_index = get_post_meta($post->ID, '_filtered_by_column_index', true);
		if($filtered_by_column_index):
			$filtered_by_column_index_array = explode(',', $filtered_by_column_index);
		endif;

		$num_filters=0;
		if ($country_filter_enabled): $num_filters++; endif;
		if ($language_filter_enabled): $num_filters++; endif;
		if ($taxonomy_filter_enabled): $num_filters++; endif;

		if (isset($filters_datatables_list)): $num_filters = count($filters_datatables_list_array) + count($filters_list_by_type_array) + 1; endif;
		if (isset($filters_from_selected_fieldnames)): $num_filters = count($value_filters_from_selected_fieldnames) + count($filters_list_by_type_array) + 1; endif;
		if (isset($group_filter_list)): $num_filters = count($group_filter_list_array) + count($filters_list_by_type_array) + 1; endif;
		if (($dataset_type) && $dataset_type == 'all'): $num_filters++; endif;
		if(isset($filtered_by_column_index_array)):	$num_filters += count($filtered_by_column_index_array);	endif;
		$filters_specified = $num_filters > 1;

		//Caculate Column Number Class
		$max_columns = 12;
		$num_filters = ($num_filters > 4) ? round($num_filters/2) : $num_filters;
		$num_columns_int = 12;
		if ($filters_specified && odm_screen_manager()->is_desktop()):
			$num_columns_int = round($max_columns / $num_filters);
		endif;
		$num_columns = integer_to_text($num_columns_int);

		$group_filter_select_name = "group_type";
		$sub_group_filter_select_name = strtolower(str_replace(" ", "_", $sub_group_filter_label));
		$param_country = odm_country_manager()->get_current_country() == 'mekong' && isset($_GET['country']) ? $_GET['country'] : odm_country_manager()->get_current_country();
		$param_query = !empty($_GET['query']) ? $_GET['query'] : null;
		$param_content_type = !empty($_GET[$group_filter_select_name]) ? $_GET[$group_filter_select_name] : null;
		$param_document_types = !empty($_GET[$sub_group_filter_select_name]) ? $_GET[$sub_group_filter_select_name] : null; //'document_types'
		$param_taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : null;
		$param_content = isset($_GET['content']) ? $_GET['content'] : null;
		$param_language = isset($_GET['language']) ? $_GET['language'] : null;
		$active_filters = !empty($param_query) || !empty($param_taxonomy) || !empty($param_language) || !empty($param_query);
		$countries = odm_country_manager()->get_country_codes();
		$datasets = array();
		$filter_fields = array();

		//By default: the content types were defined by default
		if ($dataset_type && $dataset_type !== 'all'){
			$dataset_filter_type = $dataset_type[0];
			if(count($dataset_type) > 1):
				$dataset_filter_type = "(\"" . implode("\" OR \"", $dataset_type). "\")";
			endif;
			$attrs['type'] = $dataset_filter_type;
		}else{
			$attrs = array(
				'type' => '(dataset OR library_record OR laws_record)'
			);
		}

		if($additional_filters_option == "filters-list-from-selected-fieldnames"):
			if(isset($filters_from_selected_fieldnames_arr) && !empty($filters_from_selected_fieldnames_arr)):
				foreach ($filters_from_selected_fieldnames_arr as $custom_fieldname):
					if (strpos($custom_fieldname, $content_type_name) !== false):
						$group_filter_fields_fieldname[$content_type_name] = $custom_fieldname;
						break;
					endif;
				endforeach;
				$param_custom_fieldname = isset($_GET['document_type']) ? $_GET['document_type'] : null;
			endif;
		elseif($additional_filters_option == "filters-list-from-selected-fieldnames-as-group"):
				if(isset($group_filter_list) && !empty($group_filter_list)):
					$group_filter_array = [];
					$group_filter_fields_value = "";
					foreach ($group_filter_list_array as $content_type_name => $group_filter):
						if (strpos($group_filter, '{') !== FALSE):
								$group_filter_explode_1 = explode("{", trim($group_filter));
								$group_filter_explode_2 = explode("[", trim($group_filter_explode_1[1]));
								$group_filter_explode_3 = str_replace("]}", "", str_replace(" ", "", $group_filter_explode_2[1]));
								$group_filter_array[$content_type_name]['label'] = $group_filter_explode_1[0];
								$group_filter_array[$content_type_name]['metafield'] = $group_filter_explode_2[0];
								$group_filter_array[$content_type_name]['value'] = explode(",",  $group_filter_explode_3);
								$group_filter_fields_fieldname[$content_type_name] = explode(",",  $group_filter_explode_3);
								//$group_filter_fields_value .= $group_filter_explode_3;
						endif;
					endforeach;
					//list all attribute in document_type of laws_record and agreement into one array
					//$all_filter_metafield_attritube = explode(",",  $group_filter_fields_value);

					//Content_Type is selted: eg. group_type = laws_record
					if (isset($param_content_type) && $param_content_type !== 'all'):
						$attrs['type'] = $param_content_type;

						// Docuemnt_Type = all
						if (isset($param_document_types)	&& $param_document_types == "all") {
								$extras_custom_fieldvalue = "(\"" . implode("\" OR \"", $group_filter_array[$param_content_type]['value']). "\")";
								array_push($filter_fields,'"extras_' . $group_filter_array[$param_content_type]['metafield'] . '":'.json_encode($extras_custom_fieldvalue));
						}else{
								array_push($filter_fields,'"extras_' . $group_filter_array[$param_content_type]['metafield'] . '":"'.$param_document_types.'"');
						}
					else://Content Type =all  and  Docuemnt_Type != all
						if (isset($param_document_types)	&& $param_document_types != "all") {
								foreach($group_filter_array as $content_type => $filter_value):
									if(in_array($param_document_types, $filter_value['value'])) {
											array_push($filter_fields,'"extras_' . $group_filter_array[$content_type]['metafield'] . '":"'.$param_document_types.'"');
											break;
									}
								endforeach;
						}
					endif;
				endif;
		elseif($additional_filters_option == "filters-list-from-resource-id"):
			if(isset($filters_datatables_list_array)):
				foreach ($filters_datatables_list_array as $key => $resource_id):
					$selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
					if (isset($selected_param)	&& $selected_param !== "all") {
						array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
					}
				endforeach;
			endif;
		endif;

		if(isset($filters_list_by_type_array)):
			foreach ($filters_list_by_type_array as $key => $type):
				$selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
				if (isset($selected_param)	&& $selected_param !== "all") {
					array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
				}
			endforeach;
		endif;

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
			if (!empty($param_language)	&& $param_language !== "all") {
				array_push($filter_fields,'"extras_odm_language":"'.$param_language.'"');
			}
		endif;


		$attrs['filter_fields'] = '{' . implode($filter_fields,",") . '}';
	?>

	<section class="container">
		<header class="row">
			<div class="thirteen columns">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="three columns">
				<?php odm_get_template('social-share',array(),true); ?>
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


	$template = get_post_meta($post->ID, '_attributes_template_layout', true);
	if ($template == 'odc-laws-template'):
		include 'odc-laws-template.php';
	else:
		include 'default-laws-template.php';
	endif;
	?>

<?php endif; ?>

<?php get_footer(); ?>

<?php
if($additional_filters_option =="filters-list-from-selected-fieldnames-as-group"):
	if(!isset($_GET[$group_filter_select_name])):
		$group_data_by_column_index = count($column_field_to_display) +2;
		$order_data_by_column_index = $group_data_by_column_index;
	else:
		//Group by document_type
		$group_data_by_column_index = $group_data_by_column_index;
		//order by index column to sort law by hierarchy
		$order_data_by_column_index = count($column_field_to_display);
	endif;
endif;
 ?>
<script type="text/javascript">
jQuery(document).ready(function($) {

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
		dom: 'B<"top"<"length"l>>rt<"info"i><"pagination"p>',
		buttons: [
			{
				extend: 'csv',
        text: '<i class="fa fa-share"></i>',
				exportOptions: {
          columns: ':visible',
          rows: { page: 'current' }
        }
			},
			{
				extend: 'print',
        text: '<i class="fa fa-print"></i>',
				exportOptions: {
          columns: ':visible',
          rows: { page: 'current' }
        }
			}
		],
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		order: [[ <?php echo isset($order_data_by_column_index)?	$order_data_by_column_index : (isset($group_data_by_column_index) && !empty($group_data_by_column_index)? ($group_data_by_column_index-1) : 0) ?>, 'asc' ]],
		displayLength: 100,
		<?php if (odm_language_manager()->get_current_language() == 'km'): ?>
		"oLanguage": {
				"sLengthMenu": 'បង្ហាញចំនួន <select>'+
						'<option value="10">10</option>'+
						'<option value="25">25</option>'+
						'<option value="50">50</option>'+
						'<option value="100">100</option>'+
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
				api.column(<?php echo ($group_data_by_column_index-1) ?>, {page:'current'} ).data().each( function ( group, g ) {
					console.log(g);	console.log(group);
						if ( last !== group ) {
								$(rows).eq( g ).before(
										'<tr class="group"><td colspan="<?php echo count($column_list_array)+1; ?>">'+group+'</td></tr>'
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
	<?php
	if(isset($filtered_by_column_index_array) && !empty($filtered_by_column_index_array)):
		?>
		function create_filter_by_column_index(col_index,col_name){

		var columnIndex = col_index;
		var column_filter_oTable = oTable.api().columns( columnIndex );
		var column_headercolumnIndex = columnIndex;
		var column_header = $("#datasets_table").find("th:eq( "+column_headercolumnIndex+" )" ).text();

		var div_filter = $('<div class="filter_by filter_by_column_index_'+columnIndex+'"></div>');
		div_filter.appendTo( $('#filters'));

		var select = $('<div class="<?php echo $num_columns?> columns"><div class="adv-nav-input"><p class="label"><label>'+ column_header +'</label></p><select id="' + col_name + '" name="' + col_name + '"><option value=""><?php _e('All', 'wp-odm_tabular_pages'); ?></option></select></div></div>');

		var i = 1;
		column_filter_oTable.data().eq( 0 ).unique().sort().each(function ( d, j ) {
				d = d.replace(/[<]br[^>]*[>]/gi,"");
				var value = d.split('<');
				if (value.length > 1){
					var first_value = value[1].split('>');
					var only_value = first_value[1].split('<');
					value = first_value[1].trim();
				}
				select.find('select').append( '<option value="'+value+'">'+value+'</option>' )
			}
		);
		select.insertBefore("#search-button");
	}

		<?php
		foreach ($filtered_by_column_index_array as $column_id):
			$col_names = array_keys($column_list_array);
			$col_name = $col_names[$column_id];
		?>

		<?php if (!empty($column_id) && !empty($col_name)): ?>
			create_filter_by_column_index(<?php echo $column_id;?>,'<?php echo $col_name; ?>');
		<?php endif; ?>

		<?php
		endforeach;
	endif;
	 ?>


	$('.odm_spatial_range-specific').each(function(){
		var country = [$(this).data('current_country')];
		$(this).find('option').each(function() {
			var countryCodes = $(this).data('country_codes');
			if (countryCodes){
				var countryCodesArray = countryCodes.split(",");
				var intersection = $(countryCodesArray).filter(country);
				if (intersection.length===0){
					$(this).remove();
				}
			}
		});
	});

	$('select').select2();
	$('.datepicker').datepicker();
});

</script>
