<?php

use Citrus\Arealty\Helper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$renderGalleryItem = function ($item)
{
	static $isFirstGalleryItem = true;

	$classes = [];
	if ($isFirstGalleryItem)
	{
		$classes[] = 'is-active';
		$isFirstGalleryItem = false;
	}

	$classString = implode(' ', $classes);

	switch ($item['type'])
	{
		case 'video':
			//$itemType = 'http://schema.org/VideoObject';
			$itemType = '';
			break;
		case 'image':
		default:
			$itemType = 'http://schema.org/ImageObject';
			break;
	}

	return <<<HTML
<figure class="{$classString}"
        itemprop="associatedMedia"
		itemscope=""
        itemtype="{$itemType}"
>
	{$item['html']}

	<figcaption itemprop="caption description">{$item['title']}</figcaption>
</figure>
HTML;
};

$renderImageItem = function (array $result, $image) use (&$arParams) {
	$html = <<<HTML
<a href="{$image["src"]}"
   class="gallery-previews embed-responsive embed-responsive-{$arParams['IMAGE_RESOLUTION']}"
   itemprop="contentUrl"
   data-size="{$image["width"]}x{$image["height"]}"
   title="{$image["title"]}"
>
	<img class="embed-responsive-item"
		 src="{$image["preview"]["src"]}"
		 itemprop="thumbnail"
		 title="{$image["title"]}"
		 alt="{$image["alt"]}"
	>
</a>
HTML;

	$result[] = [
		'type' => 'image',
		'preview' => [
			'src' => $image['preview']['src'],
			'title' => $image['title'],
			'alt' => $image['alt'],
		],
		'title' => $image["title"],
		'html' => $html,
	];
	return $result;
};

$renderVideoItem = function (array $result, $url) use (&$arParams)
{
	if (!$videoId = Helper::parseYoutubeVideoIds($url)[0])
	{
		return $result;
	}

	$url = (new \Bitrix\Main\Web\Uri($url))
		->addParams(['controls' => 0]);

	// from youtube preview image
	$image = [
		'src' => "https://img.youtube.com/vi/{$videoId}/0.jpg",
		'width' => 480,
		'height' => 360,
		'title' => '',
		'preview' => [
			'src' => "https://img.youtube.com/vi/{$videoId}/0.jpg",
			'width' => 480,
			'height' => 360,
		],
	];

	$html = <<<HTML
<a href="javascript:void(0);"
   class="gallery-previews embed-responsive embed-responsive-{$arParams['IMAGE_RESOLUTION']}"
   itemprop="contentUrl"
   data-size="{$image["width"]}x{$image["height"]}"
   title="{$image["title"]}"
>
	<iframe class="embed-responsive-item"
			width="560"
			height="315"
			data-size="560x315"
			itemprop="embedUrl"
			src="{$url}"
			frameborder="0"
			allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
			allowfullscreen
	></iframe>
	<img src="{$image["preview"]["src"]}" class="embed-responsive-item print" alt="">
</a>

HTML;



	$result[] = [
		'preview' => [
			'src' => "https://img.youtube.com/vi/$videoId/0.jpg",
			'title' => '',
			'alt' => '',
		],
		'html' => $html,
	];
	return $result;
};