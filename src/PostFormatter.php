<?php

namespace Corcel;

/**
 * Corcel\PostFormatter.
 *
 * @author Mike Forcer <michael@goodbytes.co.uk>
 */
class PostFormatter
{
    /**
     * Process WordPress post content to improve out of box formatting.
     *
     * @param      $content
     * @param bool $lineBreaks
     *
     * @return mixed|string
     */
    public function process($content, $lineBreaks = true)
    {
        // If content is empty, return empty.
        if (trim($content) === '') {
            return '';
        }

        // Pad the end
        $content = "{$content}\n";

        // Remove content from pre tags (we'll add them back later)
        $preTags = array();
        if (strpos($content, '<pre') !== false) {
            $contentParts = explode('</pre>', $content);
            $lastContentPart = array_pop($contentParts);
            $content = '';
            $i = 0;

            foreach ($contentParts as $contentPart) {
                $start = strpos($contentPart, '<pre');

                if ($start === false) {
                    $content .= $contentPart;
                    continue;
                }

                $name = "<pre wp-pre-tag-{$i}></pre>";
                $preTags[$name] = substr($contentPart, $start) . '</pre>';
                $content .= substr($contentPart, 0, $start) . $name;
                $i++;
            }

            $content .= $lastContentPart;
        }

        // Change multiple <br>s into two line breaks, which will turn into paragraphs.
        $content = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $content);

        $regex = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

        // Add a double line break above block-level opening tags.
        $content = preg_replace('!(<' . $regex . '[\s/>])!', "\n\n$1", $content);

        // Add a double line break below block-level closing tags.
        $content = preg_replace('!(</' . $regex . '>)!', "$1\n\n", $content);

        // Standardize newline characters to "\n".
        $content = str_replace(array("\r\n", "\r"), "\n", $content);

        // Find newlines in all elements and add placeholders.
        $content = $this->replaceWithinHtmlTags($content, array(
            "\n" => " <!-- wpnl --> "
        ));

        // Collapse line breaks before and after <option> elements
        if (strpos($content, '<option') !== false) {
            $content = preg_replace('|\s*<option|', '<option', $content);
            $content = preg_replace('|</option>\s*|', '</option>', $content);
        }

        // Collapse line breaks inside <object> elements, before <param> and <embed> elements
        if (strpos($content, '</object>') !== false) {
            $content = preg_replace('|(<object[^>]*>)\s*|', '$1', $content);
            $content = preg_replace('|\s*</object>|', '</object>', $content);
            $content = preg_replace('%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $content);
        }

        // Collapse line breaks inside <audio> and <video> elements, before and after <source> and <track> elements.
        if (strpos($content, '<source') !== false || strpos($content, '<track') !== false) {
            $content = preg_replace('%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $content);
            $content = preg_replace('%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $content);
            $content = preg_replace('%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $content);
        }

        // Remove more than two contiguous line breaks.
        $content = preg_replace("/\n\n+/", "\n\n", $content);

        // Split up the contents into an array of strings, separated by double line breaks.
        $contentArray = preg_split('/\n\s*\n/', $content, -1, PREG_SPLIT_NO_EMPTY);

        // Reset the content prior to rebuilding.
        $content = '';

        // Rebuild the content as a string, wrapping every bit with a <p>.
        foreach ($contentArray as $value) {
            $content .= '<p>' . trim($value, "\n") . "</p>\n";
        }

        // Under certain strange conditions it could create a P of entirely whitespace.
        $content = preg_replace('|<p>\s*</p>|', '', $content);

        // Add a closing <p> inside <div>, <address>, or <form> tag if missing.
        $content = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $content);

        // If an opening or closing block element tag is wrapped in a <p>, unwrap it.
        $content = preg_replace('!<p>\s*(</?' . $regex . '[^>]*>)\s*</p>!', "$1", $content);

        // In some cases <li> may get wrapped in <p>, fix them.
        $content = preg_replace("|<p>(<li.+?)</p>|", "$1", $content);

        // If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
        $content = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $content);
        $content = str_replace('</blockquote></p>', '</p></blockquote>', $content);

        // If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
        $content = preg_replace('!<p>\s*(</?' . $regex . '[^>]*>)!', "$1", $content);

        // If an opening or closing block element tag is followed by a closing <p> tag, remove it.
        $content = preg_replace('!(</?' . $regex . '[^>]*>)\s*</p>!', "$1", $content);

        // Optionally insert line breaks.
        if ($lineBreaks) {
            $content = $this->insertLineBreaks($content);
        }

        // If a <br /> tag is after an opening or closing block tag, remove it.
        $content = preg_replace('!(</?' . $regex . '[^>]*>)\s*<br />!', "$1", $content);

        // If a <br /> tag is before a subset of opening or closing block tags, remove it.
        $content = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $content);

        $content = preg_replace("|\n</p>$|", '</p>', $content);

        // Replace placeholder <pre> tags with their original content.
        if (!empty($preTags)) {
            $content = str_replace(array_keys($preTags), array_values($preTags), $content);
        }

        // Restore newlines in all elements.
        if (strpos($content, '<!-- wpnl -->') !== false) {
            $content = str_replace(array(' <!-- wpnl --> ', '<!-- wpnl -->'), "\n", $content);
        }

        return $content;
    }

    /**
     * Replace characters or phrases within HTML elements only.
     *
     * @param $haystack
     * @param $replacePairs
     *
     * @return string
     */
    private function replaceWithinHtmlTags($haystack, $replacePairs)
    {
        // Find all elements.
        $regex = '/(<(?(?=!--|!\[CDATA\[)(?(?=!-)!(?:-(?!->)[^\-]*+)*+(?:-->)?|!\[CDATA\[[^\]]*+(?:](?!]>)[^\]]*+)*+(?:]]>)?)|[^>]*>?))/';
        $textArray = preg_split($regex, $haystack, -1, PREG_SPLIT_DELIM_CAPTURE);
        $changed = false;

        // Optimize when searching for one item.
        if (count($replacePairs) === 1) {
            // Extract $needle and $replace.
            foreach ($replacePairs as $needle => $replace);
            // Loop through delimiters (elements) only.
            for ($i = 1, $c = count($textArray); $i < $c; $i += 2) {
                if (strpos($textArray[$i], $needle) !== false) {
                    $textArray[$i] = str_replace($needle, $replace, $textArray[$i]);
                    $changed = true;
                }
            }
        } else {
            // Extract all $needles.
            $needles = array_keys($replacePairs);
            // Loop through delimiters (elements) only.
            for ($i = 1, $c = count($textArray); $i < $c; $i += 2) {
                foreach ($needles as $needle) {
                    if (strpos($textArray[$i], $needle) !== false) {
                        $textArray[$i] = strtr($textArray[$i], $replacePairs);
                        $changed = true;

                        break;
                    }
                }
            }
        }

        if ($changed) {
            $haystack = implode($textArray);
        }

        return $haystack;
    }

    /**
     * Replace relevant placeholder with line breaks.
     *
     * @param $content
     *
     * @return mixed
     */
    private function insertLineBreaks($content)
    {
        // Replace newlines that shouldn't be touched with a placeholder.
        $s = str_replace("\n", "<WPPreserveNewline />", $content);

        $content = preg_replace('/<(script|style).*?<\/\\1>/s', $s, $content);
        // Normalize <br>
        $content = str_replace(array('<br>', '<br/>'), '<br />', $content);
        // Replace any new line characters that aren't preceded by a <br /> with a <br />.
        $content = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $content);
        // Replace newline placeholders with newlines.
        $content = str_replace('<WPPreserveNewline />', "\n", $content);

        return $content;
    }
}
