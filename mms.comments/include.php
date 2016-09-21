<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;

require_once("constants.php");

Loader::registerAutoLoadClasses('mms.comments', array(
    'Mms\Comments\CommentTable' => 'lib/commenttable.php',
    'Mms\Comments\CommentPropertyTable' => 'lib/commentpropertytable.php',
    'MmsCommentsHandlers' => 'lib/handlers.php',
));

$arJSCoreConfig = array(
    'bootstrap' => array(
        'js'        => '/bitrix/js/mms.comments/bootstrap.min.js',
        'css'       => '/bitrix/css/mms.comments/bootstrap.min.css',
        'lang'      => '',
        'rel'       => array('jquery2'),
        'skip_core' => true,
        'use'       => CJSCore::USE_PUBLIC
    ),
    'comments' => array(
        'js'        => '/bitrix/js/mms.comments/comments.js',
        'css'       => '/bitrix/css/mms.comments/comments.css',
        'lang'      => '',
        'rel'       => array('bootstrap'),
        'skip_core' => true,
        'use'       => CJSCore::USE_PUBLIC
    ),
);

foreach ($arJSCoreConfig as $ext => $arExt)
{
    CJSCore::RegisterExt($ext, $arExt);
}
