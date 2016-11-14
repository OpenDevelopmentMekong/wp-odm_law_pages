<?php get_header(); ?>

<?php	if (have_posts()) : ?>


  <?php
		global $post;
		$dataset_type = get_post_meta($post->ID, '_attributes_dataset_type', true);
		$dataset_type_label = get_post_meta($post->ID, '_attributes_dataset_type_label', true);

		$param_country = odm_country_manager()->get_current_country() == 'mekong' && isset($_GET['country']) ? $_GET['country'] : odm_country_manager()->get_current_country();
	  $param_query = !empty($_GET['query']) ? $_GET['query'] : null;
	  $param_taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : null;
	  $param_language = isset($_GET['language']) ? $_GET['language'] : null;
	  $active_filters = !empty($param_taxonomy) || !empty($param_language) || !empty($param_query);

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

	<div class="container">
    <div class="row">

      <form class="advanced-nav-filters sixteen columns panel">

        <div class="five columns">
          <div class="adv-nav-input">
            <p class="label"><label for="s"><?php _e('Text search', 'odm'); ?></label></p>
            <input type="text" id="query" name="query" placeholder="<?php _e('Type your search here', 'odm'); ?>" value="<?php echo $param_query; ?>" />
          </div>
        </div>

        <?php
          $languages = odm_language_manager()->get_supported_languages();
        ?>
        <div class="three columns">
          <div class="adv-nav-input">
            <p class="label"><label for="language"><?php _e('Language', 'odm'); ?></label></p>
            <select id="language" name="language" data-placeholder="<?php _e('Select language', 'odm'); ?>">
              <option value="<?php _e('All','odm') ?>" selected><?php _e('All','odm') ?></option>
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
        <div class="three columns">
          <div class="adv-nav-input">
            <p class="label"><label for="country"><?php _e('Country', 'odm'); ?></label></p>
            <select id="country" name="country" data-placeholder="<?php _e('Select country', 'odm'); ?>">
              <?php
                if (odm_country_manager()->get_current_country() == 'mekong'): ?>
                  <option value="<?php _e('All','odm') ?>" selected><?php _e('All','odm') ?></option>
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
          $taxonomy_list = odm_taxonomy_manager()->get_taxonomy_list();
        ?>
        <div class="three columns">
          <div class="adv-nav-input">
            <p class="label"><label for="taxonomy"><?php _e('Taxonomy', 'odm'); ?></label></p>
            <select id="taxonomy" name="taxonomy" data-placeholder="<?php _e('Select term', 'odm'); ?>">
              <option value="<?php _e('All','odm') ?>" selected><?php _e('All','odm') ?></option>
              <?php
                foreach($taxonomy_list as $value): ?>
                <option value="<?php echo $value; ?>" <?php if($value == $param_taxonomy) echo 'selected'; ?>><?php echo $value; ?></option>
              <?php
                endforeach; ?>
            </select>
          </div>
        </div>

        <div class="two columns">
          <input class="button" type="submit" value="<?php _e('Search Filter', 'odm'); ?>"/>
          <?php
            if ($active_filters):
              ?>
              <a href="?clear"><?php _e('Clear','odm') ?></a>
          <?php
            endif;
           ?>
        </div>

      </form>

      <?php
        if (!$active_filters):
          $shortcode = '[wpckan_number_of_query_datasets limit="1"';
          if (isset($param_country)):
            $shortcode .= ' filter_fields=\'{"extras_odm_spatial_range":"'. $countries[$param_country]['iso2'] . '"}\'';
          endif;
          ?>
          <div class="sixteen columns">
            <div class="data-number-results-small">
              <p>
                <p class="label"><label><?php _e('Current statistics: ','odm'); ?></label></p>
                <?php echo do_shortcode($shortcode . ' type="dataset" suffix=" Datasets"]'); ?>
                <?php echo do_shortcode($shortcode . ' type="library_record" suffix=" Library records"]'); ?>
                <?php echo do_shortcode($shortcode . ' type="laws_record" suffix=" Laws"]'); ?>
              </p>
            </div>
          </div>
          <?php
        endif; ?>

    </div>
  </div>

  <section class="container">
    <div class="row">
		  <div class="sixteen columns">
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

  $("#query").keyup(function () {
    console.log("filtering page " + this.value);
    oTable.fnFilterAll(this.value);
 });

});

</script>
