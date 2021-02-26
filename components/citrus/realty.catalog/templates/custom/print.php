<?php
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php";
$APPLICATION->SetPageProperty("show_title", "N");

\CModule::IncludeModule("citrus.arealty");
use Citrus\Arealty\Entity\SettingsTable,
	Citrus\Arealty\Helper;

if (empty($_GET["id"]))
{
	return;
}

?>
<style>
	@media print {
		.header {
			display: none;
		}
	}
</style>
<?
$elementIds = !is_array($_GET["id"])? array($_GET["id"]) : $_GET["id"];
$count = count($elementIds);
$i = 0;
foreach ($elementIds as $elementId)
{
	ob_start();
	?>

	<div class="print-header display-xs-n display-print-b">
		<div class="row-ib">
			<div class="col-md-5 col-lg-4 va-xs-m col-print-5">
				<?php
				SettingsTable::showControls(function ($settings) {

					$showText = $settings['LOGO_SHOW_TEXT'] == 'Y';

					?>
					<a href="<?=SITE_DIR?>" class="header-logo <?=$showText ? "with_desc": ""?>">
						<?if (SettingsTable::getLogoId()): ?>
							<span class="logo-image logo-image--min"><img src="<?=SettingsTable::getLogoPath(array('width' => 400, 'height' => 64))?>" alt=""></span>
						<?else:?>
							<span class="logo-image logo-image--min">
                                        <span class="icon-logo"></span>
                                    </span>
						<?endif ?>
						<?if($showText):?>
							<? $formatSiteName = preg_replace(
								'#^([^\s]+?)\s(.*)$#',
								'$1<span>$2</span>',
								$settings["SITE_NAME"]
							)?>
							<span class="logo-text"><?=$formatSiteName?></span>
						<?endif;?>
					</a>

					<?
				});
				?>
			</div>

			<div class="col-lg-3 va-xs-m ta-xs-r col-print-7">
				<div class="header-bottom">
					<div class="header-phone">
						<?if(SettingsTable::getValue("PHONE")):?>
							<a href="tel:<?=Helper::clearPhoneNumber(SettingsTable::getValue("PHONE"))?>" class="header-phone-number"><?=SettingsTable::showValue("PHONE")?></a>
						<?endif;?>
					</div>
				</div>
			</div>
		</div><!-- .row-ib -->
	</div><!-- .print-header -->

	<?$resultElementId = $APPLICATION->IncludeComponent(
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
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
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

			"ELEMENT_ID" => $elementId,
			"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
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
			"PRINT" => "N", // fix for new template
		),
		$component
	);?>
	<?php
	$resultElementContent = ob_get_clean();
	$i++;
	if ($resultElementId) {
		echo $resultElementContent;
		if ($i < $count) echo '<p class="indent print-break-after">&nbsp;</p>';
	}
	?>
	<?php
}
?>

<script>
	window.print();
</script>




<?php require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"; ?>
