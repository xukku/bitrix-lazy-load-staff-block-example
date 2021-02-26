<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$innerComponentDescription = require $_SERVER['DOCUMENT_ROOT'] . getLocalPath('components/bitrix/catalog.element/.description.php');

$arComponentDescription['NAME'] .= Loc::getMessage("CITRUS_AREALTY_AREALTY");
$arComponentDescription['PATH'] = array(
	"ID" => "citrus",
	"NAME" => Loc::getMessage("CITRUS_AREALTY_CITRUS"),
);
