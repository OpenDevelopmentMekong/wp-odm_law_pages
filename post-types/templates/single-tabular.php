<?php get_header(); ?>

<?php	if (have_posts()) : ?>

  <?php
		global $post;
		$dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true);
		$dataset_type_label = get_post_meta($post->ID, '_attributes_dataset_type_label', true);

    $filter_odm_document_type = null;
    if (isset($_GET['odm_document_type'])) {
        $filter_odm_document_type = htmlspecialchars($_GET['odm_document_type']);
    }
    $filter_odm_taxonomy = null;
    if (isset($_GET['odm_taxonomy'])) {
        $filter_odm_taxonomy = htmlspecialchars($_GET['odm_taxonomy']);
    }
    $datasets = array();
		$headline = "";
    if (!empty($filter_odm_taxonomy)) {
        $attrs = array(
          'type' => $dataset_type,
          'filter_fields' => '{"extras_taxonomy":"'.$filter_odm_taxonomy.'"}',
        );
        $datasets = wpckan_api_package_search(wpckan_get_ckan_domain(),$attrs);
				$headline = $filter_odm_taxonomy;
    } elseif (!empty($filter_odm_document_type)) {
        $attrs = array(
          'type' => $dataset_type,
          'filter_fields' => '{"extras_odm_document_type":"'.$filter_odm_document_type.'"}',
        );
        $datasets = wpckan_api_package_search(wpckan_get_ckan_domain(),$attrs);
				$headline = $filter_odm_document_type;
    } else {
        $attrs = array(
          'type' => $dataset_type
        );
        $datasets = wpckan_api_package_search(wpckan_get_ckan_domain(),$attrs);
    }

   ?>
  <section class="container">
		<header class="row">
			<div class="sixteen columns">
        <a href="<?php get_page_link(); ?>"><h1><?php the_title(); ?></h1></a>
        <h2><?php _e($headline, 'wp-odm_tabular_pages'); ?></h2>
			</div>
		</header>
	</section>

  <section class="container">
    <div class="row">
		  <div class="eleven columns">
        <?php the_content(); ?>
        <table id="datasets_table" class="data-table">
          <thead>
            <tr>
              <th><?php _e('Title', 'wp-odm_tabular_pages');?></th>
              <th><?php _e('Document type', 'wp-odm_tabular_pages');?></th>
              <th><?php _e('Document number', 'wp-odm_tabular_pages');?></th>
              <th><?php _e('Promulgation date', 'wp-odm_tabular_pages');?></th>
              <th><?php _e('Download', 'wp-odm_tabular_pages');?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($datasets['results'] as $dataset): ?>
              <?php
              if (empty($dataset['odm_document_type'])):
                continue;
              endif; ?>
              <tr>
                <td class="entry_title">
                  <a href="<?php echo wpckan_get_link_to_dataset($dataset['id']);?>"><?php echo getMultilingualValueOrFallback($dataset['title_translated'], odm_language_manager()->get_current_language(),$dataset['title']);?></a>
                </td>
                <td>
                  <?php
                    if (isset($dataset['odm_document_type'])):
                      $doc_type = $dataset['odm_document_type'];
                      echo _e($doc_type, 'wp-odm_tabular_pages');
                    endif; ?>
                </td>
                <td>
                  <?php
                  if (isset($dataset['odm_document_number'])):
                    echo $dataset['odm_document_number'][odm_language_manager()->get_current_language()];
                  endif; ?>
                </td>
                <td>
                  <?php
                  if (isset($dataset['odm_promulgation_date'])):
                    if (odm_language_manager()->get_current_language() == 'km'):
                        echo convert_date_to_kh_date(date('d.m.Y', strtotime($dataset['odm_promulgation_date'])));
                    else:
                        echo $dataset['odm_promulgation_date'];
                    endif;
                  endif;
                  ?>
                </td>
                <td class="download_buttons">
                    <?php foreach ($dataset['resources'] as $resource) :?>
                      <?php if (isset($resource['odm_language']) && count($resource['odm_language']) > 0 && $resource['odm_language'][0] == 'en'): ?>
                        <span>
                          <a href="<?php echo $resource['url'];?>">
                          <i class="fa fa-download"></i> EN</a></span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                    <?php foreach ($dataset['resources'] as $resource) :?>
                      <?php if (isset($resource['odm_language']) && count($resource['odm_language']) > 0 && $resource['odm_language'][0] == 'km'): ?>
                        <span><a href="<?php echo $resource['url'];?>">
                          <i class="fa fa-download"></i> KM</a></span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                </td>
              </tr>
    				<?php endforeach; ?>
  				</tbody>
  			</table>
		  </div>

      <div class="four columns offset-by-one">
				<aside id="sidebar">
            <ul class="widgets">
							<li class="widget widget_odm_taxonomy_widget">
									<h2 class="widget-title">
										<?php
				            if ($headline): ?>
				                <?php _e('Search', 'wp-odm_tabular_pages'); ?>
				                <?php $dataset_type_label.' '._e('in', 'wp-odm_tabular_pages');?>
				                <?php _e($headline, 'wp-odm_tabular_pages');?>
				            <?php
				            elseif ($dataset_type_label): ?>
				                <?php _e('Search', 'wp-odm_tabular_pages'); the_title();?>
									  <?php
				            endif; ?>
									</h2>
									<div>
										<input type="text" id="search_all" placeholder=<?php _e('Search', 'wp-odm_tabular_pages').' '.$dataset_type; ?>>
									</div>
							</li>

							<li class="widget widget_odm_taxonomy_widget">
									<h2 class="widget-title">
										<?php _e('Thematic areas', 'wp-odm_tabular_pages');?>
									</h2>
									<div>
										<ul class="odm_taxonomy_widget_ul taxonomy_widget_ul">
											<?php echo buildStyledTopTopicList(odm_language_manager()->get_current_language()); ?>
										</ul>
									</div>
							</li>
						</ul>
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
    order: [[ 0, 'asc' ]],
    displayLength: 50,
    "aoColumns": [{ "sWidth": "40%" }, { "sWidth": "20%" }, { "sWidth": "14%" }, { "sWidth": "14%" }, { "sWidth": "17%" }]
		<?php if (odm_language_manager()->get_current_language() == 'km') { ?>
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
		}
	 	<?php } ?>
  });

setTimeout(function ()
{
oTable.fnAdjustColumnSizing();
}, 10 );

  $("#search_all").keyup(function () {
    console.log("filtering page " + this.value);
    oTable.fnFilterAll(this.value);
 });

});

</script>
