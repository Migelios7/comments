<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule("mms.comments"))
{
    return;
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"AJAX_MODE" => array(
            "PARENT" => "AJAX",
            "NAME" => GetMessage("MMS_COMMENTS_IS_AJAX"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => 'Y',
            "REFRESH" => "Y",
        ),
		"COMMENT_OBJECT_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MMS_COMMENTS_OBJECT_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array(MMS_COMMENTS_PROPERTY_OBJECT_TYPE_PAGE => "Страница", MMS_COMMENTS_PROPERTY_OBJECT_TYPE_ELEMENT => "Элемент"),
			"DEFAULT" => MMS_COMMENTS_PROPERTY_OBJECT_TYPE_PAGE,
			"REFRESH" => "Y",
		),
		"COMMENT_OBJECT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MMS_COMMENTS_OBJECT"),
			"TYPE" => "STRING",
			"DEFAULT" => "news",
		)
	)
);
