<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MMS_COMMENTS_NAME"),
	"DESCRIPTION" => GetMessage("MMS_COMMENTS_DESCRIPTION"),
	"ICON" => "/images/comments.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "comments",
			"NAME" => GetMessage("MMS_COMMENTS_NAME"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "comments_cmpx",
			),
		),
	),
);
