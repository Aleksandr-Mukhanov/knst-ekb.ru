<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?

use \Bitrix\Main\Localization\Loc; ?>
<section class="school">
	<div class="container">
		<div class="school__inner">
			<h3 class="school__title">
				<? if ($bShowUrl) : ?>
					<a class="show_all muted font_upper" href="<?= $arResult['DISPLAY_PROPERTIES']['URL']['VALUE']; ?>">
					<? else : ?>
					<? endif; ?>
					<? if (in_array('NAME', $arParams['FIELD_CODE']) && $arResult['FIELDS']['NAME']) : ?>
						<?= $arResult['FIELDS']['NAME'] ?>
					<? endif; ?>
					<? if ($bShowUrl) : ?>
					</a>
				<? else : ?>
				<? endif; ?>
			</h3>
			<? // preview image
			$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);
			$bShowUrl = (isset($arResult['DISPLAY_PROPERTIES']['URL']) && strlen($arResult['DISPLAY_PROPERTIES']['URL']['VALUE']));

			if ($bShowImage) {
				$bImage = strlen($arResult['FIELDS']['PREVIEW_PICTURE']['SRC']);
				$arImage = ($bImage ? CFile::ResizeImageGet($arResult['FIELDS']['PREVIEW_PICTURE']['ID'], array('width' => 1000, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true) : array());
				$imageSrc = ($bImage ? $arImage['src'] : '');
			}
			?>
			<img class="school__img" src="<?= $imageSrc; ?>" alt="">
			<p class="school__desc">
				<? if ($arResult['PREVIEW_TEXT']) : ?>
					<?= $arResult['PREVIEW_TEXT']; ?>
				<? endif; ?>
			</p>
			<div class="school__bottom"><a href="<?= $arResult['DISPLAY_PROPERTIES']['URL']['VALUE']; ?>" class="school__btn">подробнее</a></div>
		</div>
	</div>
</section>