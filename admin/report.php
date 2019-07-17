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
use Xmf\Request;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';
$myts = \MyTextSanitizer::getInstance();
xoops_load('XoopsLocal');

$thisFileName = basename(__FILE__);

$op      = Request::getCmd('op', '');
$ok      = Request::getBool('ok', false, 'POST');
$format  = Request::getString('format', 'v', 'GET');
$formId  = Request::getInt('form_id', 0, 'GET');
$showAll = Request::getBool('showall', false, 'POST');

if (empty($formId) && (!empty($_POST['op']) && !preg_match('/^purge(_do)*$/', $op))) {
    $op = '';
}

switch ($op) {
    case 'show': /*Show the report in the page*/
        // get the UserData to see if there's any reports
        if ((!$form = $xformsFormsHandler->get($formId)) || $form->isNew()) {
            $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
        } elseif (Constants::DO_NOT_SAVE_IN_DB == $form->getVar('form_save_db')) {
            $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTSAVE);
        }

        $uDataHandler = $helper->getHandler('Userdata');
        $uData        = $uDataHandler->getReport($formId);
        if (empty($uData)) { // is there anything to report?
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_RPT_NODATA);
        }

        /* Now show the report */
        xoops_cp_header();
        $adminObject = Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($thisFileName);
        $adminObject->addItemButton(_AM_XFORMS_REPORT_PURGE, "{$thisFileName}?op=purge", 'delete');
        $adminObject->displayButton('left', '');

        echo "<table class='outer width100 bspacing1'>\n"
             . "  <thead>\n"
             . "  <tr>\n"
             . "    <th colspan='6'>"
             . _AM_XFORMS_REPORT_FORM
             . ': '
             . $form->getVar('form_title')
             . "</th>\n"
             . "  </tr>\n"
             . "  <tr>\n"
             . "    <th class='head center width10'>"
             . _AM_XFORMS_NO
             . "</td>\n"
             . "    <th class='head center width10'>"
             . _AM_XFORMS_RPT_USER
             . "</th>\n"
             . "    <th class='head center width10'>"
             . _AM_XFORMS_RPT_DATETIME
             . "</th>\n"
             . "    <th class='head center width10'>"
             . _AM_XFORMS_RPT_IP
             . "</th>\n"
             . "    <th class='head center width30'>"
             . _AM_XFORMS_RPT_QUESTION
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_RPT_ANSWER
             . "</th>\n"
             . "  </tr>\n"
             . "  </thead>\n"
             . "  <tbody>\n";

        $countu = $dproc = 0;
        $ipproc = '';
        //        $firstRow   = true;

        foreach ($uData as $data) {
            $dtime  = $data['udata_time'];
            $ipuser = $data['udata_ip'];
            if ((0 == $dproc) || ($dproc != $dtime || $ipproc != $ipuser)) {
                //                $firstRow = true;
                $border = " style='border-top: 2px solid #000000;'";
                ++$countu;
                $ucount = $countu;
                $uid    = $data['uid'];
                $uname  = $myts->htmlSpecialChars($data['uname']);
                //                $uname  = XoopsUser::getUnameFromId($uid);  // replaced to reduce dB calls
                $datet = XoopsLocal::formatTimestamp($dtime, 'l');
                $uip   = $data['udata_ip'];
            } else {
                $border = '';
                $uname  = $datet = $uip = $ucount = '&nbsp;';
            }
            $dproc  = $dtime;
            $ipproc = $ipuser;

            $cssClass   = (empty($cssClass) || 'even' === $cssClass) ? 'odd' : 'even';
            $eleCaption = $myts->displayTarea($data['ele_caption'], Constants::ALLOW_HTML);
            echo "  <tr class='{$cssClass}'>\n"
                 . "    <td{$border} class='center' nowrap>{$ucount}</td>\n"
                 . "    <td{$border} class='center' nowrap>{$uname}</td>\n"
                 . "    <td{$border} class='center' nowrap>{$datet}</td>\n"
                 . "    <td{$border} class='center'>{$uip}</td>\n"
                 . "    <td{$border}>{$eleCaption}</td>\n";
            /*
                        $uDataValue = unserialize($data['udata_value'][0]);
                        $uDataValue = array_map('base64_decode', $uDataValue);
            */
            $uDataValue = $data['udata_value'];

            if (is_array($uDataValue)) {
                switch ($data['ele_type']) {
                    case 'color':
                    case 'country':
                    case 'email':
                    case 'number':
                    case 'obfuscated':
                    case 'pattern':
                    case 'range':
                    case 'select2': // left for backward compatibility
                    case 'text':
                    case 'textarea':
                    case 'time':
                    case 'url':
                    case 'yn':
                        echo "    <td{$border}>{$uDataValue[0]}</td>\n";
                        break;
                    case 'upload':
                    case 'uploadimg':
                        echo "    <td{$border}>\n" . "      <a href='" . $helper->url("file.php?f={$uDataValue['file']}&fn={$uDataValue['name']}") . "'>{$uDataValue['name']}</a>\n" . "   </td>\n";
                        break;
                    case 'checkbox':
                    case 'date':
                    case 'radio':
                    case 'select':
                        echo "    <td{$border}>";
                        if (is_array($uDataValue)) {
                            foreach ($uDataValue as $dValue) {
                                echo "{$dValue}<br>";
                            }
                        } else {
                            echo $uDataValue;
                        }
                        echo "</td>\n";
                        break;
                    default:
                        echo "    <td{$border}>&nbsp;</td>\n";
                        break;
                }
            } else {
                echo "    <td{$border}>&nbsp;</td>\n";
            }

            echo "  </tr>\n";
            //            $firstRow = false;
        }
        echo "  </tbody>\n" . "  <tfoot>\n" . "  <tr><td class='foot center' colspan='6'>";
        $bexportch = new \XoopsFormButton('', 'export-ch', _AM_XFORMS_RPT_EXPORT_CH, 'button');
        $bexportch->setExtra(" onclick=\"window.location='{$thisFileName}?op=export-horiz&format=c&form_id={$formId}'\"");
        $bexporthh = new \XoopsFormButton('', 'export_hh', _AM_XFORMS_RPT_EXPORT_HH, 'button');
        $bexporthh->setExtra(" onclick=\"window.location='{$thisFileName}?op=export-horiz&format=h&form_id={$formId}'\"");
        $bexportcv = new \XoopsFormButton('', 'export-cv', _AM_XFORMS_RPT_EXPORT_CV, 'button');
        $bexportcv->setExtra(" onclick=\"window.location='{$thisFileName}?op=export-vert&format=c&form_id={$formId}'\"");
        $bexporthv = new \XoopsFormButton('', 'export_hv', _AM_XFORMS_RPT_EXPORT_HV, 'button');
        $bexporthv->setExtra(" onclick=\"window.location='{$thisFileName}?op=export-vert&format=h&form_id={$formId}'\"");
        echo $bexportch->render() . $bexporthh->render() . $bexportcv->render() . $bexporthv->render();
        echo "</td></tr>\n" . "  </tfoot>\n" . "</table>\n";
        break;
    case 'purge':
        xoops_cp_header();
        $adminObject = Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($thisFileName);

        //setup date selector
        $outputForm  = new \XoopsThemeForm('Purge Reports', 'purge_report_form', $thisFileName, 'post', true);
        $defaultDate = new DateTime(); //set to today
        $defaultDate->setTime(0, 0, 0); //set to midnight
        $outputForm->addElement(new \XoopsFormDateTime('Reports older than', 'purge_date', 10, $defaultDate->getTimestamp(), false));
        $outputForm->addElement(new \XoopsFormButtonTray('purge_buttons', _SUBMIT, 'submit'));
        $outputForm->addElement(new \XoopsFormHidden('op', 'purge_do'));
        $outputForm->addElement(new \XoopsFormHidden('ok', 0));
        $outputForm->display();
        break;
    case 'purge_do':
        if ($ok) {
            //security check to make sure came from a good location
            if (!$xoopsSecurity->check()) {
                $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            // ok - delete reports
            xoops_cp_header();
            $purgeDate = Request::getString('purge_date', [], 'POST');
            $purgeDate = unserialize($purgeDate);

            $purgeDateTimeObj = DateTime::createFromFormat(_SHORTDATESTRING, $purgeDate['date']);
            $purgeDateTimeObj->setTime(0, 0, 0);
            $pDTtimestamp = $purgeDateTimeObj->getTimestamp();

            $uDataHandler = $helper->getHandler('Userdata');
            $numItems     = $uDataHandler->deleteAll(new \Criteria('udata_time', $pDTtimestamp, '<'));
            if ($numItems > 0) {
                $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, sprintf(_AM_XFORMS_RPT_PURGE_DELETED, (int)$numItems));
            } elseif (0 === $numItems) {
                $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_RPT_PURGE_NOTHING);
            } else {
                $helper->redirect("admin/{$thisFileName}", Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_RPT_PURGE_ERR);
            }
        } else {
            xoops_cp_header();
            $purgeDate = Request::getArray('purge_date', ['date' => date(_SHORTDATESTRING), 'time' => '0'], 'POST');
            $theDate   = array_key_exists('date', $purgeDate) ? $purgeDate['date'] : date(_SHORTDATESTRING);
            $purgeDate = serialize($purgeDate);
            xoops_confirm(['op' => 'purge_do', 'purge_date' => $purgeDate, 'ok' => Constants::CONFIRM_OK], $thisFileName, sprintf(_AM_XFORMS_REPORT_CONFIRM_DELETE, $theDate));
        }
        break;
    case 'export-horiz':
        if ((!$form = $xformsFormsHandler->get($formId)) && $form->isNew()) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
        } elseif (0 == $form->getVar('form_save_db')) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTSAVE);
        }

        $uDataHandler = $helper->getHandler('Userdata');
        $uData        = $uDataHandler->getReport($formId);
        if (empty($uData)) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_RPT_NODATA);
        }

        /*Disable debug*/
        error_reporting(0);
        $GLOBALS['xoopsLogger']->activated = false;

        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xformsTpl = new \XoopsTpl();

        $xformsTpl->assign('form_title', $form->getVar('form_title'));
        $xformsTpl->assign('delim', ','); //force delimiter for now

        $xformsEleHandler = $helper->getHandler('Element');
        $criteria         = new \CriteriaCompo();
        $criteria->add(new \Criteria('form_id', $form->getVar('form_id')), 'AND');
        $criteria->add(new \Criteria('ele_display', Constants::ELEMENT_DISPLAY), 'AND');
        $criteria->setSort('ele_order');
        $criteria->setOrder('ASC');
        $elements = $xformsEleHandler->getObjects($criteria, true);
        $eleCount = count($elements);
        foreach ($elements as $el) {
            $xformsTpl->append('captions', $myts->displayTarea($el->getVar('ele_caption'), Constants::ALLOW_HTML));
        }

        $dl     = new stdClass();
        $format = mb_strtolower($format);
        switch ($format) {
            case 'h': //html
                $xformsTpl->assign('col_count', $eleCount + 3);
                $dl->_template = $helper->path('templates/admin/xforms_export_hh.tpl');
                $dl->_ext      = 'html';
                $dl->_type     = 'horiz';
                $dl->_nl       = '<br>';
                $dl->_mime     = 'text/html';
                break;
            case 'c':
            default:
                $xformsTpl->register_outputfilter('xformsUndoHtmlEntities');
                $dl->_template = $helper->path('templates/admin/xforms_export_ch.tpl');
                $dl->_ext      = 'csv';
                $dl->_type     = 'horiz';
                $dl->_nl       = "\n";
                $dl->_mime     = 'text/csv';
                break;
        }

        $rptArray      = $tmpTpl = [];
        $rptTime       = null;
        $rptIp         = '';
        $blankEleArray = array_fill_keys(array_keys($elements), '&nbsp;');
        $uDataCount    = count($uData);
        foreach ($uData as $key => $reportData) {
            $reportDataValue = $reportData['udata_value'];
            if (((int)$reportData['udata_time'] !== $rptTime) || ($reportData['udata_ip'] != $rptIp)) {
                $rptTime        = (int)$reportData['udata_time'];
                $rptIp          = (string)$reportData['udata_ip'];
                $rptArray[$key] = [
                    'user'     => $reportData['uname'],
                    'time'     => $rptTime,
                    'ip'       => $rptIp,
                    'elements' => $blankEleArray,
                ];
                $lineKey        = $key;
            }
            if (is_array($reportDataValue)) {
                switch ($reportData['ele_type']) {
                    case 'color':
                    case 'number':
                    case 'obfuscated':
                    case 'pattern':
                    case 'text':
                    case 'textarea':
                    case 'url':
                    case 'yn':
                        $rptEleValue = $reportDataValue[0];
                        break;
                    case 'upload':
                    case 'uploadimg':
                        $rptEleValue = "<a href='" . $helper->url("file.php?f={$reportDataValue['file']}&fn={$reportDataValue['name']}") . "'>{$reportDataValue['name']}</a>";
                        break;
                    case 'checkbox':
                    case 'country':
                    case 'date':
                    case 'radio':
                    case 'select':
                    case 'select2': // left for backward compatibility
                        $rptEleValue = implode(', ', $reportDataValue);
                        break;
                    default:
                        $rptEleValue = '&nbsp;';
                        break;
                }
            } else {
                $rptEleValue = '&nbsp;';
            }

            $rptArray[$lineKey]['elements'][$reportData['ele_id']] = $rptEleValue;
        }
        $xformsTpl->assign('rptArray', $rptArray);

        $tableData = $xformsTpl->fetch($dl->_template);
        //        header('Content-Type: application/force-download');
        header('Content-Type: ' . $dl->_mime); // don't lie to browser, hope they do the right thing
        header("Content-Disposition: attachment; filename=report_form_{$formId}_{$dl->_type}.{$dl->_ext}");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Transfer-Encoding: binary');
        echo $tableData;
        exit();
        break;
    case 'export-vert':
        if ((!$form = $xformsFormsHandler->get($formId)) && $form->isNew()) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
        } elseif (0 == $form->getVar('form_save_db')) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTSAVE);
        }

        $uDataHandler = $helper->getHandler('Userdata');
        $uData        = $uDataHandler->getReport($formId);
        if (empty($uData)) {
            redirect_header($thisFileName, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_RPT_NODATA);
        }

        /*Disable debug*/
        error_reporting(0);
        $GLOBALS['xoopsLogger']->activated = false;

        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xformsTpl = new \XoopsTpl();
        $xformsTpl->assign('form_title', $form->getVar('form_title'));
        $xformsTpl->assign('delim', ','); //force delimiter for now
        $countu     = $dproc = 0;
        $ipproc     = '';
        $firstRow   = true;
        $uDataCount = count($uData);

        $dl     = new stdClass();
        $format = mb_strtolower($format);
        switch ($format) {
            case 'h': //html
                $dl->_template = $helper->path('templates/admin/xforms_export_hv.tpl');
                $dl->_ext      = 'html';
                $dl->_type     = 'vert';
                $dl->_nl       = '<br>';
                $dl->_mime     = 'text/html';
                break;
            case 'c': //csv
            default:
                $xformsTpl->register_outputfilter('xformsUndoHtmlEntities');
                $dl->_template = $helper->path('templates/admin/xforms_export_cv.tpl');
                $dl->_ext      = 'csv';
                $dl->_type     = 'vert';
                $dl->_nl       = "\n";
                $dl->_mime     = 'text/csv';
                break;
        }

        for ($i = 0; $i < $uDataCount; ++$i) {
            $border = '';
            $dtime  = (int)$uData[$i]['udata_time'];
            $ipuser = $uData[$i]['udata_ip'];
            if ((0 != $dproc) && ($dproc != $dtime || $ipproc != $ipuser)) {
                $firstRow = true;
                $border   = ' border-top: 2px solid #000000;';
            }
            $dproc  = $dtime;
            $ipproc = $ipuser;
            $uname  = $datet = $uip = $ucount = '';
            if ($firstRow) {
                ++$countu;
                $ucount = $countu;
                $uname  = $uData[$i]['uname'];
                $datet  = date('d-m-Y H:i:s', $dtime);
                $uip    = $ipuser;
            }
            $eleCaption  = $myts->displayTarea($uData[$i]['ele_caption'], Constants::ALLOW_HTML);
            $tplElements = [
                'border'      => $border,
                'ucount'      => $ucount,
                'uname'       => $uname,
                'datet'       => $datet,
                'uip'         => $uip,
                'ele_caption' => $eleCaption,
            ];
            $uDataValue  = $uData[$i]['udata_value'];
            if (is_array($uDataValue)) {
                switch ($uData[$i]['ele_type']) {
                    case 'color':
                    case 'number':
                    case 'obfuscated':
                    case 'pattern':
                    case 'text':
                    case 'textarea':
                    case 'url':
                    case 'yn':
                        $tplElements['ele_value'] = $uDataValue[0];
                        break;
                    case 'upload':
                    case 'uploadimg':
                        $tplElements['ele_value'] = "<a href='" . $helper->url("file.php?f={$uDataValue['file']}&fn={$uDataValue['name']}") . "'>{$uDataValue['name']}</a>";
                        break;
                    case 'checkbox':
                    case 'country':
                    case 'date':
                    case 'radio':
                    case 'select':
                    case 'select2': // left for backward compatibility
                        if (is_array($uDataValue)) {
                            $first = true;
                            foreach ($uDataValue as $dValue) {
                                $nl                       = $first ? '' : $dl->nl;
                                $tplElements['ele_value'] = "{$nl}{$dValue}";
                            }
                        } else {
                            $tplElements['ele_value'] = $uDataValue;
                        }
                        break;
                    default:
                        $tplElements['ele_value'] = '&nbsp;';
                        break;
                }
            } else {
                $tplElements['ele_value'] = '&nbsp;';
            }
            $firstRow = false;
            $xformsTpl->append('elements', $tplElements);
        }
        $tableData = $xformsTpl->fetch($dl->_template);
        //        header('Content-Type: application/force-download');
        header('Content-Type: ' . $dl->_mime); // don't lie to browser, hope they do the right thing
        header("Content-Disposition: attachment; filename=report_form_{$formId}_{$dl->_type}.{$dl->_ext}");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Transfer-Encoding: binary');
        echo $tableData;
        exit();
        break;
    default: /* Show list of forms with reports */
        xoops_cp_header();
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url("browse.php?modules/{$moduleDirName}/assets/css/style.css"));

        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation($thisFileName);

        // first get forms that have data in the Userdata table
        $uDataHandler = $helper->getHandler('Userdata');
        $fields       = ['form_id'];
        $criteria     = new \CriteriaCompo();
        $criteria->setGroupBy('form_id');
        $uDataForms = $uDataHandler->getAll($criteria, $fields, false, false);
        $formList   = [];
        foreach ($uDataForms as $uData) {
            $formList[] = (int)$uData['form_id'];
        }

        //now get all those forms from the Form table
        $perpage = (int)$helper->getConfig('perpage'); // get number of items to show per page

        $xformsDisplay          = new stdClass();
        $xformsDisplay->start   = Request::getInt('start', 0);
        $xformsDisplay->perpage = ($perpage > 0) ? $perpage : Constants::FORMS_PER_PAGE_DEFAULT;
        $xformsDisplay->order   = 'ASC';
        $xformsDisplay->sort    = 'form_order';

        $criteria = new \CriteriaCompo();
        if (!$showAll) {
            $criteria->add(new \Criteria('form_active', Constants::FORM_ACTIVE));
        }
        $criteria->add(new \Criteria('form_id', '(' . implode(',', $formList) . ')', 'IN'));
        $criteria->setSort($xformsDisplay->sort);
        $criteria->setOrder($xformsDisplay->order);
        $ttlFormCount = $xformsFormsHandler->getCount($criteria); // count all forms with reports
        // now get the forms we want
        $criteria->setStart($xformsDisplay->start);
        $criteria->setLimit($xformsDisplay->perpage);
        $forms = $xformsFormsHandler->getAll($criteria);

        $formList = "<select name='form_id' id='inputSel' size='1' style='width: 25em;'>\n";
        foreach ($forms as $formItem) {
            $formList .= "<option value='" . $formItem->getVar('form_id') . "'>" . $formItem->getVar('form_title', 's') . "</option>\n";
        }
        $formList .= "</select>\n";

        $formsCount = ((false === $forms) && (count($forms) <= 0)) ? 0 : count($forms);
        if ($formsCount > 0) {
            $adminObject->addItemButton(_AM_XFORMS_REPORT_PURGE, "{$thisFileName}?op=purge", 'delete');
            $adminObject->displayButton('left', '');

            echo "<table class='outer'><tr><td>\n"
                 . "<form action='{$thisFileName}' method='GET'>\n"
                 . "  <input type='hidden' name='op' value='show'>\n"
                 . "  <table class='outer width100 bspacing1'>\n"
                 . "    <tr>\n"
                 . "      <td class='foot left'>\n"
                 . "        <label for='inputID'>"
                 . _AM_XFORMS_ENTER_ID
                 . "</label>\n"
                 . "        <input type='number' name='form_id' size='5' min='1' id='inputID' style='width: 5em;'>\n"
                 . "        <input type='submit' value='"
                 . _AM_XFORMS_SHOW_REPORT
                 . "'>\n"
                 . "      </td>\n"
                 . "    </tr>\n"
                 . "  </table>\n"
                 . "</form>\n"
                 . "</td</tr>\n"
                 . "<form action='{$thisFileName}' method='GET'>\n"
                 . "  <input type='hidden' name='op' value='show'>\n"
                 . "  <table class='outer width100 bspacing1'>\n"
                 . "    <tr>\n"
                 . "      <td class='foot left'>\n"
                 . "        <label for='inputSel'>"
                 . _AM_XFORMS_SHOW_BY_TITLE
                 . "</label>\n"
                 . "          {$formList}\n"
                 . "        <input type='submit' value='"
                 . _AM_XFORMS_SHOW_REPORT
                 . "'>\n"
                 . "      </td>\n"
                 . "    </tr>\n"
                 . "  </table>\n"
                 . "</form>\n"
                 . "</td</tr>\n"
                 . "<tr><td>\n"
                 . '</table>';
        }

        //Now display the form listing table
        echo "<form action='{$thisFileName}' method='POST'>\n";
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "  <table class='outer width100 bspacing1'>\n"
             . "    <thead>\n"
             . "    <tr><th class='middle line140' colspan='5'>"
             . _AM_XFORMS_LISTING
             . "</th></tr>\n"
             . "    <tr>\n"
             . "      <td class='head center bottom width5'>"
             . _AM_XFORMS_ID
             . "</td>\n"
             . "      <td class='head center bottom'>"
             . _AM_XFORMS_TITLE
             . "</td>\n"
             . "      <td class='head center bottom width5'>"
             . _AM_XFORMS_STATUS
             . "</td>\n"
             . "      <td class='head center bottom width15'>"
             . _AM_XFORMS_SENDTO
             . "</td>\n"
             . "      <td class='head center bottom width10'>"
             . _AM_XFORMS_ACTION
             . "</td>\n"
             . "    </tr>\n"
             . "    </thead>\n"
             . "    <tbody>\n";

        if ($forms) {
            $cssClass = 'even';
            $groupMgr = xoops_getHandler('group');
            foreach ($forms as $f) {
                if ($showAll || $f->isActive()) {
                    $id     = $f->getVar('form_id');
                    $sendTo = $f->getVar('form_send_to_group');
                    if (-1 == (int)$sendTo) {
                        $sendTo = '<b>' . _AM_XFORMS_SENDTO_OTHER . ': </b>' . $f->getVar('form_send_to_other');
                    } else {
                        if ((false !== $sendTo) && ($group = $groupMgr->get($sendTo))) {
                            $sendTo = $group->getVar('name');
                        } else {
                            $sendTo = _AM_XFORMS_SENDTO_ADMIN;
                        }
                    }
                    $fStatus = "<img src='{$pathModIcon16}/active.gif' title='" . _AM_XFORMS_STATUS_ACTIVE . "' alt='" . _AM_XFORMS_STATUS_ACTIVE . "'>&nbsp;" . _AM_XFORMS_STATUS_ACTIVE;
                    if (!$f->isActive()) {
                        if (Constants::FORM_INACTIVE == $f->getVar('form_active')) {
                            $fStatus = "<img src='{$pathModIcon16}/inactive.gif' title='" . _AM_XFORMS_STATUS_INACTIVE . "' alt='" . _AM_XFORMS_STATUS_INACTIVE . "'>&nbsp;" . _AM_XFORMS_STATUS_INACTIVE;
                        } else {
                            $fStatus = "<img src='{$pathModIcon16}/expired.gif' title='" . _AM_XFORMS_STATUS_EXPIRED . "' alt='" . _AM_XFORMS_STATUS_EXPIRED . "'>&nbsp;" . _AM_XFORMS_STATUS_EXPIRED;
                        }
                    }
                    $cssClass = ('even' === $cssClass) ? 'odd' : even;
                    echo "    <tr>\n"
                         . "      <td class='{$cssClass} center'>{$id}</td>\n"
                         . "      <td class='{$cssClass}'>\n"
                         . "        <a target='_blank' href='"
                         . $helper->url("index.php?form_id={$id}")
                         . "'>"
                         . $f->getVar('form_title')
                         . "</a><br>\n"
                         . '        '
                         . $f->getVar('form_desc', 's')
                         . "\n"
                         . "      </td>\n"
                         . "      <td class='{$cssClass} center'>{$fStatus}</td>\n"
                         . "      <td class='{$cssClass} center'>{$sendTo}</td>\n"
                         . "      <td class='{$cssClass} center'>\n";
                    if (0 !== (int)$f->getVar('form_save_db')) {
                        echo "        <a href='{$thisFileName}?op=show&form_id={$id}'>"
                             . "<img src='{$pathModIcon16}/rptsee.png' class='tooltip floatcenter1' title='"
                             . _AM_XFORMS_SHOW_REPORT
                             . "' alt='"
                             . _AM_XFORMS_SHOW_REPORT
                             . "'>\n"
                             . "</a>\n"
                             . "        <a href='{$thisFileName}?op=export-horiz&format=c&form_id={$id}'>"
                             . "<img src='{$pathModIcon16}/rpthorizc.png' class='tooltip floatcenter1' title='"
                             . _AM_XFORMS_RPT_EXPORT_CH
                             . "' alt='"
                             . _AM_XFORMS_RPT_EXPORT_CH
                             . "'>"
                             . "</a>\n"
                             . "        <a href='{$thisFileName}?op=export-horiz&format=h&form_id={$id}'>"
                             . "<img src='{$pathModIcon16}/rpthor.png' class='tooltip floatcenter1' title='"
                             . _AM_XFORMS_RPT_EXPORT_HH
                             . "' alt='"
                             . _AM_XFORMS_RPT_EXPORT_HH
                             . "'>"
                             . "</a>\n"
                             . "      <a href='{$thisFileName}?op=export-vert&format=c&form_id={$id}'>"
                             . "<img src='{$pathModIcon16}/rptvertc.png' class='tooltip floatcenter1' title='"
                             . _AM_XFORMS_RPT_EXPORT_CV
                             . "' alt='"
                             . _AM_XFORMS_RPT_EXPORT_CV
                             . "'>"
                             . "</a>\n"
                             . "        <a href='{$thisFileName}?op=export-vert&format=h&form_id={$id}'>"
                             . "<img src='{$pathModIcon16}/rptvert.png' class='tooltip floatcenter1' title='"
                             . _AM_XFORMS_RPT_EXPORT_HV
                             . "' alt='"
                             . _AM_XFORMS_RPT_EXPORT_HV
                             . "'>"
                             . "</a>\n";
                    } else {
                        echo '        <b>'
                             . _AM_XFORMS_FORM_NOTSAVE
                             . "</b>\n"
                             . "        <a href='"
                             . $GLOBALS['xoops']->buildUrl('/modules/xforms/admin/main.php', ['op' => 'edit', 'form_id' => $id])
                             . "'>"
                             . "<img src='"
                             . Admin::iconUrl('edit.png', '16')
                             . "' title='"
                             . _AM_XFORMS_ACTION_EDITFORM
                             . "' alt='"
                             . _AM_XFORMS_ACTION_EDITFORM
                             . "'>"
                             . "</a>\n";
                    }
                    echo "    </tr>\n";
                }
            }
            $bshow = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo "    <tr>\n" . "      <td class='foot'>&nbsp;</td>\n" . "      <td class='foot' colspan='5'>" . $bshow->render() . "</td>\n" . "    </tr>\n" . "    </tbody>\n" . "  </table><br>\n" . "</form>\n";

            if ($ttlFormCount > $xformsDisplay->perpage) {
                xoops_load('pagenav');
                $xformsPagenav = new \XoopsPageNav($ttlFormCount, $xformsDisplay->perpage, $xformsDisplay->start, 'start', 'perpage=' . $xformsDisplay->perpage);
                echo "<div class='center middle larger width100 line160'>" . $xformsPagenav->renderNav() . "</div>\n";
            }

            echo "<fieldset><legend class='bold' style='color: #900;'>"
                 . _AM_XFORMS_STATUS_INFORMATION
                 . "</legend>\n"
                 . "<div class='pad7'>\n"
                 . "  <div class='center'>\n"
                 . "    <img src='{$pathModIcon16}/active.gif'>&nbsp;"
                 . _AM_XFORMS_STATUS_ACTIVE
                 . '&nbsp; &nbsp; &nbsp;'
                 . "    <img src='{$pathModIcon16}/inactive.gif'>&nbsp;"
                 . _AM_XFORMS_STATUS_INACTIVE
                 . '&nbsp; &nbsp; &nbsp;'
                 . "    <img src='{$pathModIcon16}/expired.gif'>&nbsp;"
                 . _AM_XFORMS_STATUS_EXPIRED
                 . "\n"
                 . "  </div>\n"
                 . "</div>\n"
                 . "</fieldset>\n";
        } else {
            /*Show 'No forms' message*/
            $bshow = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo "    <tr>\n"
                 . "      <td class='odd center' colspan='6'>"
                 . _AM_XFORMS_NO_FORMS_TOREPORT
                 . "</td>\n"
                 . "    </tr>\n"
                 . "    <tr>\n"
                 . "      <td class='foot'>&nbsp;</td>\n"
                 . "      <td class='foot' colspan='5'>"
                 . $bshow->render()
                 . "</td>\n"
                 . "    </tr>\n"
                 . "    </tbody>\n"
                 . "  </table><br>\n"
                 . "</form>\n";
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
xoops_cp_footer();
