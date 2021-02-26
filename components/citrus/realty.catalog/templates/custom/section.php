<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	ShowError(GetMessage("CITRUS_REALTY_MODULE_NOT_FOUND"));

	return;
}

use Citrus\Arealty\SortOrder;
use Citrus\Arealty\IblockPropertyList;
use Bitrix\Main\Localization\Loc;

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

$arFilter = array(
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "ACTIVE" => "Y",
    "GLOBAL_ACTIVE" => "Y",
);
global $APPLICATION;
if (0 < (int)$arResult["VARIABLES"]["SECTION_ID"])
{
    $APPLICATION->SetPageProperty("activeCatalogSectionID", $arResult["VARIABLES"]["SECTION_ID"]);
    $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
}
elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
{
    $APPLICATION->SetPageProperty("activeCatalogSectionCode", $arResult["VARIABLES"]["SECTION_CODE"]);
    $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
}

$getSortFieldNames = function($currentSection) use ($arParams){
    $arReturn = array();
    if (is_array($currentSection["UF_SORT_FIELDS"]) && !empty($currentSection["UF_SORT_FIELDS"])) {
        $iblockFields = IblockPropertyList::getPropertiesWithCustomFields($arParams["IBLOCK_ID"]);
        foreach ($currentSection["UF_SORT_FIELDS"] as $propertyId) {
            switch ($propertyId){
                case IblockPropertyList::NAME:
                    $arReturn["NAME"] = $iblockFields["NAME"]["NAME"];
                    break;
                case IblockPropertyList::DETAIL_PICTURE:
                    $arReturn["DETAIL_PICTURE"] = $iblockFields["DETAIL_PICTURE"]["NAME"];
                    break;
                case IblockPropertyList::DATE_CREATE:
                    $arReturn[SortOrder::getOfferDateField()] = $iblockFields["DATE_CREATE"]["NAME"];
                    break;
                default:
                    $propertyFields = \CIBlockProperty::GetByID($propertyId)->GetNext();
                    $arReturn['PROPERTY_'.$propertyFields["CODE"]] = $propertyFields["NAME"];
            }
        }
    }
    return $arReturn;
};

