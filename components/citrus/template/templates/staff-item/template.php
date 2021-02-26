<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$issetProperties = function ($arProperties = array()) use (&$arItem) {
	$isset = false;
	foreach ($arProperties as $propertyCode) {
		if ($arItem['PROPERTIES'][$propertyCode]['VALUE']) $isset = true;
	}
	return $isset;
};

$arItem = $arParams['~ITEM'];?>

<div class="staff-item" onclick="" >
    <div class="staff-item__image img-placeholder">
		<? if ($arItem['DETAIL_PAGE_URL']): ?>
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
				<span class="lazy" data-bg="<?=$arItem['PREVIEW_PICTURE']['src']?>"></span>
            </a>
		<? else: ?>
			<span  class="lazy" data-bg="<?=$arItem['PREVIEW_PICTURE']['src']?>"></span>
		<? endif; ?>
    </div>
	<div class="staff-item__content">
		<?if($issetProperties(array('email','timetable', 'phone'))):?>
			<span class="staff-item__content-icon icon-arrow_top"></span>
		<?endif;?>

		<div class="staff-item__content-top">
			<? if ($arItem['DETAIL_PAGE_URL']): ?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="staff-item__name"
                   title="<?=$arItem['NAME']?>"><?=$arItem['DISPLAY_NAME']?></a>
			<? else: ?>
                <div class="staff-item__name" title="<?=$arItem['NAME']?>"><?=$arItem['DISPLAY_NAME']?></div>
			<? endif; ?>
			<?if($arItem['PROPERTIES']['position']['VALUE']):?>
				<div class="staff-item__position"><?=$arItem['PROPERTIES']['position']['VALUE']?></div>
			<?endif;?>
		</div><!-- .staff-item__content-top -->

		<?
		$APPLICATION->IncludeComponent(
			"citrus.arealty:properties",
			'',
			[
			    'CSS_CLASS' => 'js-properties',
				'PROPERTIES' => $arItem['PROPERTIES'],
				'DISPLAY_PROPERTIES' => array_diff(
					!empty($arParams['DISPLAY_PROPERTIES'])? $arParams['DISPLAY_PROPERTIES'] : array_keys($arItem['PROPERTIES']),
					['user', 'position', 'contacts', 'office']
				),
			],
			$component,
            ['HIDE_ICONS' => 'Y']
        );

		?>

	</div><!-- .staff-item__content -->
</div><!-- .staff-item -->