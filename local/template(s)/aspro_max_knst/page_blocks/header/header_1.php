<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $arTheme, $arRegion, $bLongHeader, $bColoredHeader;
$arRegions = CMaxRegionality::getRegions();
if ($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
$bLongHeader = true;
$bColoredHeader = true;
?>
<div class="header-wrapper">
	<div class="logo_and_menu-row">
		<div class="logo-row short paddings">
			<div class="maxwidth-theme">
				<div class="row">
					<div class="col-md-12">
						<div class="logo-block pull-left floated">
							<div class="logo<?= $logoClass ?>">
								<?= CMax::ShowLogo(); ?>
							</div>
						</div>

						<div class="float_wrapper pull-left">
							<div class="hidden-sm hidden-xs pull-left">
								<div class="top-description addr">
									<span class="header__logo-title">
										<? $APPLICATION->IncludeFile(
											SITE_DIR . "include/top_page/slogan.php",
											array(),
											array(
												"MODE" => "html",
												"NAME" => "Text in title",
												"TEMPLATE" => "include_area.php",
											)
										); ?>
									</span>
								</div>
							</div>
						</div>

						<? if ($arRegions) : ?>
							<div class="inline-block pull-left">
								<div class="top-description no-title">
									<? \Aspro\Functions\CAsproMax::showRegionList(); ?>
								</div>
							</div>
						<? endif; ?>

						<div class="pull-left">
							<div class="wrap_icon inner-table-block">
								<div class="phone-block">
									<? if ($bPhone) : ?>
										<? CMax::ShowHeaderPhones('no-icons'); ?>
									<? endif ?>
									<? $callbackExploded = explode(',', $arTheme['SHOW_CALLBACK']['VALUE']);
									if (in_array('HEADER', $callbackExploded)) : ?>
										<div class="inline-block">
											<span class="callback-block animate-load colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?= GetMessage("CALLBACK") ?></span>
										</div>
									<? endif; ?>
								</div>
							</div>
						</div>

						<div class="right-icons pull-right wb">
							<div class="pull-left">
								<div class="wrap_icon">
									<a href="#" class="to-favorites-link">
										<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M8.27538 3.62383L8.99845 4.3807L9.72152 3.62383C10.4506 2.86065 11.574 1.99152 12.7454 1.99152C13.8954 1.99152 14.8118 2.38834 15.4383 3.01376C16.0647 3.63905 16.4615 4.55296 16.4615 5.69921C16.4615 6.93514 15.9825 7.97817 15.1854 8.97168C14.3691 9.98909 13.2711 10.8937 12.0887 11.8625C12.0886 11.8625 12.0886 11.8626 12.0885 11.8626L12.0644 11.8824C11.0431 12.7188 9.91183 13.6453 8.99895 14.6814C8.09503 13.6542 6.97449 12.7351 5.96222 11.9049L5.91112 11.863L5.91072 11.8626C4.72798 10.8935 3.63004 9.98864 2.81402 8.97123C2.01719 7.97775 1.53845 6.93487 1.53845 5.69921C1.53845 4.55296 1.93529 3.63908 2.56173 3.0138C3.1883 2.38839 4.1049 1.99152 5.25538 1.99152C6.42537 1.99152 7.5455 2.85983 8.27538 3.62383Z" stroke="currentColor" stroke-width="2"/>
										</svg>
									</a>
								</div>
							</div>
							<div class="pull-right">
								<?= CMax::ShowBasketWithCompareLink('', 'big', '', 'wrap_icon wrap_basket baskets'); ?>
							</div>

							<div class="pull-right">
								<div class="wrap_icon inner-table-block person">
									<?= CMax::showCabinetLink(true, true, 'big'); ?>
								</div>
							</div>

							<div class="pull-right">
								<div class="wrap_icon">
									<button class="top-btn inline-search-show">
										<?= CMax::showIconSvg("search", SITE_TEMPLATE_PATH . "/images/svg/Search.svg"); ?>
										<span class="title"><?= GetMessage("CT_BST_SEARCH_BUTTON") ?></span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><? // class=logo-row
				?>
	</div>
	<div class="menu-row middle-block bg<?= strtolower($arTheme["MENU_COLOR"]["VALUE"]); ?>">
		<div class="maxwidth-theme">
			<div class="row">
				<div class="col-md-12">
					<div class="menu-only">
						<nav class="mega-menu sliced">
							<? $APPLICATION->IncludeComponent(
								"bitrix:main.include",
								".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR . "include/menu/menu." . ($arTheme["HEADER_TYPE"]["LIST"][$arTheme["HEADER_TYPE"]["VALUE"]]["ADDITIONAL_OPTIONS"]["MENU_HEADER_TYPE"]["VALUE"] == "Y" ? "top_catalog_wide" : "top") . ".php",
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
		</div>
	</div>
	<div class="line-row visible-xs"></div>
</div>