<?php

namespace XoopsModules\Xforms;

/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Module: xForms
 *
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @author          Mamba, ZySpec
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           2.00
 */

use XoopsModules\Xforms;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

    /**
     * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
     * www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags
     * www.cakephp.org
     *
     * @param string $text         String to truncate.
     * @param int    $length       Length of returned string, including ellipsis.
     * @param string $ending       Ending to be appended to the trimmed string.
     * @param bool   $exact        If false, $text will not be cut mid-word
     * @param bool   $considerHtml If true, HTML tags would be handled correctly
     *
     * @return string Trimmed string.
     */
    public static function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (mb_strlen(preg_replace('/<.*?' . '>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?' . '>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = mb_strlen($ending);
            $open_tags    = [];
            $truncate     = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags, true);
                        if (false !== $pos) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?' . '>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left            = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($left >= $entity[1] + 1 - $entities_length) {
                                $left--;
                                $entities_length += mb_strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= mb_substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                }
                $truncate     .= $line_matchings[2];
                $total_length += $content_length;

                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            }
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    /**
     * Callback function to convert item to integer
     *
     * @param mixed $item
     *
     * @return void
     */
    public static function intArray(&$item)
    {
        $item = (int)$item;
    }

    /**
     * @param $var
     * @return mixed
     */
    public static function quoteThisString($var)
    {
        return $GLOBALS['xoopsDB']->escape($var);
    }

    /**
     * @param        $key
     * @param int    $id
     * @param string $caption
     *
     * @return bool|string false on error | string for 'other' element
     * @global       array err - used to keep error messages
     * @global array $_POST
     *
     * @todo refactor code to eliminate use of 'global $err' to track errors
     */
    public static function checkOther($key, $id, $caption)
    {
        $myts = \MyTextSanitizer::getInstance();
        $id   = (int)$id;
        global $err;
        if (!preg_match('/\{OTHER\|+\d+\}/', $key)) {
            return false;
        }
        if (!empty($_POST['other']["ele_{$id}"])) {
            return _MD_XFORMS_OPT_OTHER . $_POST['other']["ele_{$id}"];
        }
        $err[] = sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption));

        return false;
    }

    /**
     * function used for smarty output of csv files
     * @param unknown $tpl_output
     * @return string
     */
    public function undoHtmlEntities($tpl_output)
    {
        return html_entity_decode($tpl_output);
    }
}
