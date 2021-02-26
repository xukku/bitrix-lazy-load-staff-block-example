<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Object\Address;
use Citrus\Arealty;
use Citrus\Arealty\Object\GeoProperty;
use function \Citrus\Core\array_get;

/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$this->__component->setResultCacheKeys(["DEAL_TYPE", "CONTACT", 'COMPLEX', 'OFFERS', 'OFFERS_FIELDS', 'SECTION_FIELDS', 'DRAFT_FOR']);

$arResult['SECTION_FIELDS'] = null;
if (Loader::includeModule('iblock'))
{
    $dbRes = CIBlockSection::GetList([], [
    	'IBLOCK_ID' => $arParams['IBLOCK_ID'],
	    'ACTIVE' => 'Y',
	    'GLOBAL_ACTIVE' => 'Y',
	    'ID' => $arResult['IBLOCK_SECTION_ID'],
    ], false, ['ID', 'UF_HIDE_IP']);
    $arResult['SECTION_FIELDS'] = $dbRes->Fetch();
}

$contactId = array_get($arResult, 'PROPERTIES.contact.VALUE');
$arResult["CONTACT"] = null;
if ($contactId)
{
	if ($stafIblockId = Arealty\Template\TemplateHelper::requireIblock('staff')) {
		$contactDataset = CIBlockElement::GetList([], ['IBLOCK_ID' => $stafIblockId, '=ID' => $contactId])
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

if ($dealType = array_get($arResult, 'PROPERTIES.deal_type.VALUE'))
{
	$arResult['DEAL_TYPE'] = is_array($dealType) ? reset($dealType) : $dealType;
}

$arResult['ADDRESS'] = $address = Address::createFromFields($arResult);

// убрать свойства которые не выбраны у раздела (вкладка "Свойства элементов")
$displayProperties = array_column(\CIBlockSectionPropertyLink::GetArray(
	$arResult['IBLOCK_ID'],
	$arResult['IBLOCK_SECTION_ID']
), null, 'PROPERTY_ID');
foreach ($arResult['DISPLAY_PROPERTIES'] as $k => $v)
{
	if (!isset($displayProperties[$v['ID']]))
	{
		unset($arResult['DISPLAY_PROPERTIES'][$k]);
	}
}

$arResult['NAME'] = \Citrus\Arealty\Helper::fixDraftTitle(
	trim(str_replace(GeoProperty::getSeoValue($address->getGeo()), '', $arResult['NAME']), ', '),
	$isDraft
);

if (array_get($arParams, 'DETAIL_DISPLAY_NUMBER', 'N') == 'Y')
{
	$arResult['NAME'] .= Loc::getMessage('CITRUS_AREALTY_DETAIL_TITLE_NUMBER', array('#NUM#' => $arResult['ID']));
}

if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y')
{
	// Свойства объектов для отображения, выбранные в Доп. полей раздела
	$displayProperties = new Arealty\DisplayProperties($arResult['IBLOCK_ID']);
	$displayPropertiesByXmlId = [
		$arResult['XML_ID'] => $displayProperties->getForSection($arResult['IBLOCK_SECTION_ID'], $isDefault)
	];

	try
	{
		$offersIblockId = Arealty\Template\TemplateHelper::requireOffersIblock();
		if ($offersIblockId) {
			$complexService = new Arealty\ComplexService($offersIblockId);
			$arResult['OFFERS_FIELDS'] = reset($complexService->getOfferFields(array($arResult['XML_ID']), [], $displayPropertiesByXmlId));
			$tmp = current($displayPropertiesByXmlId);
			$costTitle = $tmp['cost'];
			// убрать Цена из списка свойств
			unset($arResult['OFFERS_FIELDS']['display'][$costTitle]);
		}
	}
	catch (\Exception $e)
	{
		ShowError($e->getMessage());
		$arResult['OFFERS_FIELDS'] = null;
	}
}
else
{
	$arResult['COMPLEX'] = array_get($arResult, 'PROPERTIES.complex.VALUE');
}

$arResult['DRAFT_FOR'] = false;
if (!empty($arResult['PROPERTIES']['draft_for']['VALUE']))
{
	$arResult['DRAFT_FOR'] = $arResult['PROPERTIES']['draft_for']['VALUE'];
}