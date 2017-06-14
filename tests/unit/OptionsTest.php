<?php

use Corcel\Options;

class OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getting some options.
     */
    public function testGetOptions()
    {
        // Get all the options and test some
        $options = Options::getAll();
        $this->assertNotNull($options);

        // String value
        $this->assertArrayHasKey('blogname', $options);
        $this->assertEquals('Wordpress Corcel', $options['blogname']);

        // Array value
        $this->assertArrayHasKey('wp_user_roles', $options);
        $this->assertCount(5, $options['wp_user_roles']);
        $this->assertEquals('Administrator', $options['wp_user_roles']['administrator']['name']);

        // Get single values
        $this->assertEquals('juniorgro@gmail.com', Options::get('admin_email'));
        $this->assertEquals(false, Options::get('moderation_keys'));

        $themeRoots = Options::get('_site_transient_theme_roots');
        $this->assertNotNull($themeRoots);
        $this->assertEquals('/themes', $themeRoots['twentyfourteen']);
    }

    public function testOptionValue()
    {
        //test value when option_value is string
        $optionWithString = Options::find(1);
        $stringValue = '2016-04-03';
        $optionWithString->option_value = $stringValue;
        $this->assertEquals($stringValue, $optionWithString->value);

        //test value when option_value is serialized array
        $optionWithArray = Options::find(1);
        $arrayValue = ['key' => 'value'];
        $optionWithArray->option_value = serialize($arrayValue);
        $this->assertEquals($arrayValue, $optionWithArray->value);
    }

    public function testInsert()
    {
        $option = new Options();
        $option->option_name = 'test_insert_'.uniqid();
        $option->option_value = serialize(array('test' => '1234'));
        $option->save();
    }
}
