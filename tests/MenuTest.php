<?php

use Corcel\Menu;

class MenuTest extends PHPUnit_Framework_TestCase
{
    public function testMenuConstructor()
    {
        $menu = new Menu;
        $this->assertTrue($menu instanceof \Corcel\Menu);
    }


    public function testMenuId()
    {
        foreach(array(1, 2, 3) as $id) {
            $menu = Menu::find($id);

            if ($id == 3) {
                $this->assertEquals($menu->term_taxonomy_id, $id);
            } else {
                $this->assertNull($menu);
            }
        }
    }


    public function testMenuBySlug() {
        $menu = Menu::slug('menu1')->first();
        $this->assertEquals($menu->term_taxonomy_id, 3);
        $this->assertEquals(count($menu->nav_items), 3);
        

        $menu = Menu::slug('non_existing_menu')->first();
        $this->assertNull($menu);
    }



    public function testMultiLevelMenu() {
        $menu = Menu::slug('menu1')->first();

        $menuArray = array();
        foreach ($menu->nav_items as $item) {
            $parent_id = $item->meta->_menu_item_menu_item_parent;
            $menuArray[$parent_id][] = $item;
        }

        $this->assertEquals(count($menuArray[0]), 2);
        $this->assertEquals(count($menuArray[16]), 1);
        $this->assertEquals($menuArray[16][0]->ID, 17);
    }
}