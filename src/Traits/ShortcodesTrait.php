<?php

namespace Corcel\Traits;

use Thunder\Shortcode\ShortcodeFacade;

/**
 * Trait ShortcodesTrait
 *
 * @package Corcel\Traits
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait ShortcodesTrait
{
    /**
     * @var array
     */
    protected static $shortcodes = [];

    /**
     * @param string $tag the shortcode tag
     * @param \Closure $function the shortcode handling function
     */
    public static function addShortcode($tag, $function)
    {
        self::$shortcodes[$tag] = $function;
    }

    /**
     * Removes a shortcode handler.
     *
     * @param string $tag the shortcode tag
     */
    public static function removeShortcode($tag)
    {
        if (isset(self::$shortcodes[$tag])) {
            unset(self::$shortcodes[$tag]);
        }
    }

    /**
     * Process the shortcodes.
     *
     * @param string $content the content
     * @return string
     */
    public function stripShortcodes($content)
    {
        $facade = new ShortcodeFacade();

        foreach (self::$shortcodes as $tag => $func) {
            $facade->addHandler($tag, $func);
        }

        return $facade->process($content);
    }
}
