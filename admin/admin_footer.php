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

use XoopsModules\Xforms;

/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::iconUrl('', 32);

$language = empty($GLOBALS['xoopsConfig']['language']) ? 'english' : $GLOBALS['xoopsConfig']['language'];
if (file_exists($fileinc = $GLOBALS['xoops']->path("Frameworks/moduleclasses/moduleadmin/language/{$language}/main.php"))) {
    require_once $fileinc;
} elseif (file_exists($fileinc = $GLOBALS['xoops']->path('Frameworks/moduleclasses/moduleadmin/language/english/main.php'))) {
    require_once $fileinc;
}

echo "<div class='adminfooter'>\n" . "  <div class='center'>\n" . "    <a href='https://xoops.org' rel='external'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n" . "  </div>\n" . '  ' . _AM_MODULEADMIN_ADMIN_FOOTER . "\n" . "</div>\n";

xoops_cp_footer();
