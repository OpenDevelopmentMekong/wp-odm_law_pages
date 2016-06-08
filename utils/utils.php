<?php

function tabular_pages_get_datasets($ckan_domain, $dataset_type, $filter_key, $filter_value)
{
    $ckanapi_url = $ckan_domain.'/api/3/action/package_search?q=*:*&fq=type:'. $dataset_type .'&rows=1000';  
    $json = @file_get_contents($ckanapi_url);
    if ($json === false) {
        return [];
    }
    $result = json_decode($json, true) ?: [];
    $datasets = $result['result']['results'];
    if (isset($filter_key) && isset($filter_value)) {
        foreach ($datasets as $key => $dataset) {
            if (!isset($dataset[$filter_key])) {
                unset($datasets[$key]);
            } else {
                if (is_array($dataset[$filter_key])) {
                    if (!in_array($filter_value, $dataset[$filter_key])) {
                        unset($datasets[$key]);
                    }
                } elseif ($dataset[$filter_key] != $filter_value) {
                    unset($datasets[$key]);
                }
            }
        }
    }

    return $datasets;
}

function buildStyledTopTopicList($lang)
{
    $navigation_vocab = @file_get_contents(get_stylesheet_directory().'/odm-taxonomy/top_topics/top_topics_multilingual.json');
    if ($navigation_vocab === false) {
        echo '<ul></ul>';
    }
    $json_a = json_decode($navigation_vocab, true);

    echo '<ul>';
    // get Top Topic Names
    foreach ($json_a as $key => $value) {
        foreach ($json_a[$key]['children'] as $child) {
            echo '<li><a href="?odm_taxonomy='.$child['titles']['en'].'">'.$child['titles'][$lang].'</a></li>';
        }
    }
    echo '</ul>';
}

function check_requirements_tabular_pages()
{
    return function_exists('wpckan_get_ckan_domain') && function_exists('wpckan_validate_settings_read') && wpckan_validate_settings_read();
}
