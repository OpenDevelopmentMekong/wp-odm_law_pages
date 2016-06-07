<?php

function get_law_datasets($ckan_domain, $filter_key, $filter_value)
{
    $ckanapi_url = $ckan_domain.'/api/3/action/package_search?q=*:*&fq=type:laws_record&rows=1000';
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

function buildStyledTopTopicListForLaws($lang)
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
            echo '<li><a href="/laws/?odm_taxonomy='.$child['titles']['en'].'">'.$child['titles'][$lang].'</a></li>';
        }
    }
    echo '</ul>';
}

function getMultilingualValueOrFallback($field, $lang)
{
    if (!isset($field[$lang]) || IsNullOrEmptyString($field[$lang])) {
        return $field['en'];
    }

    return $field[$lang];
}

/****** Add function convert date, H-E/**/
//echo convert_date_to_kh_date("18.05.2014");
function convert_date_to_kh_date($date_string, $splitted_by = '.')
{ //$date_string = Day.Month.Year
  if ((CURRENT_LANGUAGE == 'kh') || (CURRENT_LANGUAGE == 'km')) {
      $splitted_date = explode($splitted_by, $date_string); // split the date by "."
        $joined_date = '';
      if (count($splitted_date) > 1) {
          if (strlen($date_string) == 7) { //month and year //Month.Year  02.2014
                $month_year = $splitted_date; //get Month.Year  02.2014
                    if ($month_year[0] != '00') {
                        $joined_date .= ' ខែ'.convert_to_kh_month($month_year[0]);
                    }
              if ($month_year[1] != '0000') {
                  $joined_date .= ' ឆ្នាំ'.convert_to_kh_number($month_year[1]);
              }
          } else {
              $day_month_year = $splitted_date; //get Day.Month.Year  20.02.2014
                    if ($day_month_year[0] != '00') {
                        $joined_date .= 'ថ្ងៃទី '.convert_to_kh_number($day_month_year[0]);
                    }
              if ($day_month_year[1] != '00') {
                  $joined_date .= ' ខែ'.convert_to_kh_month($day_month_year[1]);
              }
              if ($day_month_year[2] != '0000') {
                  $joined_date .= ' ឆ្នាំ'.convert_to_kh_number($day_month_year[2]);
              }
          }
      } else {
          if (strlen($date_string) == 4) { //only year
                $joined_date = ' ឆ្នាំ'.convert_to_kh_number($date_string);
          }
      }

      return $joined_date;
  }//if CURRENT_LANGUAGE
  else {
      $return_date = date('d F Y', strtotime($date_string));

      return  $my_date;
  }
}
function convert_to_kh_month($month = '')
{
    if ((CURRENT_LANGUAGE == 'kh') || (CURRENT_LANGUAGE == 'km')) {
        if ($month == 'Jan') {
            $kh_month = 'មករា';
        } elseif ($month == 'Feb') {
            $kh_month = 'កុម្ភៈ';
        } elseif ($month == 'Mar') {
            $kh_month = 'មីនា';
        } elseif ($month == 'Apr') {
            $kh_month = 'មេសា';
        } elseif ($month == 'May') {
            $kh_month = 'ឧសភា';
        } elseif ($month == 'Jun') {
            $kh_month = 'មិថុនា';
        } elseif ($month == 'Jul') {
            $kh_month = 'កក្កដា';
        } elseif ($month == 'Aug') {
            $kh_month = 'សីហា';
        } elseif ($month == 'Sep') {
            $kh_month = 'កញ្ញា';
        } elseif ($month == 'Oct') {
            $kh_month = 'តុលា';
        } elseif ($month == 'Nov') {
            $kh_month = 'វិច្ឆិកា';
        } elseif ($month == 'Dec') {
            $kh_month = 'ធ្នូ';
        } elseif ($month == '01') {
            $kh_month = 'មករា';
        } elseif ($month == '02') {
            $kh_month = 'កុម្ភៈ';
        } elseif ($month == '03') {
            $kh_month = 'មីនា';
        } elseif ($month == '04') {
            $kh_month = 'មេសា';
        } elseif ($month == '05') {
            $kh_month = 'ឧសភា';
        } elseif ($month == '06') {
            $kh_month = 'មិថុនា';
        } elseif ($month == '07') {
            $kh_month = 'កក្កដា';
        } elseif ($month == '08') {
            $kh_month = 'សីហា';
        } elseif ($month == '09') {
            $kh_month = 'កញ្ញា';
        } elseif ($month == '10') {
            $kh_month = 'តុលា';
        } elseif ($month == '11') {
            $kh_month = 'វិច្ឆិកា';
        } elseif ($month == '12') {
            $kh_month = 'ធ្នូ';
        } elseif ($month == '០១') {
            $kh_month = 'មករា';
        } elseif ($month == '០២') {
            $kh_month = 'កុម្ភៈ';
        } elseif ($month == '០៣') {
            $kh_month = 'មីនា';
        } elseif ($month == '០៤') {
            $kh_month = 'មេសា';
        } elseif ($month == '០៥') {
            $kh_month = 'ឧសភា';
        } elseif ($month == '០៦') {
            $kh_month = 'មិថុនា';
        } elseif ($month == '០៧') {
            $kh_month = 'កក្កដា';
        } elseif ($month == '០៨') {
            $kh_month = 'សីហា';
        } elseif ($month == '០៩') {
            $kh_month = 'កញ្ញា';
        } elseif ($month == '១០') {
            $kh_month = 'តុលា';
        } elseif ($month == '១១') {
            $kh_month = 'វិច្ឆិកា';
        } elseif ($month == '១២') {
            $kh_month = 'ធ្នូ';
        }

        return $kh_month;
    }//if CURRENT_LANGUAGE
  else {
      return $month;
  }
}

