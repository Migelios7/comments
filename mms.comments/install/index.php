<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SiteTable;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/mms.comments/constants.php");

Loc::loadMessages(__FILE__);

if (class_exists('mms_comments'))
{
    return;
}

class mms_comments extends CModule
{
    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    /** @var integer */
    public $iblockId;

    /** @var string */
    public $errors;

    public function __construct()
    {
        global $APPLICATION;

        if(!Loader::includeModule("iblock"))
        {
            $APPLICATION->ThrowException(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        }

        $this->MODULE_ID = MMS_COMMENTS_MODULE_ID;
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2016-09-20 16:23:14";
        $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION");
        $this->MODULE_GROUP_RIGHTS = "N";
        $this->PARTNER_NAME = "Mms";
        $this->PARTNER_URI = "";
    }

    public function doInstall()
    {
        global $APPLICATION;
        if ($this->prepareIblock())
        {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->installFiles();
            $this->registerModuleDependences();
        }
        else
        {
            $APPLICATION->ThrowException($this->errors);
        }
    }

    public function prepareIblock()
    {
        global $DB;

        $DB->StartTransaction();

        if ($this->addCommentsIblockType() && $this->addCommentsIblock() && $this->addProperties())
        {
            $DB->Commit();
            return true;
        }
        else
        {
            $DB->Rollback();
            return false;
        }
    }

    public function addCommentsIblockType()
    {
        $obIblockType = new CIBlockType;

        $result = $obIblockType->Add(array(
            "ID" => MMS_COMMENTS_IBLOCK_TYPE_CODE,
            "SECTIONS" => "N",
            "IN_RSS" => "N",
            "SORT" => 100,
            "LANG" => array(
                "ru" => array(
                    "NAME" => "Комментарии",
                    "ELEMENT_NAME" => "Комментарий"
                ),
                "en" => array(
                    "NAME" => "Comments",
                    "ELEMENT_NAME" => "Comment"
                )
            )
        ));

        if(!$result)
        {
            $this->errors .= 'Error: '.$obIblockType->LAST_ERROR.'<br>';
            return false;
        }
        else
        {
            return true;
        }
    }

    public function addCommentsIblock()
    {
        $obIblock = new CIBlock;
        $arSites = array();

        $result = SiteTable::getList();
        while ($site = $result->fetch())
        {
            $arSites[] = $site["LID"];
        }

        $id = $obIblock->Add(array(
                "ACTIVE" => "Y",
                "NAME" => "Комментарии",
                "CODE" => MMS_COMMENTS_IBLOCK_CODE,
                "LIST_PAGE_URL" => "",
                "DETAIL_PAGE_URL" => "",
                "IBLOCK_TYPE_ID" => MMS_COMMENTS_IBLOCK_TYPE_CODE,
                "SITE_ID" => $arSites,
                "SORT" => 100,
                "PICTURE" => array(),
                "DESCRIPTION" => "",
                "DESCRIPTION_TYPE" => "",
                "GROUP_ID" => array("2" => "R"),
                "VERSION" => MMS_COMMENTS_PROPERTIES_IN_IBLOCK_TABLE,
                "INDEX_ELEMENT" => "N",
                "INDEX_SECTION" => "N"
            )
        );

        if(!$id)
        {
            $this->errors .= 'Error: '.$obIblock->LAST_ERROR.'<br>';
            return false;
        }
        else
        {
            $this->iblockId = $id;
            \Bitrix\Main\Config\Option::set($this->MODULE_ID, "iblock_id", $id);
            return true;
        }
    }

    public function addProperties()
    {
        $obProperty = new CIBlockProperty();

        $id = $obProperty->Add(array(
                "NAME" => "Тип объекта комментария",
                "ACTIVE" => "Y",
                "SORT" => "100",
                "CODE" => MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE,
                "PROPERTY_TYPE" => "L",
                "IBLOCK_ID" => $this->iblockId,
                "VALUES" => array(
                    array(
                        "VALUE" => "Элемент",
                        "DEF" => "N",
                        "SORT" => "100",
                        "XML_ID" => MMS_COMMENTS_PROPERTY_OBJECT_TYPE_ELEMENT
                    ),
                    array(
                        "VALUE" => "Страница",
                        "DEF" => "N",
                        "SORT" => "200",
                        "XML_ID" => MMS_COMMENTS_PROPERTY_OBJECT_TYPE_PAGE
                    ),
                ),
            )
        );

        if(!$id)
        {
            $this->errors .= 'Error: '.$obProperty->LAST_ERROR.'<br>';
            return false;
        }
        else
        {
            \Bitrix\Main\Config\Option::set($this->MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE), $id);
        }

        $id = $obProperty->Add(array(
                "NAME" => "Объект комментария",
                "ACTIVE" => "Y",
                "SORT" => "200",
                "CODE" => MMS_COMMENTS_PROPERTY_OBJECT_CODE,
                "PROPERTY_TYPE" => "S",
                "IBLOCK_ID" => $this->iblockId,
            )
        );

        if(!$id)
        {
            $this->errors .= 'Error: '.$obProperty->LAST_ERROR.'<br>';
            return false;
        }
        else
        {
            \Bitrix\Main\Config\Option::set($this->MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_CODE), $id);
        }

        $id = $obProperty->Add(array(
                "NAME" => "Родитель",
                "ACTIVE" => "Y",
                "SORT" => "300",
                "CODE" => MMS_COMMENTS_PROPERTY_PARENT_CODE,
                "PROPERTY_TYPE" => "E",
                "IBLOCK_ID" => $this->iblockId,
                "LINK_IBLOCK_ID" => $this->iblockId,
            )
        );

        if(!$id)
        {
            $this->errors .= 'Error: '.$obProperty->LAST_ERROR.'<br>';
            return false;
        }
        else
        {
            \Bitrix\Main\Config\Option::set($this->MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_PARENT_CODE), $id);
        }

        return true;
    }

    function installFiles()
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/", true, true);
        return true;
    }

    public function registerModuleDependences()
    {
        RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyUpdateHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyUpdateHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockPropertyDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyDeleteHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockUpdateHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockDeleteHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockElementUpdateHandler");

        RegisterModuleDependences("iblock", "OnBeforeIBlockElementDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockElementDeleteHandler");
    }

    public function doUninstall()
    {
        $this->unRegisterModuleDependences();
        $this->unInstallFiles();
        $this->delCommentsIblockType();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function unRegisterModuleDependences()
    {
        UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyUpdateHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyAdd", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyUpdateHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockPropertyDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockPropertyDeleteHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockUpdateHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockDeleteHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockElementUpdateHandler");

        UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementDelete", $this->MODULE_ID, "MmsCommentsHandlers", "beforeIBlockElementDeleteHandler");
    }

    function unInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID."/");
        DeleteDirFilesEx("/bitrix/css/".$this->MODULE_ID."/");
        return true;
    }

    public function delCommentsIblockType()
    {
        Bitrix\Main\Config\Option::delete($this->MODULE_ID);
        $obIblockType = new CIBlockType;
        return $obIblockType->Delete(MMS_COMMENTS_IBLOCK_TYPE_CODE);
    }
}
