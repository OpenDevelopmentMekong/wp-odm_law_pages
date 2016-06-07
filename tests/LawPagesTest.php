<?php

require_once dirname(dirname(__FILE__)).'/utils/utils.php';

class LawPagesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // init vars here
    }

    public function tearDown()
    {
        // undo stuff here
    }

    public function testIncorrectCkanDomain()
    {
        $results = wpckan_get_datastore_resource('incorrect_domain', 'some_resource_id');
        $this->assertEmpty($results);
    }
}