function convert_to_kh_number($number)
{
    if ((CURRENT_LANGUAGE == 'kh') || (CURRENT_LANGUAGE == 'km')) {
        $conbine_num = '';
        $split_num = str_split($number);
        foreach ($split_num as $num) {
            if ($num == '0') {
                $kh_num = '០';
            } elseif ($num == '1') {
                $kh_num = '១';
            } elseif ($num == '2') {
                $kh_num = '២';
            } elseif ($num == '3') {
                $kh_num = '៣';
            } elseif ($num == '4') {
                $kh_num = '៤';
            } elseif ($num == '5') {
                $kh_num = '៥';
            } elseif ($num == '6') {
                $kh_num = '៦';
            } elseif ($num == '7') {
                $kh_num = '៧';
            } elseif ($num == '8') {
                $kh_num = '៨';
            } elseif ($num == '9') {
                $kh_num = '៩';
            } else {
                $kh_num = $num;
            }

            $conbine_num .= $kh_num;
        }

        return $conbine_num;
    }//if CURRENT_LANGUAGE
else {
    return $number;
}
}

if (!function_exists('IsNullOrEmptyString')) {
    function IsNullOrEmptyString($question)
    {
        return !isset($question) || @trim($question) === '';
    }
}

function get_datastore_resources_filter($ckan_domain,$resource_id,$key,$value){
  $datastore_url = $ckan_domain . "/api/3/action/datastore_search?resource_id=" . $resource_id . "&limit=1000&filters={\"" . $key . "\":\"" . $value . "\"}";
  $json = @file_get_contents($datastore_url);
  if ($json === FALSE) return [];
  $profiles = json_decode($json, true) ?: [];
  return $profiles["result"]["records"];
}

function get_datastore_resource($ckan_domain,$resource_id){
  $datastore_url = $ckan_domain . "/api/3/action/datastore_search?resource_id=" . $resource_id . "&limit=1000";
  $json = @file_get_contents($datastore_url);
  if ($json === FALSE) return [];
  $profiles = json_decode($json, true) ?: [];
  return $profiles["result"]["records"];
}

?>
