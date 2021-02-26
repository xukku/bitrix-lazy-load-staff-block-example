<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
	Citrus\Arealty\Helper,
	Bitrix\Main\Web\Json;

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

$images = [];
if (is_array($arResult["DETAIL_PICTURE"]))
{
	Helper::addImage(600, 600, $images, $arResult, false,
		$arResult["DETAIL_PICTURE"]["ID"], $arResult["DETAIL_PICTURE"]["TITLE"], $arResult["DETAIL_PICTURE"]["ALT"]);
}
elseif (is_array($arResult["PREVIEW_PICTURE"]))
{
	Helper::addImage(600, 600, $images, $arResult, false,
		$arResult["PREVIEW_PICTURE"]["ID"], $arResult["PREVIEW_PICTURE"]["TITLE"], $arResult["PREVIEW_PICTURE"]["ALT"]);
}
if (is_array($arResult["PROPERTIES"]["photo"]['VALUE']))
{
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $key => $id)
	{
		Helper::addImage(600, 600, $images, $arResult, false,
			$id, $arResult["PROPERTIES"]["photo"]['DESCRIPTION'][$key]);
	}
}

require __DIR__ . '/gallery.inc.php';
$videos = array_flip(Helper::getVideoUrls(\Citrus\Core\array_get($arResult, 'PROPERTIES.video.VALUE')));
$galleryItems = array_merge(
	array_reduce($videos, $renderVideoItem, []),
	array_reduce($images, $renderImageItem, [])
);

$detailText = (trim(strip_tags($arResult["DETAIL_TEXT"])) <> '') ? $arResult["DETAIL_TEXT"] : ((trim(strip_tags($arResult["PREVIEW_TEXT"])) <> '') ? $arResult["PREVIEW_TEXT"] : false);

$printProp = function ($propertyCode, $emptyPlaceholder = '&mdash;') use (&$arResult)
{
	$value = $arResult["PROPERTIES"][$propertyCode]["VALUE"];
	if (empty($value))
	{
		return $emptyPlaceholder;
	}

	if (!is_array($value))
	{
		$value = array($value);
	}

	return implode(', ', $value);
};

?>

<?if($arParams['PRINT'] === 'Y'):?>
	<h1 class="content-title"><?=$arResult['NAME']?></h1>
