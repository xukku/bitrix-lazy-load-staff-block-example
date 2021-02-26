<?php

use Citrus\Arealty\Template\TemplateHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

$offersIblockId = TemplateHelper::getIblock('offers');
if (!$offersIblockId) {
	return;
}
$aMenuLinks = array_merge($aMenuLinks, $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
    "",
    array(
        "IBLOCK_TYPE" => "realty",
        "IBLOCK_ID" => $offersIblockId,
        "DEPTH_LEVEL" => "2",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
    ),
    false,
    array('HIDE_ICONS' => 'Y')
));
