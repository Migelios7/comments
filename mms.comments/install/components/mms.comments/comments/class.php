<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Mms\Comments;

/**
 * Class CommentsComponent
 */
class CommentsComponent extends CBitrixComponent
{
    /**
     * @var string
     */
    public $iblockId;
    /**
     * @var string
     */
    public $propObjectTypeId;
    /**
     * @var string
     */
    public $propObjectId;
    /**
     * @var string
     */
    public $propParentId;

    /**
     * CommentsComponent constructor.
     * @param CBitrixComponent|null $component
     */
    public function __construct($component)
    {
        Loader::includeModule("mms.comments");
        parent::__construct($component);

        $this->iblockId = Option::get(MMS_COMMENTS_MODULE_ID, "iblock_id");
        $this->propObjectTypeId = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE));
        $this->propObjectId = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_OBJECT_CODE));
        $this->propParentId = Option::get(MMS_COMMENTS_MODULE_ID, mb_strtolower(MMS_COMMENTS_PROPERTY_PARENT_CODE));

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        if (!$request->get("bxajaxid"))
        {
            CJSCore::Init(array("bootstrap", "comments"));
        }
    }

    /**
     * @param array $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams["AJAX_MODE"] = "Y";
        return $arParams;
    }

    /**
     * @param array $arSelect
     * @param array $arFilter
     * @param array $arOrder
     * @param int $depthLevel
     * @return array
     */
    public function getChildren($arSelect, $arFilter, $arOrder, $depthLevel = 1)
    {
        $arReturn = array();
        $dbComments = Comments\CommentTable::getList(array("select" => $arSelect, "filter" => $arFilter, "order" => $arOrder));

        $depthLevel++;

        while ($arComment = $dbComments->fetch())
        {
            $arComment["DEPTH_LEVEL"] = $depthLevel;
            $arFilter["=PROPERTIES.PROPERTY_{$this->propParentId}"] = $arComment["ID"];
            $arChildren = $this->getChildren($arSelect, $arFilter, $arOrder, $depthLevel);
            $arComment["IS_PARENT"] = ((!empty($arChildren)) ? true : false);
            $arComment["DATE"] = $arComment["DATE_CREATE"]->toString();
            $arComment["USER"] = (!empty($arComment["MMS_COMMENTS_COMMENT_CREATED_BY_USER_ID"]) ? $this->getUserName($arComment["MMS_COMMENTS_COMMENT_CREATED_BY_USER_ID"]) : "");
            $arReturn[] = $arComment;
            $arReturn = array_merge($arReturn, $arChildren);
        }

        return $arReturn;
    }

    /**
     * @param string $userId
     * @return string
     */
    public function getUserName($userId)
    {
        $nameString = "";
        $dbUser = \Bitrix\Main\UserTable::getList(array(
            "select" => array("LAST_NAME", "NAME", "SECOND_NAME"),
            "filter" => array("=ID" => $userId)
        ));

        while ($arUser = $dbUser->fetch())
        {
            $nameString .= (($arUser["LAST_NAME"]) ? "{$arUser["LAST_NAME"]} " : "");
            $nameString .= (($arUser["NAME"]) ? "{$arUser["NAME"]} " : "");
            $nameString .= (($arUser["SECOND_NAME"]) ? "{$arUser["SECOND_NAME"]} " : "");
        }

        return $nameString;
    }

    /**
     * @param string $text
     * @param string $parent
     * @param array $arParams
     * @return mixed
     */
    public function addComment($text, $parent, $arParams)
    {
        global $USER;
        $el = New CIBlockElement();
        $userName = (($USER->IsAuthorized()) ? $this->getUserName($USER->GetID()) : "");
        $obDate = new Bitrix\Main\Type\DateTime();
        $date = $obDate->toString();
        $arFields = array(
            "IBLOCK_ID" => $this->iblockId,
            "NAME" => "Коментарий {$userName}{$date}",
            "DETAIL_TEXT" => $text,
            "PROPERTY_VALUES" => array(
                MMS_COMMENTS_PROPERTY_OBJECT_TYPE_CODE => $arParams["COMMENT_OBJECT_TYPE"],
                MMS_COMMENTS_PROPERTY_OBJECT_CODE => $arParams["COMMENT_OBJECT"],
                MMS_COMMENTS_PROPERTY_PARENT_CODE => $parent
            ),
        );

        if ($ID = $el->Add($arFields))
        {
            return $ID;
        }
        else
        {
            return false;
        }
    }
}
