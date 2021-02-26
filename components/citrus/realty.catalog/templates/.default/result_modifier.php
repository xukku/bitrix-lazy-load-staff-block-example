<?
if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	ShowError(GetMessage("CITRUS_REALTY_MODULE_NOT_FOUND"));
	return;
}

$arParams['FILTER_NAME'] = empty($arParams['FILTER_NAME']) ? 'arrF' : $arParams['FILTER_NAME'];