$currentSection = array();
$obCache = new CPHPCache();
if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
{
    $currentSection = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
    if (\Bitrix\Main\Loader::includeModule("iblock"))
    {
        $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "NAME", "UF_TYPE", "UF_SORT_FIELDS", 'UF_TABLE_COLS'));

        if (defined("BX_COMP_MANAGED_CACHE"))
        {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache("/iblock/catalog");

            if ($currentSection = $dbRes->Fetch())
            {
                $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
                $obEnum = new CUserFieldEnum();
                if ($currentSection["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $currentSection["UF_TYPE"]))->Fetch()))
                {
                    $currentSection["UF_TYPE_XML_ID"] = $enum["XML_ID"];
                }
                else
                {
                    $currentSection["UF_TYPE_XML_ID"] = "cards";
                }
                //получим названия для полей сортировки
                $currentSection["SORT_FIELDS"] = $getSortFieldNames($currentSection);
            }
            $CACHE_MANAGER->EndTagCache();
        }
        else
        {
            if ($currentSection = $dbRes->Fetch())
            {
                $obEnum = new CUserFieldEnum();
                if ($currentSection["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $currentSection["UF_TYPE"]))->Fetch()))
                {
                    $currentSection["UF_TYPE_XML_ID"] = $enum["XML_ID"];
                }
                else
                {
                    $currentSection["UF_TYPE_XML_ID"] = "cards";
                }
            }
            else
            {
                $currentSection = null;
            }
            //получим названия для полей сортировки
            $currentSection["SORT_FIELDS"] = $getSortFieldNames($currentSection);
        }
    }
    $obCache->EndDataCache($currentSection);
}

$displayProperties = new \Citrus\Arealty\DisplayProperties($arParams['IBLOCK_ID']);
$arParams['LIST_PROPERTY_CODE'] = array_filter(array_merge(
	$arParams['LIST_PROPERTY_CODE'] ?: [],
	array_keys($displayProperties->getForSection($currentSection['ID'], $_))
));

$APPLICATION->SetPageProperty('pageSectionClass', '_compact');
$APPLICATION->SetPageProperty('pageSectionContentClass', 'catalog-section-content');
$APPLICATION->SetTitle($currentSection['NAME']);

?><? $APPLICATION->IncludeComponent(
	"citrus:realty.favourites",
	"block",
	array(
		"PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["favourites"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
); ?><?

ob_start();
?><? $APPLICATION->IncludeComponent(
    "citrus.arealty:catalog.section.list",
    "line-sections",
    array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
        "TOP_DEPTH" => 1,
        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
        "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
        "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
        "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
        "SECTION_FIELDS" => array("PICTURE"),
        "SECTION_USER_FIELDS" => array("UF_SECTION_COLOR"),
    ),
    $component,
    ['HIDE_ICONS' => 'Y']
); ?><?php

$subsectionsContent = trim(ob_get_flush());
$filterContent = '';

ob_start();
if (isset($currentSection))
{
    ?><?$APPLICATION->IncludeComponent(
        "citrus.arealty:smart.filter",
        ".default",
        array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "DISPLAY_ELEMENT_COUNT" => "N",
            "FILTER_NAME" => $arParams["FILTER_NAME"],
            "FILTER_VIEW_MODE" => "vertical",
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "INSTANT_RELOAD" => "N",
            "POPUP_POSITION" => "left",
            "SAVE_IN_SESSION" => "N",
            "SECTION_ID" => $currentSection["ID"],
            "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
            "XML_EXPORT" => "Y",
            "PRICE_CODE" => $arParams["PRICE_CODE"],
            "SEF_MODE" => (($arResult["URL_TEMPLATES"]["smart_filter"] <> '') ? "Y" : "N"),
            "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
            "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
            "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );?><?php
}
$filterContent = trim(ob_get_flush());

// подзаголовок страницы выводится только есть есть подразделы и/или фильтр
if ($subsectionsContent || $filterContent)
{
    $APPLICATION->SetPageProperty('PAGE_SUBHEADER', Loc::getMessage("CATALOG_SECTION_DESCRIPTION"));
}

$intSectionID = 0;

//default sort
$arSortFields = array(
    SortOrder::getOfferDateField() => Loc::getMessage("SORT_FIELD_DATE_CREATE"),
    "PROPERTY_cost" => Loc::getMessage("SORT_FIELD_COST"),
    "PROPERTY_common_area" => Loc::getMessage("SORT_FIELD_COMMON_AREA")
);
//section sort field
if (is_array($currentSection["SORT_FIELDS"]) && !empty($currentSection["SORT_FIELDS"]))
{
    $arSortFields = $currentSection["SORT_FIELDS"];
}

?><?$citrusSort = $APPLICATION->IncludeComponent(
    "citrus.core:sort",
    ".default",
    array(
        "COMPONENT_TEMPLATE" => ".default",
        "IBLOCK_TYPE" => "realty",
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "SORT_FIELDS" => $arSortFields,
        "DEFAULT_SORT_ORDER" => "DESC",
        "VIEW_LIST" => array(
            0 => "CARDS",
            1 => "LIST",
            2 => "TABLE",
        ),
        "VIEW_DEFAULT" => strtoupper($currentSection["UF_TYPE_XML_ID"]),

    ),
    $component
);?>

<?php

if (strtolower($citrusSort["VIEW"]) == 'table')
{
	// explode combined properties (ex. prop1|prop2)
	$tableCols = array_map(function ($v) {
		return explode('|', $v['code']);
	}, $currentSection['UF_TABLE_COLS'] ?: []);

	// flatten array
	$props = array_reduce($tableCols, function ($res, $item) {
		return array_merge($res, $item);
	}, []);

	// filter out FIELDS
	$props = array_filter($props, function ($code) {
		return $code[0] !== '~';
	});

	$arParams['LIST_PROPERTY_CODE'] = array_unique(array_filter(array_merge(
		$arParams['LIST_PROPERTY_CODE'] ?: [],
		$props
	)));
}

?>
<? $intSectionID = $APPLICATION->IncludeComponent(
    "citrus.arealty:catalog.section",
    ".default",
    array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ELEMENT_SORT_FIELD" => $citrusSort["SORT"]["CODE"],
        "ELEMENT_SORT_ORDER" => $citrusSort["SORT"]["ORDER"],
        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
        "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
        "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
        "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
        "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "MESSAGE_404" => $arParams["MESSAGE_404"],
        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
        "SHOW_404" => $arParams["SHOW_404"],
        "FILE_404" => $arParams["FILE_404"],
        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
        "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

        'LABEL_PROP' => $arParams['LABEL_PROP'],
        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
        'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
        'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
        'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
        'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
		"SHOW_DATE" => $arParams["SHOW_DATE"],

        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
        "ADD_SECTIONS_CHAIN" => "N",

        "SECTION_USER_FIELDS" => array("UF_TYPE", "UF_PROP_LINK", "UF_SORT_FIELDS", "UF_TABLE_COLS"),
        "SHOW_MAP" => "Y",
        "CITRUS_THEME" => \Citrus\Arealty\Helper::getTheme(),
        "EMPTY_LIST_MESSAGE" => $arParams['EMPTY_LIST_MESSAGE'],
        "VIEW_TEMPLATE" => "catalog_" . strtolower($citrusSort["VIEW"]),
	    "USE_MAIN_ELEMENT_SECTION" => "Y",
    ),
    $component
); ?>
<?php

$APPLICATION->SetPageProperty('mapCatalogSectionId', $intSectionID);
$APPLICATION->SetPageProperty('mapCatalogIblockId', $arParams["IBLOCK_ID"]);
$APPLICATION->SetPageProperty('mapCatalogFilterName', $arParams["FILTER_NAME"]);
