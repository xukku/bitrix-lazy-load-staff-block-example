<?php

$arItem = &$arParams['~ITEM'];

if ($arItem["PREVIEW_PICTURE"])
{
	if (is_array($arItem["PREVIEW_PICTURE"]) && array_key_exists("ID", $arItem["PREVIEW_PICTURE"]))
	{
		$arItem["PREVIEW_PICTURE"] = $arItem["PREVIEW_PICTURE"]['ID'];
	}
	$arItem["PREVIEW_PICTURE"] = CFile::ResizeImageGet(
		$arItem["PREVIEW_PICTURE"],
		['width' => 367, 'height' => 351],
		BX_RESIZE_IMAGE_EXACT,
		true
	);
}
else
{
	$gender = \Citrus\Core\array_get($arItem, 'PROPERTIES.gender.VALUE_XML_ID');
	$gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

	$arItem["PREVIEW_PICTURE"] = [
		'src' => getLocalPath('components/citrus/template/templates/staff-item/img/' . $gender . '.jpg'),
		'width' => 367,
		'height' => 351,
	];
}

// first word of name is bold
$tmp_name = explode(' ', $arItem['NAME']);
$arItem['DISPLAY_NAME'] = '<b>'.array_shift($tmp_name).'</b> '.implode(' ', $tmp_name);
