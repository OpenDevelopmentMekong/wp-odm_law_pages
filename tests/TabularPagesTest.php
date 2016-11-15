<?php

require_once dirname(dirname(__FILE__)).'/utils/utils.php';

class TabularPagesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // init vars here
    }

    public function tearDown()
    {
        // undo stuff here
    }

    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testGetMultilingualValueOrFallback()
    {
      $multilingual = array("en" => "hello","de" => "hallo", "es" => "hola");
      $value = getMultilingualValueOrFallback($multilingual,"es","fallback");
      $this->assertEquals($value,"hola");
    }
}
