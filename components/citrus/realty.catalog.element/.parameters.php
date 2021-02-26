<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$innerComponent = 'bitrix:catalog.element';

$arComponentParameters = [
	"GROUPS" => [],
	"PARAMETERS" => [],
];

$innerComponentParams = CComponentUtil::GetComponentProps($innerComponent, $arCurrentValues);
if (isset($innerComponentParams['GROUPS']))
{
	$arComponentParameters['GROUPS'] += $innerComponentParams['GROUPS'];
}
if (isset($innerComponentParams['PARAMETERS']))
{
	$arComponentParameters['PARAMETERS'] += $innerComponentParams['PARAMETERS'];
}
$arComponentParameters += $innerComponentParams;
