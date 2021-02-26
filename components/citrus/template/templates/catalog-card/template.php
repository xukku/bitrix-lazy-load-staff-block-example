<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Template\TemplateHelper;

$this->setFrameMode(true);

$arItem = $arParams['~DATA'];

$printProp = function ($propertyCode, $emptyPlaceholder = '&mdash;', $showHint = false) use (&$arItem)
{
	if (is_array($arItem['DISPLAY_PROPERTIES']) && array_key_exists($propertyCode, $arItem['DISPLAY_PROPERTIES']))
	{
		$value = $arItem['DISPLAY_PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];
	}
	else
	{
		$value = $arItem["PROPERTIES"][$propertyCode]["VALUE"];
	}
	if (empty($value))
	{
		return $emptyPlaceholder;
	}

	if (!is_array($value))
	{
		$value = array($value);
	}

	$value = implode(', ', $value);

	if ($showHint) {
		// добавим обознвчение единиц для площади
		if (stripos($propertyCode, 'land_area') !== false)
			$value .= GetMessage("CITRUS_REALTY_HUNDRED_SQR_METERS");
		elseif (stripos($propertyCode, '_area') !== false)
			$value .= GetMessage("CITRUS_REALTY_SQR_METERS");
	}

	return $value;
};

$preview = \Citrus\Arealty\Helper::resizeOfferImage($arItem, 250, 225);
$property = new \Citrus\Arealty\Template\Property($arItem);
?>

<div class="catalog-card">

	<?# card image?>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"
	   class="catalog-card__image-w">
		<?= TemplateHelper::quickSaleLabel($arItem, 'span.share-label.theme--color > span.share-label__inner > span.share-label__text')?>

		<?if($preview["src"]):?>
			<span class="catalog-card__image lazy" data-bg="<?=$preview["src"]?>"></span>
		<?else:?>
			<span class="catalog-card__image img-placeholder"><span></span></span>
		<?endif;?>
	</a>


	<div class="catalog-card__body">

		<?# card content?>
		<div class="catalog-card__content">
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"
			   title="<?=$arItem['NAME']?>"
			   class="catalog-card__name h3"><?=$arItem['NAME']?></a>
			<div class="catalog-card__address"><?=$property->getMapLink($arItem)?></div>
		</div>

		<?# card hidden content?>
		<div class="catalog-card__hidden-content">
			<?php if (!empty($arItem['SHOW_DATE']) && ($arItem['SHOW_DATE'] == 'Y')) { ?>
			<div class="catalog-card__date"><?=FormatDate(
				"j F Y",
				MakeTimeStamp($arItem[\Citrus\Arealty\SortOrder::getOfferDateField()]))
				?>
			</div>
			<?php } ?>

			<div class="catalog-card__properties">
				<?
				#todo optimize code
				if (!$arItem["DISPLAY_COLUMNS_DEFAULT"]) :
					foreach ($arItem["DISPLAY_COLUMNS"] as $propertyCode => $column)
					{
						if (0 === strpos($propertyCode, '~'))
						{
							switch ($propertyCode)
							{
								case "~DETAIL_PICTURE":
									break;

								case "~NAME":
									break;

								default:
									echo '<div class="catalog-card__property">' . $arItem[substr($propertyCode, 1)] . '</div>';
									break;
							}
						}
						else if (isset($arItem['PROPERTIES'][$propertyCode]))
						{
							$arProp = $arItem['PROPERTIES'][$propertyCode];
							if ($propertyCode == 'cost')
							{
								if (empty($arItem['IS_JK']) || $arItem['IS_JK'] != 'Y')
								{
									echo '<div class="catalog-card__property">
										<span class="fw600">' . $arProp['NAME'] . '</span> ' . ($printProp("cost", 0) ? number_format($printProp("cost", 0), 0, ',', ' ') . '<span class="icon-ruble"></span>' : '') . '</div>';
								}
							}
							else
							{
								echo '<div class="catalog-card__property"><span class="catalog-card__property-name">' . $arProp['NAME'] . ':</span> <span class="catalog-card__property-value">' . $printProp($propertyCode, '&mdash;', true). '</span></div>';
							}
						}
					}
				else:?>
					<div class="catalog-card__property">
						<span class="catalog-card__property-name">
							<?=Loc::getMessage("CITRUS_REALTY_AREA")?>:
						</span>
						<span class="nobr catalog-card__property-value">
							<?=$printProp("common_area")?>/<?=$printProp("living_area")?>/<?=$printProp("kitchen_area")?>
						</span>
					</div>
					<div class="catalog-card__property">
						<span class="catalog-card__property-name">
							<?=$arItem["PROPERTIES"]["rooms"]["NAME"]?>:
						</span>
						<span class="catalog-card__property-value">
							<?=$printProp("rooms")?>
						</span>
					</div>
					<div class="catalog-card__property">
						<span class="catalog-card__property-name">
							<?=$arItem["PROPERTIES"]["floor"]["NAME"]?>:
						</span>
						<span class="catalog-card__property-value">
							<?=$printProp("floor")?>/<?=$printProp("floors")?>
						</span>
					</div>
				<?endif; ?>

				<?php
				if (isset($arItem['OFFERS_FIELDS']))
				{
					foreach ($arItem['OFFERS_FIELDS']['display'] as $fieldTitle => $fieldValue)
					{
						?>
						<div class="catalog-card__property">
							<span class="catalog-card__property-name"><?=$fieldTitle?></span>
							<span class="catalog-card__property-value"><?=$fieldValue?></span>
						</div>
						<?php
					}
				}
				?>
			</div><!-- .catalog-card__properties -->

			<?php if (empty($arItem['IS_JK']) || $arItem['IS_JK'] != 'Y') { ?>
			<a class="add2favourites catalog-card__favorite-link"
			   data-id="<?=$arItem["ID"]?>"
			   href="javascript:void(0);"
			   rel="nofollow">
				<span class="catalog-card__favorite-icon icon-favorites"></span>
				<span class="catalog-card__favorite-label control-link-label"><?=Loc::getMessage("CITRUS_REALTY_2FAV")?></span>
			</a>
			<?php } ?>

		</div><!-- .catalog-card__hidden-content -->

		<?# card footer?>
		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"  class="catalog-card__footer">
			<span class="catalog-card__price">
				<?if ($arItem["PROPERTIES"]["cost"]["VALUE"]){
					$priceAdditional = '';
					if ($printProp("cost_unit", ''))
					{
						$priceAdditional .= '<span class="catalog-item-price__unit"> / ' . str_replace(GetMessage("CITRUS_AREALTY_COST_UNIT_SEARCH"), GetMessage("CITRUS_AREALTY_COST_UNIT_REPLACE"), $printProp("cost_unit", '')) . '</span>';
					}
					if ($printProp("cost_period", ''))
					{
						$priceAdditional .= ' <span class="catalog-item-price__period"> / ' . GetMessage("CITRUS_AREALTY_COST_PERIOD_IN") . $printProp("cost_period", '') . '</span>';
					}?>
						<?#currency set in js?>
						<span
							data-currency-base="<?=$printProp("cost", 0)?>"
							data-currency-icon="">&nbsp;</span>
						<?=$priceAdditional?>
					<?
				}?>
				<?if (isset($arItem['OFFERS_FIELDS']) && $arItem['OFFERS_FIELDS']['raw']["MIN_COST"]) { ?>
					<?#currency set in js?>
					<?= Loc::getMessage("CITRUS_AREALTY_TEMPLATE_FROM") ?>&nbsp;
					<span
						data-currency-base="<?= $arItem['OFFERS_FIELDS']['raw']["MIN_COST"] ?>"
						data-currency-icon="">&nbsp;</span>
				<?php } ?>
			</span>
			<span class="catalog-card__footer-icon icon-arrow-right"></span>
		</a>
	</div><!-- .catalog-card__body -->
</div><!-- .catalog-card -->