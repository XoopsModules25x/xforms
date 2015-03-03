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

include __DIR__ . '/admin_header.php';
$myts    = MyTextSanitizer::getInstance();
$op      = isset($_GET['op']) ? trim($_GET['op']) : '';
$format  = isset($_GET['format']) ? trim($_GET['format']) : 'v';
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id'], 10) : 0;
$showAll = isset($_POST['showall']) ? true : false;

if (empty($form_id)) {
    $op = "";
}

switch ($op) {
    case "show": /*Show the report in the page*/
        if ($form = $xforms_form_mgr->get($form_id)) {
            if ($form->getVar('form_save_db') == 0) {
                redirect_header('report.php', 5, _AM_XFORMS_FORM_NOTSAVE);
            }
        } else {
            redirect_header('report.php', 5, _AM_XFORMS_FORM_NOTEXISTS);
        }

        /* Show the report */
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('report.php');

        echo '<table class="outer" cellspacing="1" width="100%">
                <tr><th colspan="6">' . _AM_XFORMS_REPORT_FORM . ': ' . $form->getVar('form_title') . '</th></tr>
                <tr>
                    <td class="head" align="center" width="10%">Nº</td>
                    <td class="head" align="center" width="10%">' . _AM_XFORMS_RPT_USER . '</td>
                    <td class="head" align="center" width="10%">' . _AM_XFORMS_RPT_DATETIME . '</td>
                    <td class="head" align="center" width="10%">' . _AM_XFORMS_RPT_IP . '</td>
                    <td class="head" align="center" width="30%">' . _AM_XFORMS_RPT_QUESTION . '</td>
                    <td class="head" align="center" width="40%">' . _AM_XFORMS_RPT_ANSWER . '</td>
                </tr>';

        $udata_mgr = xoops_getmodulehandler('userdata');
        $data      = $udata_mgr->getReport($form_id);
        if (empty($data)) {
            echo '<tr><td colspan="6" class="foot" align="center">' . _AM_XFORMS_RPT_NODATA . '</td></tr>';
        } else {
            $countu   = 0;
            $dproc    = 0;
            $ipproc   = "";
            $firstRow = true;
            for ($i = 0; $i < count($data); ++$i) {
                $border = "";
                $dtime  = intval($data[$i]["udata_time"], 10);
                $ipuser = $data[$i]["udata_ip"];
                if ($dproc != 0 && ($dproc != $dtime || $ipproc != $ipuser)) {
                    $firstRow = true;
                    $border   = ' style="border-top: 2px solid #000000;"';
                }
                $dproc  = $dtime;
                $ipproc = $ipuser;
                $uname  = "&nbsp;";
                $datet  = "&nbsp;";
                $uip    = "&nbsp;";
                $ucount = "&nbsp;";
                if ($firstRow) {
                    ++$countu;
                    $ucount = $countu;
                    $uname  = $data[$i]["uname"];
                    $datet  = date("d-m-Y H:i:s", $dtime);
                    $uip    = $data[$i]["udata_ip"];
                }
                $cssclass    = (($i + 1) % 2 == 0) ? "even" : "odd";
                $ele_caption = $myts->displayTarea($myts->stripSlashesGPC($data[$i]["ele_caption"]), 1);
                echo '<tr class="' . $cssclass . '">';
                echo '<td' . $border . ' nowrap>' . $ucount . '</td>
                      <td' . $border . ' nowrap>' . $uname . '</td>
                      <td' . $border . ' nowrap>' . $datet . '</td>
                      <td' . $border . '>' . $uip . '</td>
                      <td' . $border . '>' . $ele_caption . '</td>';
                $udata = unserialize($data[$i]["udata_value"]);
                if (is_array($udata)) {
                    switch ($data[$i]["ele_type"]) {
                        case 'yn':
                        case 'text':
                        case 'textarea':
                            echo '<td' . $border . '>' . $udata[0] . '</td>';
                            break;

                        case 'upload':
                        case 'uploadimg':
                            echo '<td' . $border . '>
                                    <a href="' . XFORMS_URL . '/file.php?f=' . $udata['file'] . '&fn=' . $udata['name'] . '">
                                    ' . $udata['name'] . '</a>
                                  </td>';
                            break;

                        case 'select':
                        case 'select2':
                        case 'date':
                        case 'checkbox':
                        case 'radio':
                            echo '<td' . $border . '>' . implode(', ', $udata) . '</td>';
                            break;

                        default:
                            echo '<td' . $border . '>&nbsp;</td>';
                            break;
                    }
                } else {
                    echo '<td' . $border . '>&nbsp;</td>';
                }
                echo '</tr>';
                $firstRow = false;
            }
        }
        echo '<tr><td class="foot" colspan="6" align="center">';
        $bexportv = new XoopsFormButton('', 'export_v', _AM_XFORMS_RPT_EXPORT_V, 'button');
        $bexportv->setExtra(' onclick="window.location=\'report.php?op=export&format=v&form_id=' . $form_id . '\'"');
        $bexporth = new XoopsFormButton('', 'export_h', _AM_XFORMS_RPT_EXPORT_H, 'button');
        $bexporth->setExtra(' onclick="window.location=\'report.php?op=export&format=h&form_id=' . $form_id . '\'"');
        echo $bexportv->render() . $bexporth->render();
        echo '</td></tr>';
        echo '</table>';
        break;

    case "export": /*Export data saved to xls file*/
        if ($form = $xforms_form_mgr->get($form_id)) {
            if ($form->getVar('form_save_db') == 0) {
                redirect_header('report.php', 5, _AM_XFORMS_FORM_NOTSAVE);
            }
        } else {
            redirect_header('report.php', 5, _AM_XFORMS_FORM_NOTEXISTS);
        }
        $udata_mgr = xoops_getmodulehandler('userdata');
        $data      = $udata_mgr->getReport($form_id);

        /*Disable debug*/
        error_reporting(0);
        $xoopsLogger->activated = false;
        ob_start();

        echo '<html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    </head>
                <body>
                <table border="1">';
        if ($format == "v") {
            echo '<tr><th colspan="6" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_REPORT_FORM . ': ' . $form->getVar('form_title') . '</th></tr>
                    <tr>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">Nº</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_USER . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_DATETIME . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_IP . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_QUESTION . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_ANSWER . '</th>
                    </tr>';
            if (empty($data)) {
                echo '<tr><td colspan="6" align="center">' . _AM_XFORMS_RPT_NODATA . '</td></tr>';
            } else {
                $countu   = 0;
                $dproc    = 0;
                $ipproc   = "";
                $firstRow = true;
                for ($i = 0; $i < count($data); ++$i) {
                    $bgItem = (($i + 1) % 2 == 0) ? "background: #FFFFFF;" : "background: #DEDEDE;";
                    $border = "";
                    $dtime  = intval($data[$i]["udata_time"], 10);
                    $ipuser = $data[$i]["udata_ip"];
                    if ($dproc != 0 && ($dproc != $dtime || $ipproc != $ipuser)) {
                        $firstRow = true;
                        $border   = ' border-top: 2px solid #000000;';
                    }
                    $dproc  = $dtime;
                    $ipproc = $ipuser;
                    $uname  = "";
                    $datet  = "";
                    $uip    = "";
                    $ucount = "";
                    if ($firstRow) {
                        ++$countu;
                        $ucount = $countu;
                        $uname  = $data[$i]["uname"];
                        $datet  = date("d-m-Y H:i:s", $dtime);
                        $uip    = $ipuser;
                    }
                    $ele_caption = $myts->displayTarea($myts->stripSlashesGPC($data[$i]["ele_caption"]), 1);
                    echo '<tr>';
                    echo '<td style="' . $border . '" nowrap>' . $ucount . '</td>
                          <td style="' . $border . '" nowrap>' . $uname . '</td>
                          <td style="' . $border . '" nowrap>' . $datet . '</td>
                          <td style="' . $border . '">' . $uip . '</td>
                          <td style="' . $bgItem . $border . '">' . $ele_caption . '</td>';
                    $udata = unserialize($data[$i]["udata_value"]);
                    echo '<td style="' . $bgItem . $border . '">';
                    if (is_array($udata)) {
                        switch ($data[$i]["ele_type"]) {
                            case 'yn':
                            case 'text':
                            case 'textarea':
                                echo $udata[0];
                                break;
                            case 'upload':
                            case 'uploadimg':
                                echo '<a href="' . XFORMS_URL . '/file.php?f=' . $udata['file'] . '&fn=' . $udata['name'] . '">' . $udata['name'] . '</a>';
                                break;
                            case 'select':
                            case 'checkbox':
                            case 'radio':
                                echo implode(', ', $udata);
                                break;
                            default:
                                echo '&nbsp;';
                                break;
                        }
                    } else {
                        echo '&nbsp;';
                    }
                    echo '</td>';
                    echo '</tr>';
                    $firstRow = false;
                }
            }
        } else {
            $xforms_ele_mgr = xoops_getmodulehandler('elements');
            $criteria       = new CriteriaCompo();
            $criteria->add(new Criteria('form_id', $form->getVar('form_id')), 'AND');
            $criteria->add(new Criteria('ele_display', 1), 'AND');
            $criteria->setSort('ele_order');
            $criteria->setOrder('ASC');
            $elements = $xforms_ele_mgr->getObjects($criteria, false);
            $celems   = count($elements);
            echo '<tr><th colspan="' . ($celems + 3) . '" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_REPORT_FORM . ': ' . $form->getVar('form_title') . '</th></tr>
                    <tr>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_USER . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_DATETIME . '</th>
                        <th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . _AM_XFORMS_RPT_IP . '</th>';
            foreach ($elements as $el) {
                $ele_caption = $myts->displayTarea($myts->stripSlashesGPC($el->getVar("ele_caption")), 1);
                echo '<th align="center" style="border-bottom: 2px solid #000000; background: #ACACAC;">' . $ele_caption . '</th>';
            }
            echo '</tr>';
            if (empty($data)) {
                echo '<tr><td colspan="' . ($celems + 3) . '" align="center">' . _AM_XFORMS_RPT_NODATA . '</td></tr>';
            } else {
                $bgItem = "FFFFFF";
                $dproc  = 0;
                $ipproc = "";
                $eproc  = -1;
                for ($i = 0; $i < count($data); ++$i) {
                    $dtime  = intval($data[$i]["udata_time"], 10);
                    $ipuser = $data[$i]["udata_ip"];
                    if ($dproc == 0 || $dproc != $dtime || $ipproc != $ipuser) {
                        if ($dproc > 0) {
                            while ($eproc < ($celems - 1)) {
                                echo '<td style="background: #' . $bgItem . ';">&nbsp;</td>';
                                ++$eproc;
                            }
                            echo '</tr>';
                        }
                        $bgItem = ($bgItem == "FFFFFF") ? "DEDEDE" : "FFFFFF";
                        echo '<tr>';
                        echo '<td style="background: #' . $bgItem . ';" nowrap>' . $data[$i]["uname"] . '</td>
                              <td style="background: #' . $bgItem . ';" nowrap>' . date("d-m-Y H:i:s", $dtime) . '</td>
                              <td style="background: #' . $bgItem . ';">' . $data[$i]["udata_ip"] . '</td>';
                        $eproc = -1;
                    }
                    $dproc  = $dtime;
                    $ipproc = $ipuser;
                    ++$eproc;
                    if ($eproc >= $celems) {
                        break;
                    }
                    if (intval($data[$i]["ele_id"], 10) != $elements[$eproc]->getVar("ele_id")) {
                        echo '<td style="background: #' . $bgItem . ';">&nbsp;</td>';
                        $i--;
                        continue;
                    }
                    $udata = unserialize($data[$i]["udata_value"]);
                    echo '<td style="background: #' . $bgItem . ';">';
                    if (is_array($udata)) {
                        switch ($data[$i]["ele_type"]) {
                            case 'yn':
                            case 'text':
                            case 'textarea':
                                echo $udata[0];
                                break;
                            case 'upload':
                            case 'uploadimg':
                                echo '<a href="' . XFORMS_URL . '/file.php?f=' . $udata['file'] . '&fn=' . $udata['name'] . '">' . $udata['name'] . '</a>';
                                break;
                            case 'select':
                            case 'checkbox':
                            case 'radio':
                                echo implode(', ', $udata);
                                break;
                            default:
                                echo '&nbsp;';
                                break;
                        }
                    } else {
                        echo '&nbsp;';
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</table>
              </body>
              </html>';
        $tableXLS = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=report_form_' . $form_id . '.xls');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Transfer-Encoding: binary');
        echo $tableXLS;
        exit();
        break;

    default: /*Show selector of forms*/
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('report.php');

        echo '<form action="report.php" method="GET">
              <input type="hidden" name="op" value="show">
              <table class="outer" cellspacing="1" width="100%">
                <tr>
                    <td class="foot" align="left">
                        ' . _AM_XFORMS_ENTER_ID . '
                        <input type="text" name="form_id" size="5">
                        <input type="submit" value="' . _AM_XFORMS_SHOW_REPORT . '">
                    </td>
                </tr>
              </table>
              </form>';

        echo '<form action="report.php" method="POST">
              <table class="outer" cellspacing="1" width="100%">
                <tr><th colspan="5">' . _AM_XFORMS_LISTING . '</th></tr>
                <tr>
                    <td class="head" align="center">' . _AM_XFORMS_BYID . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_STATUS . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_TITLE . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_SENDTO . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_ACTION . '</td>
                </tr>';

        $criteria = null;
        if ($showAll) {
            $criteria = new Criteria(1, 1);
        } else {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('form_active', 0, '<>'));
        }
        $criteria->setSort('form_order');
        $criteria->setOrder('ASC');
        $totalList = 0;
        if ($forms = $xforms_form_mgr->getObjects($criteria)) {
            foreach ($forms as $f) {
                if ($showAll || (!$showAll && $f->isActive())) {
                    $id     = $f->getVar('form_id');
                    $sendto = $f->getVar('form_send_to_group');
                    if (intval($sendto, 10) == -1) {
                        $sendto = "<b>" . _AM_XFORMS_SENDTO_OTHER . ": </b>" . $f->getVar('form_send_to_other');
                    } else {
                        $group_mgr = xoops_gethandler('group');
                        if (false != $sendto && $group = $group_mgr->get($sendto)) {
                            $sendto = $group->getVar('name');
                        } else {
                            $sendto = _AM_XFORMS_SENDTO_ADMIN;
                        }
                    }
                    $fstatus = '<img src="' . $mypathIcon16 . '/active.gif" title="' . _AM_XFORMS_STATUS_ACTIVE . '" alt="' . _AM_XFORMS_STATUS_ACTIVE . '">&nbsp;' . _AM_XFORMS_STATUS_ACTIVE;
                    if (!$f->isActive()) {
                        if ($f->getVar('form_active') == 0) {
                            $fstatus
                                =
                                '<img src="' . $mypathIcon16 . '/inactive.gif" title="' . _AM_XFORMS_STATUS_INACTIVE . '" alt="' . _AM_XFORMS_STATUS_INACTIVE . '">&nbsp;' . _AM_XFORMS_STATUS_INACTIVE;
                        } else {
                            $fstatus
                                = '<img src="' . $mypathIcon16 . '/expired.gif" title="' . _AM_XFORMS_STATUS_EXPIRED . '" alt="' . _AM_XFORMS_STATUS_EXPIRED . '">&nbsp;' . _AM_XFORMS_STATUS_EXPIRED;
                        }
                    }
                    echo '
                        <tr>
                            <td class="odd" align="center">' . $id . '</td>
                            <td class="odd" align="center">' . $fstatus . '</td>
                            <td class="odd"><a target="_blank" href="' . XFORMS_URL . '/?form_id=' . $id . '">' . $f->getVar('form_title') . '</a></td>
                            <td class="odd" align="center">' . $sendto . '</td>
                            <td class="odd" align="center">';
                    if ($f->getVar('form_save_db') != 0) {
                        echo '	<a href="report.php?op=show&form_id=' . $id . '">
                                    <img src="' . $mypathIcon16 . '/rptsee.png" class="tooltip" title="' . _AM_XFORMS_SHOW_REPORT . '" alt="' . _AM_XFORMS_SHOW_REPORT . '">
                                </a>&nbsp;&nbsp;';
                        echo '	<a href="report.php?op=export&format=v&form_id=' . $id . '">
                                    <img src="' . $mypathIcon16 . '/rptvert.png" class="tooltip" title="' . _AM_XFORMS_RPT_EXPORT_V . '" alt="' . _AM_XFORMS_RPT_EXPORT_V . '">
                                </a>&nbsp;&nbsp;';
                        echo '	<a href="report.php?op=export&format=h&form_id=' . $id . '">
                                    <img src="' . $mypathIcon16 . '/rpthor.png" class="tooltip" title="' . _AM_XFORMS_RPT_EXPORT_H . '" alt="' . _AM_XFORMS_RPT_EXPORT_H . '">
                                </a>';
                    } else {
                        echo '<b>' . _AM_XFORMS_FORM_NOTSAVE . '</b>
                              <a href="' . XFORMS_ADMIN_URL . '?op=edit&form_id=' . $id . '">
                                  <img src="' . $pathIcon16 . '/edit.png" title="' . _AM_XFORMS_ACTION_EDITFORM . '" alt="' . _AM_XFORMS_ACTION_EDITFORM . '">
                              </a>';
                    }
                    echo '</tr>';

                    ++$totalList;
                }
            }
            if ($totalList > 0) {
                $bshow = new XoopsFormButton('', (($showAll) ? 'shownormal' : 'showall'), (($showAll) ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
                echo '	<tr>
                            <td class="foot">&nbsp;</td>
                            <td class="foot" colspan="5">' . $bshow->render() . '</td>
                        </tr>
                        </table><br />';
            }
        }

        /*Show message no forms*/
        if ($totalList == 0) {
            $bshow = new XoopsFormButton('', (($showAll) ? 'shownormal' : 'showall'), (($showAll) ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo '  <tr>
                        <td class="odd" colspan="6" align="center">
                            ' . _AM_XFORMS_NO_FORMS_TOREPORT . '
                        </td>
                    </tr>
                    <tr>
                        <td class="foot">&nbsp;</td>
                        <td class="foot" colspan="5">' . $bshow->render() . '</td>
                    </tr>
                  </table><br />';
        }
        echo "\n</form>\n";

        echo '<form action="report.php" method="GET">
              <input type="hidden" name="op" value="show">
              <table class="outer" cellspacing="1" width="100%">
                <tr><th>' . _AM_XFORMS_BYID . '</th></tr>
                <tr>
                    <td class="foot" align="center">
                        ' . _AM_XFORMS_ENTER_ID . '
                        <input type="text" name="form_id" size="5">
                        <input type="submit" value="' . _AM_XFORMS_SHOW_REPORT . '">
                    </td>
                </tr>
              </table>
              </form>';
        break;
}

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
