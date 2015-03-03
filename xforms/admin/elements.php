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
$xforms_ele_mgr = xoops_getmodulehandler('elements');
include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';
define('_THIS_PAGE', XFORMS_URL . '/admin/elements.php');

if (!isset($_POST['op']) || $_POST['op'] != 'save') {
    $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
    if (empty($form_id)) {
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
    }
    $form = $xforms_form_mgr->get($form_id);

    xoops_cp_header();
    $jump    = array();
    $jump[0] = new XoopsFormSelect('', 'ele_type');
    $jump[0]->addOptionArray(
        array(
            'text'      => _AM_XFORMS_ELE_TEXT,
            'textarea'  => _AM_XFORMS_ELE_TAREA,
            'select'    => _AM_XFORMS_ELE_SELECT,
            'select2'   => _AM_XFORMS_ELE_SELECT_CTRY,
            'date'      => _AM_XFORMS_ELE_DATE,
            'checkbox'  => _AM_XFORMS_ELE_CHECK,
            'radio'     => _AM_XFORMS_ELE_RADIO,
            'yn'        => _AM_XFORMS_ELE_YN,
            'html'      => _AM_XFORMS_ELE_HTML,
            'uploadimg' => _AM_XFORMS_ELE_UPLOADIMG,
            'upload'    => _AM_XFORMS_ELE_UPLOADFILE
        )
    );
    $jump[1] = new XoopsFormHidden('op', 'edit');
    $jump[2] = new XoopsFormHidden('form_id', $form_id);
    $jump[3] = new XoopsFormButton('', 'submit', _GO, 'submit');
    echo '<div align="center">
            <form action="' . XFORMS_URL . '/admin/editelement.php" method="post">
                <b>' . _AM_XFORMS_ELE_CREATE . '</b>';
    foreach ($jump as $j) {
        echo "\n" . $j->render();
    }
    echo '</form>
        </div>
        <form action="' . _THIS_PAGE . '" method="post">
        <table class="outer" cellspacing="1" width="100%">
        <tr><th colspan="7">' . sprintf(_AM_XFORMS_ELEMENTS_OF_FORM, $form->getVar('form_title')) . '</th></tr>
        <tr>
            <td class="head" align="center" colspan="2">' . _AM_XFORMS_ELE_CAPTION . ' / ' . _AM_XFORMS_ELE_DEFAULT . '</td>
            <td class="head" align="center">' . _AM_XFORMS_ELE_REQ . '</td>
            <td class="head" align="center">' . _AM_XFORMS_ELE_ORDER . '</td>
            <td class="head" align="center">' . _AM_XFORMS_ELE_DISPLAY_ROW . '</td>
            <td class="head" align="center">' . _AM_XFORMS_ELE_DISPLAY . '</td>
            <td class="head" align="center">' . _AM_XFORMS_ACTION . '</td>
        </tr>
    ';

    $criteria = new Criteria('form_id', $form_id);
    $criteria->setSort('ele_order');
    $criteria->setOrder('ASC');

    if ($elements = $xforms_ele_mgr->getObjects($criteria)) {
        foreach ($elements as $i) {
            $id        = $i->getVar('ele_id');
            $renderer  = new XFormsElementRenderer($i);
            $ele_type  = $i->getVar('ele_type');
            $req       = $i->getVar('ele_req');
            $check_req = new XoopsFormCheckBox('', 'ele_req[' . $id . ']', $req);
            $check_req->addOption(1, ' ');
            $ele_value     = $renderer->constructElement(true);
            $order         = $i->getVar('ele_order');
            $text_order    = new XoopsFormText('', 'ele_order[' . $id . ']', 3, 2, $order);
            $display       = $i->getVar('ele_display');
            $check_display = new XoopsFormCheckBox('', 'ele_display[' . $id . ']', $display);
            $check_display->addOption(1, ' ');
            $display_row       = $i->getVar('ele_display_row');
            $check_display_row = new XoopsFormCheckBox('', 'ele_display_row[' . $id . ']', $display_row);
            $check_display_row->addOption(2, ' ');
            $hidden_id = new XoopsFormHidden('ele_id[]', $id);
            echo '<tr>';
            if ($i->getVar('ele_type') != "html") {
                $myts = MyTextSanitizer::getInstance();
                echo '<td class="odd" colspan="2">' . $myts->displayTarea($myts->stripSlashesGPC($i->getVar('ele_caption')), 1) . "</td>\n";
                echo '<td class="even" rowspan="2" align="center">' . $check_req->render() . "</td>\n";
                echo '<td class="even" rowspan="2" align="center">' . $text_order->render() . "</td>\n";
                echo '<td class="even" rowspan="2" align="center">' . $check_display_row->render() . "</td>\n";
                echo '<td class="even" rowspan="2" align="center">' . $check_display->render() . $hidden_id->render() . "</td>\n";
                echo '<td class="even" align="center" nowrap="nowrap" rowspan="2">
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '"><img src=' . $pathIcon16 . '/edit.png title="' . _EDIT . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '&amp;clone=1"><img src=' . $pathIcon16 . '/editcopy.png title="' . _CLONE . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=delete&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '"><img src=' . $pathIcon16 . '/delete.png title="' . _DELETE . '"></a></td>';
                echo '</tr>';
                echo '<tr><td class="even" colspan="2">' . $ele_value->render() . "</td>\n</tr>";
            } else {
                echo '<tr><td class="even" colspan="2">' . $ele_value->render() . "</td>\n";
                echo '<td class="even" align="center" valign="top">&nbsp;</td>';
                echo '<td class="even" align="center" valign="top">' . $text_order->render() . "</td>\n";
                echo '<td class="even" align="center" valign="top">&nbsp;</td>';
                echo '<td class="even" align="center" valign="top">' . $check_display->render() . $hidden_id->render() . "</td>\n";
                echo '<td class="even" align="center" valign="top" nowrap="nowrap">
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '"><img src=' . $pathIcon16 . '/edit.png title="' . _EDIT . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '&amp;clone=1"><img src=' . $pathIcon16 . '/editcopy.png title="' . _CLONE . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=delete&amp;ele_id=' . $id . '&amp;form_id=' . $form_id . '"><img src=' . $pathIcon16 . '/delete.png title="' . _DELETE . '"></a></td>';
                echo '</tr>';
            }
        }
    }

    $submit  = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
    $submit1 = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_FORM, 'submit');
    $tray    = new XoopsFormElementTray('');
    $tray->addElement($submit);
    $tray->addElement($submit1);
    echo '
        <tr>
            <td class="foot" colspan="7" align="center">' . $tray->render() . '</td>
        </tr>
    </table>
    ';
    $hidden_op      = new XoopsFormHidden('op', 'save');
    $hidden_form_id = new XoopsFormHidden('form_id', $form_id);
    echo $hidden_op->render();
    echo $hidden_form_id->render();
    echo '</form>';
} else {
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    if (empty($form_id)) {
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
    }
    extract($_POST);
    $error = '';

    $magicQuotes = false; // Flag to fix problem with slashes
    if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
        $magicQuotes = true;
    }
    foreach ($ele_id as $id) {
        $element = $xforms_ele_mgr->get($id);
        $req     = (isset($ele_req[$id])) ? 1 : 0;
        $element->setVar('ele_req', $req);
        $order = (!empty($ele_order[$id])) ? intval($ele_order[$id]) : 0;
        $element->setVar('ele_order', $order);
        $display_row = (isset($ele_display_row[$id])) ? 2 : 1;
        $element->setVar('ele_display_row', $display_row);
        $display = (isset($ele_display[$id])) ? 1 : 0;
        $element->setVar('ele_display', $display);
        $type  = $element->getVar('ele_type');
        $value = $element->getVar('ele_value');
        switch ($type) {
            case 'text':
                $value[2] = ($magicQuotes) ? stripslashes($ele_value[$id]) : $ele_value[$id];
                break;

            case 'textarea':
                $value[0] = ($magicQuotes) ? stripslashes($ele_value[$id]) : $ele_value[$id];
                break;

            case 'html':
                $value[0] = ($magicQuotes) ? stripslashes($ele_value[$id]) : $ele_value[$id];
                $element->setVar('ele_display_row', 0);
                break;
            case 'date':
                $value = array();
        $value[] = $ele_value[$id];
            break;
            case 'select2':
               $value[2] = !empty($ele_value[$id]) ? $ele_value[$id] : 'LB';
            break;
            case 'select':
                $new_vars  = array();
                $opt_count = 1;
                if (isset($ele_value[$id])) {
                    if (is_array($ele_value[$id])) {
                        while ($j = each($value[2])) {
                            if (in_array($opt_count, $ele_value[$id])) {
                                $new_vars[$j['key']] = 1;
                            } else {
                                $new_vars[$j['key']] = 0;
                            }
                            ++$opt_count;
                        }
                    } else {
                        if (count($value[2]) > 1) {
                            while ($j = each($value[2])) {
                                if ($opt_count == $ele_value[$id]) {
                                    $new_vars[$j['key']] = 1;
                                } else {
                                    $new_vars[$j['key']] = 0;
                                }
                                ++$opt_count;
                            }
                        } else {
                            while ($j = each($value[2])) {
                                if (!empty($ele_value[$id])) {
                                    $new_vars = array($j['key'] => 1);
                                } else {
                                    $new_vars = array($j['key'] => 0);
                                }
                            }
                        }
                    }
                    $value[2] = $new_vars;
                } else {
                    foreach ($value[2] as $k => $v) {
                        $value[2][$k] = 0;
                    }
                }
                break;

            case 'checkbox':
                $new_vars  = array();
                $opt_count = 1;
                if (isset($ele_value[$id]) && is_array($ele_value[$id])) {
                    while ($j = each($value)) {
                        if (in_array($opt_count, $ele_value[$id])) {
                            $new_vars[$j['key']] = 1;
                        } else {
                            $new_vars[$j['key']] = 0;
                        }
                        ++$opt_count;
                    }
                } else {
                    if (count($value) > 1) {
                        while ($j = each($value)) {
                            $new_vars[$j['key']] = 0;
                        }
                    } else {
                        while ($j = each($value)) {
                            if (!empty($ele_value[$id])) {
                                $new_vars = array($j['key'] => 1);
                            } else {
                                $new_vars = array($j['key'] => 0);
                            }
                        }
                    }
                }
                $value = $new_vars;
                break;

            case 'radio':
            case 'yn':
                $new_vars = array();
                $i        = 1;
                while ($j = each($value)) {
                    if ($ele_value[$id] == $i) {
                        $new_vars[$j['key']] = 1;
                    } else {
                        $new_vars[$j['key']] = 0;
                    }
                    ++$i;
                }
                $value = $new_vars;
                break;

            case 'uploadimg':
                $value[0] = intval($ele_value[$id][0]);
                $value[4] = intval($ele_value[$id][4]);
                $value[5] = intval($ele_value[$id][5]);
                break;
            case 'upload':
                $value[0] = intval($ele_value[$id][0]);
                break;
            default:
                break;
        }
        $element->setVar('ele_value', $value, true);
        if (!$xforms_ele_mgr->insert($element)) {
            $error .= $element->getHtmlErrors();
        }
    }
    if (empty($error)) {
        if ($_POST['submit'] == _AM_XFORMS_SAVE_THEN_FORM) {
            redirect_header(XFORMS_ADMIN_URL . '?op=edit&form_id=' . $form_id, 0, _AM_XFORMS_DBUPDATED);
        } else {
            redirect_header(_THIS_PAGE . '?form_id=' . $form_id, 0, _AM_XFORMS_DBUPDATED);
        }
    } else {
        xoops_cp_header();
        echo $error;
    }
}

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
