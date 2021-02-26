<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

use Bitrix\Main\Localization\Loc;


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"DATA" => Array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_TEMPLATE_COMPONENT_PARAM_DATA"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, true);
?>
