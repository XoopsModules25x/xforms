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
 * @since           2.00
 * @param mixed $queryArray
 * @param mixed $andor
 * @param mixed $limit
 * @param mixed $offset
 * @param mixed $uid
 */

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * xforms_search()
 *
 * @uses CriteriaCompo
 * @uses Criteria
 * @uses Xmf\Module\Helper
 * @uses Xmf\Module\Helper\Permission
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
        $moduleDirName = basename(dirname(__DIR__));
        /** @var \XoopsModules\Xforms\Helper $helper */
        $helper             = \XoopsModules\Xforms\Helper::getInstance();
        $xformsFormsHandler = $helper->getHandler('Forms');

        // get all forms user has rights to view
        if ($permittedForms = $xformsFormsHandler->getPermittedForms()) {
            $pIdArray = [];
            foreach ($permittedForms as $pForm) {
                $pIdArray[] = $pForm->getVar('form_id');
            }
            $pIds = '(' . implode(',', $pIdArray) . ')';

            $criteria = new \CriteriaCompo();
            $criteria->setSort('form_order');
            $criteria->setOrder('ASC');
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
                    $qual        = (0 == $idx) ? 'AND' : $andor;
                    $subCriteria = new \CriteriaCompo();
                    $subCriteria->add(new \Criteria('form_title', "%{$queryArray[$idx]}%", 'LIKE'));
                    $subCriteria->add(new \Criteria('form_desc', "%{$queryArray[$idx]}%", 'LIKE'), 'OR');
                    $subCriteria->add(new \Criteria('form_intro', "%{$queryArray[$idx]}%", 'LIKE'), 'OR');
                    $criteria->add($subCriteria, $qual);
                    unset($subCriteria);
                }

                $formObjArray = $xformsFormsHandler->getAll($criteria);
                foreach ($formObjArray as $id => $formObj) {
                    $ret[] = [
                        'image' => 'assets/images/icons/32/content.png',
                        'link'  => "index.php?form_id={$id}",
                        'title' => $formObj->getvar('form_title'),
                        'time'  => ($formObj->getVar('form_begin') > 0) ? $formObj->getVar('form_begin') : 0,
                    ];
                }
            }
        }
    }

    return $ret;
}
