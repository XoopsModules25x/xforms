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
 * @since           1.30
 */
use Xmf\Module\Admin;

include_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$moduleAdmin = Admin::getInstance();
$moduleAdmin->displayNavigation(basename(__FILE__));
//Admin::setPaypal('xoopsfoundation@gmail.com');
$moduleAdmin->displayAbout(false);

include __DIR__ . '/admin_footer.php';
