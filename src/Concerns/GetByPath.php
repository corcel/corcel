<?php

namespace Corcel\Concerns;

/**
 * Trait GetByPath
 *
 * @package Corcel\Traits
 * @author João Henrique S. Mendonça <joao.mendonca@pdmfc.com>
 */
trait GetByPath
{
    /**
     * Retrieves a post (usually pages) given its path.
     *
     * @param string $pagePath Page path.
     * @param string|array $postType Optional. Post type or array of post types. Default 'page'.
     * @return \Corcel\Model\Post|null \Corcel\Model\Post on success, or null on failure.
     */
    public static function getByPath($pagePath, $postType = 'page')
    {
        $inString = self::sanitizePathToSql($pagePath);
        $postTypeInString = self::sanitizeTypesToSql($postType);
        $sql = "post_name IN ($inString) AND post_type IN ($postTypeInString)";

        $pages = self::whereRaw($sql)->get()->keyBy('ID');

        $reverseParts = self::reversePathParts($pagePath);

        $foundId = self::searchIdInPath($pages, $reverseParts, $postType);

        if ($foundId) {
            return $pages[$foundId];
        }
    }

    /**
     * Returns a sql string with the sanitized and exploded path
     *
     * @param string $path
     * @return string
     */
    private static function sanitizePathToSql($path)
    {
        $parts = self::getPathParts($path);
        $escapedParts = array_map('str_slug', $parts);

        return "'" . implode("','", $escapedParts) . "'";
    }

    /**
     * Returns an array of decoded path parts
     *
     * @param $path
     * @return array
     */
    private static function getPathParts($path)
    {
        $pagePath = rawurlencode(urldecode($path));
        $pagePath = str_replace('%2F', '/', $pagePath);
        $pagePath = str_replace('%20', ' ', $pagePath);
        return explode('/', trim($pagePath, '/'));
    }

    /**
     * Returns an array of reversed path parts
     *
     * @param $path
     * @return array
     */
    private static function reversePathParts($path)
    {
        return array_reverse(self::getPathParts($path));
    }

    /**
     * Returns a sql string with the sanitized and exploded types
     *
     * @param string|array $type
     * @return string
     */
    private static function sanitizeTypesToSql($type)
    {
        if (is_array($type)) {
            $postTypes = $type;
        } else {
            $postTypes = array($type, 'attachment');
        }

        $postTypes = array_map('str_slug', $postTypes);
        return "'" . implode("','", $postTypes) . "'";
    }

    /**
     * Loops through a collection of pages (or posts) looking for the id of the child
     *
     * @param $pages
     * @param $reverseParts
     * @param $postType
     * @return int
     */
    private static function searchIdInPath($pages, $reverseParts, $postType)
    {
        $foundId = 0;
        $pagesWithTheSameName = $pages->filter(function ($page) use ($reverseParts) {
            return $page->post_name == $reverseParts[0];
        });
        $pagesWithTheSameName->each(function ($page) use (&$foundId, $pages, $reverseParts, $postType) {
            $count = 0;
            $currentPage = $page;

            /*
            * Loop through the given path parts from right to left,
            * ensuring each matches the post ancestry.
            */
            while ($currentPage->post_parent != 0 && isset($pages[$currentPage->post_parent])) {
                if (!isset($reverseParts[++$count]) || $pages[$currentPage->post_parent]->post_name != $reverseParts[$count]) {
                    break;
                }
                $currentPage = $pages[$currentPage->post_parent];
            }

            if ($currentPage->post_parent == 0 && $count + 1 == count($reverseParts) && $currentPage->post_name == $reverseParts[$count]) {
                $foundId = $page->ID;
                if ($page->post_type == $postType) {
                    return false;
                }
            }
        });

        return $foundId;
    }
}
