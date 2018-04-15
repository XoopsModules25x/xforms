<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

//require_once   dirname(dirname(dirname(__DIR__))) . '/mainfile.php';


function getConfig()
{
    return (object)[
        'name'          => strtoupper($moduleDirName) . ' Module Configurator',
        'paths'         => [
            'dirname'    => $moduleDirName,
            'admin'      => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/admin',
            //        'path'       => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName,
            //        'url'        => XOOPS_URL . '/modules/' . $moduleDirName,
            'uploadPath' => XOOPS_UPLOAD_PATH . '/' . $moduleDirName,
            'uploadUrl'  => XOOPS_UPLOAD_URL . '/' . $moduleDirName,
        ],
        'uploadFolders' => [
            constant($capsDirName . '_UPLOAD_PATH'),
            constant($capsDirName . '_UPLOAD_PATH') . '/category',
            constant($capsDirName . '_UPLOAD_PATH') . '/screenshots',
            XOOPS_UPLOAD_PATH . '/flags'
        ],
        'blankFiles'    => [
            constant($capsDirName . '_UPLOAD_PATH'),
            constant($capsDirName . '_UPLOAD_PATH') . '/category',
            constant($capsDirName . '_UPLOAD_PATH') . '/screenshots',
            XOOPS_UPLOAD_PATH . '/flags'
        ],

        'templateFolders' => [
            '/templates/',
            '/templates/blocks/',
            '/templates/admin/'

        ],
        'oldFiles'        => [
            '/sql/wflinks.sql',
            '/class/wfl_lists.php',
            '/class/class_thumbnail.php',
            '/vcard.php',
        ],
        'oldFolders'      => [
            '/images',
            '/css',
            '/js',
            '/tcpdf',
            '/images',
        ],
        'modCopyright'    => "<a href='https://xoops.org' title='XOOPS Project' target='_blank'>
                     <img src='" . constant($capsDirName . '_AUTHOR_LOGOIMG') . '\' alt=\'XOOPS Project\' /></a>',
    ];
}
