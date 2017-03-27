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
						<input type="text" id="query" name="query" placeholder="<?php _e('Search for title or other attributes', 'wp-odm_tabular_pages'); ?>" value="<?php echo $param_query; ?>" data-solr-host="<?php echo $GLOBALS['wp_odm_solr_options']->get_option('wp_odm_solr_setting_solr_host'); ?>" data-solr-scheme="<?php echo $GLOBALS['wp_odm_solr_options']->get_option('wp_odm_solr_setting_solr_scheme'); ?>" data-solr-path="<?php echo $GLOBALS['wp_odm_solr_options']->get_option('wp_odm_solr_setting_solr_path'); ?>" data-solr-core-wp="<?php echo $GLOBALS['wp_odm_solr_options']->get_option('wp_odm_solr_setting_solr_core_wp'); ?>" data-solr-core-ckan="<?php echo $GLOBALS['wp_odm_solr_options']->get_option('wp_odm_solr_setting_solr_core_ckan'); ?>"></input>
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
										endif;
										?>
								</div>
							</div>
							<?php
						endif;
					endif;
					?>

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

<?php if(!empty(get_the_content())): ?>
<section class="container">
	<div class="row">
		<div class="sixteen columns">
			<?php echo the_content();?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="container">
	<div class="row">
		<div class="sixteen columns">
			<table id="datasets_table" class="data-table">
				<thead>
					<tr>
						<?php
							foreach ($column_list_array as $key => $value): ?>
								<?php $exploded_key = explode(",", $key);?>
								<th><?php _e($value, 'wp-odm_tabular_pages'); ?></th>
								<?php
								if($custom_filter_fieldname && $custom_filter_list && $group_filter_list):
									if(array_intersect($custom_filter_fieldname_arr, $exploded_key)):
										$additional_columns = 1;
										if($group_filter_enabled):
											$group_th = $group_filter_label;
											$additional_columns++;
										endif;
									endif;
								endif;
							endforeach;
							if(isset($additional_columns)):
								echo "<th class='hide'>Index</th>";
								if(isset($group_th)):
									echo "<th class='hide ".$group_filter_select_name."'>".$group_th."</th>";
								endif;
							endif;
						?>
					 <th><?php _e('Download', 'wp-odm_tabular_pages');?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$control_attrs = null;
						$result = WP_Odm_Solr_CKAN_Manager()->query($param_query,$attrs,$control_attrs);
						$results = $result["resultset"];
						foreach ($results as $result):
							$dataset = $result->getFields();?>
						<tr>
						<?php
							foreach ($column_list_array as $key => $value):
								$exploded_key = explode(",", $key);
								if(count($exploded_key) > 1):
									foreach ($exploded_key as $single_key):
										if(isset($document[$single_key]) ):
											$key = $single_key;
											break;
										endif;
									endforeach;
								endif;
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
										$mapped_value = mb_substr($mapped_value, 0, 300) . ' ...';
									endif;
									if (in_array($key,$link_to_detail_columns_array)): ?>
										<a target="_blank" href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo __($mapped_value, 'wp-odm_tabular_pages');?></a>
									<?php
									else:
										echo $mapped_value == '' || empty($mapped_value) ? __('Unknown', 'wp-odm_tabular_pages') : __($mapped_value, 'wp-odm_tabular_pages');
									endif;
								endif;
								echo "</td>";
								 	if($custom_filter_fieldname && $custom_filter_list && $group_filter_list):
									if(array_intersect($custom_filter_fieldname_arr, $exploded_key)):
										$group_index = array_search($single_value, array_values($custom_filter_array));
										if($group_filter_enabled && isset($group_filter_fields_attr)):
											$in_group = null;
											foreach($group_filter_fields_attr as $group => $group_value ):
												if(in_array(trim($single_value), $group_value)):
													$in_group = $group_filter_fields_label[$group];
													$group_index = array_search($group, array_keys($group_filter_fields_attr));
													if (isset($_GET[$group_filter_select_name])	&& $_GET[$group_filter_select_name]!= "all"):
														$group_index = array_search($single_value, array_values($group_value));
													endif;
													break;
												endif;
											endforeach;
									 	endif;
									 	endif;
								endif;

							endforeach;
							if(isset($additional_columns)):
								echo "<td class='hide'>";
									echo $group_index;
								echo "</td>";

								if($group_filter_enabled):
									echo "<td class='hide'>";
										echo $in_group;
									echo "</td>";
									if (!isset($_GET[$group_filter_select_name])	|| $_GET[$group_filter_select_name]== "all"):
										$group_data_by_column_index = count($column_list_array) +1;
									endif;
								endif;

								$order_data_by_column_index = count($column_list_array);
							endif;
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
 																echo '<img alt="'.$language.'" src="'.WP_PLUGIN_URL.'/wp-odm_tabular_pages/img/'.$language.'.png"></img>';  ?> &nbsp;</a>
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
						endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</section>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php if($group_filter_enabled && $custom_filter_fieldname && $group_filter_list):?>
						var group_filter_name = "<?php echo $group_filter_select_name ?>";
					  var filter_fieldname = "<?php echo $custom_filter_fieldname_arr[0] ?>";
					  var selected_document_type = "<?php echo isset($_GET['odm_document_type'])? $_GET['odm_document_type'] : 'all'; ?>";

						function group_filter(item){
							var current_group;
							if( typeof(item) != 'undefined'){
								if ($(item).data('options') == undefined) {
							    $(item).data('options', $('#'+filter_fieldname+' option').clone());
							  }
							  current_group = $(item).val();
										console.log(filter_fieldname);
								if(current_group == 'all'){
									$('#'+filter_fieldname).html($(item).data('options')).val('all');
								}else{
							  	var options = $(item).data('options').filter('[in-group=' + current_group + ']');
							  	$('#'+filter_fieldname).html(options);
									$('#'+filter_fieldname).prepend("<option value='all'><?php _e('All','wp-odm_tabular_pages') ?></option>").val('all');
								}
							}else{
								if( $("#"+group_filter_name).val() !='all' ){

									if ($("#"+group_filter_name).data('options') == undefined) {
								    $("#"+group_filter_name).data('options', $('#'+filter_fieldname+' option').clone());
								  }
									current_group = $("#"+group_filter_name).val();
								  var group_options = $("#"+group_filter_name).data('options').filter('[in-group=' + current_group + ']');
							  	$('#'+filter_fieldname).html(group_options);
										$('#'+filter_fieldname).prepend("<option value='all'><?php _e('All','wp-odm_tabular_pages') ?></option>").val(selected_document_type);
								}
							}
						}

						group_filter();
						$("#"+group_filter_name).change(function() {
						  group_filter(this);
						});

		<?php endif; ?>


		jQuery('#query').autocomplete({
        source: function( request, response ) {
          var host = jQuery('#search_field').data("solr-host");
          var scheme = jQuery('#search_field').data("solr-scheme");
          var path = jQuery('#search_field').data("solr-path");
          var core_wp = jQuery('#search_field').data("solr-core-wp");
          var core_ckan = jQuery('#search_field').data("solr-core-ckan");
          var url = scheme + "://" + host  + path + core_ckan + "/suggest";
					console.log("pulling suggestions from: " + url);
          jQuery.ajax({
            url: url,
            data: {'wt':'json', 'q':request.term, 'json.wrf': 'callback'},
            dataType: "jsonp",
            jsonpCallback: 'callback',
            contentType: "application/json",
            success: function( data ) {

              var options = [];
              if (data){
                if(data.spellcheck){
                  var spellcheck = data.spellcheck;
                  if (spellcheck.suggestions){
                    var suggestions = spellcheck.suggestions;
                    if (suggestions[1]){
                      var suggestionObject = suggestions[1];
                      options = suggestionObject.suggestion;
                    }
                  }
                }
              }
              response( options );
            }
          });
        },
        minLength: 2,
        select: function( event, ui ) {
          var terms = this.value.split(" ");
          terms.pop();
          terms.push( ui.item.value );
          this.value = terms.join( " " );
          return false;
        }
      });
    });
	});
</script>
