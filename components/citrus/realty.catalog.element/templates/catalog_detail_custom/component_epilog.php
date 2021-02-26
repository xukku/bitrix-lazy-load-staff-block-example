<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Citrus\Arealty\Helper;
use Citrus\Arealty\Template\TemplateHelper;
use Citrus\Arealtypro\Manage\RightsFactory;
use Citrus\ArealtyPro\Manage\RightsProvider;
use Citrus\ArealtyPro\Manage\ComponentUtils;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams Параметры, чтение/изменение не затрагивает одноименный член компонента. */
/** @var array $arResult Результат, чтение/изменение не затрагивает одноименный член класса компонента. */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component Ссылка на $this. */
/** @var CBitrixComponent $this Ссылка на текущий вызванный компонент, можно использовать все методы класса. */
/** @var string $epilogFile Путь к файлу component_epilog.php относительно DOCUMENT_ROOT */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFile Путь к файлу шаблона от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные закешируются и будут доступны в component_epilog.php на каждом хите */
/** @var @global CMain $APPLICATION */
/** @var @global CUser $USER */

CUtil::InitJSCore(Array("citrusUI__base", "realtyAddress", "photoSwipe", "swiper"));
\Bitrix\Main\UI\Extension::load('citrus.arealty.lazyload');

Helper::setLastSection($arResult["IBLOCK_SECTION_ID"], $arResult['DEAL_TYPE'] ? array('PROPERTY_deal_type' => $arResult['DEAL_TYPE']) : array());

Loc::loadMessages(__DIR__ . '/template.php');

$APPLICATION->SetPageProperty('SHOW_TITLE', 'N');

$jsMessages = [
	'CITRUS_AREALTY_PDF_SEND_URL' => $arParams["PDF_DETAIL_URL"],
	'CITRUS_AREALTY_PDF_SEND_PROMPT' => GetMessage("CITRUS_AREALTY_PDF_SEND_PROMPT"),
	'CITRUS_AREALTY_PDF_SEND_RESULT' => GetMessage("CITRUS_AREALTY_PDF_SEND_RESULT"),
];
Asset::getInstance()
    ->addString('<script>BX.message(' . CUtil::PhpToJSObject($jsMessages) . ');</script>');

if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y')
{
	if ($USER->IsAuthorized() && \Bitrix\Main\Loader::includeModule('citrus.arealtypro') && class_exists(RightsFactory::class))
	{
		$rights = RightsFactory::getInstance($arResult['IBLOCK_ID']);
		if ($rights->canDoOperation(RightsProvider::OP_ELEMENT_EDIT, $arResult['ID']))
		{
			ComponentUtils::initLinksToKabinetFromDetail($arResult);
			?>
			<a href="<?= $arResult['URL_KABINET_EDIT'] ?>"
			   class="image-actions__link print-hidden" id="object-edit-link" style="display: none;">
				<span class="image-actions__link-icon"><i class="icon-edit"></i></span>
				<span class="image-actions__link-text"><?=Loc::getMessage("CITRUS_REALTY_CHANGE_OBJECT")?></span>
			</a>

			<?php if ($arResult['DRAFT_FOR']) { ?>
				<a href="<?= $arResult['URL_KABINET_APPLY'] ?>" class="image-actions__link print-hidden" id="object-publish-link" style="display: none;">
					<span class="image-actions__link-icon"><i class="icon-edit"></i></span>
					<span class="image-actions__link-text"><?= Loc::getMessage("CITRUS_AREALTY_PUBLISH_OBJECT") ?></span>
				</a>
			<?php } ?>

			<?
		}
	}
}
?>

<?php if (!empty($arResult['COMPLEX']) && ($complexIblockId = TemplateHelper::requireIblock('complexes'))) { ?>
	<? $APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		array(
			"AREA_FILE_SHOW" => "component",
			"_COMPONENT" => "citrus.arealty:contentblock",
			"_COMPONENT_TEMPLATE" => "contentwithimageandtitle",
			"IBLOCK_ID" => $complexIblockId,
			"IBLOCK_TYPE" => "realty",
			"ELEMENT_XML_ID" => $arResult['COMPLEX'],
			'TITLE' => Loc::getMessage('CITRUS_AREALTY_BLOCK_COMPLEX_TITLE'),
			"h" => ".h1",
			"PAGE_SECTION" => "Y",
			"WIDGET_REL" => "",
			"COMPONENT_TEMPLATE" => ".default",
			"PADDING" => "Y",
			"BG_COLOR" => "N",
			"BTN_TITLE" => Loc::getMessage('CITRUS_AREALTY_BLOCK_COMPLEX_BTN_TITLE'),
			"CLASS" => 'desc_complex',
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000"
		),
		false
	); ?>
<?php } ?>