<?endif;?>
<div class="section _with-padding">
    <div class="w">
        <hr class="section__border-top">
        <div class="section-inner">
            <div class="section__header">
	            <h1 id="pagetitle"><?=$arResult['NAME']?></h1>
                <div class="section-description"><?=(string)$arResult['ADDRESS']?></div>
            </div><!-- .section__header -->

            <div class="section__content catalog-section-content">
                <div class="row row-grid">
					<?if (count($images) || $arParams['PRINT'] != 'Y'):?>
                        <div class="<?=count($images) ? 'col-lg-6 col-print-12' : 'col-lg-12 col-print-12'?>">
							<?php if ($arParams['PRINT'] != 'Y')
							{
								?>

								<div class="image-actions print-hidden">
									<?php
									$pdfParams = [
										'ID' => $arResult['ID'],
										'IBLOCK_ID' => $arParams["IBLOCK_ID"],
										'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
										'COMPONENT_TEMPLATE' => 'pdf_detail',
										'IS_JK' => isset($arParams['IS_JK']) ? $arParams['IS_JK'] : 'N',
										'PRICE' => $arResult['PROPERTIES']['cost']['VALUE'],
									];
									?>
									<a href="javascript:window.print();" class="image-actions__link">
										<span class="image-actions__link-icon"><i class="icon-print1"></i></span>
										<span class="image-actions__link-text"><?= Loc::getMessage("CITRUS_REALTY_PRINT_VERSION") ?></span>
									</a>
									<?php if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y') { ?>
										<a href="javascript:void(0);" class="image-actions__link add2favourites"
										   data-id="<?= $arResult["ID"] ?>">
											<span class="image-actions__link-icon"><i class="icon-favorites"></i></span>
											<span class="image-actions__link-text control-link-label"><?= Loc::getMessage("CITRUS_REALTY_ADD_TO_FAV") ?></span>
										</a>
									<?php } ?>
									<div class="image-actions__link share-social js-popup">
										<a href="javascript:void(0);" class="share-social__link">
											<span class="image-actions__link-icon">
												<svg class="share-social__icon"><use x="0" y="0"
												                                    xlink:href="#share"></use></svg>
											</span>
											<span class="image-actions__link-text"><?= Loc::getMessage("CITRUS_REALTY_SHARE_SOCIAL") ?></span>
										</a>
										<div class="share-social__popup">
											<div class="share-social__title"><?= Loc::getMessage("CITRUS_REALTY_SHARE_SOCIAL") ?></div>
											<div class="share-social__social-component">
												<? $APPLICATION->IncludeComponent("bitrix:main.share", "flat", Array(
														"HIDE" => "N",
														"HANDLERS" => $arParams["SHARE_SERVICES"],
														"PAGE_URL" => CHTTP::URN2URI($APPLICATION->GetProperty('canonical') ?: $APPLICATION->GetCurPage(false)),
														"PAGE_TITLE" => $APPLICATION->GetTitle(),
													),
													$component
												); ?>
											</div>
											<div class="share-social__print">
												<a href="<?= SITE_DIR
												?>ajax/pdf.php" class="share-social__print-link"
												   data-toggle="modal"
												   data-params="<?= \Citrus\Core\Components\Pdf::encodeParams($pdfParams, 'citrus.arealty:catalog.element') ?>"
												   data-id="<?= $arResult["ID"] ?>">
													<span class="share-social__link-icon"><i
																class="icon-letter"></i></span>
													<span class="share-social__link-text"><?= Loc::getMessage("CITRUS_REALTY_PDF_SEND") ?></span>
												</a>
											</div>
											<button class="js-popup__close">+</button>
										</div>
									</div>
								</div>

								<?php
							} ?>
	                        <?php $frame = $this->createFrame()->begin() ?>
	                            <div class="object-gallery">
		                            <div class="object-gallery-previews"
		                                 itemscope=""
	                                     itemtype="http://schema.org/ImageGallery"
	                                >
			                            <?=implode('', array_map($renderGalleryItem, $galleryItems))?>
		                            </div>
		                            <? if (count($galleryItems) > 1): ?>
			                            <div class="object-gallery-thumbs">
				                            <div class="swiper-container">
					                            <div class="swiper-wrapper">
						                            <? foreach (array_column($galleryItems, 'preview') as $id => $preview): ?>
							                            <div class="swiper-slide">
								                            <a href="javascript:void(0)" rel="nofollow"
								                               class="gallery-thumbs <? if (!$id): ?>is-active<? endif; ?>">
									                            <img class="lazy"
																	 data-src="<?=$preview["src"]?>"
									                                 title="<?=$preview["title"]?>"
																	 alt="<?=$preview["alt"]?>">
								                            </a>
							                            </div>
						                            <? endforeach; ?>
					                            </div>
					                            <div class="swiper-scrollbar"></div>
				                            </div>
			                            </div>
		                            <? endif; ?>
	                            </div>
	                        <?php $frame->beginStub() ?>
		                        <div class="object-gallery">
			                        <div class="object-gallery-previews"
			                             itemscope=""
			                             itemtype="http://schema.org/ImageGallery"
			                        >
				                        <?=implode('', array_map($renderGalleryItem, $galleryItems))?>
			                        </div>
			                        <? if (count($galleryItems) > 1): ?>
				                        <div class="object-gallery-thumbs">
					                        <div class="swiper-container">
						                        <div class="swiper-wrapper">
							                        <? foreach (array_column($galleryItems, 'preview') as $id => $preview): ?>
								                        <div class="swiper-slide">
									                        <a href="javascript:void(0)" rel="nofollow"
									                           class="gallery-thumbs <? if (!$id): ?>is-active<? endif; ?>">
										                        <img class="lazy"
																	 data-src="<?=$preview["src"]?>"
										                             title="<?=$preview["title"]?>"
																	 alt="<?=$preview["alt"]?>">
									                        </a>
								                        </div>
							                        <? endforeach; ?>
						                        </div>
						                        <div class="swiper-scrollbar"></div>
					                        </div>
				                        </div>
			                        <? endif; ?>
		                        </div>
	                        <?php $frame->end() ?>
                        </div>
					<? endif; ?>
                    <div class="<?=count($images) ? 'col-lg-6 col-print-12' : 'col-xs-12 col-print-12'?>">
                        <div class="object-info">

                            <div class="object-option dl-menu">
								<? if ((string)$arResult['ADDRESS']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span><?= Loc::getMessage("CITRUS_AREALTY_OBJECT_ADDRESS") ?></span>
                                        <span>
                                            <a href="javascript:void(0);" class="map-link"
                                               data-address="<?=(string)$arResult['ADDRESS']?>"
                                               data-coords="<?=Json::encode($arResult['ADDRESS']->getCoordinates())?>"><?=(string)$arResult['ADDRESS']?>
                                            </a>
                                        </span>
                                    </div>
								<? endif; ?>

								<?
								$skipProperties = array("cost", "address", "photo", "contact");
								if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
								{
									?>
									<?
									foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty)
									{
										if (array_search($pid, $skipProperties) !== false)
										{
											continue;
										}

				                if ($arProperty["PROPERTY_TYPE"] == 'F')
				                {
					                if (!is_array($arProperty['VALUE'])) {
						                $arProperty['VALUE'] = array($arProperty['VALUE']);
						                $arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
					                }
					                $arProperty["DISPLAY_VALUE"] = Array();
					                foreach ($arProperty["VALUE"] as $idx=>$value) {
						                $path = CFile::GetPath($value);
						                $desc = ($arProperty["DESCRIPTION"][$idx] <> '') ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
						                if ($path <> '')
						                {
							                $ext = pathinfo($path, PATHINFO_EXTENSION);
							                $fileinfo = '';
							                if ($arFile = CFile::GetByID($value)->Fetch())
								                $fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE']/1024) . GetMessage('FILE_SIZE_Kb') . ')';
							                $arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
						                }
					                }
					                $val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
				                }
				                else
				                {
					                if (!is_array($arProperty["DISPLAY_VALUE"]))
						                $arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);

					                array_map(function (&$v) {
						                $v = strip_tags($v);
					                }, $arProperty["DISPLAY_VALUE"]);

					                // добавим обознвчение единиц для площади
					                if (stripos($pid, 'land_area') !== false)
						                foreach ($arProperty["DISPLAY_VALUE"] as &$val)
							                $val .= GetMessage("CITRUS_REALTY_HUNDRED_SQR_METERS");
					                elseif (stripos($pid, '_area') !== false)
						                foreach ($arProperty["DISPLAY_VALUE"] as &$val)
							                $val .= GetMessage("CITRUS_REALTY_SQR_METERS");

					                $ar = array();
					                foreach ($arProperty["DISPLAY_VALUE"] as $idx=>$value)
						                $ar[] = $value . (($arProperty["DESCRIPTION"][$idx] <> '') ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')': '');

					                $val = implode(', ', $ar);
				                }

										?>
                                        <div class="dl_element">
                                            <span><?=$arProperty["NAME"]?></span>
                                            <span><?=$val?></span>
                                        </div>
										<?
									}
									?>
									<?
								}
								$offerFields = \Citrus\Core\array_get($arResult, 'OFFERS_FIELDS.display');
								if (is_array($offerFields))
								{
									foreach ($arResult["OFFERS_FIELDS"]['display'] as $title => $val) {
										?>
										<div class="dl_element">
											<span><?= $title ?></span>
											<span><?= $val ?></span>
										</div>
										<?php
									}
								}
								?>
                            </div>
							<? if ($arResult["PROPERTIES"]["cost"]["VALUE"]): ?>
								<?
								$priceAdditional = '';
								if ($printProp("cost_unit", ''))
								{
									$priceAdditional .= '<span class="catalog-item-price__unit"> / ' . str_replace(GetMessage("CITRUS_AREALTY_COST_UNIT_SEARCH"),
											GetMessage("CITRUS_AREALTY_COST_UNIT_REPLACE"),
											$printProp("cost_unit", '')) . '</span>';
								}
								if ($printProp("cost_period", ''))
								{
									$priceAdditional .= ' <span class="catalog-item-price__period">' . GetMessage("CITRUS_AREALTY_COST_PERIOD_IN") . $printProp("cost_period",
											'') . '</span>';
								} ?>
                                <div class="object-price_new">
									<? #currency set in js?>
                                    <span data-currency-base="<?=$printProp("cost", 0)?>"
                                          data-currency-icon="">&nbsp;</span>
									<?=$priceAdditional?>
                                </div>
                                <script>currency.updateHtml($('.object-price_new'))</script>
							<? endif; ?>
	                        <?php if ($arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]) { ?>
		                        <div class="object-price_new">
			                        <? #currency set in js?>
			                        <?=Loc::getMessage('CITRUS_TEMPLATE_PRICE_FROM')?>
			                        <span data-currency-base="<?=$arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]?>"
			                              data-currency-icon="">&nbsp;</span>
		                        </div>
		                        <script>currency.updateHtml($('.object-price_new'))</script>
	                        <?php } ?>

                            <br>
                            <div class="object-info_footer">
                                <a class="btn btn-primary print-hidden"
                                   rel="nofollow" data-toggle="modal"
                                   href="<?=SITE_DIR?>ajax/request_shedule.php?id=<?=$arResult["ID"]?>">
                                    <span class="btn-label"><?=Loc::getMessage("CITRUS_AREALTY_BTN_REQUEST_SHEDULE")?></span>
                                </a>
	                            <?php

	                            if ((empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y')
	                            		&& \CModule::IncludeModule('citrus.arealtypro'))
	                            {
	                            	if (empty($arResult['SECTION_FIELDS']['UF_HIDE_IP']))
	                            	{
			                            ?>
			                            <a class="mortgage_link print-hidden"
				                               data-toggle="modal"
				                               data-params="<?= \Citrus\Core\Components\Pdf::encodeParams($pdfParams,'citrus.arealty:catalog.element') ?>"
				                               data-id="<?=$arResult["ID"]?>"
				                               href="<?=SITE_DIR?>ajax/pdf.php?icalculator=1">
				                            <i class="fa fa-calculator" aria-hidden="true"></i>
				                            <span class="btn-label"><?=Loc::getMessage("CITRUS_AREALTY_BTN_MORTGAGE_LINK")?></span>
		                                </a>
			                            <?php
		                        	}
	                            }
	                            else if ($arResult['CONTACT'])
	                            {
	                            	?>
		                            <a class="personal_manager_link print-hidden" href="javascript:void(0)">
			                            <i class="icon-owner"></i>
			                            <span class="btn-label"><?=Loc::getMessage("CITRUS_AREALTY_BTN_PERSONAL_MANAGER")?></span>
		                            </a>
		                            <?php
	                            }
	                            ?>
                            </div>
                        </div>
                    </div>
                </div>

	            <?php if ($arResult['PROPERTIES']['callout']['VALUE'])
			    {
			        ?><?$APPLICATION->IncludeComponent(
			            "citrus:realty.callout",
			            ".default",
			            array(
			                "IBLOCK_ID" => $arResult['PROPERTIES']['callout']['LINK_IBLOCK_ID'],
			                "ID" => $arResult['PROPERTIES']['callout']['VALUE']
			            ),
			            $component
			        )?><?
			    } ?>

                <div class="object-text">
					<?=$detailText?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->SetViewTarget('element-page-bottom');

if ($arResult['CONTACT'])
{
	?><a id="personal_manager"></a><?php

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
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);
}