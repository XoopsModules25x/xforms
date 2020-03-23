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
 * @package   \XoopsModules\Xforms\search
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;

//defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * xforms_search()
 *
 * @uses \CriteriaCompo
 * @uses \Criteria
 * @uses \Xmf\Module\Helper
 * @uses \Xmf\Module\Helper\Permission
 *
 * @param mixed $queryArray
 * @param mixed $andor
 * @param mixed $limit
 * @param mixed $offset
 * @param mixed $uid
 * @return array
 */
function xforms_search($queryArray, $andor, $limit, $offset, $uid)
{
    $ret = [];
    if (0 == (int)$uid) {
        /** @var \XoopsModules\Xforms\Helper $helper */
        $helper       = Helper::getInstance();
        $formsHandler = $helper::getInstance()->getHandler('Forms');

        // get all forms user has rights to view
        if ($permittedForms = $formsHandler->getPermittedForms()) {
            $pIdArray = [];
            foreach ($permittedForms as $pForm) {
                $pIdArray[] = $pForm->getVar('form_id');
            }
            $pIds = '(' . implode(',', $pIdArray) . ')';

            $criteria = new \CriteriaCompo();
            $criteria->setSort('form_order');
            $criteria->order = 'ASC';
            $criteria->add(new \Criteria('form_id', $pIds, 'IN'));

            if (isset($limit) && ((int)$limit > 0)) {
                $criteria->setLimit((int)$limit);
            }
            if (!empty($offset)) {
                $criteria->setStart((int)$offset);
            }

            if (is_array($queryArray) && !empty($queryArray)) {
                $queryCount = count($queryArray);
                for ($idx = 0; $idx < $queryCount; ++$idx) {
                    $qual = (0 === $idx) ? 'AND' : $andor;
                    $subCriteria = new \CriteriaCompo();
                    $subCriteria->add(new \Criteria('form_title', '%' . $queryArray[$idx] . '%', 'LIKE'));
                    $subCriteria->add(new \Criteria('form_desc', '%' . $queryArray[$idx] . '%', 'LIKE'), 'OR');
                    $subCriteria->add(new \Criteria('form_intro', '%' . $queryArray[$idx] . '%', 'LIKE'), 'OR');
                    $criteria->add($subCriteria, $qual);
                    unset($subCriteria);
                }

                $formObjArray = $formsHandler->getAll($criteria);
                foreach ($formObjArray as $id => $formObj) {
                    $ret[] = [
                        'image' => 'assets/images/icons/32/content.png',
                                    'link'  => 'index.php?form_id=' . $id,
                                    'title' => $formObj->getvar('form_title'),
                        'time'  => ($formObj->getVar('form_begin') > 0) ? $formObj->getVar('form_begin') : 0,
                    ];
                }
            }
        }
    }

    return $ret;
}
