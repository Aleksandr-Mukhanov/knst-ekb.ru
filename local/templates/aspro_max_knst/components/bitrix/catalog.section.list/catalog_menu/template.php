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

$strTitle = "";
?>
<?php
    foreach ($arResult['SECTIONS'] as $arSection)
    {
        if (!$arSection['IBLOCK_SECTION_ID'])
        {
            $new_sections['MAIN'][$arSection['ID']] = $arSection;
        } else {
            $new_sections['CHILDS'][$arSection['IBLOCK_SECTION_ID']][$arSection['ID']] = $arSection;
        }
    }
?>
<?php if (!empty($new_sections)): ?>
    <div class="cats-modal__inner">
    <? foreach ($new_sections['MAIN'] as $arSection): ?>
        <div class="cats-modal-cat">
            <div class="cats-modal-cat__inner">
                <div class="cats-modal-cat__image"><a href="<?=$arSection['LIST_PAGE_URL'];?>">
                        <img src="<?=($arSection['PICTURE'] ? $arSection['PICTURE']['SRC'] : SITE_TEMPLATE_PATH . '/images/cats/cat1.png');?>" alt="">
                    </a></div>
				<div class="cats-modal-cat__title"><a href="<?=$arSection['SECTION_PAGE_URL'];?>/"><?=$arSection['NAME'];?> <span>(<?=$arSection['ELEMENT_CNT'];?>)</span></a></div>
            </div>
            <? if (!empty($new_sections['CHILDS'][$arSection['ID']])): ?>
                <div class="cats-modal-cat__list">
                    <dl class="cats-modal-cat__lvl2">
                        <dt><b class="arrow-back"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/arrow-l.svg"></i></b> <?=$arSection['NAME'];?> <span>(<?=$arSection['ELEMENT_CNT'];?>)</span></dt>
                        <? foreach ($new_sections['CHILDS'][$arSection['ID']] as $arChildSection): ?>
						<dd<?=(!empty($new_sections['CHILDS'][$arChildSection['ID']]) ? ' class="have-child"' : '');?>><a href="<?=$arSection['SECTION_PAGE_URL'];?>/<?=$arChildSection['CODE'];?>/"><?=$arChildSection['NAME'];?></a>
                                <? if (!empty($new_sections['CHILDS'][$arChildSection['ID']])): ?>
                                    <dl class="cats-modal-cat__lvl3">
                                        <dt><b class="arrow-back"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/arrow-l.svg"></i></b> <?=$arChildSection['NAME'];?></dt>
                                        <? foreach ($new_sections['CHILDS'][$arChildSection['ID']] as $arChild): ?>
										<dd><a href="<?=$arSection['SECTION_PAGE_URL'];?>/<?=$arChildSection['CODE'];?>/<?=$arChild['CODE'];?>/"><?=$arChild['NAME'];?></a></dd>
                                        <? endforeach; ?>
                                    </dl>
                                <? endif; ?>
                            </dd>
                        <? endforeach; ?>
                    </dl>
                </div>
            <? endif; ?>
        </div>
    <? endforeach; ?>
</div>
<?php endif; ?>