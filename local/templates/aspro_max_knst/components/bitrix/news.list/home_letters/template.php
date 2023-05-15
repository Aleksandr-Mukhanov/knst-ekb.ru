<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?php if ($arResult['ITEMS']): ?>
    <section class="letters">
    <div class="wrap">
        <h2>Спросите сами!<br> Плюсы нашей работы из первых уст</h2>
        <div class="subtitle">По запросу мы вышлем благодарственные письма<br> и контакты заказчиков, чтобы вы смогли сами убедиться<br> в нашей компетенции. Вот некоторые из них</div>

        <div class="letters__inner">
            <div class="letters-slider">
                <div class="letters-slider__js">
                    <?
                        foreach ($arResult['ITEMS'] as $arItem):
                            $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>199, 'height'=>288), BX_RESIZE_IMAGE_EXACT, true);
                    ?>
                            <div class="letters-slide">
                                <a href="<?=$arItem['PREVIEW_PICTURE']['SRC'];?>" data-fancybox="letters">
                                    <img src="<?=$img['src'];?>" alt="">
                                </a>
                            </div>
                    <? endforeach; ?>
                </div>

                <div class="letters-slider__nav">
                    <div class="letters-slider__prev slick-arrow slick-prev"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/prev.svg"></i></div>
                    <div class="letters-slider__next slick-arrow slick-next"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/next.svg"></i></div>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>