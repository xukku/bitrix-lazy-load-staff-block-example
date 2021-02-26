<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($this->getTemplatePage() == 'favourites')
{
	$APPLICATION->SetPageProperty('SHOW_TITLE', 'N');
}
