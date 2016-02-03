<?php
namespace Corcel;

/**
 * Menu class
 *
 * @author Yoram de Langen <yoramdelangen@gmail.com>
 */
class Menu extends TermTaxonomy
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
	protected $with = ['term', 'nav_items'];
}