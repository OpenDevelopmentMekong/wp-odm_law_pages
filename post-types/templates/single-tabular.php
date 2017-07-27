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

		$additional_filters_by = get_post_meta($post->ID, '_attributes_additional_filters_by', true);
		$filters_datatables_list = get_post_meta($post->ID, '_attributes_filters_datatables_list', true);
		$filters_datatables_list_array = parse_mapping_pairs($filters_datatables_list);

		$country_filter_enabled = get_post_meta($post->ID, '_attributes_country_filter_enabled', true) == "true" ? true : false;
		$language_filter_enabled = get_post_meta($post->ID, '_attributes_language_filter_enabled', true) == "true" ? true : false;
		$taxonomy_filter_enabled = get_post_meta($post->ID, '_attributes_taxonomy_filter_enabled', true) == "true" ? true : false;

		$custom_filter_fieldname = get_post_meta($post->ID, '_attributes_custom_filter_fieldname', true);
		if (isset($custom_filter_fieldname)){
			$custom_filter_fieldname_arr = explode(",", trim($custom_filter_fieldname));
		}
		$custom_filter_list = get_post_meta($post->ID, '_attributes_custom_filters_list', true);
		$group_filter_enabled = get_post_meta($post->ID, '_attributes_group_filter_enabled', true) == "true" ? true : false;
		$group_filter_label = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_group_filter_label_localization', true) : get_post_meta($post->ID, '_attributes_group_filter_label', true);
		$group_filter_list = (odm_language_manager()->get_current_language() != "en") ? get_post_meta($post->ID, '_attributes_filters_group_list_localization', true) : get_post_meta($post->ID, '_attributes_filters_group_list', true);
		$group_filter_list_array = parse_mapping_pairs($group_filter_list);

		$filtered_by_column_index = get_post_meta($post->ID, '_filtered_by_column_index', true);
		if($filtered_by_column_index):
			$filtered_by_column_index_array = explode(',', $filtered_by_column_index);
		endif;
		$num_filters = count($filters_datatables_list_array) + count($filters_list_array) + 1;
		if ($country_filter_enabled): $num_filters++; endif;
		if ($language_filter_enabled): $num_filters++; endif;
		if ($taxonomy_filter_enabled): $num_filters++; endif;
		if ($custom_filter_fieldname && $custom_filter_list): $num_filters++; endif;
		if ($group_filter_enabled && $group_filter_label && $group_filter_list): $num_filters++; endif;
		if (isset($dataset_type) && $dataset_type == 'all'): $num_filters++; endif;
		if(isset($filtered_by_column_index_array)):
			$num_filters += count($filtered_by_column_index_array);
		endif;
		$filters_specified = $num_filters > 1;

		//Caculate Column Number Class
		$max_columns = 12;
		$num_filters = ($num_filters > 4) ? round($num_filters/2) : $num_filters;
		$num_columns_int = 12;
		if ($filters_specified && odm_screen_manager()->is_desktop()):
			$num_columns_int = round($max_columns / $num_filters);
		endif;
		$num_columns = integer_to_text($num_columns_int);

		$param_country = odm_country_manager()->get_current_country() == 'mekong' && isset($_GET['country']) ? $_GET['country'] : odm_country_manager()->get_current_country();
		$param_query = !empty($_GET['query']) ? $_GET['query'] : null;
		$param_type = !empty($_GET['group_type']) ? $_GET['group_type'] : null;
		$param_taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : null;
		$param_content = isset($_GET['content']) ? $_GET['content'] : null;
		$param_language = isset($_GET['language']) ? $_GET['language'] : null;
		$param_custom_fieldname = isset($_GET[$custom_filter_fieldname_arr[0]]) ? $_GET[$custom_filter_fieldname_arr[0]] : null;

		$active_filters = !empty($param_query) || !empty($param_taxonomy) || !empty($param_language) || !empty($param_query);

		$countries = odm_country_manager()->get_country_codes();

		$datasets = array();
		$filter_fields = array();
		$attrs = array(
			'type' => '(dataset OR library_record OR laws_record)'
		);

		if (isset($dataset_type) && $dataset_type !== 'all'){
			$dataset_filter_type = $dataset_type[0];
			if(count($dataset_type) > 1):
				$dataset_filter_type = "(\"" . implode("\" OR \"", $dataset_type). "\")";
			endif;
			$attrs['type'] = $dataset_filter_type;
		}
		
		if(isset($group_filter_list) && !empty($group_filter_list)):
			foreach ($group_filter_list_array as $group_name => $group_filter):
				if (strpos($group_filter, '[') !== FALSE):
						$group_filter_info= explode("[", str_replace(" ", "", $group_filter));
						$group_label = trim($group_filter_info[0]);
						$group_value = str_replace("]", "", $group_filter_info[1]);
						$group_filter_fields_label[$group_name] = $group_label;
						$group_filter_fields_attr[$group_name] = explode(",",  $group_value);
				endif;

				if(isset($custom_filter_fieldname_arr) && !empty($custom_filter_fieldname_arr)):
					foreach ($custom_filter_fieldname_arr as $custom_fieldname):
						if (strpos($custom_fieldname, $group_name) !== false):
							$group_filter_fields_fieldname[$group_name] = $custom_fieldname;
							break;
						endif;
					endforeach;
				endif;
			endforeach;
		endif;
		$group_filter_fields_fieldname['laws_record'] = $custom_filter_fieldname_arr[0];

		if (isset($param_type) && $param_type !== 'all'){
			$attrs['type'] = $param_type;
		}
		if (!empty($param_country) && $param_country != 'mekong' && $param_country !== "all") {
			array_push($filter_fields,'"extras_odm_spatial_range":"'. $countries[$param_country]['iso2'] .'"');
		}

		if (!empty($param_custom_fieldname)	&& $param_custom_fieldname !== "all") {
			$extras_custom_fieldname = "extras_".$group_filter_fields_fieldname['laws_record'];
			foreach ($group_filter_fields_attr as $group_name => $group_value):
				if (in_array($param_custom_fieldname, $group_value)):
					$extras_custom_fieldname = "extras_".$group_filter_fields_fieldname[$group_name];
					$attrs['type'] = $group_name;
					break;
				endif;
			endforeach;
			array_push($filter_fields, '"'.$extras_custom_fieldname.'":"'.$param_custom_fieldname.'"');
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

		foreach ($filters_list_array as $key => $type):
			$selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
			if (isset($selected_param)	&& $selected_param !== "all") {
				array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
			}
		endforeach;

		foreach ($filters_datatables_list_array as $key => $resource_id):
			$selected_param = !empty($_GET[$key]) ? $_GET[$key] : null;
			if (isset($selected_param)	&& $selected_param !== "all") {
				array_push($filter_fields,'"extras_' . $key . '":"'.$selected_param.'"');
			}
		endforeach;

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
		order: [[ <?php echo isset($order_data_by_column_index) && !empty($order_data_by_column_index) ?	$order_data_by_column_index :( isset($group_data_by_column_index) && !empty($group_data_by_column_index)? $group_data_by_column_index : 0) ?>, 'asc' ]],
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
				api.column(<?php echo $group_data_by_column_index ?>, {page:'current'} ).data().each( function ( group, i ) {
						if ( last !== group ) {
								$(rows).eq( i ).before(
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
	if(isset($filtered_by_column_index_array) && !empty($filtered_by_column_index_array)):
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
