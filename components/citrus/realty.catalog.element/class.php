<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$parentComponentName = 'bitrix:catalog.element';
CBitrixComponent::includeComponentClass($parentComponentName);

class CitrusRealtyCatalogElementComponent extends CatalogElementComponent
{
	public function onPrepareComponentParams($arParams)
	{
		$arParams = parent::onPrepareComponentParams($arParams);

		if (!isset($arParams['PREVIEW_MODE']))
		{
			if ($arParams['PARENT_TEMPLATE_PAGE'] == 'preview')
			{
				$arParams['PREVIEW_MODE'] = 'Y';
			}
		}

		return $arParams;
	}

	protected function getFilter()
	{
		$filterFields = parent::getFilter();
		$filterFields['ACTIVE_DATE'] = \Citrus\Arealty\Entity\SettingsTable::getValue('USE_ACTIVE_DATE') == 'N' ? '' : 'Y';
		if ($this->arParams['PREVIEW_MODE'] != 'Y')
		{
			$filterFields['PROPERTY_draft_for'] = false;
		}

		return $filterFields;
	}

	protected function checkElementId()
	{
		//NOTE копипаста из bitrix/modules/iblock/lib/component/element.php чтоб переопределить ACTIVE_DATE
		if ($this->arParams['ELEMENT_ID'] <= 0)
		{
			$findFilter = array(
				'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
				'IBLOCK_LID' => $this->getSiteId(),
				'ACTIVE_DATE' => \Citrus\Arealty\Entity\SettingsTable::getValue('USE_ACTIVE_DATE') == 'N' ? '' : 'Y',
				'CHECK_PERMISSIONS' => 'Y',
				'MIN_PERMISSION' => 'R',
			);

			if ($this->arParams['SHOW_DEACTIVATED'] !== 'Y')
			{
				$findFilter['ACTIVE'] = 'Y';
			}

			$this->arParams['ELEMENT_ID'] = \CIBlockFindTools::GetElementID(
				$this->arParams['ELEMENT_ID'],
				$this->arParams['~ELEMENT_CODE'],
				$this->arParams['STRICT_SECTION_CHECK']? $this->arParams['SECTION_ID']: 0,
				$this->arParams['STRICT_SECTION_CHECK']? $this->arParams['~SECTION_CODE']: '',
				$findFilter
			);
		}

		return $this->arParams['ELEMENT_ID'] > 0;
	}

	protected function makeOutputResult()
	{
		parent::makeOutputResult();

		\Citrus\Arealty\Entity\CurrenciesTable::initBaseCostForItem($this->arResult);
	}
}