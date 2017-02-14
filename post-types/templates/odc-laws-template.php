<div class="container">
	<div class="row">
		<form class="advanced-nav-filters">
			<div id="filters" class="sixteen columns panel">

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
							<option value="dataset" <?php if ($param_type == "dataset"): echo "selected"; endif; ?>><?php _e('Dataset', 'wp-odm_tabular_pages'); ?></option>
							<option value="library_record" <?php if ($param_type == "library_record"): echo "selected"; endif; ?>><?php _e('Publication', 'wp-odm_tabular_pages'); ?></option>
							<option value="laws_record" <?php if ($param_type == "laws_record"): echo "selected"; endif; ?>><?php _e('Laws record', 'wp-odm_tabular_pages'); ?></option>
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

				<div class="<?php echo $num_columns?> columns">
					<div class="adv-nav-input">
						<p class="label"><label for="content-type"><?php _e('Content Type', 'wp-odm_tabular_pages'); ?></label></p>
						<select id="content-type" name="content-type" data-placeholder="<?php _e('Select', 'wp-odm_tabular_pages'); ?>">
							<option value="all" selected><?php _e('All','wp-odm_tabular_pages') ?></option>
							<option value="laws" <?php if("laws" == $param_content) echo 'selected'; ?>><?php _e("Laws", "odm_tabular_pages"); ?></option>
							<option value="policies" <?php if("policies" == $param_content) echo 'selected'; ?>><?php _e("Policies", "odm_tabular_pages"); ?></option>
							<option value="agreements" <?php if("agreement" == $param_content) echo 'selected'; ?>><?php _e("Agreements", "odm_tabular_pages"); ?></option>
							<?php
								/*foreach($taxonomy_list as $value):
									$val = apply_filters('translate_term', $value, odm_language_manager()->get_current_language());
								?>
								<option value="<?php echo $value; ?>" <?php if($value == $param_content) echo 'selected'; ?>><?php echo $val; ?></option>
							<?php
						endforeach; */?>
						</select>
					</div>
				</div>

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

				<?php
				if($filters_datatables_list_array):
					foreach ($filters_datatables_list_array as $key => $resource_id):
						$mapped_key = in_array($key,array_keys($values_mapping_array)) ?	$values_mapping_array[$key] : $key;
						$options = wpckan_get_datastore_resource(wpckan_get_ckan_domain(),$resource_id);

							echo "<pre>";
							//print_r($options);
							echo "</pre>";
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
				endif;

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

			</div>

		</form>

	</div>
</div>

<section class="container">
	<div class="row">
		<div class="sixteen columns">
			<?php echo the_content();?>
		</div>
	</div>
</section>

<section class="container">
	<div class="row">
		<div class="sixteen columns">
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
									$translated_dataset = isset($dataset[str_replace("_translated","",$key)])? $dataset[str_replace("_translated","",$key)] : null;
									$metadata_key = isset($dataset[$key]) ? $dataset[$key] : $translated_dataset;
									echo "<td>";
									if (isset($metadata_key)):
										$single_value = getMultilingualValueOrFallback($metadata_key, odm_language_manager()->get_current_language(),$metadata_key);
										if (is_array($single_value) && isset($single_value["en"])):
											$single_value = $single_value["en"];
										endif;
										$mapped_value = in_array($single_value,array_keys($values_mapping_array)) ?	$values_mapping_array[$single_value] : $single_value;
										if (strlen($mapped_value) > 300):
											$mapped_value = substr($mapped_value, 0, 300) . ' ...';
										endif;
										if (in_array($key,$link_to_detail_columns_array)): ?>
											<a target="_blank" href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo __($mapped_value, 'wp-odm_tabular_pages');?></a>
										<?php
										else:
											echo $mapped_value == '' || empty($mapped_value) ? __('Unknown', 'wp-odm_tabular_pages') : __($mapped_value, 'wp-odm_tabular_pages');
										endif;
									endif;
									echo "</td>";
								endforeach;
						 ?>
							<td class="download_buttons">
								<?php if (isset($dataset['resources'])) :?>
									<?php foreach ($dataset['resources'] as $resource) :?>
										<?php if (isset($resource['format']) && ($resource['format'] =="PDF" )): ?>
											<?php if (isset($resource['odm_language']) && !empty($resource['odm_language'])): ?>
												<span>
													<?php
														if (is_array($resource['odm_language'])):
															foreach ($resource['odm_language'] as $language) :?>
																<a target="_blank" href="<?php echo $resource['url'];?>"><?php
																echo '<img alt="'.$language.'" src="'.qtranxf_flag_location().$q_config['flag'][$language].'"></img>';  ?> &nbsp;</a>
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
