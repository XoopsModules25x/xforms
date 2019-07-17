<?php
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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 */

use Xmf\Module\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

$xformsHelper = Helper::getHelper(basename(dirname(dirname(__DIR__))));

/**
 * Email element
 *
 * value
 *      [0] = element rendered box size
 *      [1] = maximum size length
 */
$size      = !empty($value[0]) ? (int)$value[0] : $xformsHelper->getConfig('t_width');
$max       = !empty($value[1]) ? (int)$value[1] : $xformsHelper->getConfig('t_max');
$sizeInput = new XformsFormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 3, $size, null, 'number');
$sizeInput->setAttribute('min', 1);
$sizeInput->setExtra('style="width: 5em;"');

$maxInput = new XformsFormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 3, 3, $max, null, 'number');
$maxInput->setAttribute('min', 1);
$maxInput->setExtra('style="width: 5em;"');

$output->addElement($sizeInput);
$output->addElement($maxInput);
