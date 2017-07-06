<?php

namespace Corcel\Tests\Unit;

use Corcel\CustomLink;
use Corcel\Menu;
use Corcel\MenuItem;
use Corcel\Post;
use Corcel\Term;
use Corcel\TermTaxonomy;

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

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'page';
        });

        $posts->each(function (MenuItem $item, $i) {
            $this->assertEquals("page-title#$i", $item->instance()->title);
            $this->assertEquals("page-content#$i", $item->instance()->content);
        });
    }

    /**
     * @test
     */
    public function it_can_have_posts()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'post';
        });

        $posts->each(function (MenuItem $item, $i) {
            $this->assertEquals("post-title#$i", $item->instance()->title);
            $this->assertEquals("post-content#$i", $item->instance()->content);
        });
    }

    /**
     * @test
     */
    public function it_can_have_custom_links()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'custom';
        });

        $posts->each(function (MenuItem $item, $i) {
            $this->assertEquals("http://example.com#$i", $item->instance()->url);
            $this->assertEquals("custom-link-text#$i", $item->instance()->link_text);
        });
    }

    /**
     * @test
     */
    public function it_can_have_categories()
    {
        $menu = $this->createComplexMenu();

        $posts = $menu->items->filter(function ($item) {
            return $item->meta->_menu_item_object === 'category';
        });

        $posts->each(function (MenuItem $item, $i) {
            $this->assertEquals("category-name#$i", $item->instance()->name);
            $this->assertEquals("category-slug#$i", $item->instance()->slug);
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
        $pages = $this->buildPages(2);
        $posts = $this->buildPosts(2);
        $custom = $this->buildCustomLinks(2);
        $categories = $this->buildCategories(2);

        // TODO

    }

    /**
     * @param int $times
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function buildPages($times = 2)
    {
        $pages = factory(Post::class, $times)->make(['post_type' => 'page']);

        $pages->each(function ($page, $i) {
            $page->post_title = "page-title#$i";
            $page->post_content = "page-content#$i";
            $page->save();
        });

        return $pages;
    }

    /**
     * @param int $times
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function buildPosts($times = 2)
    {
        $posts = factory(Post::class, $times)->make();

        $posts->each(function ($post, $i) {
            $post->post_title = "post-title#$i";
            $post->post_content = "post-content#$i";
            $post->save();
        });

        return $posts;
    }

    /**
     * @param int $times
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function buildCustomLinks($times = 2)
    {
        $links = factory(CustomLink::class, $times)->make();

        $links->each(function ($link, $i) {
            $link->post_title = "custom-link-text#$i";
            $link->save();

            $link->saveMeta('_menu_item_url', "http://example.com#$i");
        });

        return $links;
    }

    /**
     * @param int $times
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function buildCategories($times)
    {
        $terms = factory(Term::class, $times)->create();

        $terms->each(function ($term, $i) {
            $term->name = "category-name#$i";
            $term->slug = "category-slug#$i";
            $term->save();
        });

        return $terms->map(function ($term, $i) {
            return factory(TermTaxonomy::class)->create([
                'taxonomy' => 'category',
                'term_id' => $term->term_id,
            ]);
        });
    }
}
