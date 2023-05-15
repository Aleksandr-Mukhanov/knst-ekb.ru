<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Web\Json;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);
?>

<section class="popular">
    <div class="wrap">
        <h3>Популярные товары</h3>

        <div class="popular__inner">
            <div class="products">
                <div class="products__inner">
                    <? foreach ($arResult['ITEMS'] as $arItem) : ?>
                        <?
                        $arPrice = current($arItem['PRICES']);
                        $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width' => 190, 'height' => 190), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        ?>
                        <div class="product">
                            <div class="product__image">
                                <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>">
                                    <img src="<?= $img['src']; ?>" alt="">
                                </a>
                            </div>
                            <div class="product__price"><strong>Цена:</strong> <?= $arPrice['PRINT_VALUE']; ?></div>
                            <div class="product__title">
                                <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>"><?= $arItem['NAME']; ?></a>
                            </div>
                            <div class="button_block">
                                <!--noindex-->
                                <?= $arAddToBasketData["HTML"] ?>
                                <!--/noindex-->
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>

            <? $APPLICATION->IncludeComponent(
	"bitrix:form", 
	".default", 
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "Y",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"EDIT_ADDITIONAL" => "N",
		"EDIT_STATUS" => "N",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"NAME_TEMPLATE" => "",
		"NOT_SHOW_FILTER" => array(
			0 => "",
			1 => "",
		),
		"NOT_SHOW_TABLE" => array(
			0 => "",
			1 => "",
		),
		"RESULT_ID" => $_REQUEST[RESULT_ID],
		"SEF_MODE" => "N",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_ANSWER_VALUE" => "N",
		"SHOW_EDIT_PAGE" => "N",
		"SHOW_LIST_PAGE" => "N",
		"SHOW_STATUS" => "N",
		"SHOW_VIEW_PAGE" => "N",
		"START_PAGE" => "new",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "N",
		"WEB_FORM_ID" => "17",
		"COMPONENT_TEMPLATE" => ".default",
		"VARIABLE_ALIASES" => array(
			"action" => "action",
		)
	),
	false
); ?>
        </div>
    </div>
</section>