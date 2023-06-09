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
	<div class="maxwidth-theme wides">
		<div class="wrapp_block">
			<div class="row">
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
		"PATH" => SITE_DIR."include/menu/menu.topest2.php",
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
									<span class="callback-block animate-load font_upper_xs colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage("CALLBACK") ?></span>
								</div>
							<? endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<div class="header-wrapper header-v17">
	<div class="logo_and_menu-row longs">
		<div class="logo-row paddings">
			<div class="maxwidth-theme wides">
				<div class="row pos-static">
					<div class="col-md-12 pos-static">


						<div class="pull-left">
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

						<div class="right-icons1 pull-right wb">
							<div class="pull-right longest">
								<?= CMax::ShowBasketWithCompareLink('', 'big', '', 'wrap_icon wrap_basket baskets'); ?>
							</div>
						</div>

						<div class="search-block inner-table-block" style="padding-left: 30px; display: flex; width: auto;">
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
									<div class="wrap_icon inner-table-block1 person">
										<?= CMax::showCabinetLink(true, true, 'big'); ?>
									</div>
								</div>

							</div>
						</div>

					</div>
				</div>
				<div class="lines-row"></div>
			</div>
		</div><? // class=logo-row
				?>
	</div>
</div>