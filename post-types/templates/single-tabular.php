<?php get_header(); ?>

<?php	if (have_posts()) : ?>

  <?php
    $filter_odm_document_type = null;
    if (isset($_GET['odm_document_type'])) {
        $filter_odm_document_type = htmlspecialchars($_GET['odm_document_type']);
    }
    $filter_odm_taxonomy = null;
    if (isset($_GET['odm_taxonomy'])) {
        $filter_odm_taxonomy = htmlspecialchars($_GET['odm_taxonomy']);
    }
    $datasets = array();
    if (!IsNullOrEmptyString($filter_odm_taxonomy)) {
        $datasets = tabular_pages_get_datasets(wpckan_get_ckan_domain(), $DATASET_TYPE, 'taxonomy', $filter_odm_taxonomy);
    } elseif (!IsNullOrEmptyString($filter_odm_document_type)) {
        $datasets = tabular_pages_get_datasets(wpckan_get_ckan_domain(), $DATASET_TYPE, 'odm_document_type', $filter_odm_document_type);
    } else {
        $datasets = tabular_pages_get_datasets(wpckan_get_ckan_domain(), $DATASET_TYPE, null, null);
    }

    $headline = $filter_odm_taxonomy;

  ?>

  <section id="content" class="single-post">
    <div class="container">
      <div class="row">
  			<div class="twelve columns">
          <header class="single-post-header">
            <h1 class=""><a href="<?php get_page_link(); ?>"><?php the_title(); ?></a></h1>
            <h2 class=""><?php _e($headline, 'tabular'); ?></h2>
    		  </header>
  			</div>
      </div>

      <div class="row">
			  <div class="nine columns">
          <?php the_content(); ?>
          <table id="datasets_table" class="data-table">
            <thead>
              <tr>
                <th><?php _e('Title', 'tabular');?></th>
                <th><?php _e('Document type', 'tabular');?></th>
                <th><?php _e('Document number', 'tabular');?></th>
                <th><?php _e('Promulgation date', 'tabular');?></th>
                <th><?php _e('Download', 'tabular');?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($datasets as $dataset): ?>
                <?php if (IsNullOrEmptyString($dataset['odm_document_type'])):
                        continue;
                      endif; ?>
                <tr>
                  <td class="entry_title">
                    <a href="<?php echo wpckan_get_ckan_domain().'/dataset/'.$dataset['id'];?>"><?php echo getMultilingualValueOrFallback($dataset['title_translated'], get_current_language());?></a>
                  </td>
                  <td>
                    <?php
                      if (isset($dataset['odm_document_type'])) {
                          $doc_type = $dataset['odm_document_type'];
                          echo _e($LAWS_DOCUMENT_TYPE[$doc_type], 'tabular');
                      }
                    ?>
                  </td>
                  <td>
                    <?php
                    if (isset($dataset['odm_document_number'])) {
                        echo $dataset['odm_document_number'][get_current_language()];
                    }?>
                  </td>
                  <td>
                    <?php
                    if (isset($dataset['odm_promulgation_date'])) {
                        if (get_current_language() == 'km') {
                            echo convert_date_to_kh_date(date('d.m.Y', strtotime($dataset['odm_promulgation_date'])));
                        } else {
                            echo $dataset['odm_promulgation_date'];
                        }
                    }
                    ?>
                  </td>
                  <td class="download_buttons">
                      <?php foreach ($dataset['resources'] as $resource) :?>
                        <?php if (isset($resource['odm_language']) && count($resource['odm_language']) > 0 && $resource['odm_language'][0] == 'en') {
    ?>
                          <span><a href="<?php echo $resource['url'];
    ?>">
                            <span class="icon-arrow-down"></span>EN</a></span>
                        <?php

} ?>
                      <?php endforeach; ?>
                      <?php foreach ($dataset['resources'] as $resource) :?>
                        <?php if (isset($resource['odm_language']) && count($resource['odm_language']) > 0 && $resource['odm_language'][0] == 'km') {
    ?>
                          <span><a href="<?php echo $resource['url'];
    ?>">
                            <span class="icon-arrow-down"></span>KM</a></span>
                        <?php

} ?>
                      <?php endforeach; ?>
                  </td>
                </tr>
      				<?php endforeach; ?>
    				</tbody>
    			</table>
			  </div>

        <div class="three columns">
          <div class="sidebar_box">
  					<div class="sidebar_header">
              <?php if ($headline) {
    ?>
                <h2><?php _e('SEARCH', 'tabular');
    ?> <?php $DATASET_TYPE_NAME . " " . _e('in', 'tabular');
    ?> <?php _e($headline, 'tabular');
    ?></h2>
              <?php

} else {
    ?>
  	               <h2><?php _e('SEARCH', 'tabular');
    ?></h2> <?php _e('in', 'tabular') . " " . $DATASET_TYPE_NAME;
    ?>
             <?php

} ?>
  					</div>
  					<div class="sidebar_box_content">
  						<input type="text" id="search_all" placeholder=<?php _e('Search all', 'tabular'). " " . $DATASET_TYPE_NAME; ?>>
              <?php if (!IsNullOrEmptyString($filter_odm_document_type) || !IsNullOrEmptyString($filter_odm_taxonomy)): ?>
                <a href="/tabular/<?php echo strtolower($DATASET_TYPE_NAME); ?>"><?php _e('Clear filter', 'tabular') ?>
              <?php endif; ?>
  					</div>
				  </div>

          <div class="sidebar_box">
  					<div class="sidebar_header">
  						<h2><?php _e('Filter by taxonomy', 'tabular');?></h2>
  					</div>
  					<div class="sidebar_box_content">
              <?php echo buildStyledTopTopicList(get_current_language()); ?>
  					</div>
  				</div>

          <div class="sidebar_box">
  					<div class="sidebar_header">
  						<h2><?php _e('Filter by type', 'tabular');?></h2>
  					</div>
  					<div class="sidebar_box_content">
              <ul>
                <?php foreach($LAWS_DOCUMENT_TYPE as $key => $value): ?>
                  <li><a href="?odm_document_type=<?php echo $key ?>;"><?php echo($value);?></a></li>
                <?php endforeach; ?>
              </ul>
  					</div>
  				</div>
			  </div>
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
    dom: '<"top"<"info"i><"pagination"p><"length"l>>rt',
    processing: true,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    order: [[ 0, 'asc' ]],
    displayLength: 25
  });

  $("#search_all").keyup(function () {
    console.log("filtering page " + this.value);
    oTable.fnFilterAll(this.value);
 });

});

</script>
