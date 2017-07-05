<?php

use Corcel\Menu;
use Corcel\Post;

/**
 * Class MenuTest
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_has_the_correct_class_name()
    {
        $menu = factory(Menu::class)->create();

        $this->assertInstanceOf(Menu::class, $menu);
    }

    /**
     * @test
     */
    public function it_has_integer_id()
    {
        $menus = factory(Menu::class, 2)->create();

        collect($menus)->each(function ($menu) {
            $this->assertNotNull($menu);
            $this->assertInternalType('integer', $menu->term_taxonomy_id);
        });
    }

    /**
     * @test
     */
    public function it_can_be_queried_by_slug()
    {
        factory(Menu::class)->create();
        $menu = Menu::slug('foo')->first();

        $this->assertGreaterThanOrEqual(0, count($menu->nav_items));
    }

    /**
     * @test
     */
    public function it_has_items_as_posts()
    {
        $menu = $this->createMenu();

        $this->assertCount(2, $menu->items);

        collect($menu->items)->each(function ($post) {
            $this->assertNotNull($post);
            $this->assertInstanceOf(Post::class, $post);
        });
    }

    /**
     * @test
     */
    public function it_can_have_multilevel_children()
    {
        $menu = $this->createMenu();

        $parent = $menu->posts->first();
        $child = $menu->posts->last();

        $this->assertNotNull($parent);
        $this->assertNotNull($child);
        $this->assertEquals($parent->ID, $child->meta->_menu_item_menu_item_parent);
        $this->assertEquals(0, $parent->_menu_item_menu_item_parent);
    }

    /**
     * @return Menu
     */
    private function createMenu()
    {
        $parent = factory(Post::class)->create(['post_type' => 'nav_menu_item']);
        $parent->saveMeta('_menu_item_menu_item_parent', 0);

        $child = factory(Post::class)->create(['post_type' => 'nav_menu_item']);
        $child->saveMeta('_menu_item_menu_item_parent', $parent->ID);

        $params = ['term_order' => 0];

        return tap(factory(Menu::class)->create(), function ($menu) use ($parent, $child, $params) {
            $menu->posts()->attach([
                $parent->ID => $params,
                $child->ID => $params,
            ]);
        });
    }
}
