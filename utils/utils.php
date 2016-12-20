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

function integer_to_text($number)
{
    switch ($number){
			case 1:
				return "one";
			case 2:
				return "two";
			case 3:
				return "three";
			case 4:
				return "four";
			case 5:
				return "five";
			case 6:
				return "six";
			case 7:
				return "seven";
			case 8:
				return "eight";
			case 9:
				return "nine";
			case 10:
				return "ten";
			case 11:
				return "eleven";
			case 12:
				return "twelve";
			case 13:
				return "thirteen";
			case 14:
				return "fourteen";
			case 15:
				return "fifteen";
			case 16:
				return "sixteen";
			default:
				return "sixteen";
		}
}
