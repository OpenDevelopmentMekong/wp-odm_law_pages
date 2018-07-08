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
						if($column_field_to_display):
							foreach ($column_field_to_display as $key => $value): ?>
							<th><?php _e($key, 'wp-odm_tabular_pages'); ?></th>
							<?php
							endforeach;
						endif;
						if($group_data_by_column_index!=''):
							echo "<th class='hide'>Index</th>";
						endif;
						if($additional_filters_option =="filters-list-from-selected-fieldnames-as-group"):
							echo "<th class='hide content_type'>".$group_filter_label."</th>";
						endif;
						?>
					 <th><?php _e('Download', 'wp-odm_tabular_pages');?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$datasets = wpckan_api_package_search(wpckan_get_ckan_domain(), $attrs);
					if (in_array('results',array_keys($datasets))):
						foreach ($datasets['results'] as $dataset):
							$content_type_of_dataset = $dataset['type'];
							if(!in_array($dataset[$group_filter_array[$content_type_of_dataset]['metafield']], $group_filter_array[$content_type_of_dataset]['value'])):
								continue;
							endif;
							?>
							<tr>
								<?php
								if($column_field_to_display):
									foreach ($column_field_to_display as $column_name => $item): ///foreach ($get_all_filter_fields as $field_name):
										foreach ($item as $key => $field_name):
											$column_value = null;
											if(isset($dataset[$field_name][odm_language_manager()->get_current_language()])):
												$column_value = $dataset[$field_name][odm_language_manager()->get_current_language()];
											elseif(isset($dataset[$field_name])):
												$column_value = $dataset[$field_name];
											endif;

											if(isset($column_value)):
												$mapped_value = in_array($column_value, array_keys($values_mapping_array)) ?	$values_mapping_array[$column_value] : $column_value;
												echo "<td>";
													if (in_array($field_name, $link_to_detail_columns_array)){ ?>
														<a target="_blank" href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo __($mapped_value, 'wp-odm_tabular_pages');?></a>
													<?php
													}else{
														echo $mapped_value == '' || empty($mapped_value) ? __('Not found', 'wp-odm_tabular_pages') : __($mapped_value, 'wp-odm_tabular_pages');
													}
												echo "</td>";
											endif;
										endforeach;
									endforeach;
								endif;

								//Show index of element
								if($group_data_by_column_index):
									$index_attr_name = null;
									if(in_array($dataset[$group_filter_array[$content_type_of_dataset]['metafield']], $group_filter_array[$content_type_of_dataset]['value'])):
										$index_attr_name = array_search($dataset[$group_filter_array[$content_type_of_dataset]['metafield']], $group_filter_array[$content_type_of_dataset]['value']); //get index item
									endif;
									echo "<td class='hide'>" .$index_attr_name."</td>";
								endif;
								//Show content type
								if($additional_filters_option =="filters-list-from-selected-fieldnames-as-group"):
									echo "<td class='hide content_type'>".$group_filter_array[$content_type_of_dataset]['label']."</td>";
								endif;
								?>
								<td class="download_buttons">
	 								<?php
									if (isset($dataset['resources'])) : //show the first one, then break; ?>
	 									<?php foreach ($dataset['resources'] as $resource) :?>
	 										<?php if (isset($resource['format']) && ($resource['format'] =="PDF" )): ?>
	 											<?php if (isset($resource['odm_language']) && !empty($resource['odm_language'])): ?>
	 												<span>
	 													<?php
	 														if (is_array($resource['odm_language'])):
	 															foreach ($resource['odm_language'] as $language) :?>
	 																<a target="_blank" href="<?php echo $resource['url'];?>"><?php
	 																echo '<img alt="'.$language.'" src="'.WP_PLUGIN_URL.'/wp-odm_tabular_pages/img/'.$language.'.png" />';  ?> &nbsp;</a>
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
											<?php break; ?>
	 									<?php endforeach; ?>
	 								<?php endif;  ?>
	 							</td>
							</tr>
							<?php
						endforeach;
					endif;
					?>
				</tbody>
			</table>
		</div>
	</div>
</section>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php if(($additional_filters_option == "filters-list-from-selected-fieldnames-as-group")):?>
						var group_filter_name = "<?php echo $group_filter_select_name ?>";
					  var filter_fieldname = "<?php echo $sub_group_filter_select_name ?>";
					  var selected_document_type = "<?php echo isset($_GET[$sub_group_filter_select_name])? $_GET[$sub_group_filter_select_name] : 'all'; ?>";

						function group_filter(item){
							var current_group;
							if( typeof(item) != 'undefined'){
								if ($(item).data('options') == undefined) {
							    $(item).data('options', $('#'+filter_fieldname+' option').clone());
							  }
							  current_group = $(item).val();
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
