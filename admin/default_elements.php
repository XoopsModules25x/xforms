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
 * @copyright       {@see http://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             http://xoops.org XOOPS
 * @since           1.30
 */
use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$xformsHelper = Helper::getHelper(basename(dirname(__DIR__)));

$defaults = array(
    0 => array(
        'caption'         => _AM_XFORMS_DEFAULT_ELE_YOURNAME,
        'req'             => true,
        'ele_display_row' => 1,
        'order'           => 1,
        'display'         => 1,
        'type'            => 'text',
        'value'           => array(
            0 => $xformsHelper->getConfig('t_width'),
            1 => $xformsHelper->getConfig('t_max'),
            2 => '{U_uname}'
        )
    ),

    1 => array(
        'caption'         => _AM_XFORMS_DEFAULT_ELE_YOUREMAIL,
        'req'             => true,
        'ele_display_row' => 1,
        'order'           => 2,
        'display'         => 1,
        'type'            => 'text',
        'value'           => array(
            0 => $xformsHelper->getConfig('t_width'),
            1 => $xformsHelper->getConfig('t_max'),
            2 => '{U_email}'
        )
    ),

    2 => array(
        'caption'         => _AM_XFORMS_DEFAULT_ELE_COMMENTS,
        'req'             => true,
        'ele_display_row' => 1,
        'order'           => 3,
        'display'         => 1,
        'type'            => 'textarea',
        'value'           => array(
            0 => '',
            1 => $xformsHelper->getConfig('ta_rows'),
            2 => $xformsHelper->getConfig('ta_cols')
        )
    )
);
