<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $arTheme, $arRegion, $dopClass;
$arRegions = CMaxRegionality::getRegions();
if ($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
$dopClass = 'wides_menu smalls';
?>
<div class="top-block top-block-v1 header-v16">
	<div class="wrapper_inner">
		<div class="items-wrapper flexbox flexbox--row justify-content-between">
			<div class="logo-block pull-left floated logo-custom">
				<div class="logo<?= $logoClass ?>">
					<?= CMax::ShowLogo(); ?>
				</div>
			</div>

			<div class="menus">
				<? $APPLICATION->IncludeComponent(
					"bitrix:main.include",
					".default",
					array(
						"COMPONENT_TEMPLATE" => ".default",
						"PATH" => SITE_DIR . "include/menu/menu.topest2.php",
						"AREA_FILE_SHOW" => "file",
						"AREA_FILE_SUFFIX" => "",
						"AREA_FILE_RECURSIVE" => "Y",
						"EDIT_TEMPLATE" => "include_area.php"
					),
					false
				); ?>
			</div>
			<div class="top-block-item">
				<div class="phone-block icons">
					<? if ($bPhone) : ?>
						<div>
							<? CMax::ShowHeaderPhones(''); ?>
						</div>
					<? endif ?>
					<? $callbackExploded = explode(',', $arTheme['SHOW_CALLBACK']['VALUE']);
					if (in_array('HEADER', $callbackExploded)) : ?>
						<div class="inline-block">
							<span class="callback-block animate-load colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage("CALLBACK") ?></span>
						</div>
					<? endif; ?>
				</div>
			</div>
			<div id="mobileheader" class="visible-xs visible-sm">
				<div class="burger pull-left">
					<i class="svg inline  svg-inline-burger dark" aria-hidden="true">
						<svg width="26" height="16" viewBox="0 0 26 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="26" height="2" rx="1" fill="black" />
							<rect y="7" width="26" height="2" rx="1" fill="black" />
							<rect y="14" width="26" height="2" rx="1" fill="black" />
						</svg>
					</i>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="header-wrapper header-v17">
	<div class="logo_and_menu-row longs">
		<div class="logo-row paddings">
			<div class="wrapper_inner">
				<div class="pull-left" style="margin-left: -15px;">
					<div class="menu-row">
						<div class="menu-only">
							<nav class="mega-menu">
								<? $APPLICATION->IncludeComponent(
									"bitrix:main.include",
									".default",
									array(
										"COMPONENT_TEMPLATE" => ".default",
										"PATH" => SITE_DIR . "include/menu/menu.only_catalog.php",
										"AREA_FILE_SHOW" => "file",
										"AREA_FILE_SUFFIX" => "",
										"AREA_FILE_RECURSIVE" => "Y",
										"EDIT_TEMPLATE" => "include_area.php"
									),
									false,
									array("HIDE_ICONS" => "Y")
								); ?>
							</nav>
						</div>
					</div>
				</div>

				<div class="search-block inner-table-block" style="padding-left: 12px; display: flex; width: auto;">
					<? $APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR . "include/top_page/search.title.catalog.php",
							"EDIT_TEMPLATE" => "include_area.php",
							'SEARCH_ICON' => 'Y',
						)
					); ?>
					<div class="right-icons top-block-item logo_and_menu-row showed">
						<div class="pull-right">
							<div class="wrap_icon">
								<i data-event="jqm" data-param-form_id="PRICE_CALC" data-name="PRICE_CALC" onclick="goModalCustom()">
									<?= CMax::showIconSvg("price_icon", SITE_TEMPLATE_PATH . "/images/svg/price_icon.svg"); ?>
								</i>
							</div>
							<div class="wrap_icon">
								<?= CMax::ShowBasketWithCompareLink('', 'big', '', ''); ?>
							</div>
							<div class="wrap_icon">
								<?= CMax::showCabinetLink(true, true, 'big'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><? // class=logo-row
				?>
	</div>
</div>
<script>
	function copytext(el) {
		var $tmp = $("<textarea>");
		$("body").append($tmp);
		$tmp.val($(el).text()).select();
		document.execCommand("copy");
		$tmp.remove();
	};
	$(function() {
		$('.real-show-hint').on("click", function(e) {
			e = e || window.event;
			e.preventDefault();
			var RealHint = $(this).data('hint');
			$(RealHint).toggle("fast");
			$(RealHint).delay(700);
			return;
		});

		document.onclick = function(e) {
			if ($(e.target).hasClass('real-hint') == false)
				$('.real-hint').hide("fast");
			return;
		}
	});
</script>