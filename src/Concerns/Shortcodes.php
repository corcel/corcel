<?php

namespace Corcel\Concerns;

use Corcel\Corcel;
use Thunder\Shortcode\ShortcodeFacade;

/**
 * Trait ShortcodesTrait
 *
 * @package Corcel\Traits
 * @author Mickael Burguet <www.rundef.com>
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait Shortcodes
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

        $this->parseClassShortcodes($facade);
        $this->parseConfigShortcodes($facade);

        return $facade->process($content);
    }

    /**
     * @param ShortcodeFacade $facade
     */
    private function parseClassShortcodes(ShortcodeFacade $facade)
    {
        foreach (self::$shortcodes as $tag => $func) {
            $facade->addHandler($tag, $func);
        }
    }

    /**
     * @param ShortcodeFacade $facade
     */
    private function parseConfigShortcodes(ShortcodeFacade $facade)
    {
        if (Corcel::isLaravel()) {
            $shortcodes = config('corcel.shortcodes', []);
            foreach ($shortcodes as $tag => $class) {
                $facade->addHandler($tag, [new $class, 'render']);
            }
        }
    }
}
