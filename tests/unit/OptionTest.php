<?php

use Corcel\Option;
use Illuminate\Support\Collection;

/**
 * Class OptionTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class OptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function get_all_method()
    {
        factory(Option::class, 2)->create();

        $options = Option::getAll();

        $this->assertTrue(is_array($options));
        $this->assertTrue(count($options) > 0);
    }

//    /**
//     * Test getting some options.
//     */
//    public function testGetOptions()
//    {
//        // Get all the options and test some
//        $options = Option::getAll();
//        $this->assertNotNull($options);
//
//        // String value
//        $this->assertArrayHasKey('blogname', $options);
//        $this->assertEquals('Wordpress Corcel', $options['blogname']);
//
//        // Array value
//        $this->assertArrayHasKey('wp_user_roles', $options);
//        $this->assertCount(5, $options['wp_user_roles']);
//        $this->assertEquals('Administrator', $options['wp_user_roles']['administrator']['name']);
//
//        // Get single values
//        $this->assertEquals('juniorgro@gmail.com', Option::get('admin_email'));
//        $this->assertEquals(false, Option::get('moderation_keys'));
//
//        $themeRoots = Option::get('_site_transient_theme_roots');
//        $this->assertNotNull($themeRoots);
//        $this->assertEquals('/themes', $themeRoots['twentyfourteen']);
//    }
//
//    public function testOptionValue()
//    {
//        //test value when option_value is string
//        $optionWithString = Option::find(1);
//        $stringValue = '2016-04-03';
//        $optionWithString->option_value = $stringValue;
//        $this->assertEquals($stringValue, $optionWithString->value);
//
//        //test value when option_value is serialized array
//        $optionWithArray = Option::find(1);
//        $arrayValue = ['key' => 'value'];
//        $optionWithArray->option_value = serialize($arrayValue);
//        $this->assertEquals($arrayValue, $optionWithArray->value);
//    }
//
//    public function testInsert()
//    {
//        $option = new Option();
//        $option->option_name = 'test_insert_'.uniqid();
//        $option->option_value = serialize(array('test' => '1234'));
//        $option->save();
//    }
}
