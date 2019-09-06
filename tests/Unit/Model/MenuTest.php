<?php

namespace Corcel\Tests\Unit\Model;

use Corcel\Model\CustomLink;
use Corcel\Model\Menu;
use Corcel\Model\MenuItem;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;

/**
 * Class MenuTest
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class MenuTest extends \Corcel\Tests\TestCase
{
    public function test_it_has_the_correct_class_name()
    {
        $menu = factory(Menu::class)->create();

        $this->assertInstanceOf(Menu::class, $menu);
    }

    public function test_it_has_integer_id()
    {
        $menus = factory(Menu::class, 2)->create();

        collect($menus)->each(function ($menu) {
            $this->assertNotNull($menu);
            $this->assertIsInt($menu->term_taxonomy_id);
        });
    }

    public function test_it_can_be_queried_by_slug()
    {
        factory(Menu::class)->create();
        $menu = Menu::slug('foo')->first();

        $this->assertGreaterThanOrEqual(0, count($menu->items));
    }

    public function test_it_has_items_as_posts()
    {
        $menu = $this->createMenu();

        $this->assertCount(2, $menu->items);

        collect($menu->items)->each(function ($post) {
            $this->assertNotNull($post);
            $this->assertInstanceOf(MenuItem::class, $post);
            $this->assertInstanceOf(Post::class, $post);
        });
    }

    public function test_it_can_have_multilevel_children()
    {
        $menu = $this->createMenu();

        $parent = $menu->posts->first();
        $child = $menu->posts->last();

        $this->assertNotNull($parent);
        $this->assertNotNull($child);
        $this->assertEquals($parent->ID, $child->meta->_menu_item_menu_item_parent);
        $this->assertEquals(0, $parent->_menu_item_menu_item_parent);
    }

    public function test_it_has_parent_relation()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'post';
        });

        $parent = $posts->first()->instance();
        $child = $posts->last();

        $this->assertEquals($parent->ID, $child->parent()->ID);
        $this->assertEquals($parent->post_name, $child->parent()->post_name);
    }

    public function test_it_can_have_custom_links_associated_as_meta()
    {
        $item = factory(MenuItem::class)->create([
            'post_title' => 'Foobar',
        ]);

        $item->saveMeta([
            '_menu_item_type' => 'custom',
            '_menu_item_menu_item_parent' => 0,
            '_menu_item_object_id' => $item->ID,
            '_menu_item_object' => 'custom',
            '_menu_item_target' => '',
            '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
            '_menu_item_xfn' => '',
            '_menu_item_url' => 'http://example.com',
        ]);

        $this->assertEquals('Foobar', $item->post_title);
        $this->assertEquals('Foobar', $item->instance()->link_text);
        $this->assertEquals('http://example.com', $item->meta->_menu_item_url);
        $this->assertEquals('http://example.com', $item->instance()->url);
    }

    public function test_it_can_have_pages()
    {
        $menu = $this->createComplexMenu();

        $pages = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'page';
        });

        $pages->each(function (MenuItem $item) {
            $this->assertEquals("page-title", $item->instance()->post_title);
            $this->assertEquals("page-content", $item->instance()->post_content);
        });
    }

    public function test_it_can_have_posts()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'post';
        });

        $posts->each(function (MenuItem $item) {
            $this->assertEquals("post-title", $item->instance()->title);
            $this->assertEquals("post-content", $item->instance()->content);
        });
    }

    public function test_it_can_have_custom_links()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'custom';
        });

        $posts->each(function (MenuItem $item) {
            $this->assertEquals("http://example.com", $item->instance()->url);
            $this->assertEquals("custom-link-text", $item->instance()->link_text);
        });
    }

    public function test_it_can_have_categories()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'category';
        });

        $posts->each(function (MenuItem $item) {
            $this->assertEquals("Bar", $item->instance()->name);
            $this->assertEquals("bar", $item->instance()->slug);
        });
    }

    private function createMenu(): Menu
    {
        $parent = factory(Post::class)->create(['post_type' => 'nav_menu_item']);
        $parent->saveMeta('_menu_item_menu_item_parent', 0);

        $child = factory(Post::class)->create(['post_type' => 'nav_menu_item']);
        $child->saveMeta('_menu_item_menu_item_parent', $parent->ID);

        return tap(factory(Menu::class)->create(), function ($menu) use ($parent, $child) {
            $menu->posts()->attach([$parent->ID, $child->ID]);
        });
    }

    private function createComplexMenu(): Menu
    {
        $menu = factory(Menu::class)->create();

        $this->buildPage($menu);

        $post = $this->buildPost($menu);
        $this->buildPost($menu, $post->ID);

        $this->buildCustomLink($menu);
        $this->buildCategory($menu);

        return $menu;
    }

    private function buildPage(Menu $menu): void
    {
        $page = factory(Post::class)->create([
            'post_type' => 'page',
            'post_title' => 'page-title',
            'post_content' => 'page-content',
        ]);

        $item = factory(MenuItem::class)->create();

        $item->saveMeta([
            '_menu_item_type' => 'post_type',
            '_menu_item_menu_item_parent' => 0,
            '_menu_item_object_id' => $page->ID,
            '_menu_item_object' => $page->post_type,
            '_menu_item_target' => '',
            '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
            '_menu_item_xfn' => '',
            '_menu_item_url' => '',
        ]);

        $menu->items()->save($item);
    }

    private function buildPost(Menu $menu, int $parentId = 0): Post
    {
        $post = factory(Post::class)->create([
            'post_title' => 'post-title',
            'post_content' => 'post-content',
        ]);

        $item = factory(MenuItem::class)->create();

        $item->saveMeta([
            '_menu_item_type' => 'post_type',
            '_menu_item_menu_item_parent' => $parentId,
            '_menu_item_object_id' => $post->ID,
            '_menu_item_object' => $post->post_type,
            '_menu_item_target' => '',
            '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
            '_menu_item_xfn' => '',
            '_menu_item_url' => '',
        ]);

        $menu->items()->save($item);

        return $post;
    }

    private function buildCustomLink(Menu $menu): void
    {
        $link = factory(CustomLink::class)->create([
            'post_title' => 'custom-link-text',
        ]);

        $link->saveMeta([
            '_menu_item_type' => 'custom',
            '_menu_item_menu_item_parent' => 0,
            '_menu_item_object_id' => $link->ID,
            '_menu_item_object' => 'custom',
            '_menu_item_target' => '',
            '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
            '_menu_item_xfn' => '',
            '_menu_item_url' => 'http://example.com',
        ]);

        $menu->items()->save($link);
    }

    private function buildCategory(Menu $menu): void
    {
        $taxonomy = factory(Taxonomy::class)->create([
            'taxonomy' => 'category',
        ]);

        $item = factory(MenuItem::class)->create();

        $item->saveMeta([
            '_menu_item_type' => 'taxonomy',
            '_menu_item_menu_item_parent' => 0,
            '_menu_item_object_id' => $taxonomy->term_taxonomy_id,
            '_menu_item_object' => 'category',
            '_menu_item_target' => '',
            '_menu_item_classes' => 'a:1:{i:0;s:0:"";}',
            '_menu_item_xfn' => '',
            '_menu_item_url' => '',
        ]);

        $menu->items()->save($item);
    }
}
