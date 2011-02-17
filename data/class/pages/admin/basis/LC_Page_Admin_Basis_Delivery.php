<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 配送業者設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Delivery extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->tpl_subtitle = '配送業者設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $mode = $this->getMode();

        if (!empty($_POST)) {
            $this->arrErr = $this->lfCheckError($mode);
            if (!empty($this->arrErr['deliv_id'])) {
                SC_Utils_Ex::sfDispException();
                return;
            }
        }

        switch($mode) {
        case 'delete':
            // ランク付きレコードの削除
            $objDb->sfDeleteRankRecord("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        case 'up':
            $objDb->sfRankUp("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        case 'down':
            $objDb->sfRankDown("dtb_deliv", "deliv_id", $_POST['deliv_id']);
            $this->objDisplay->reload(); // PRG pattern
            break;
        default:
            break;
        }

        $this->arrDelivList = $this->lfGetDelivList();
    }

    /**
     * 配送業者一覧の取得
     *
     * @return array
     */
    function lfGetDelivList() {
        $objQuery =& SC_Query::getSingletonInstance();

        $col = "deliv_id, name, service_name";
        $where = "del_flg = 0";
        $table = "dtb_deliv";
        $objQuery->setOrder("rank DESC");

        return $objQuery->select($col, $table, $where);
    }

    /**
     * 入力エラーチェック
     *
     * @param string $mode
     * @return array
     */
    function lfCheckError($mode) {
        $arrErr = array();
        switch ($mode) {
            case "delete":
            case "up":
            case "down":
                $this->objFormParam = new SC_FormParam();
                $this->objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $this->objFormParam->setParam($_POST);
                $this->objFormParam->convParam();

                $arrErr = $this->objFormParam->checkError();
                break;
            default:
                break;
        }
        return $arrErr;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
