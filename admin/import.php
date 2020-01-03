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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           1.30
 */

use Xmf\Database\Tables;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Permission;
use Xmf\Request;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';
require_once dirname(__DIR__) . '/include/functions.php';
$thisFile = basename(__FILE__);

$op = Request::getString('op', '');
$ok = Request::getInt('ok', Constants::CONFIRM_NOT_OK, 'POST');

switch ($op) {
    default:
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($thisFile);
        $eformsHelper = Helper::getHelper('eforms');
        $message      = [];
        if (false !== $eformsHelper) {
            $eformsHelper->getModule()->loadInfo('eforms', false);
            $eformsModversion = $eformsHelper->getModule()->modinfo;
            $eformsImage      = $eformsModversion['image'];
            $message[]        = "<a href=\"{$thisFile}?op=eforms\"><img src=\"" . $eformsHelper->url($eformsImage) . "\" style=\"margin-right: 2em;\" alt=\"Import eForms module data\" title=\"Click to import from eForms\"></a>\n";
            //            $message[] = "<a href=\"{$thisFile}?op=eforms\">Import eForms module data</a>\n";
        }
        $liaiseHelper = Helper::getHelper('liaise');
        if (false !== $liaiseHelper) {
            $liaiseHelper->getModule()->loadInfo('liaise', false);
            $liaiseModversion = $liaiseHelper->getModule()->modinfo;
            $liaiseImage      = $liaiseModversion['image'];
            $message[]        = "<a href=\"{$thisFile}?op=liaise\"><img src=\"" . $liaiseHelper->url($liaiseImage) . "\" alt=\"Import Liaise module data\" title=\"Click to import from Liaise\"></a>\n";
            //            $message[] = "<a href=\"{$thisFile}?op=liaise\">Import Liase module data</a>\n";
        }
        if (empty($message)) {
            xoops_error(_AM_XFORMS_ERR_MODULES);
        } else {
            echo "\n<div style=\"text-align: center; margin-bottom: 2em;\"><h4>" . _AM_XFORMS_IMPORT_SELECT . "</div>\n" . "<div style=\"clear: both; width: 100%;\">\n" . "  <div style=\"text-align: center; vertical-align: middle; margin: 0 auto;\">\n";
            foreach ($message as $msg) {
                echo "  {$msg}\n";
            }
            echo "  </div>\n" . '</div>';
        }
        echo '<div style="margin-bottom: 1.2em; "></div>';
        break;
    case 'eforms':
        if ($ok) {
            if (!$xoopsSecurity->check()) {
                redirect_header($thisFile, Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }

            $eformsHelper       = Helper::getHelper('eforms');
            $eformsFormsHandler = $helper->getHandler('Eformsforms');

            if (false !== $eformsFormsHandler) {
                // make sure the eforms database tables exist
                $success   = false;
                $efTables  = $eformsHelper->getModule()->getInfo('tables');
                $tablesObj = new \Xmf\Database\Tables();
                foreach ($efTables as $efTable) {
                    $tableExists = $tablesObj->useTable($efTable);
                    if (!$tableExists) {
                        throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_TABLE_NOT_FOUND, 'eforms', $efTable));
                    }
                }

                // setup eForm handlers
                $eformsElementHandler  = $helper->getHandler('Eformselement');
                $eformsUserdataHandler = $helper->getHandler('Eformsuserdata');

                /*
                 * pseudo code:
                 *  create new xForms form with eForms form attributes
                 *  create new xForms elements using 'new' xForms ID and eForms element 'attributes'
                 *  save eForms user data in xForms user data using new xForms ID
                 *  create new xForms permissions using eForms settings
                 *  copy all uploaded files to xForms uploads folder
                 */

                // create copies of eForm forms in xForm
                $eformsFormObjects = $eformsFormsHandler->getAll();
                $formMap           = [];
                foreach ($eformsFormObjects as $eformsFormObj) {
                    $formAttribs = $eformsFormObj->getValues();
                    $eformsId    = $formAttribs['form_id'];
                    unset($formAttribs['form_id']); // will force new xForm Id
                    $xformsObj = $formsHandler->create();
                    $xformsObj->setVars($formAttribs);
                    $xformsId = $formsHandler->insert($xformsObj);
                    if (!$xformsId) {
                        throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_CREATE_FORM, 'eforms', $eformsId));
                    }
                    $formMap[$eformsId] = $xformsId;
                }

                //copy eForm elements to xForm elements
                $eleMap               = [];
                $xformsElementHandler = $helper->getHandler('Element');
                foreach ($formMap as $eId => $xId) {
                    $eformsElementObjects = $eformsElementHandler->getAll(new \Criteria('form_id', $eId));
                    if (!empty($eformsElementObjects)) {
                        foreach ($eformsElementObjects as $eformsElementObj) {
                            $eleAttribs            = $eformsElementObj->getValues();
                            $eleVars               = $eformsElementObj->getVars();
                            $eleAttribs['form_id'] = $xId;
                            unset($eleAttribs['ele_id']);
                            $xformsElementObj = $xformsElementHandler->create();
                            $xformsElementObj->setVars($eleAttribs);
                            $xformsElementId = $xformsElementHandler->insert($xformsElementObj);
                            if (!$xformsElementId) {
                                throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_CREATE_ELEMENT, 'eforms', $eformsElementObj->getVar('ele_id')));
                            }
                        }
                    }
                }

                // copy user data from eForms to xForms
                $xformsUserdataHandler = $helper->getHandler('Userdata');

                $eformsUdataObjs = $eformsUserdataHandler->getAll();
                if (!empty($eformsUdataObjs)) {
                    foreach ($eformsUdataObjs as $eformsUdataObj) {
                        $xformsUdataObj          = $xformsUserdataHandler->create();
                        $uDataAttribs            = $eformsUdataObj->getValues();
                        $uDataAttribs['form_id'] = $formMap[$eformsUdataObj->getVar('form_id')];
                        $uDataAttribs['ele_id']  = $eleMap[$eformsUdataObj->getVar('ele_id')];
                        $xformUdataObj->setVars($uDataAttribs);
                        $xformsUdataId = $xformsUserdataHandler->insert($xformsUdataObj);
                        if (!$xformUdataId) {
                            throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_EFORMS_CREATE_USERDATA, 'eforms', $eformsUdataObj->getVar('udata_id')));
                        }
                    }
                }

                // get/set form permissions
                $eformsPermHelper = new Permission('eforms');
                $xformsPermHelper = new \Xmf\Module\Helper\Permission($moduleDirName);
                if ($eformsPermHelper && $xformsPermHelper) {
                    $eformsPermName = $eformsFormsHandler->perm_name;
                    $xformsPermName = $formsHandler->perm_name;
                    foreach ($formMap as $eId => $xId) {
                        $groups = $eformsPermHelper->getGroupsForItem($eformsPermName, $eId);
                        $xformsPermHelper->savePermissionForItem($xformsPermName, $xId, $groups);
                    }
                }

                // copy uploaded files to xForms uploads directory
                $xformsUploadDir = $helper->getConfig('uploaddir');
                $eformsUploadDir = $eformsHelper->getConfig('uploaddir');
                $success         = xformsCopyFiles($eformsUploadDir, $xformsUploadDir, ['index.html'], true);
                /*
                                $xformsUploadDir = $helper->getConfig('uploaddir');
                                $xformsUploadDir = ('/' == substr($xformsUploadDir, -1, 1)) ? substr($xformsUploadDir, 0, -1) : $xformsUploadDir;
                                $xformsDirInfo = new \SplFileInfo($xformsUploadDir);
                                $eformsUploadDir = $eformsHelper->getConfig('uploaddir');
                                $eformsUploadDir = ('/' == substr($eformsUploadDir, -1, 1)) ? substr($eformsUploadDir, 0, -1) : $eformsUploadDir;
                                $eformsDirInfo = new \SplFileInfo($eformsUploadDir);
                                // validate they are valid directories
                                if ($xformsDirInfo->isDir() && $eformsDirInfo->isDir) {
                                    $fileList = array_diff(scandir($eformsUploadDir, SCANDIR_SORT_NONE), array('..', '.', 'index.html'));

                                    //now copy the file(s) to the eForms uploads directory
                                    foreach ($fileList as $fileName) {
                                        if (($fileInfo = new \SplFileInfo("{$eformsUploadDir}{$fileName}"))
                                        && ($currFileInfo = new SplFileinf("{$eformsUploadDir}{$fileName}")))
                                        {
                                            copy("{$eformsUploadDir}{$fileName}", "{$eformsUploadDir}{$fileName}");
                                        }
                                    }
                                } else {
                                    // input is not a valid directory
                                    $xoopsModule->setErrors(sprintf(_MI_XFORMS_INST_DIR_NOT_FOUND, htmlspecialchars($directory)));
                                    $success = false;
                                }
                */
                if (false === $success) {
                    throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_COPY_UPLOADS, 'eForms'));
                }
                $helper->redirect('admin/index.php', Constants::REDIRECT_DELAY_MEDIUM, sprintf(_AM_XFORMS_IMPORT_SUCCESS, count($formMap), 'eForms'));
            } else {
                xoops_cp_header();
                $adminObject = \Xmf\Module\Admin::getInstance();
                $adminObject->displayNavigation($thisFile);
                echo "<div class='floatcenter1'>" . xoops_error(sprintf(_AM_XFORMS_ERR_MODULE_NOT_FOUND, 'eForms'), _AM_XFORMS_IMPORT_FAILED) . "</div>\n";
            }
        } else {
            xoops_cp_header();
            $adminObject = \Xmf\Module\Admin::getInstance();
            $adminObject->displayNavigation($thisFile);
            xoops_confirm(['op' => 'eforms', 'ok' => Constants::CONFIRM_OK], $thisFile, sprintf(_AM_XFORMS_RUSUREEFORMS, 'eForms'), _YES);
        }
        break;
    case 'liaise':
        if ($ok) {
            if (!$xoopsSecurity->check()) {
                redirect_header($thisFile, Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }

            $liaiseHelper       = Helper::getHelper('liaise');
            $liaiseFormsHandler = $helper->getHandler('Liaiseforms');
            // make sure the liaise database tables exist
            $success      = false;
            $liaiseTables = $liaiseHelper->getModule()->getInfo('tables');
            $tablesObj    = new \Xmf\Database\Tables();
            foreach ($liaiseTables as $liaiseTable) {
                $tableExists = $tablesObj->useTable($liaiseTable);
                if (!$tableExists) {
                    throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_TABLE_NOT_FOUND, 'liaise', $liaiseTable));
                }
            }

            /*
             * pseudo code:
             *  create new xForms form with Liaise form attributes
             *  create new xForms elements using 'new' xForms ID and Liaise element 'attributes'
             *  create new xForms permissions using eForms settings
             *  copy all uploaded files to xForms uploads folder
             */
            // setup Liaise element handler
            $liaiseElementHandler = $helper->getHandler('Liaiseelement');

            // create copies of Liaise forms in xForm
            $liaiseFormObjects = $liaiseFormsHandler->getAll();
            $formMap           = [];
            foreach ($liaiseFormObjects as $liaiseFormObj) {
                $formAttribs = $liaiseFormObj->getValues();
                $liaiseId    = $formAttribs['form_id'];
                unset($formAttribs['form_id']); // will force new xForm Id
                $xformsObj = $formsHandler->create();
                $xformsObj->setVars($formAttribs);
                $xformsId = $formsHandler->insert($xformsObj);
                if (!$xformsId) {
                    throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_CREATE_FORM, 'liaise', $liaiseId));
                }
                $formMap[$liaiseId] = $xformsId;
            }
            //copy Liaise elements to xForm elements
            $xformsElementHandler = $helper->getHandler('Element');
            foreach ($formMap as $liaiseId => $xId) {
                $liaiseElementObjects = $liaiseElementHandler->getAll(new \Criteria('form_id', $liaiseId));
                foreach ($liaiseElementObjects as $liaiseElementObj) {
                    $eleAttribs = $liaiseElementObj->getValues();
                    unset($eleAttribs['ele_id']);  // unset element Id, will be assigned automatically
                    $eleAttribs['form_id'] = (int)$xId; // set new form Id
                    // need to convert {EMAIL}, {NAME}, or {UNAME} to {U_email}, {U_name} and {U_uname}
                    if ('text' === $eleAttribs['ele_type']) {
                        $patternArray = ["/\{UNAME\}/", "/\{EMAIL\}/", "/\{NAME\}/"];
                        $replaceArray = ['{U_uname}', '{U_email}', '{U_name}'];
                        if (!is_array($eleAttribs['ele_value'])) {
                            $eleAttribs['ele_value'] = base64_decode($eleAttribs['ele_value'], true);
                        }
                        $eleAttribs['ele_value'][2] = preg_replace($patternArray, $replaceArray, $eleAttribs['ele_value'][2]);
                    }

                    $xformsElementObj = $xformsElementHandler->create();
                    $xformsElementObj->setVars($eleAttribs);
                    $xformsElementId = $xformsElementHandler->insert($xformsElementObj);
                    if (!$xformsElementId) {
                        throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_CREATE_ELEMENT, 'liaise', $liaiseElementObj->getVar('ele_id')));
                    }
                }
            }

            // get/set form permissions
            $liaisePermHelper = new Permission('liaise');
            $xformsPermHelper = new \Xmf\Module\Helper\Permission($moduleDirName);
            if ($liaisePermHelper && $xformsPermHelper) {
                $liaisePermName = $liaiseFormsHandler->perm_name;
                $xformsPermName = $formsHandler->perm_name;
                foreach ($formMap as $lId => $xId) {
                    $groups = $liaisePermHelper->getGroupsForItem($liaisePermName, $lId);
                    $xformsPermHelper->savePermissionForItem($xformsPermName, $xId, $groups);
                }
            }
            // copy uploaded files to xForms uploads directory
            $xformsUploadDir = $helper->getConfig('uploaddir');
            $liaiseUploadDir = $liaiseHelper->getConfig('uploaddir');
            $success         = xformsCopyFiles($liaiseUploadDir, $xformsUploadDir, ['index.html'], true);
            /*
                        $xformsUploadDir = $helper->getConfig('uploaddir');
                        $xformsUploadDir = ('/' == substr($xformsUploadDir, -1, 1)) ? substr($xformsUploadDir, 0, -1) : $xformsUploadDir;
                        $xformsDirInfo = new \SplFileInfo($xformsUploadDir);
                        $liaiseUploadDir = $liaiseHelper->getConfig('uploaddir');
                        $liaiseUploadDir = ('/' == substr($liaiseUploadDir, -1, 1)) ? substr($liaiseUploadDir, 0, -1) : $liaiseUploadDir;
                        $liaiseDirInfo = new \SplFileInfo($liaiseUploadDir);

                        // validate they are valid directories
                        if ($xformsDirInfo->isDir() && $liaiseDirInfo->isDir) {
                            $fileList = array_diff(scandir($liaiseUploadDir, SCANDIR_SORT_NONE), array('..', '.', 'index.html'));

                            //now copy the file(s) to the xForms uploads directory
                            foreach ($fileList as $fileName) {
                                if (($fileInfo = new \SplFileInfo("{$liaiseUploadDir}{$fileName}"))
                                   && ($currFileInfo = new SplFileinf("{$xformsUploadDir}{$fileName}")))
                                {
                                    copy("{$liaiseUploadDir}{$fileName}", "{$xformsUploadDir}{$fileName}");
                                }
                            }
                        } else {
                            // input is not a valid directory
                            $xoopsModule->setErrors(sprintf(_MI_XFORMS_INST_DIR_NOT_FOUND, htmlspecialchars($directory)));
                            $success = false;
                        }
            */
            if (!$success) {
                throw new \RuntimeException(sprintf(_AM_XFORMS_ERR_COPY_UPLOADS, 'Liaise'));
            }
            $helper->redirect('admin/index.php', Constants::REDIRECT_DELAY_MEDIUM, sprintf(_AM_XFORMS_IMPORT_SUCCESS, count($formMap), 'Liaise'));
        } else {
            xoops_cp_header();
            $adminObject = \Xmf\Module\Admin::getInstance();
            $adminObject->displayNavigation($thisFile);
            xoops_confirm(['op' => 'liaise', 'ok' => 1], $thisFile, sprintf(_AM_XFORMS_RUSUREEFORMS, 'Liaise'), _YES);
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
