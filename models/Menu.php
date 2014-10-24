<?php

/**
 * Menu class
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 */
class Menu extends Corcel\TermTaxonomy
{
	/**
	 * Set taxonomy type
	 * @var string
	 */
	protected $taxonomy = 'nav_menu';

	/**
	 * Add related relationships we need to use for a menu
	 * @var array
	 */
	protected $with = array('term', 'nav_items');
}