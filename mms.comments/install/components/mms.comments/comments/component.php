<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CommentsComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Mms\Comments;

if(!Loader::includeModule("iblock"))
{
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

if(!Loader::includeModule("mms.comments"))
{
    ShowError(GetMessage("MMS_COMMENTS_MODULE_NOT_INSTALLED"));
    return;
}

if( !$arParams["COMMENT_OBJECT_TYPE"] || ($arParams["COMMENT_OBJECT_TYPE"] != MMS_COMMENTS_PROPERTY_OBJECT_TYPE_ELEMENT
        && $arParams["COMMENT_OBJECT_TYPE"] != MMS_COMMENTS_PROPERTY_OBJECT_TYPE_PAGE) )
{
    ShowError(GetMessage("MMS_COMMENTS_WRONG_OBJECT_TYPE"));
    return;
}

$arParams["COMMENT_OBJECT_TYPE"] = Comments\CommentTable::getPropertyTypeValueId($arParams["COMMENT_OBJECT_TYPE"]);

$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->getTemplateName());
$request = Context::getCurrent()->getRequest();
$commentText = $request->getPost('comment_text');

if (!empty($commentText) && $request->getPost('params_hash') == $arResult["PARAMS_HASH"])
{
    $parentCommentId = $request->getPost('parent_id');
    $this->addComment($commentText, $parentCommentId, $arParams);
}

$arSelect = array(
    "ID",
    "NAME",
    "DETAIL_TEXT",
    "CREATED_BY_USER",
    "DATE_CREATE"
);

$arFilter = array(
    "IBLOCK_ID" => $this->iblockId,
    "ACTIVE" => "Y",
    "=PROPERTIES.PROPERTY_{$this->propObjectId}" => $arParams["COMMENT_OBJECT"],
    "=PROPERTIES.PROPERTY_{$this->propObjectTypeId}" => $arParams["COMMENT_OBJECT_TYPE"],
    "=PROPERTIES.PROPERTY_{$this->propParentId}" => false
);

$arOrder = array("DATE_CREATE" => "ASC");

$dbComments = Comments\CommentTable::getList(array("select" => $arSelect, "filter" => $arFilter, "order" => $arOrder));
$depthLevel = 1;

while($arComment = $dbComments->fetch())
{
    $arComment["DEPTH_LEVEL"] = $depthLevel;
    $arFilter["=PROPERTIES.PROPERTY_{$this->propParentId}"] = $arComment["ID"];
    $arChildren = $this->getChildren($arSelect, $arFilter, $arOrder, $depthLevel);
    $arComment["IS_PARENT"] = ((!empty($arChildren)) ? true : false);
    $arComment["DATE"] = $arComment["DATE_CREATE"]->toString();
    $arComment["USER"] = (!empty($arComment["MMS_COMMENTS_COMMENT_CREATED_BY_USER_ID"]) ? $this->getUserName($arComment["MMS_COMMENTS_COMMENT_CREATED_BY_USER_ID"]) : "");
    $arResult["ITEMS"][] = $arComment;
    $arResult["ITEMS"] = array_merge($arResult["ITEMS"], $arChildren);
}

$this->includeComponentTemplate();
