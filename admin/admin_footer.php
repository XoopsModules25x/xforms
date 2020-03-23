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
 * Admin footer file
 *
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright       Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 *
 * @see \Xmf\Module\Admin
 */

echo '<div class="adminfooter">'
   . '<div class="center">'
   . '  <a href="https://www.xoops.org" rel="noopener external" target="_blank"><img src="' . \Xmf\Module\Admin::iconUrl('xoopsmicrobutton.gif') . '" ' . 'alt="XOOPS" title="XOOPS"></a>'
   . '</div>' . _AM_MODULEADMIN_ADMIN_FOOTER . '</div>';

xoops_cp_footer();
