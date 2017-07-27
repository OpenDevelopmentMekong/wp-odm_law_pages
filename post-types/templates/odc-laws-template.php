<div class="container">
	<div class="row">
		<!-- Advanced Filter -->
		<form class="advanced-nav-filters">

			<!-- Filter Desktop View -->
			<?php if (odm_screen_manager()->is_desktop()): ?>

			<div class="row hideOnMobileAndTablet">

				<div id="filters" class="sixteen columns panel">

					<!-- Search Text Field -->
					<?php
						$num_columns_text_search = ($filters_specified) ? "four" : "twelve"
						?>
					<div class="<?php echo $num_columns_text_search; ?> columns">
						<div class="adv-nav-input">
							<p class="label"><label for="s"><?php _e('Text search', 'wp-odm_tabular_pages'); ?></label></p>
							<input type="text" id="query" name="query" placeholder="<?php _e('Search for title or other attributes', 'wp-odm_tabular_pages'); ?>" value="<?php echo $param_query; ?>" />
						</div>
					</div>

					<?php include('partials/adv-filters.php'); ?>
					
				</div>

			</div>
			<!-- End of Filter Desktop View -->
			<?php else: ?>
				
				<!-- Filter Mobile View -->

				<!-- Search Bar -->

				<div class="row filter-container">
					<div class="sixteen columns mobile-filter-container">              
						<div class="text_search_with_submit">
							<input type="text" id="query" name="query" placeholder="<?php _e('Search for title or other attributes', 'wp-odm_tabular_pages'); ?>" value="<?php echo $param_query; ?>" />
							<button type="submit"><i class="fa fa-search"></i></button>						
						</div>
              			<a href="#" class="button filter open-mobile-dialog">
							<i class="fa fa-filter fa-lg"></i>
						</a>
					</div>
            		<div class="fixed_datatable_tool_bar"></div>						
				</div>

				<div class="row mobile-dialog hideOnDesktop">
					<div class ="eight columns align-left">
						<h3><?php _e("Filters","wp-odm_tabular_pages"); ?></h3>
					</div>
					<div class ="eight columns">
						<div class="close-mobile-dialog align-right">
							<i class="fa fa-times-circle"></i>
						</div>
					</div>
					<div class ="sixteen columns">

						<?php include('partials/adv-filters.php'); ?>

					</div>
				</div>
				<!-- End of Filter Mobile View -->

			<?php endif; ?>

		</form>
		<!-- End of Advanced Filter -->
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
						$datasets = wpckan_api_package_search(wpckan_get_ckan_domain(),$attrs);
						if (in_array('results',array_keys($datasets))):
							foreach ($datasets['results'] as $dataset): ?>
						<tr>
						<?php
							foreach ($column_list_array as $key => $value):
								$exploded_key = explode(",", $key);
								if(count($exploded_key) > 1):
									foreach ($exploded_key as $single_key):
										if(isset($dataset[$single_key]) ):
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
							endforeach;
						endif;?>
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
	});
</script>
