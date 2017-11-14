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
     * @param string $page_path Page path.
     * @param string|array $post_type Optional. Post type or array of post types. Default 'page'.
     * @return \Corcel\Model\Post|null \Corcel\Model\Post on success, or null on failure.
     */
    public static function getByPath($page_path, $post_type = 'page')
    {
        $in_string = self::sanitisePathToSql($page_path);
        $post_type_in_string = self::sanitiseTypesToSql($post_type);
        $sql = "post_name IN ($in_string) AND post_type IN ($post_type_in_string)";

        $pages = self::whereRaw($sql)->get()->keyBy('ID');

        $revparts = self::reversePathParts($page_path);

        $foundid = 0;
        foreach ($pages as $page) {
            if ($page->post_name == $revparts[0]) {
                $count = 0;
                $p = $page;

                /*
                * Loop through the given path parts from right to left,
                * ensuring each matches the post ancestry.
                */
                while ($p->post_parent != 0 && isset($pages[$p->post_parent])) {
                    $count++;
                    $parent = $pages[$p->post_parent];
                    if (!isset($revparts[$count]) || $parent->post_name != $revparts[$count]) {
                        break;
                    }
                    $p = $parent;
                }

                if ($p->post_parent == 0 && $count + 1 == count($revparts) && $p->post_name == $revparts[$count]) {
                    $foundid = $page->ID;
                    if ($page->post_type == $post_type) {
                        break;
                    }
                }
            }
        }

        if ($foundid) {
            return $pages[$foundid];
        }
    }

    /**
     * Returns a sql string with the sanitized and exploded path
     *
     * @param string $path
     * @return string
     */
    private static function sanitisePathToSql($path)
    {
        $parts = self::getPathParts($path);
        $escaped_parts = array_map('str_slug', $parts);

        return "'" . implode("','", $escaped_parts) . "'";
    }

    /**
     * Returns an array of decoded path parts
     *
     * @param $path
     * @return array
     */
    private static function getPathParts($path)
    {
        $page_path = rawurlencode(urldecode($path));
        $page_path = str_replace('%2F', '/', $page_path);
        $page_path = str_replace('%20', ' ', $page_path);
        return explode('/', trim($page_path, '/'));
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
    private static function sanitiseTypesToSql($type)
    {
        if (is_array($type)) {
            $post_types = $type;
        } else {
            $post_types = array($type, 'attachment');
        }

        $post_types = array_map('str_slug', $post_types);
        return "'" . implode("','", $post_types) . "'";
    }
}