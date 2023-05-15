<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

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
    <div class="">
        <h3 class="title_hit">Хит продаж</h3>
        <div class="popular__inner">
            <div class="products">
                <div class="products__inner">
                    <? foreach ($arResult['ITEMS'] as $arItem): ?>
                        <?
                            $arPrice = current($arItem['PRICES']);
                            $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>190, 'height'=>190), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        ?>
                        <div class="product">
                            <div class="product__image">
                                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>">
                                    <img src="<?=$img['src'];?>" alt="">
                                </a>
                            </div>
                            <div class="product__price"><strong>Цена:</strong> <?=$arPrice['PRINT_VALUE'];?></div>
                            <div class="product__title">
                                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=$arItem['NAME'];?></a>
                            </div>
                            <div class="button"><a href="javascript:void(0);" class="btn btn-sm add2basket" data-id="<?=$arItem['ID'];?>">в корзину</a></div>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
