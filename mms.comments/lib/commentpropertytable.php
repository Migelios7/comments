<?php
namespace Mms\Comments;

use Bitrix\Main\Entity,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CommentPropertyTable
 **/

class CommentPropertyTable extends Entity\DataManager
{
	/**
	 * Returns path to the file which contains definition of the class.
	 *
	 * @return string
	 */
	public static function getFilePath()
	{
		return __FILE__;
	}

	/**
	 * Returns DB table name for entity
	 *
	 * @return string
	 */
	public static function getTableName()
	{
	    $iblockID = Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id");
		return "b_iblock_element_prop_s{$iblockID}";
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
	    $propObjectTypeId = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE));
        $propObjectId     = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_CODE));
        $propParentId     = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_PARENT_CODE));

		return array(
            "IBLOCK_ELEMENT_ID" => new Entity\IntegerField("IBLOCK_ELEMENT_ID", array(
                "primary" => true,
                "autocomplete" => true,
                "title" => Loc::getMessage("MMS_COMMENTS_ELEMENT_PROPERTY_ENTITY_IBLOCK_ELEMENT_ID_FIELD"),
            )),
            "PROPERTY_{$propObjectTypeId}" => new Entity\IntegerField("PROPERTY_{$propObjectTypeId}", array(
                "title" => Loc::getMessage("MMS_COMMENTS_ELEMENT_PROPERTY_ENTITY_PROPERTY_OBJECT_TYPE"),
            )),
            "PROPERTY_{$propObjectId}" => new Entity\StringField("PROPERTY_{$propObjectId}", array(
                "title" => Loc::getMessage("MMS_COMMENTS_ELEMENT_PROPERTY_ENTITY_PROPERTY_OBJECT"),
            )),
            "PROPERTY_{$propParentId}" => new Entity\IntegerField("PROPERTY_{$propParentId}", array(
                "title" => Loc::getMessage("MMS_COMMENTS_ELEMENT_PROPERTY_ENTITY_PROPERTY_PARENT"),
            )),
            "ELEMENT" => new Entity\ReferenceField(
                "ELEMENT",
                "Bitrix\\Iblock\\Element",
                array("=this.IBLOCK_ELEMENT_ID" => "ref.ID"),
                array("join_type" => "LEFT")
            ),
		);
	}
}
