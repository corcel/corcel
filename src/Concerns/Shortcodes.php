<?php

namespace Corcel\Concerns;

use Corcel\Corcel;
use Thunder\Shortcode\Parser\ParserInterface;
use Thunder\Shortcode\Parser\RegularParser;
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

    /** @var ParserInterface */
    private $shortcodeParser;

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
     * Change the default shortcode parser
     *
     * @param ParserInterface $parser
     * @return Shortcodes
     */
    public function setShortcodeParser(ParserInterface $parser): self
    {
        $this->shortcodeParser = $parser;

        return $this;
    }

    /**
     * Process the shortcodes.
     *
     * @param string $content the content
     * @return string
     */
    public function stripShortcodes($content)
    {
        $handler = $this->getShortcodeHandlerInstance();

        $this->parseClassShortcodes($handler);
        $this->parseConfigShortcodes($handler);

        return $handler->process($content);
    }

    /**
     * @return ShortcodeFacade
     */
    private function getShortcodeHandlerInstance(): ShortcodeFacade
    {
        if (Corcel::isLaravel()) {
            return app()->make(ShortcodeFacade::class);
        }

        return tap(new ShortcodeFacade(), function (ShortcodeFacade $handler) {
            $parser = $this->shortcodeParser ?: new RegularParser();
            $handler->setParser($parser);
        });
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
