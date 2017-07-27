<!-- Datatype Filter -->
<?php
	if (isset($dataset_type) && $dataset_type == 'all'):
?>
<div class="<?php echo $num_columns?> columns">
	<div class="adv-nav-input">
		<p class="label"><label for="type"><?php _e('Type', 'wp-odm_tabular_pages'); ?></label></p>
		<select id="type" name="type" data-placeholder="<?php _e('Select type', 'wp-odm_tabular_pages'); ?>">
			<option value="all" <?php if ($param_type == "all"): echo "selected"; endif; ?>>All</option>
			<option value="dataset" <?php if ($param_type == "dataset"): echo "selected"; endif; ?>><?php _e('Dataset', 'wp-odm_tabular_pages'); ?></option>
			<option value="library_record" <?php if ($param_type == "library_record"): echo "selected"; endif; ?>><?php _e('Publication', 'wp-odm_tabular_pages'); ?></option>
			<option value="laws_record" <?php if ($param_type == "laws_record"): echo "selected"; endif; ?>><?php _e('Laws record', 'wp-odm_tabular_pages'); ?></option>
		</select>
	</div>
</div>
<?php
	endif; ?>

<!-- Country Filter -->
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

<!-- Language Filter -->
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

<!-- Taxonomy Filter -->
<?php
	$taxonomy_list = odm_taxonomy_manager()->get_taxonomy_list();
	if ($taxonomy_filter_enabled):
?>
<div class="<?php echo $num_columns?> columns">
	<div class="adv-nav-input">
		<p class="label"><label for="taxonomy"><?php _e('Topic', 'wp-odm_tabular_pages'); ?></label></p>
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

<!-- Group Filter -->
<?php
	if($group_filter_enabled && $custom_filter_fieldname):
		if($group_filter_label && $group_filter_list):
			$group_filter_select_name = "group_type";
			$group_label = $group_filter_label;
			$selected_param = !empty($_GET[$group_filter_select_name]) ? $_GET[$group_filter_select_name] : null;
			$selected_param_array = explode(",",$selected_param);
			?>
			<div class="<?php echo $num_columns?> columns">
				<div class="adv-nav-input">
					<p class="label"><label for="group_<?php echo $group_filter_select_name; ?>"><?php _e($group_label, 'wp-odm_tabular_pages'); ?></label></p>
					<select id="<?php echo $group_filter_select_name; ?>" name="<?php echo $group_filter_select_name; ?>" class="odm_spatial_range-specific" data-current_country="<?php echo odm_country_manager()->get_current_country_code() ?>">
						<option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
						<?php
						if(isset($group_filter_list) && !empty($group_filter_list)):
							foreach ($group_filter_list_array as $group_name => $group_filter):?>
										<option value="<?php echo $group_name; ?>" data-country_codes="<?php echo odm_country_manager()->get_current_country_code() ?>"<?php if(in_array($group_name, $selected_param_array)) echo 'selected'; ?>><?php _e($group_filter_fields_label[$group_name],'wp-odm_tabular_pages'); ?></option>
								<?php
							endforeach;
						?>
						</select>
						<?php
						if (isset($_GET[$group_filter_select_name])	&& $_GET[$group_filter_select_name]!= "all"):
								if (!isset($_GET[$custom_filter_fieldname_arr[0]])	|| $_GET[$custom_filter_fieldname_arr[0]]== "all"):
									$filter_fields_obj = json_decode($attrs['filter_fields']);
									$extras_custom_fieldname = "extras_".$group_filter_fields_fieldname['laws_record'];
									foreach ($group_filter_fields_fieldname as $group_name => $filter_fields_fieldname):
										if ($selected_param == $group_name):
											$extras_custom_fieldname = "extras_".$filter_fields_fieldname;
											break;
										endif;
									endforeach;

									$filter_fields_obj->$extras_custom_fieldname = "(\"" . implode("\" OR \"", $group_filter_fields_attr[$selected_param]). "\")";
									$attrs['filter_fields'] = json_encode($filter_fields_obj);
								endif;
							endif;
						endif;
						?>
				</div>
			</div>
			<?php
		endif;
	endif;
