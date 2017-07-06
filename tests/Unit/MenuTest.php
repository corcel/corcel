<?php

namespace Corcel\Tests\Unit;

use Corcel\Menu;
use Corcel\MenuItem;
use Corcel\Post;

/**
 * Class MenuTest
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MenuTest extends \Corcel\Tests\TestCase
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

        $this->assertGreaterThanOrEqual(0, count($menu->items));
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
            $this->assertInstanceOf(MenuItem::class, $post);
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
     * @test
     */
    public function it_can_have_pages()
    {
        $menu = $this->createComplexMenu();

        $pages = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'page';
        });

        $pages->each(function ($item, $i) {
            $this->assertEquals("page-title#$i", $item->object()->title);
            $this->assertEquals("page-content#$i", $item->object()->content);
        });
    }

    /**
     * @test
     */
    public function it_can_have_posts()
    {
        $menu = $this->createComplexMenu();

        $pages = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'post';
        });

        $pages->each(function ($item, $i) {
            $this->assertEquals("post-title#$i", $item->object()->title);
            $this->assertEquals("post-content#$i", $item->object()->content);
        });
    }

    /**
     * @test
     */
    public function it_can_have_custom_links()
    {
        $menu = $this->createComplexMenu();

        $pages = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'custom';
        });

        $pages->each(function ($item, $i) {
            $this->assertEquals("http://example.com#$i", $item->object()->url);
            $this->assertEquals("custom-link-text#$i", $item->object()->link_text);
        });
    }

    /**
     * @test
     */
    public function it_can_have_categories()
    {
        $menu = $this->createComplexMenu();

        $pages = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'category';
        });

        $pages->each(function ($item, $i) {
            $this->assertEquals("category-name#$i", $item->object()->name);
            $this->assertEquals("category-slug#$i", $item->object()->slug);
        });
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

        return tap(factory(Menu::class)->create(), function ($menu) use ($parent, $child) {
            $menu->posts()->attach([$parent->ID, $child->ID]);
        });
    }

    private function createComplexMenu()
    {
        $posts = factory(Post::class, 2)->create();
        $pages = factory(Post::class, 2)->create(['post_type' => 'page']);
        $custom = factory()

    }
}
