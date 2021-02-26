<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (empty($arParams['CONTACT_ID']))
{
	return;
}

\CModule::IncludeModule('iblock');

$contactId = $arParams['CONTACT_ID'];
//FIX copy from (change path to template) \local\components\citrus\realty.catalog.element\templates\catalog_detail_custom\result_modifier.php
$arResult["CONTACT"] = null;
if ($contactId)
{
	if ($stafIblockId = \Citrus\Arealty\Template\TemplateHelper::requireIblock('staff')) {
		$contactDataset = \CIBlockElement::GetList([], ['IBLOCK_ID' => $stafIblockId, '=ID' => $contactId])
			->GetNextElement(true, false);
	} else {
		$contactDataset = null;
	}

	if ($contactDataset && ($arResult["CONTACT"] = $contactDataset->GetFields()))
	{
		$arResult["CONTACT"]["PROPERTIES"] = $contactDataset->GetProperties();
		\Citrus\Arealty\Template\Property::initOfficeAddress($arResult["CONTACT"]);
	}
}

if (empty($arResult['CONTACT']))
{
	return;
}

$APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	[
		"AREA_FILE_SHOW" => "component",
		"_COMPONENT" => "citrus:template",
		"_COMPONENT_TEMPLATE" => "staff-block",
		"ITEM" => $arResult['CONTACT'],
		"h" => "h2.h1",
		"TITLE" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK"),
		"DESCRIPTION" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK_DESc"),
		"PAGE_SECTION" => "Y",
		"PADDING" => "Y",
		"SECTION_CLASS" => "page-break-inside",

		"LAZY_LOAD" => "N",
	],
	$component,
	['HIDE_ICONS' => 'Y']
);
