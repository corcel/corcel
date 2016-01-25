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
}
