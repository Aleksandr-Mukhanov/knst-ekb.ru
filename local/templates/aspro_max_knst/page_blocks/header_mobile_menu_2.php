<div class="mobilemenu-v2 downdrop">
	<div class="burger">
		<?= CMax::showIconSvg("close dark", SITE_TEMPLATE_PATH . "/images/svg/Close.svg"); ?>
	</div>
	<div class="logo mobile-menu">
		<?= CMax::ShowLogo(); ?>
	</div>
	<div class="wrap">
		<? if (CMax::nlo('menu-mobile', 'class="loadings" style="height:47px;"')) : ?>
			<!-- noindex -->
			<? $APPLICATION->IncludeComponent(
				"bitrix:menu",
				"top_mobile",
				array(
					"COMPONENT_TEMPLATE" => "top_mobile",
					"MENU_CACHE_TIME" => "3600000",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_USE_GROUPS" => "N",
					"MENU_CACHE_GET_VARS" => array(),
					"DELAY" => "N",
					"MAX_LEVEL" => \Bitrix\Main\Config\Option::get("aspro.max", "MAX_DEPTH_MENU", 0),
					"ALLOW_MULTI_SELECT" => "Y",
					"ROOT_MENU_TYPE" => "top_content_multilevel",
					"CHILD_MENU_TYPE" => "left",
					"CACHE_SELECTED_ITEMS" => "N",
					"ALLOW_MULTI_SELECT" => "Y",
					"USE_EXT" => "Y"
				)
			); ?>
			<!-- /noindex -->
		<? endif; ?>
		<? CMax::nlo('menu-mobile'); ?>
	</div>
	<div class="top-block-item mobile-item">
		<div class="phone-block icons">
			<div>
				<span>+7 000-00-00</span>
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