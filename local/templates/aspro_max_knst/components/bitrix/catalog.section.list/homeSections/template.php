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
<div class="cats">
    <?
        foreach ($arResult['SECTIONS'] as $arSection):
        $img =  SITE_TEMPLATE_PATH . '/images/cats/cat1.png';
        if ($arSection['PICTURE'])
            $img = $arSection['PICTURE']['SRC'];
    ?>
        <div class="cat">
            <div class="cat__image">
                <a href="<?=$arSection['SECTION_PAGE_URL'];?>"><img src="<?=$img;?>" alt=""></a>
            </div>
            <div class="cat__title"><a href="<?=$arSection['SECTION_PAGE_URL'];?>"><?=$arSection['NAME'];?><span>(<?=$arSection['ELEMENT_CNT'];?>)</span></a></div>
        </div>
    <? endforeach; ?>
</div>
