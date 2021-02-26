<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;
?>

<div class="page-404">
	<img src="<?=$templateFolder?>/404.png" alt="404 error" class="page-404__image">

	<h1><?= Loc::getMessage("404_TITLE") ?></h1>
	<div class="section-description"><?= Loc::getMessage("404_DESCRIPTION_1") ?>
		<a href="<?=$arParams['LINK_1'] ?: '/'?>"><?=$arParams['LINK_1_TITLE']?></a>
		<?= Loc::getMessage("404_DESCRIPTION_OR") ?>
		<a href="<?=SITE_DIR?>ajax/request.php" data-toggle="modal"><?= Loc::getMessage("404_DESCRIPTION_LINK_2_TITLE") ?></a>!
	</div>
</div>
