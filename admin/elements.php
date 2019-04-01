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

use \Xmf\Request;

include __DIR__ . '/admin_header.php';
$xformsEleMgr = xoops_getmodulehandler('elements');
include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';
define('_THIS_PAGE', XFORMS_URL . '/admin/elements.php');

$op = Request::getCmd('op', '', 'POST');
if ('' === $op && 'save' !== $op) {
//if (!isset($_POST['op']) || $_POST['op'] != 'save') {
    $formId = Request::getInt('form_id', 0, 'GET');
    $formId = (int)$formId; // to fix \XMF\Request bug in XOOPS < 2.5.9
    if (0 === $formId) {
    //$formId = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
    //if (empty($formId)) {
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
    }
    $form = $xformsFormMgr->get($formId);

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
    $jump[2] = new XoopsFormHidden('form_id', $formId);
    $jump[3] = new XoopsFormButton('', 'submit', _GO, 'submit');
    echo '<div class="center">
            <form action="' . XFORMS_URL . '/admin/editelement.php" method="post">
                <b>' . _AM_XFORMS_ELE_CREATE . '</b>';
    foreach ($jump as $j) {
        echo "\n" . $j->render();
    }
    echo '</form>
        </div>
        <form action="' . _THIS_PAGE . '" method="post">
        <table class="outer width100" cellspacing="1">
        <tr><th colspan="7">' . sprintf(_AM_XFORMS_ELEMENTS_OF_FORM, $form->getVar('form_title')) . '</th></tr>
        <tr>
            <td class="head center" colspan="2">' . _AM_XFORMS_ELE_CAPTION . ' / ' . _AM_XFORMS_ELE_DEFAULT . '</td>
            <td class="head center">' . _AM_XFORMS_ELE_REQ . '</td>
            <td class="head center">' . _AM_XFORMS_ELE_ORDER . '</td>
            <td class="head center">' . _AM_XFORMS_ELE_DISPLAY_ROW . '</td>
            <td class="head center">' . _AM_XFORMS_ELE_DISPLAY . '</td>
            <td class="head center">' . _AM_XFORMS_ACTION . '</td>
        </tr>
    ';

    $criteria = new Criteria('form_id', $formId);
    $criteria->setSort('ele_order');
    $criteria->setOrder('ASC');

    if ($elements = $xformsEleMgr->getObjects($criteria)) {
        foreach ($elements as $i) {
            $id       = $i->getVar('ele_id');
            $renderer = new XFormsElementRenderer($i);
            $eleType  = $i->getVar('ele_type');
            $req      = $i->getVar('ele_req');
            $checkReq = new XoopsFormCheckBox('', 'ele_req[' . $id . ']', $req);
            $checkReq->addOption(1, ' ');
            $eleValue     = $renderer->constructElement(true);
            $order        = $i->getVar('ele_order');
            $textOrder    = new XoopsFormText('', 'ele_order[' . $id . ']', 3, 2, $order);
            $display      = $i->getVar('ele_display');
            $checkDisplay = new XoopsFormCheckBox('', 'ele_display[' . $id . ']', $display);
            $checkDisplay->addOption(1, ' ');
            $displayRow   = $i->getVar('ele_display_row');
            $checkDisplayRow = new XoopsFormCheckBox('', 'ele_display_row[' . $id . ']', $displayRow);
            $checkDisplayRow->addOption(2, ' ');
            $hiddenId = new XoopsFormHidden('ele_id[]', $id);
            echo '<tr>';
            if ($i->getVar('ele_type') != "html") {
                $myts = MyTextSanitizer::getInstance();
                echo '<td class="odd" colspan="2">' . $myts->displayTarea($myts->stripSlashesGPC($i->getVar('ele_caption')), 1) . "</td>\n";
                echo '<td class="even center" rowspan="2">' . $checkReq->render() . "</td>\n";
                echo '<td class="even center" rowspan="2">' . $textOrder->render() . "</td>\n";
                echo '<td class="even center" rowspan="2">' . $checkDisplayRow->render() . "</td>\n";
                echo '<td class="even center" rowspan="2">' . $checkDisplay->render() . $hiddenId->render() . "</td>\n";
                echo '<td class="even center" nowrap="nowrap" rowspan="2">
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '"><img src=' . $pathIcon16 . '/edit.png title="' . _EDIT . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '&amp;clone=1"><img src=' . $pathIcon16 . '/editcopy.png title="' . _CLONE . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=delete&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '"><img src=' . $pathIcon16 . '/delete.png title="' . _DELETE . '"></a></td>';
                echo '</tr>';
                echo '<tr><td class="even" colspan="2">' . $eleValue->render() . "</td>\n</tr>";
            } else {
                echo '<tr><td class="even" colspan="2">' . $eleValue->render() . "</td>\n";
                echo '<td class="even center" valign="top">&nbsp;</td>';
                echo '<td class="even center" valign="top">' . $textOrder->render() . "</td>\n";
                echo '<td class="even center" valign="top">&nbsp;</td>';
                echo '<td class="even center" valign="top">' . $checkDisplay->render() . $hiddenId->render() . "</td>\n";
                echo '<td class="even center" valign="top" nowrap="nowrap">
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '"><img src=' . $pathIcon16 . '/edit.png title="' . _EDIT . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '&amp;clone=1"><img src=' . $pathIcon16 . '/editcopy.png title="' . _CLONE . '"></a>&nbsp;&nbsp;
                        <a href="editelement.php?op=delete&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '"><img src=' . $pathIcon16 . '/delete.png title="' . _DELETE . '"></a></td>';
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
            <td class="foot center" colspan="7">' . $tray->render() . '</td>
        </tr>
    </table>
    ';
    $hiddenOp     = new XoopsFormHidden('op', 'save');
    $hiddenFormId = new XoopsFormHidden('form_id', $formId);
    echo $hiddenOp->render();
    echo $hiddenFormId->render();
    echo '</form>';
} else {
    $formId = Request::getInt('form_id', 0, 'POST');
    $formId = (int)$formId; // to fix \XMF\Request bug in XOOPS < 2.5.9
    //$formId = isset($_POST['form_id']) ? (int)$_POST['form_id'] : 0;
    //if (empty($formId)) {
    if (0 === $formId) {
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
    }
    //extract($_POST);
    $error = '';

    $eleId  = Request::getArray('ele_id', array(), 'POST');
    $eleId  = array_map('intval', $eleId);

    $eleOrder = Request::getArray('ele_req', array(), 'POST');
    array_walk($eleOrder, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

    $eleOrder = Request::getArray('ele_order', array(), 'POST');
    array_walk($eleOrder, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

    $eleDisplay = Request::getArray('ele_display', array(), 'POST');
    array_walk($eleDisplay, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

    $eleDisplayRow = Request::getArray('ele_display_row', array(), 'POST');
    array_walk($eleDisplayRow, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

    $eleValue = Request::getArray('ele_value', array(), 'POST');

    foreach ($eleId as $id) {
        $element    = $xformsEleMgr->get($id);
        $req        = (isset($eleOrder[$id])) ? 1 : 0;
        $element->setVar('ele_req', $req);
        $order      = (!empty($eleOrder[$id])) ? (int)$eleOrder[$id] : 0;
        $element->setVar('ele_order', $order);
        $displayRow = (isset($eleDisplayRow[$id])) ? 2 : 1;
        $element->setVar('ele_display_row', $displayRow);
        $display    = (isset($eleDisplay[$id])) ? 1 : 0;
        $element->setVar('ele_display', $display);
        $type       = $element->getVar('ele_type');
        $value      = $element->getVar('ele_value');
        switch ($type) {
            case 'text':
                $value[2] = $eleValue[$id];
                break;

            case 'textarea':
                $value[0] = $eleValue[$id];
                break;

            case 'html':
                $value[0] = $eleValue[$id];
                $element->setVar('ele_display_row', 0);
                break;
            case 'date':
                $value = array();
                $value[] = $eleValue[$id];
            break;
            case 'select2':
                $value[2] = !empty($eleValue[$id]) ? $eleValue[$id] : 'LB';
            break;
            case 'select':
                $newVars  = array();
                $optCount = 1;
                if (isset($eleValue[$id])) {
                    if (is_array($eleValue[$id])) {
                        while ($j = each($value[2])) {
                            if (in_array($optCount, $eleValue[$id])) {
                                $newVars[$j['key']] = 1;
                            } else {
                                $newVars[$j['key']] = 0;
                            }
                            ++$optCount;
                        }
                    } else {
                        if (count($value[2]) > 1) {
                            while ($j = each($value[2])) {
                                if ($optCount == $eleValue[$id]) {
                                    $newVars[$j['key']] = 1;
                                } else {
                                    $newVars[$j['key']] = 0;
                                }
                                ++$optCount;
                            }
                        } else {
                            while ($j = each($value[2])) {
                                if (!empty($eleValue[$id])) {
                                    $newVars = array($j['key'] => 1);
                                } else {
                                    $newVars = array($j['key'] => 0);
                                }
                            }
                        }
                    }
                    $value[2] = $newVars;
                } else {
                    foreach ($value[2] as $k => $v) {
                        $value[2][$k] = 0;
                    }
                }
                break;

            case 'checkbox':
                $newVars  = array();
                $optCount = 1;
                if (isset($eleValue[$id]) && is_array($eleValue[$id])) {
                    while ($j = each($value)) {
                        if (in_array($optCount, $eleValue[$id])) {
                            $newVars[$j['key']] = 1;
                        } else {
                            $newVars[$j['key']] = 0;
                        }
                        ++$optCount;
                    }
                } else {
                    if (count($value) > 1) {
                        while ($j = each($value)) {
                            $newVars[$j['key']] = 0;
                        }
                    } else {
                        while ($j = each($value)) {
                            if (!empty($eleValue[$id])) {
                                $newVars = array($j['key'] => 1);
                            } else {
                                $newVars = array($j['key'] => 0);
                            }
                        }
                    }
                }
                $value = $newVars;
                break;

            case 'radio':
            case 'yn':
                $newVars = array();
                $i        = 1;
                while ($j = each($value)) {
                    if ($eleValue[$id] == $i) {
                        $newVars[$j['key']] = 1;
                    } else {
                        $newVars[$j['key']] = 0;
                    }
                    ++$i;
                }
                $value = $newVars;
                break;

            case 'uploadimg':
                $value[0] = (int)$eleValue[$id][0];
                $value[4] = (int)$eleValue[$id][4];
                $value[5] = (int)$eleValue[$id][5];
                break;
            case 'upload':
                $value[0] = (int)$eleValue[$id][0];
                break;
            default:
                break;
        }
        $element->setVar('ele_value', $value, true);
        if (!$xformsEleMgr->insert($element)) {
            $error .= $element->getHtmlErrors();
        }
    }
    if (empty($error)) {
        if ($_POST['submit'] == _AM_XFORMS_SAVE_THEN_FORM) {
            redirect_header(XFORMS_ADMIN_URL . '?op=edit&form_id=' . $formId, 0, _AM_XFORMS_DBUPDATED);
        } else {
            redirect_header(_THIS_PAGE . '?form_id=' . $formId, 0, _AM_XFORMS_DBUPDATED);
        }
    } else {
        xoops_cp_header();
        echo $error;
    }
}

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
