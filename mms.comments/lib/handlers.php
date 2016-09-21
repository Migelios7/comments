<?php

use Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

class MmsCommentsHandlers
{
    function beforeIBlockPropertyUpdateHandler($arFields)
    {
        if ($arFields["IBLOCK_ID"] == Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id"))
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("MMS_COMMENTS_DISABLE_EDIT_IBLOCK_PROPERTY"));
            return false;
        }
    }

    function beforeIBlockPropertyDeleteHandler($id)
    {
        if ($id == Option::get(MMS_COMMENTS_MODULE_ID, "property_object_type")
            || $id == Option::get(MMS_COMMENTS_MODULE_ID, "property_object")
            || $id == Option::get(MMS_COMMENTS_MODULE_ID, "property_parent"))
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("MMS_COMMENTS_DISABLE_DELETE_IBLOCK_PROPERTY"));
            return false;
        }
    }

    function beforeIBlockUpdateHandler($arFields)
    {
        if ($arFields["ID"] == Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id"))
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("MMS_COMMENTS_DISABLE_EDIT_IBLOCK"));
            return false;
        }
    }

    function beforeIBlockDeleteHandler($id)
    {
        if ($id == Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id"))
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("MMS_COMMENTS_DISABLE_DELETE_IBLOCK"));
            return false;
        }
    }

    function beforeIBlockElementUpdateHandler($arFields)
    {
        if ($arFields["IBLOCK_ID"] == Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id"))
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("MMS_COMMENTS_DISABLE_EDIT_COMMENT_RECORD"));
            return false;
        }
    }

    function beforeIBlockElementDeleteHandler($id)
    {
        if(Loader::includeModule("iblock"))
        {
            $iblockId = CIBlockElement::GetIBlockByID($id);

            if ($iblockId == Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id"))
            {
                $obIblockElement = new CIBlockElement();
                $dbComments = $obIblockElement->GetList(array(), array("IBLOCK_ID" => $iblockId, "=PROPERTY_PARENT_COMMENT" => $id), false, false, array("ID"));

                while ($arComment = $dbComments->Fetch())
                {
                    $obIblockElement->Delete($arComment["ID"]);
                }
            }
        }
        else
        {
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return false;
        }
    }
}
