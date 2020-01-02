<?php namespace XoopsModules\Tdmcreate\Files\Templates\User;

use XoopsModules\Tdmcreate;
use XoopsModules\Tdmcreate\Files;
use XoopsModules\Tdmcreate\Files\Templates\User;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * tdmcreate module.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 *
 * @since           2.5.0
 *
 * @author          Txmod Xoops http://www.txmodxoops.org
 *
 * @version         $Id: Breadcrumbs.php 12258 2014-01-02 09:33:29Z timgno $
 */

/**
 * Class Breadcrumbs.
 */
class Breadcrumbs extends Files\CreateFile
{
    /**
    *  @public function constructor
    *  @param null
    */

    public function __construct()
    {
        parent::__construct();
    }

    /**
    *  @static function getInstance
    *  @param null
     * @return User\Breadcrumbs
     */
    public static function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
    *  @public function write
    *  @param string $module
    *  @param string $filename
     */
    public function write($module, $filename)
    {
        $this->setModule($module);
        $this->setFileName($filename);
    }

    /**
    *  @public function render
    *  @param null
     * @return bool|string
     */
    public function render()
    {
        $module = $this->getModule();
        $filename = $this->getFileName();
        $moduleDirname = $module->getVar('mod_dirname');
        $tf = Tdmcreate\Files\CreateFile::getInstance();
        $sc = Tdmcreate\Files\CreateSmartyCode::getInstance();
        $hc = Tdmcreate\Files\CreateHtmlCode::getInstance();
        $t = "\t";
        $title = $sc->getSmartyDoubleVar('itm', 'title');
        $titleElse = $sc->getSmartyDoubleVar('itm', 'title', $t."\t").PHP_EOL;
        $link = $sc->getSmartyDoubleVar('itm', 'link');
        $glyph = $hc->getHtmlTag('i', ['class' => 'glyphicon glyphicon-home'], '', false, true);
        $anchor = $hc->getHtmlAnchor('<{xoAppUrl index.php}>', $glyph, 'home');
        $into = $hc->getHtmlTag('li', ['class' => 'bc-item'], $anchor, false, true, $t) . PHP_EOL;
        $anchorIf = $hc->getHtmlAnchor($link, $title, $title, '', '', '', $t."\t").PHP_EOL;
        $breadcrumb = $sc->getSmartyConditions('itm.link', '', '', $anchorIf, $titleElse, false, false, $t);
        $foreach = $hc->getHtmlTag('li', ['class' => 'bc-item'], $breadcrumb, false, false, $t);
        $into .= $sc->getSmartyForeach('itm', 'xoBreadcrumbs', $foreach, 'bcloop', '', $t);

        $content = $hc->getHtmlTag('ol', ['class' => 'breadcrumb'], $into);

        $tf->create($moduleDirname, 'templates', $filename, $content, _AM_TDMCREATE_FILE_CREATED, _AM_TDMCREATE_FILE_NOTCREATED);

        return $tf->renderFile();
    }
}