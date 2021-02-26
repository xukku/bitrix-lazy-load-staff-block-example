<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

global $APPLICATION;

if (0 < (int)$arResult["VARIABLES"]["SECTION_ID"])
{
	$APPLICATION->SetPageProperty("activeCatalogSectionID", $arResult["VARIABLES"]["SECTION_ID"]);
	$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
} elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
{
	$APPLICATION->SetPageProperty("activeCatalogSectionCode", $arResult["VARIABLES"]["SECTION_CODE"]);
	$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
}
?>
<?$APPLICATION->IncludeComponent(
	"citrus:realty.favourites",
	"block",
	array(
        "PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["favourites"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>

<?php
$showDeactivated = (\CModule::IncludeModule('citrus.arealtypro')
	&& function_exists('\\Citrus\\ArealtyPro\\checkOfferParam')
	&& \Citrus\ArealtyPro\checkOfferParam(
	$arResult['VARIABLES']['ELEMENT_CODE'],
	'',
	$USER->GetID()
))? 'Y' : 'N';
?>

<?$elementId = $APPLICATION->IncludeComponent(
	"citrus:realty.catalog.element",
	"catalog_detail",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"CACHE_TYPE" => $showDeactivated == 'Y'? 'N' : $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $showDeactivated == 'Y'? 0 : $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $showDeactivated == 'Y'? 'N' : $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
		"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
		"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"PDF_DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["pdf"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
		'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],

		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
		'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
		'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
		'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
		'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
		'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
		'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
		'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
		'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
		'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
		'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
		'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
		'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
		'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
		"DISPLAY_PREVIEW_TEXT_MODE" => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
		"DETAIL_PICTURE_MODE" => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
		'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
		'DETAIL_DISPLAY_NUMBER' => $arParams['DETAIL_DISPLAY_NUMBER'],
		"IMAGE_RESOLUTION" => (isset($arParams["IMAGE_RESOLUTION"]) ? $arParams["IMAGE_RESOLUTION"] : '16by9'),

		'SHOW_DEACTIVATED' => $showDeactivated,
	),
	$component
);?><?

if (0 < $elementId && 'Y' == $arParams['DETAIL_SHOW_SIMILAR_OFFERS'])
{
	global $seeAlsoFilter;

	$seeAlsoFilter = array(
		"!ID" => $elementId,
		"SECTION_ID" => \Citrus\Arealty\Helper::getLastSection(),
	);

	// get similar props list
	$resSection = \CIBlockSection::GetList(
		array(),
		array(
			'ID' => \Citrus\Arealty\Helper::getLastSection(),
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		),
		false,
		array(
			"UF_SIMILAR_PROPS"
		)
	);
	$rowSection = $resSection->GetNext();
	$sectionSimilarProps = $rowSection["~UF_SIMILAR_PROPS"];
	if (trim($sectionSimilarProps) != "") {
		$sectionSimilarProps = json_decode($sectionSimilarProps, true);
	}

	/**
     * Проверяет, есть ли записи при выборке с указанным фильтром
     *
	 * @param array $filter
	 * @return bool
	 */
	$hasResults = function($filter) use ($arParams) {
		$filter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
		$rsElement = CIBlockElement::GetList(
			array(),
			$filter,
			$arGroupBy = false,
			$arNavStartParams = array('nTopCount' => 1),
			$arSelectFields = Array("ID")
		);
		return (bool)$rsElement->SelectedRowsCount();
	};

	if (empty($sectionSimilarProps))
	{
		$filter = \Citrus\Arealty\Helper::getLastFilter();
	}
	else
	{
		// init filter for similar props
		$filter = array();
		foreach ($sectionSimilarProps["CODE"] as $i => $code)
		{
			$resItemProp = \CIBlockElement::GetProperty(
				$arParams['IBLOCK_ID'],
				$elementId,
				array("sort" => "asc"),
				array("CODE" => $code)
			);
			$itemProp = $resItemProp->Fetch();
			if (trim($itemProp["VALUE"]) == "")
			{
				continue;
			}
			if (empty($sectionSimilarProps["RANGE"][$i]))
			{
				$filter["PROPERTY_" . $code] = $itemProp["VALUE"];
			}
			else
			{
				$filter[] = array(
					"LOGIC" => "AND",
					array(">=PROPERTY_" . $code => $itemProp["VALUE"] - $sectionSimilarProps["RANGE"][$i]),
					array("<PROPERTY_" . $code => $itemProp["VALUE"] + $sectionSimilarProps["RANGE"][$i]),
				);
			}
		}
	}

	// есть ли есть другие предложения с тем же типом сделки, что у текущего
	if (is_array($filter) && $hasResults(array_merge($seeAlsoFilter, $filter)))
	{
		$seeAlsoFilter = array_merge($seeAlsoFilter, $filter);
	}
	// если записей нет, даже с другими типами сделки, скроем блок похожих предложений
	elseif (!$hasResults($seeAlsoFilter))
	{
		$seeAlsoFilter = array();
	}

	if ($seeAlsoFilter)
	{
		?>
		<? $APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		array(
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"AREA_FILE_RECURSIVE" => "Y",
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PATH" => SITE_DIR . "include/offers.php",
			"EDIT_TEMPLATE" => "page_inc.php",
			"EDIT_MODE" => "php",
			"TITLE" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_TITLE"),
			"DESCRIPTION" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_DESC"),
			"PAGE_SECTION" => "Y",
			"COMPONENT_TEMPLATE" => ".default",
			"h" => "h2.h1",
			"PADDING" => "Y",
			"BG_COLOR" => "WHITE",
			"CUT_CONTENT_HEIGHT" => "",
			"SECTION_CLASS" => "page-break-inside",
			"SHOW_DATE" => $arParams["SHOW_DATE"],
		),
		false
	);
	}
}

// заполняется в шаблоне bitrix:catalog.element
$APPLICATION->ShowViewContent('element-page-bottom');