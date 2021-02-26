<?php

use Citrus\Arealty;
use Citrus\Arealty\Object\Address;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arItem = &$arParams['~DATA'];

// Колонки таблицы для отображения
$displayProperties = new Arealty\DisplayProperties($arItem['IBLOCK_ID']);
$arItem['DISPLAY_COLUMNS'] = $displayProperties->getForElement($arItem['ID'], $arItem['DISPLAY_COLUMNS_DEFAULT']);

$arResult['DISPLAY_COLUMNS'] = $displayProperties->getForSection($arResult['ID'], $arResult['DISPLAY_COLUMNS_DEFAULT']);

if (!empty($arItem['IS_JK']) && $arItem['IS_JK'] == 'Y')
{
	$arItem['DISPLAY_COLUMNS_DEFAULT'] = false;
}