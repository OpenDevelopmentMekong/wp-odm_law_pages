<?php

/**
 * Plugin Name: ODM Laws Pages
 * Plugin URI: http://github.com/OpenDevelopmentMekong/wp-odm_law_pages
 * Description: Internal ODM Wordpress plugin for exposing a page template for law pages
 * Version: 0.9.0
 * Author: Alex Corbi (mail@lifeformapps.com)
 * Author URI: http://www.lifeformapps.com
 * License: GPLv3.
 */
require_once dirname(__FILE__).'/utils/utils.php';
require_once dirname(__FILE__).'/templates/page-laws.php';

class OpenDev_Law_Pages
{
    public function __construct()
    {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_post_data'));
    }
}

$GLOBALS['opendev_law_pages'] = new OpenDev_Law_Pages();

?>
