<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arItem = $arParams['~ITEM'];

?>

<div class="manager-row">
	<div class="manager-left print-no-break">
		<div class="manager-img-container">
			<img class="manager-img lazy"
			     data-src="<?=$arItem['PREVIEW_PICTURE']['src']?>"
			     alt="<?=$arItem['NAME']?>">
		</div>
		<div class="manager__content">
			<div class="manager__name"><?=$arItem['DISPLAY_NAME']?></div>
			<div class="manafer_post"><?=\Citrus\Core\array_get($arItem, 'PROPERTIES.position.VALUE')?></div>
			<div class="manager__properties">
				<?$APPLICATION->IncludeComponent(
					"citrus.arealty:properties",
					'',
					[
						'CSS_CLASS' => '',
						'PROPERTIES' => $arItem['PROPERTIES'],
						'DISPLAY_PROPERTIES' => array_diff(
							array_keys($arItem['PROPERTIES']),
							['user', 'position', 'contacts', 'office']
						),
					],
					$component,
					['HIDE_ICONS' => 'Y']
				);?>
				<?php

				if ($arItem['DETAIL_PAGE_URL'] && $arItem['ACTIVE'] == 'Y')
				{
					?><a href="<?=$arItem['DETAIL_PAGE_URL']?>#manager_objects"><?=Loc::getMessage('CITRUS_AREALTY_STAFF_BLOCK_DETAIL_LINK')?></a><?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="manager-right print-hidden">
		<div class="section-footer display-lg-n ta-xs-c">
			<a href="javascript:void(0);" data-href="<?=SITE_DIR?>ajax/order_call.php" data-toggle="modal"
			   class="btn btn-primary btn-stretch"><?= Loc::getMessage("CITRUS_AREALTY_STAFF_ORDER_CALL") ?></a>
		</div>
		<div class="display-xs-n display-lg-b">
			<div class="h3">
				<?= Loc::getMessage("CITRUS_AREALTY_STAFF_ORDER_CALL") ?>
			</div>
			<?$APPLICATION->IncludeComponent(
				"citrus.core:include",
				'.default',
				[
					"AREA_FILE_SHOW" => "sect",
					"AREA_FILE_SUFFIX" => "form_staff_order_call",
					"PAGE_SECTION" => "N",
					"PADDING" => "N",
				],
				$component
			);?>
		</div>
	</div>
</div>