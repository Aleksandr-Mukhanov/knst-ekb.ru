<?
global $arTheme, $arRegion;
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<div class="mobileheader-v4">
	<div class="mobile-header-line">
		<div class="pull-left">
			<button class="top-btn inline-search-show twosmallfont">
				<?= CMax::showIconSvg("search_mobile", SITE_TEMPLATE_PATH . "/images/svg/search_mobile.svg"); ?>
			</button>
		</div>
		<div class="logo-block ">
			<div class="logo<?= $logoClass ?>">
				<?= CMax::ShowLogo(); ?>
			</div>
		</div>
		<div class="right-icons pull-right">
			<div class="pull-right burger">
				<div class="wrap_icon wrap_phones">
					<i class="svg inline  svg-inline-burger dark" aria-hidden="true">
						<svg width="26" height="16" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="26" height="2" rx="1" fill="black" />
							<rect y="7" width="26" height="2" rx="1" fill="black" />
							<rect y="14" width="26" height="2" rx="1" fill="black" />
						</svg>
					</i>
					<?= CMax::showIconSvg("close dark", SITE_TEMPLATE_PATH . "/images/svg/Close.svg"); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="top-block-item">
		<div>
			<? CMax::ShowHeaderPhones(''); ?>
		</div>
		<div class="phone-block icons">

			<div class="email">
				<?= CMax::showEmail('email blocks') ?>
			</div>
			<? $callbackExploded = explode(',', $arTheme['SHOW_CALLBACK']['VALUE']);
			if (in_array('HEADER', $callbackExploded)) : ?>
				<div class="inline-block">
					<span class="callback-block animate-load colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage("CALLBACK") ?></span>
				</div>
			<? endif; ?>
		</div>
	</div>
</div>