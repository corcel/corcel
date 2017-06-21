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
    public function as_array_method_values()
    {
        factory(Option::class)->create([
            'option_name' => 'foo',
            'option_value' => 'bar',
        ]);

        $options = Option::asArray();
        $expected = ['foo' => 'bar'];

        $this->assertArraySubset($expected, $options);
        $this->assertArrayHasKey('foo', $options);
        $this->assertEquals('bar', $options['foo']);
    }

    /**
     * @test
     */
    public function as_array_method_count()
    {
        factory(Option::class, 2)->create();

        $options = Option::asArray();

        $this->assertTrue(is_array($options));
        $this->assertTrue(count($options) > 0);
    }

    /**
     * @test
     */
    public function option_can_have_serialized_data()
    {
        factory(Option::class)->create([
            'option_name' => 'foo',
            'option_value' => serialize($array = ['foo', 'bar']),
        ]);

        $options = Option::asArray();

        $this->assertArrayHasKey('foo', $options);
        $this->assertInternalType('array', $options['foo']);
        $this->assertContains($array, $options);
        $this->assertArraySubset($array, $options['foo']);
    }

//    /**
//     * Test getting some options.
//     */
//    public function testGetOptions()
//    {
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
