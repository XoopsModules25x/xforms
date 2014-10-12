<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

if ( !defined('xforms_ROOT_PATH') ) { exit(); }

 if ($value[0] != "Y-m-d" && $value[0] != "") {
         $date_value = strtotime($value[0]);
 } else {
         $date_value = "";
 }
 $date = new XoopsFormTextDateSelect (_AM_XFORMS_ELE_DATE, 'ele_value',  $size = 15, $date_value);
 $output->addElement($date);
