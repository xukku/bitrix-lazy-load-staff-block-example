<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"SECTIONS_MAX_SUBSECTIONS" => Array(
		"NAME" => GetMessage("CITRUS_AREALTY3_CATALOG_SECTIONS_MAX_SUBSECTIONS"),
		"TYPE" => "INT",
		"DEFAULT" => "5",
	),
	"SHOW_DATE" => Array(
		"NAME" => GetMessage("CITRUS_TEMPLATE_SHOW_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
);
