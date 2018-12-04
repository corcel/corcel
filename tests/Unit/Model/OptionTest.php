<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\Option;

/**
 * Class OptionTest
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class OptionTest extends \Corcel\Tests\TestCase
{
    public function test_it_can_return_all_configs_as_array()
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

    public function test_it_can_return_just_the_config_passing_the_keys()
    {
        Option::add('one', 'two');
        Option::add('three', 'four');
        Option::add('five', 'six');

        $options = Option::asArray(['three', 'five']);

        $this->assertCount(2, $options);
        $this->assertArrayHasKey('three', $options);
        $this->assertArrayHasKey('five', $options);
        $this->assertArrayNotHasKey('one', $options);
        $this->assertEquals('four', $options['three']);
    }

    public function test_it_has_a_countable_as_array_method()
    {
        factory(Option::class, 2)->create();

        $options = Option::asArray();

        $this->assertInternalType('array', $options);
        $this->assertGreaterThan(0, count($options));
    }

    public function test_it_can_have_serialized_data()
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

    public function test_it_returns_null_if_not_found()
    {
        $value = Option::get('b03e3fd');

        $this->assertNull($value);
    }

    public function test_it_has_simple_value_attribute()
    {
        $option = factory(Option::class)->create([
            'option_name' => 'foo',
            'option_value' => 'bar',
        ]);

        $this->assertEquals('bar', $option->value);
    }

    public function test_it_can_unserialize_data_if_necessary()
    {
        $option = factory(Option::class)->create([
            'option_name' => 'foo',
            'option_value' => serialize($array = [1, 2, 3]),
        ]);

        $this->assertEquals($array, $option->value);
    }

    public function test_it_can_be_converted_to_simple_array()
    {
        $option = factory(Option::class)->create([
            'option_name' => 'foo',
            'option_value' => 'bar',
        ]);

        $this->assertArraySubset(['foo' => 'bar'], $option->toArray());
    }

    public function test_it_can_add_new_option_using_add_static_method()
    {
        $option = Option::add('foo', 'bar');

        $this->assertEquals('bar', $option->value);
        $this->assertArraySubset(['foo' => 'bar'], $option->toArray());
    }
}
