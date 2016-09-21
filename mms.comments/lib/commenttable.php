<?php

namespace Mms\Comments;

use Bitrix\Iblock;
use Bitrix\Main;

class CommentTable extends Iblock\ElementTable
{
    public static function getMap()
    {
        $arMap = parent::getMap();
        $arMap["PROPERTIES"] = new Main\Entity\ReferenceField(
            "PROPERTIES",
            "Mms\\Comments\\CommentProperty",
            array("=this.ID" => "ref.IBLOCK_ELEMENT_ID"),
            array("join_type" => "LEFT")
        );

        return $arMap;
    }

    public static function getPropertyTypeValueId($typeCode)
    {
        $arPropertyEnum = Iblock\PropertyEnumerationTable::getList(array(
            "filter" => array("XML_ID" => $typeCode, "PROPERTY.CODE" => MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE),
            "select" => array("ID")
        ))->fetch();
        return $arPropertyEnum["ID"];
    }
}