?>

<!-- Custom Filter -->
<?php
	if($custom_filter_fieldname && isset($custom_filter_fieldname_arr)):
		if($custom_filter_list):
			$custom_filter_array = explode("\r\n", $custom_filter_list);
			$mapped_key = in_array($custom_filter_fieldname_arr[0], array_keys($values_mapping_array)) ?	$values_mapping_array[$custom_filter_fieldname_arr[0]] : $custom_filter_fieldname_arr[0];

			$selected_param = !empty($_GET[$custom_filter_fieldname_arr[0]]) ? $_GET[$custom_filter_fieldname_arr[0]] : null;
			$selected_param_array = explode(",",$selected_param);
			?>
			<div class="<?php echo $num_columns?> columns">
				<div class="adv-nav-input">
					<p class="label"><label for="<?php echo $custom_filter_fieldname_arr[0]; ?>"><?php _e($mapped_key, 'wp-odm_tabular_pages'); ?></label></p>
					<select id="<?php echo $custom_filter_fieldname_arr[0]; ?>" name="<?php echo $custom_filter_fieldname_arr[0]; ?>" class="odm_spatial_range-specific" data-current_country="<?php echo odm_country_manager()->get_current_country_code() ?>">
						<option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
						<?php
					    foreach ($custom_filter_array as $option):
								$option = trim($option);
								$mapped_option = in_array($option ,array_keys($values_mapping_array)) ?	$values_mapping_array[$option] : $option;
								if(isset($group_filter_fields_attr) && !empty($group_filter_fields_attr)):
										foreach($group_filter_fields_attr as $group => $group_value ):
											$in_group = null;
											if(in_array($option, $group_value)):
												$in_group = $group;
												break;
											endif;
										endforeach;
								endif;
								?>
								<option value="<?php echo $option; ?>" in-group="<?php echo $in_group; ?>" data-country_codes="<?php echo odm_country_manager()->get_current_country_code() ?>" <?php if(in_array($option, $selected_param_array)) echo 'selected'; ?>><?php _e($mapped_option,'wp-odm_tabular_pages'); ?></option>
								<?php
							endforeach; ?>
					</select>
				</div>
			</div>
			<?php
	endif;
endif;
?>

<!-- Filter List Array -->
<?php
	foreach ($filters_list_array as $key => $type):
		$mapped_key = in_array($key,array_keys($values_mapping_array)) ?	$values_mapping_array[$key] : $key;
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

<!-- Filters Datatables List Array -->
<?php
if($filters_datatables_list_array):
	foreach ($filters_datatables_list_array as $key => $resource_id):
		$mapped_key = in_array($key,array_keys($values_mapping_array)) ?	$values_mapping_array[$key] : $key;
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
						foreach($options as $option):
							if(isset($group_filter_fields) && !empty($group_filter_fields)):
									foreach($group_filter_fields as $group => $group_value ):
										$in_group = null;
										if(in_array( trim($option['id']), $group_value)):
											$in_group = $group;
											break;
										endif;
									endforeach;
							endif;
							?>
						<option
							value="<?php echo $option['id']; ?>" in-group="<?php echo $in_group; ?>"
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
endif;
?>

<!-- Search Button -->
<?php
	$num_columns_button = $filters_specified ? integer_to_text($max_columns - (round($max_columns / $num_filters) * ($num_filters -1))) : "four";
	?>

	<div id="search-button" class="<?php echo $num_columns_button ?> columns">
		<input class="button" type="submit" value="<?php _e('Search', 'wp-odm_tabular_pages'); ?>"/>
		<?php
			if ($active_filters):
				?>
				<a href="?clear"><?php _e('Clear','wp-odm_tabular_pages') ?></a>
				<?php
			endif;
		 ?>
	</div>
