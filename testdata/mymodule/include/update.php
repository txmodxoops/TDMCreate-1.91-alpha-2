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
 * Mymodule module for xoops
 *
 * @param mixed      $module
 * @param null|mixed $prev_version
 * @package        Mymodule
 * @since          1.0
 * @min_xoops      2.5.9
 * @author         Wedega - Email:<webmaster@wedega.com> - Website:<https://wedega.com>
 * @version        $Id: 1.0 update.php 1 Mon 2018-03-19 10:04:53Z XOOPS Project (www.xoops.org) $
 * @copyright      module for xoops
 * @license        GPL 2.0 or later
 */

/**
 * @param      $module
 * @param null $prev_version
 *
 * @return bool|null
 */
function xoops_module_update_mymodule(&$module, $prev_version = null)
{
    $ret = null;
    if ($prev_version < 10) {
        $ret = update_mymodule_v10($module);
    }

    $ret = mymodule_check_db($module);

    //check upload directory
	include_once __DIR__ . '/install.php';
    $ret = xoops_module_install_mymodule($module);

    $errors = $module->getErrors();
    if (!empty($errors)) {
        print_r($errors);
    }

    return $ret;

}

// irmtfan bug fix: solve templates duplicate issue
/**
 * @param $module
 *
 * @return bool
 */
function update_mymodule_v10($module)
{
    global $xoopsDB;
    $result = $xoopsDB->query(
        'SELECT t1.tpl_id FROM ' . $xoopsDB->prefix('tplfile') . ' t1, ' . $xoopsDB->prefix('tplfile') . ' t2 WHERE t1.tpl_refid = t2.tpl_refid AND t1.tpl_module = t2.tpl_module AND t1.tpl_tplset=t2.tpl_tplset AND t1.tpl_file = t2.tpl_file AND t1.tpl_type = t2.tpl_type AND t1.tpl_id > t2.tpl_id'
    );
    $tplids = [];
    while (false !== (list($tplid) = $xoopsDB->fetchRow($result))) {
        $tplids[] = $tplid;
    }
    if (count($tplids) > 0) {
        $tplfileHandler  = xoops_getHandler('tplfile');
        $duplicate_files = $tplfileHandler->getObjects(new \Criteria('tpl_id', '(' . implode(',', $tplids) . ')', 'IN'));

        if (count($duplicate_files) > 0) {
            foreach (array_keys($duplicate_files) as $i) {
                $tplfileHandler->delete($duplicate_files[$i]);
            }
        }
    }
    $sql = 'SHOW INDEX FROM ' . $xoopsDB->prefix('tplfile') . " WHERE KEY_NAME = 'tpl_refid_module_set_file_type'";
    if (!$result = $xoopsDB->queryF($sql)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);

        return false;
    }
    $ret = [];
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret[] = $myrow;
    }
    if (!empty($ret)) {
        $module->setErrors("'tpl_refid_module_set_file_type' unique index is exist. Note: check 'tplfile' table to be sure this index is UNIQUE because XOOPS CORE need it.");

        return true;
    }
    $sql = 'ALTER TABLE ' . $xoopsDB->prefix('tplfile') . ' ADD UNIQUE tpl_refid_module_set_file_type ( tpl_refid, tpl_module, tpl_tplset, tpl_file, tpl_type )';
    if (!$result = $xoopsDB->queryF($sql)) {
        xoops_error($xoopsDB->error() . '<br>' . $sql);
        $module->setErrors("'tpl_refid_module_set_file_type' unique index is not added to 'tplfile' table. Warning: do not use XOOPS until you add this unique index.");

        return false;
    }

    return true;
}

// irmtfan bug fix: solve templates duplicate issue

/**
 * @param $module
 *
 * @return bool
 */
function mymodule_check_db($module)
{
    $ret = true;
	//insert here code for database check

    return $ret;
}
