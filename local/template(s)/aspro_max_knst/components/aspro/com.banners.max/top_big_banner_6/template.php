<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>

<section class="header-info">
	<div class="container">
		<div class="header-info__inner">


			<div class="header-info__row">
				<?
				$arSelect = array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_*");
				$arFilter = array("IBLOCK_ID" => IntVal(55), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
				$res = CIBlockElement::GetList(array(), $arFilter, false, array("iNumPage" => 1, "nPageSize" => 500, "checkOutOfRange" => true), $arSelect);
				$i = 0;
				while ($ob = $res->GetNextElement()) {
					$arFields = $ob->GetFields();
					$i++;
					$img = CFile::GetPath($arFields["PREVIEW_PICTURE"]);
				?>
					<div class="header-info__row-item">
						<a href="<?= $arFields['DETAIL_PAGE_URL'] ?>">
							<img class="header-info__img" src="<?= $img ?>" alt="<?= $arFields['NAME'] ?>">
						</a>
						<br>
						<a href="<?= $arFields['DETAIL_PAGE_URL'] ?>" class="header-info__title"><?= $arFields['NAME'] ?></a>
					</div>
				<?
					if ($i % 4 == 0) {
						echo '</div><div class="header-info__row hidden-items ">';
					}
				}
				?>
			</div>
		</div>

	</div>
	<div class="header-info__bottom">
		<span class="header-info__btn">показать все</span>
		<span class="header-info__btn-hidden">скрыть все</span>
	</div>
</section>

<? if ($arResult['ITEMS']) : ?>
	<?/*if($arParams['WIDE_BANNER'] != 'Y'):?>
		<div class="maxwidth-theme">
	<?endif;*/ ?>
	<div class="top_big_one_banner only_banner nop top_big_banners <?= ($arResult['HAS_CHILD_BANNERS'] ? 'with_childs' : ''); ?> <?= ($arParams['MORE_HEIGHT'] ? 'more_height' : ''); ?>" style="overflow: hidden;">
		<? include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/aspro/com.banners.max/common_files/slider.php'); ?>
		<? if ($arResult['HAS_SLIDE_BANNERS'] && $arResult['HAS_CHILD_BANNERS']) : ?>
			<div class="items clearfix">
				<? foreach ($arResult['ITEMS'][$arParams['BANNER_TYPE_THEME_CHILD']]['ITEMS'] as $key => $arItem) : ?>
					<? include('float.php'); ?>
				<? endforeach; ?>
			</div>
		<? endif; ?>
		<? if ($arResult['HAS_CHILD_BANNERS2']) : ?>
			<div class="items <?= $arParams['SLIDER_VIEW_MOBILE'] ?><?= ($arParams['SLIDER_VIEW_MOBILE'] === 'slider' ? ' swipeignore mobile-overflow' : '') ?> c_<?= count($arResult['ITEMS'][$arParams['BANNER_TYPE_THEME_CHILD2']]['ITEMS']); ?>">
				<? foreach ($arResult['ITEMS'][$arParams['BANNER_TYPE_THEME_CHILD2']]['ITEMS'] as $key => $arItem) : ?>
					<? include('float.php'); ?>
				<? endforeach; ?>
			</div>
		<? endif; ?>
	</div>
	<?/*if($arParams['WIDE_BANNER'] != 'Y'):?>
		</div>
	<?endif;*/ ?>
<? endif; ?>