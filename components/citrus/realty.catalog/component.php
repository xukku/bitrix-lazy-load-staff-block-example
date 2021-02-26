<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
	return;

if (!isset($arParams['DETAIL_SHOW_SIMILAR_OFFERS']) || !in_array($arParams['DETAIL_SHOW_SIMILAR_OFFERS'], array('N', 'Y')))
{
	$arParams['DETAIL_SHOW_SIMILAR_OFFERS'] = 'Y';
}

if (!is_numeric($arParams["IBLOCK_ID"]))
{
	try
	{
		$arParams['IBLOCK_ID'] = \Citrus\Arealty\Helper::getIblock($arParams['IBLOCK_ID']);
	}
	catch (Exception $e)
	{

	}
}

if (!$arParams['DETAIL_SET_CANONICAL_URL']) $arParams['DETAIL_SET_CANONICAL_URL'] = 'Y';

if (isset($arParams["USE_FILTER"]) && $arParams["USE_FILTER"]=="Y")
{
	$arParams["FILTER_NAME"] = trim($arParams["FILTER_NAME"]);
	if ($arParams["FILTER_NAME"] === '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		$arParams["FILTER_NAME"] = "arrFilter";
}
else
	$arParams["FILTER_NAME"] = "";

$smartBase = ($arParams["SEF_URL_TEMPLATES"]["section"]? $arParams["SEF_URL_TEMPLATES"]["section"]: "#SECTION_ID#/");
$arDefaultUrlTemplates404 = array(
	"sections" => "",
	"section" => "#SECTION_ID#/",
	"element" => "#SECTION_ID#/#ELEMENT_ID#/",
	"favourites" => "favourites/",
	"print" => "print/",
	"pdf" => "pdf/",
	"smart_filter" => $smartBase."filter/#SMART_FILTER_PATH#/apply/"
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
	"SECTION_ID",
	"SECTION_CODE",
	"ELEMENT_ID",
	"ELEMENT_CODE",
	"action",
);

if($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$engine = new CComponentEngine($this);
	if (\Bitrix\Main\Loader::includeModule('iblock'))
	{
		$engine->addGreedyPart("#SECTION_CODE_PATH#");
		$engine->addGreedyPart("#SMART_FILTER_PATH#");
		$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
	}
	$arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = $engine->guessComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);

	if ($componentPage === "smart_filter")
		$componentPage = "section";

	if(!$componentPage && isset($_REQUEST["q"]))
		$componentPage = "search";

	$b404 = false;
	if(!$componentPage)
	{
		$componentPage = "sections";
		$b404 = true;
	}

	if ($componentPage == "section" || $componentPage == "element")
	{
		if (isset($arVariables["SECTION_ID"]))
		{
			$b404 |= (intval($arVariables["SECTION_ID"]) . "" !== $arVariables["SECTION_ID"]);
		}
		elseif (isset($arVariables['SECTION_CODE']))
		{
			$b404 |= ($arVariables["SECTION_CODE"] == '');
		}
		elseif (isset($arVariables['ELEMENT_CODE']))
		{
			$b404 |= ($arVariables["ELEMENT_CODE"] == '');
		}
		elseif ($arParams['REDIRECT_TO_SECTION_404'] == 'Y')
		{
			$sectionUrl = explode('/', $requestURL = Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage());
			$sectionUrl = implode('/', array_slice($sectionUrl, 0, -2)) . '/index.php';

			$arVariables = array();
			$componentPage = $engine->guessComponentPath(
				$arParams["SEF_FOLDER"],
				$arUrlTemplates,
				$arVariables,
				$sectionUrl
			);

			if ($componentPage == 'section' && isset($arVariables["SECTION_ID"]))
			{
				$redirectUrl = $arParams["SEF_FOLDER"] . CComponentEngine::makePathFromTemplate($arUrlTemplates[$componentPage], $arVariables);
				LocalRedirect($redirectUrl, false, '301 Found');
			}
			else
			{
				$b404 |= !isset($arVariables["SECTION_CODE"]);
			}
		}
		else
		{
			$b404 = true;
		}
	}

	if($b404 && CModule::IncludeModule('iblock'))
	{
		$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
		if ($folder404 != "/")
			$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
		if (substr($folder404, -1) == "/")
			$folder404 .= "index.php";

		if($folder404 != $APPLICATION->GetCurPage(true))
		{
			if (class_exists('\Bitrix\Iblock\Component\Tools'))
			{
				\Bitrix\Iblock\Component\Tools::process404(
					""
					,($arParams["SET_STATUS_404"] === "Y")
					,($arParams["SET_STATUS_404"] === "Y")
					,($arParams["SHOW_404"] === "Y")
					,empty($arParams["FILE_404"]) ? SITE_DIR . '404.php' : $arParams['FILE_404']
				);
			}
			elseif ($arParams["SET_STATUS_404"]==="Y")
			{
				CHTTP::SetStatus("404 Not Found");
			}
		}
	}

	CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
		"FOLDER" => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}
else
{
	ShowError(Loc::getMessage("CITRUS_REALTY_CATALOG_SEF_ERROR"));
	return;
}

$this->IncludeComponentTemplate($componentPage);