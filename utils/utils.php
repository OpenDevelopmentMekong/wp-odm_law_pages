<?php

function buildStyledTopTopicList($lang)
{
		$path_to_taxonomy = plugin_dir_path(dirname(__FILE__)).'odm-taxonomy/top_topics/top_topics_multilingual.json';

    $navigation_vocab = @file_get_contents($path_to_taxonomy);
    if ($navigation_vocab === false) {
        echo '<ul></ul>';
    }
    $json_a = json_decode($navigation_vocab, true);

    echo '<ul>';
    // get Top Topic Names
    foreach ($json_a as $key => $value) {
        foreach ($json_a[$key]['children'] as $child) {
            echo '<li><span class="nochildimage nochildimage-'.odm_country_manager()->get_current_country().'"><a href="?odm_taxonomy='.$child['titles']['en'].'">'.$child['titles'][$lang].'</a></li></span>';
        }
    }
    echo '</ul>';
}

function check_requirements_tabular_pages()
{
    return function_exists('wpckan_get_ckan_domain') && function_exists('wpckan_validate_settings_read') && wpckan_validate_settings_read();
}
