<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? global $arMainPageOrder; //global array for order blocks

CModule::IncludeModule('iblock');

$arSelect = array("ID", "NAME", "IBLOCK_SECTION_ID", "PROPERTY_SUBTITLE", "PROPERTY_LINK", "PROPERTY_TEXT", "PREVIEW_PICTURE", "PROPERTY_SECTIONS");
$arFilter = array("IBLOCK_ID" =>136, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
$res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
while ($ob = $res->Fetch()) {
    if($ob['PROPERTY_SECTIONS_VALUE']){
        foreach($ob['PROPERTY_SECTIONS_VALUE'] as $section){
            $sectionsID[$section] = $section;
        }
    }
    $sliders[$ob['IBLOCK_SECTION_ID']][] = $ob;
}
if($sectionsID){
    $arFilter = Array('IBLOCK_ID'=>128, 'GLOBAL_ACTIVE'=>'Y', 'ID'=>$sectionsID);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, false,array('ID',"NAME","SECTION_PAGE_URL"));
    while($ar_result = $db_list->GetNext()) {
        $arSections[$ar_result['ID']] = $ar_result;
    }
}
?>
<?if($sliders[742]):?>
<div class="drag-block container">
    <section class="s-categories">
        <div class="maxwidth-theme">
            <div class="categories__list">
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-1.svg" alt="точка роста">
                        </span>
                        <span class="category__title">точка роста</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-2.png" alt="науколаб">
                        </span>
                        <span class="category__title">науколаб</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-3.svg" alt="Внеурочная деятельность">
                        </span>
                        <span class="category__title">Внеурочная деятельность</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-4.svg" alt="Логопед в школе">
                        </span>
                        <span class="category__title">Логопед в школе</span>
                    </a>
                </div>
            </div>

            <div class="categories__list js-categories-sliding" style="display: none">
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-5.png" alt="кванториум">
                        </span>
                        <span class="category__title">кванториум</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-6.png" alt="цифровая образовательная среда">
                        </span>
                        <span class="category__title">цифровая<br> образовательная среда</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-7.png" alt="молодые профессионалы">
                        </span>
                        <span class="category__title">молодые профессионалы</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-8.png" alt="успех каждого ребенка">
                        </span>
                        <span class="category__title">успех каждого ребенка</span>
                    </a>
                </div>
                <div class="category__item">
                    <a href="#" class="category__link">
                        <span class="category__thumb">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/category/category-9.png" alt="учитель будущего">
                        </span>
                        <span class="category__title">учитель будущего</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="categories-toggler">
            <span>показать все</span>
            <svg class="icon" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.83203 5.66418L-6.09162e-05 2.83209H5.66412L2.83203 5.66418Z" fill="currentColor"/>
            </svg>
        </div>
    </section>
    <section class="s-category-slider s-category-slider--main section-narrow" style="background-image: url(/local/templates/aspro_max_knst/assets/img/bg-category-2.png);">
        <div class="container">
            <div class="category-slider-inner">
                <div class="product-category product-category--main">
                    <?/*foreach($sliders[742] as $slide):?>
                        <div class="product-category__content">
                            <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="product-category__title"><span><?=$slide['NAME']?></span> <small><?=$slide['PROPERTY_SUBTITLE_VALUE']?></small></a>
                            <?if($slide['PROPERTY_SECTIONS_VALUE']):?>
                                <div class="product-category__group">
                                    <ul class="product-category__list">
                                        <?foreach($slide['PROPERTY_SECTIONS_VALUE'] as $key=>$section):?>
                                            <li><a href="<?=$arSections[$section]['SECTION_PAGE_URL']?>"><span><?=$arSections[$section]['NAME']?></span></a></li>
                                        <?if(count($slide['PROPERTY_SECTIONS_VALUE'])%2==$key-1):?></ul><ul class="product-category__list"><?endif;?>
                                        	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
                                    </ul>
                                </div>
                            <?endif;?>
                            <div class="product-category__action">
                                <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="knst-btn knst-btn--dark"><?=$slide['PROPERTY_TEXT_VALUE']?></a>
                            </div>
                        </div>
                        <?if($slide['PREVIEW_PICTURE']):?>
                            <div class="product-category__thumb">
                                <div class="category-slider knst-slider owl-carousel">
                                    <img src="<?=CFIle::GetPath($slide['PREVIEW_PICTURE']);?>" alt="<?=$slide['NAME']?>">
                                </div>
                            </div>
                        <?endif;?>
                    <?endforeach;*/?>
                    <?$slide = $sliders[742][0];?>
                    <div class="product-category__content">
                        <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="product-category__title"><span><?=$slide['NAME']?></span> <small><?=$slide['PROPERTY_SUBTITLE_VALUE']?></small></a>
                        <?if($slide['PROPERTY_SECTIONS_VALUE']):?>
                            <div class="product-category__group">
                                <ul class="product-category__list">
                                    <?foreach($slide['PROPERTY_SECTIONS_VALUE'] as $key=>$section):?>
                                        <li><a href="<?=$arSections[$section]['SECTION_PAGE_URL']?>"><span><?=$arSections[$section]['NAME']?></span></a></li>
                                    <?if(round(count($slide['PROPERTY_SECTIONS_VALUE'])/2)==$key+1):?></ul><ul class="product-category__list"><?endif;?>
                                    	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
                                </ul>
                            </div>
                        <?endif;?>
                        <div class="product-category__action">
                            <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="knst-btn knst-btn--dark"><?=$slide['PROPERTY_TEXT_VALUE']?></a>
                        </div>
                    </div>
                    <?if($slide['PREVIEW_PICTURE']):?>
                        <div class="product-category__thumb">
                            <div class="category-slider knst-slider owl-carousel">
                                <?//foreach($sliders[742] as $slide):?>
                                    <div class="slide">
                                        <img src="<?=CFIle::GetPath($slide['PREVIEW_PICTURE']);?>" alt="<?=$slide['NAME']?>">
                                    </div>
                                <?//endforeach;?>
                            </div>
                        </div>
                    <?endif;?>
                </div>
            </div>
        </div>
    </section>
</div>
<?endif;?>
<?
$arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE","IBLOCK_SECTION_ID", "PROPERTY_HIT", "PROPERTY_CML2_ARTICLE");
$arFilter = Array("IBLOCK_ID"=>128, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_HIT_VALUE"=>"Новинка");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>10), $arSelect);
while($ob = $res->GetNext()) {
    $sectionsId3[$ob['IBLOCK_SECTION_ID']] = $ob['IBLOCK_SECTION_ID'];
    $db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'], "CATALOG_GROUP_ID" => 1));
    if ($ar_res = $db_res->Fetch()) {
        $ob['PRICE'] = $ar_res["PRICE"];
    }


    $arProducts[] = $ob;
}

if($sectionsId3){
    $arFilter = Array('IBLOCK_ID'=>128, 'GLOBAL_ACTIVE'=>'Y', 'ID'=>$sectionsId3);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, false,array('ID','NAME','SECTION_PAGE_URL'));
    while($ar_result = $db_list->GetNext()) {
        $sections3[$ar_result['ID']] = $ar_result;
    }
}
?>

<section class="s-product-slider section-narrow bg-silver">
    <div class="container">
        <div class="section-header">
            <div class="toggle-dropdown">
                <div class="toggle-dropdown__header">
                    <div class="toggle-dropdown__title">Новинки</div>
                    <?/*svg class="toggle-dropdown__icon" width="6" height="6" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.83203 5.66406L-6.09162e-05 2.83197H5.66412L2.83203 5.66406Z" fill="black"/>
                    </svg*/?>
                </div>
                <?/*div class="toggle-dropdown__body">
                    <div class="toggle-dropdown__list">
                        <a href="#recommend" class="current">Рекомендуем</a>
                        <a href="#popular">Популярные</a>
                        <a href="#new">Новинки</a>
                    </div>
                </div*/?>
            </div>
        </div>
        <div class="product-slider knst-slider navigation-top owl-carousel">
            <?foreach($arProducts as $product):?>
                <div class="slide">
                    <div class="product-item">
                        <?/*a href="#" class="product-item__favorite active" title="Добавить в избранное">
                            <svg class="favorite-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.27539 5.62377L9.99846 6.38064L10.7215 5.62377C11.4506 4.86059 12.574 3.99146 13.7454 3.99146C14.8954 3.99146 15.13618 4.38828 16.4383 5.01369C17.0647 5.63899 17.4615 6.5529 17.4615 7.69915C17.4615 8.93508 16.9825 9.971361 16.1854 10.9716C15.3691 11.989 14.2711 12.8936 13.0887 13.8624C13.0886 13.8625 13.0886 13.8625 13.0885 13.8626L13.0644 13.8823C12.0431 14.7188 10.9118 15.6452 9.99895 16.61363C9.09504 15.6542 7.9745 14.73128 6.96222 13.9048L6.91113 13.8629L6.91073 13.8626C5.72798 12.8934 4.63005 11.9886 3.136403 10.9712C3.0172 9.97768 2.53846 8.934136 2.53846 7.69915C2.53846 6.5529 2.9353 5.63902 3.56174 5.01374C4.1883 4.38833 5.10491 3.99146 6.25538 3.99146C7.42538 3.99146 8.545128 4.85977 9.27539 5.62377Z" stroke-width="2"/>
                            </svg>
                        </a*/?>
                        <div class="product-item__thumb">
                            <?if($product['PROPERTY_HIT_VALUE'] == 'Новинка' || in_array('Новинка',$product['PROPERTY_HIT_VALUE'])):?>
                                <div class="product-item__labels"><div class="product-label product-label--new">Новинка!</div></div>
                            <?endif;?>
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__quick">
                                <?if($product['DETAIL_PICTURE']):?>
                                    <img src="<?=CFile::GetPath($product['DETAIL_PICTURE'])?>" alt="<?=$product['NAME']?>">
                                <?endif;?>
                                <?/*span class="product-item__view">быстрый просмотр</span*/?>
                            </a>
                        </div>
                        <?/*div class="product-item__action">
                            <div class="quantity">
                                <div class="quantity__controller js-quantity-minus">-</div>
                                <input type="text" value="1" class="quantity__value js-quantity-val">
                                <div class="quantity__controller js-quantity-plus">+</div>
                            </div>
                            <a href="#" class="knst-btn add-baket">Добавить</a>
                        </div*/?>
                        <div class="product-item__content">
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__title"><?=$product['NAME']?></a>
                            <div class="product-item__price"><?=$product["PRICE"]?> р/шт</div>
                        </div>
                        <div class="product-item__extra">
                            <?if($product["PROPERTY_CML2_ARTICLE_VALUE"]):?>
                                <div class="product-item__article">Артикул: <?=$product["PROPERTY_CML2_ARTICLE_VALUE"]?></div>
                            <?endif;?>
                            <?if($sections3[$product['IBLOCK_SECTION_ID']]):?>
                                <a href="<?=$sections3[$product['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>" class="product-item__category"><span><?=$sections3[$product['IBLOCK_SECTION_ID']]['NAME']?></span></a>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
        </div>
    </div>
</section>
<?if($sliders[743]):?>
<div class="drag-block container">
    <section class="s-category-slider section-narrow" style="background-image: url(/local/templates/aspro_max_knst/assets/img/bg-category-1.jpg);">
        <div class="container">
            <div class="category-slider-inner">
                <div class="product-category product-category--bordered">
                    <?$slide = $sliders[743][0];?>
                    <div class="product-category__content">
                        <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="product-category__title"><span><?=$slide['NAME']?></span> <small><?=$slide['PROPERTY_SUBTITLE_VALUE']?></small></a>
                        <?if($slide['PROPERTY_SECTIONS_VALUE']):?>
                            <div class="product-category__group">
                                <ul class="product-category__list">
                                    <?foreach($slide['PROPERTY_SECTIONS_VALUE'] as $key=>$section):?>
                                    <li><a href="<?=$arSections[$section]['SECTION_PAGE_URL']?>"><span><?=$arSections[$section]['NAME']?></span></a></li>
                                    <?if(round(count($slide['PROPERTY_SECTIONS_VALUE'])/2)==$key+1):?></ul><ul class="product-category__list"><?endif;?>
                                    	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
                                </ul>
                            </div>
                        <?endif;?>
                    </div>
                    <?if($slide['PREVIEW_PICTURE']):?>
                        <div class="product-category__thumb">
                            <div class="category-slider category-slider--bordered knst-slider owl-carousel">
                                <?//foreach($sliders[742] as $slide):?>
                                <div class="slide">
                                    <img src="<?=CFIle::GetPath($slide['PREVIEW_PICTURE']);?>" alt="<?=$slide['NAME']?>">
                                </div>
                                <?//endforeach;?>
                            </div>
                            <div class="product-category__action">
                                <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="knst-btn knst-btn--dark"><?=$slide['PROPERTY_TEXT_VALUE']?></a>
                            </div>
                        </div>
                    <?endif;?>
                </div>
            </div>
        </div>
    </section>
</div>
<?endif;?>


<?
$arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE","IBLOCK_SECTION_ID", "PROPERTY_HIT", "PROPERTY_CML2_ARTICLE");
$arFilter = Array("IBLOCK_ID"=>128, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_HIT_VALUE"=>"Хит");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>10), $arSelect);
while($ob = $res->GetNext()) {
    $sectionsId2[$ob['IBLOCK_SECTION_ID']] = $ob['IBLOCK_SECTION_ID'];
    $db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'], "CATALOG_GROUP_ID" => 1));
    if ($ar_res = $db_res->Fetch()) {
        $ob['PRICE'] = $ar_res["PRICE"];
    }


    $arProducts[] = $ob;
}

if($sectionsId2){
    $arFilter = Array('IBLOCK_ID'=>128, 'GLOBAL_ACTIVE'=>'Y', 'ID'=>$sectionsId2);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, false,array('ID','NAME','SECTION_PAGE_URL'));
    while($ar_result = $db_list->GetNext()) {
        $sections2[$ar_result['ID']] = $ar_result;
    }
}
?>

<section class="s-product-slider section-narrow bg-silver">
    <div class="container">
        <div class="section-header">
            <div class="toggle-dropdown">
                <div class="toggle-dropdown__header">
                    <div class="toggle-dropdown__title">Хиты продаж</div>
                    <?/*svg class="toggle-dropdown__icon" width="6" height="6" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.83203 5.66406L-6.09162e-05 2.83197H5.66412L2.83203 5.66406Z" fill="black"/>
                    </svg*/?>
                </div>
                <?/*div class="toggle-dropdown__body">
                    <div class="toggle-dropdown__list">
                        <a href="#recommend" class="current">Рекомендуем</a>
                        <a href="#popular">Популярные</a>
                        <a href="#new">Новинки</a>
                    </div>
                </div*/?>
            </div>
        </div>
        <div class="product-slider knst-slider navigation-top owl-carousel">
            <?foreach($arProducts as $product):?>
                <div class="slide">
                    <div class="product-item">
                        <?/*a href="#" class="product-item__favorite active" title="Добавить в избранное">
                            <svg class="favorite-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.27539 5.62377L9.99846 6.38064L10.7215 5.62377C11.4506 4.86059 12.574 3.99146 13.7454 3.99146C14.8954 3.99146 15.13618 4.38828 16.4383 5.01369C17.0647 5.63899 17.4615 6.5529 17.4615 7.69915C17.4615 8.93508 16.9825 9.971361 16.1854 10.9716C15.3691 11.989 14.2711 12.8936 13.0887 13.8624C13.0886 13.8625 13.0886 13.8625 13.0885 13.8626L13.0644 13.8823C12.0431 14.7188 10.9118 15.6452 9.99895 16.61363C9.09504 15.6542 7.9745 14.73128 6.96222 13.9048L6.91113 13.8629L6.91073 13.8626C5.72798 12.8934 4.63005 11.9886 3.136403 10.9712C3.0172 9.97768 2.53846 8.934136 2.53846 7.69915C2.53846 6.5529 2.9353 5.63902 3.56174 5.01374C4.1883 4.38833 5.10491 3.99146 6.25538 3.99146C7.42538 3.99146 8.545128 4.85977 9.27539 5.62377Z" stroke-width="2"/>
                            </svg>
                        </a*/?>
                        <div class="product-item__thumb">
                            <?if($product['PROPERTY_HIT_VALUE'] == 'Новинка' || in_array('Новинка',$product['PROPERTY_HIT_VALUE'])):?>
                                <div class="product-item__labels"><div class="product-label product-label--new">Новинка!</div></div>
                            <?endif;?>
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__quick">
                                <?if($product['DETAIL_PICTURE']):?>
                                    <img src="<?=CFile::GetPath($product['DETAIL_PICTURE'])?>" alt="<?=$product['NAME']?>">
                                <?endif;?>
                                <?/*span class="product-item__view">быстрый просмотр</span*/?>
                            </a>
                        </div>
                        <?/*div class="product-item__action">
                            <div class="quantity">
                                <div class="quantity__controller js-quantity-minus">-</div>
                                <input type="text" value="1" class="quantity__value js-quantity-val">
                                <div class="quantity__controller js-quantity-plus">+</div>
                            </div>
                            <a href="#" class="knst-btn add-baket">Добавить</a>
                        </div*/?>
                        <div class="product-item__content">
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__title"><?=$product['NAME']?></a>
                            <div class="product-item__price"><?=$product["PRICE"]?> р/шт</div>
                        </div>
                        <div class="product-item__extra">
                            <?if($product["PROPERTY_CML2_ARTICLE_VALUE"]):?>
                                <div class="product-item__article">Артикул: <?=$product["PROPERTY_CML2_ARTICLE_VALUE"]?></div>
                            <?endif;?>
                            <?if($sections2[$product['IBLOCK_SECTION_ID']]):?>
                                <a href="<?=$sections2[$product['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>" class="product-item__category"><span><?=$sections2[$product['IBLOCK_SECTION_ID']]['NAME']?></span></a>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
        </div>
    </div>
</section>
<?if($sliders[744]):?>
<section class="s-category-slider section-narrow">
    <div class="container">
        <div class="category-slider-inner">
            <div class="product-category">
                <?$slide = $sliders[744][0];?>
                <div class="product-category__content">
                    <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="product-category__title"><span><?=$slide['NAME']?></span> <small><?=$slide['PROPERTY_SUBTITLE_VALUE']?></small></a>
                    <?if($slide['PROPERTY_SECTIONS_VALUE']):?>
                        <div class="product-category__group">
                            <ul class="product-category__list">
                                <?foreach($slide['PROPERTY_SECTIONS_VALUE'] as $key=>$section):?>
                                <li><a href="<?=$arSections[$section]['SECTION_PAGE_URL']?>"><span><?=$arSections[$section]['NAME']?></span></a></li>
                                <?if(round(count($slide['PROPERTY_SECTIONS_VALUE'])/2)==$key+1):?></ul><ul class="product-category__list"><?endif;?>
                                	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
                            </ul>
                        </div>
                    <?endif;?>
                    <div class="product-category__action">
                        <a href="<?=$slide['PROPERTY_LINK_VALUE']?>" class="knst-btn knst-btn--dark"><?=$slide['PROPERTY_TEXT_VALUE']?></a>
                    </div>
                </div>
                <?if($slide['PREVIEW_PICTURE']):?>
                    <div class="product-category__thumb">
                        <div class="category-slider knst-slider owl-carousel">
                            <?//foreach($sliders[742] as $slide):?>
                            <div class="slide">
                                <img src="<?=CFIle::GetPath($slide['PREVIEW_PICTURE']);?>" alt="<?=$slide['NAME']?>">
                            </div>
                            <?//endforeach;?>
                        </div>
                    </div>
                <?endif;?>
            </div>
        </div>
    </div>
</section>
<?endif;?>
<?
$arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE","IBLOCK_SECTION_ID", "PROPERTY_HIT", "PROPERTY_CML2_ARTICLE");
$arFilter = Array("IBLOCK_ID"=>128, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_HIT_VALUE"=>"Советуем");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>10), $arSelect);
while($ob = $res->GetNext()) {
    $sectionsId[$ob['IBLOCK_SECTION_ID']] = $ob['IBLOCK_SECTION_ID'];
    $db_res = CPrice::GetList(array(),array("PRODUCT_ID" => $ob['ID'], "CATALOG_GROUP_ID" => 1));
    if ($ar_res = $db_res->Fetch()) {
        $ob['PRICE'] = $ar_res["PRICE"];
    }


    $arProducts[] = $ob;
}

if($sectionsId){
    $arFilter = Array('IBLOCK_ID'=>128, 'GLOBAL_ACTIVE'=>'Y', 'ID'=>$sectionsId);
    $db_list = CIBlockSection::GetList(Array(), $arFilter, false,array('ID','NAME','SECTION_PAGE_URL'));
    while($ar_result = $db_list->GetNext()) {
        $sections[$ar_result['ID']] = $ar_result;
    }
}
?>
<section class="s-product-slider section-narrow bg-silver">
    <div class="container">
        <div class="section-header">
            <div class="toggle-dropdown">
                <div class="toggle-dropdown__header">
                    <div class="toggle-dropdown__title">Рекомендуем</div>
                    <?/*svg class="toggle-dropdown__icon" width="6" height="6" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.83203 5.66406L-6.09162e-05 2.83197H5.66412L2.83203 5.66406Z" fill="black"/>
                    </svg*/?>
                </div>
                <?/*div class="toggle-dropdown__body">
                    <div class="toggle-dropdown__list">
                        <a href="#recommend" class="current">Рекомендуем</a>
                        <a href="#popular">Популярные</a>
                        <a href="#new">Новинки</a>
                    </div>
                </div*/?>
            </div>
        </div>
        <div class="product-slider knst-slider navigation-top owl-carousel">
            <?foreach($arProducts as $product):?>
                <div class="slide">
                    <div class="product-item">
                        <?/*a href="#" class="product-item__favorite active" title="Добавить в избранное">
                            <svg class="favorite-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.27539 5.62377L9.99846 6.38064L10.7215 5.62377C11.4506 4.86059 12.574 3.99146 13.7454 3.99146C14.8954 3.99146 15.13618 4.38828 16.4383 5.01369C17.0647 5.63899 17.4615 6.5529 17.4615 7.69915C17.4615 8.93508 16.9825 9.971361 16.1854 10.9716C15.3691 11.989 14.2711 12.8936 13.0887 13.8624C13.0886 13.8625 13.0886 13.8625 13.0885 13.8626L13.0644 13.8823C12.0431 14.7188 10.9118 15.6452 9.99895 16.61363C9.09504 15.6542 7.9745 14.73128 6.96222 13.9048L6.91113 13.8629L6.91073 13.8626C5.72798 12.8934 4.63005 11.9886 3.136403 10.9712C3.0172 9.97768 2.53846 8.934136 2.53846 7.69915C2.53846 6.5529 2.9353 5.63902 3.56174 5.01374C4.1883 4.38833 5.10491 3.99146 6.25538 3.99146C7.42538 3.99146 8.545128 4.85977 9.27539 5.62377Z" stroke-width="2"/>
                            </svg>
                        </a*/?>
                        <div class="product-item__thumb">
                            <?if($product['PROPERTY_HIT_VALUE'] == 'Новинка' || in_array('Новинка',$product['PROPERTY_HIT_VALUE'])):?>
                                <div class="product-item__labels"><div class="product-label product-label--new">Новинка!</div></div>
                            <?endif;?>
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__quick">
                                <?if($product['DETAIL_PICTURE']):?>
                                <img src="<?=CFile::GetPath($product['DETAIL_PICTURE'])?>" alt="<?=$product['NAME']?>">
                                <?endif;?>
                                <?/*span class="product-item__view">быстрый просмотр</span*/?>
                            </a>
                        </div>
                        <?/*div class="product-item__action">
                            <div class="quantity">
                                <div class="quantity__controller js-quantity-minus">-</div>
                                <input type="text" value="1" class="quantity__value js-quantity-val">
                                <div class="quantity__controller js-quantity-plus">+</div>
                            </div>
                            <a href="#" class="knst-btn add-baket">Добавить</a>
                        </div*/?>
                        <div class="product-item__content">
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" class="product-item__title"><?=$product['NAME']?></a>
                            <div class="product-item__price"><?=$product["PRICE"]?> р/шт</div>
                        </div>
                        <div class="product-item__extra">
                            <?if($product["PROPERTY_CML2_ARTICLE_VALUE"]):?>
                                <div class="product-item__article">Артикул: <?=$product["PROPERTY_CML2_ARTICLE_VALUE"]?></div>
                            <?endif;?>
                            <?if($sections[$product['IBLOCK_SECTION_ID']]):?>
                                <a href="<?=$sections[$product['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>" class="product-item__category"><span><?=$sections[$product['IBLOCK_SECTION_ID']]['NAME']?></span></a>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
        </div>
    </div>
</section>
<?
$arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "DATE_CREATE");
$arFilter = Array("IBLOCK_ID"=>122, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>7), $arSelect);
while($ob = $res->GetNext()) {
    $arNews[] = $ob;
}
if($arNews):?>
<section class="s-news-slider section-narrow container">
    <div class="container">
        <div class="news-slider-inner">
            <div class="section-title">Новости</div>
            <div class="news-slider knst-slider navigation-top owl-carousel">
                <?foreach($arNews as $news):?>
                    <div class="slide">
                        <div class="news-item">
                            <a href="<?=$news['DETAIL_PAGE_URL']?>" class="news-item__thumb">
                                <img src="<?=CFile::GetPath($news['PREVIEW_PICTURE']);?>" alt="<?=$news['NAME']?>">
                            </a>
                            <div class="news-item__date"><?=$news['DATE_CREATE']?></div>
                            <a href="<?=$news['DETAIL_PAGE_URL']?>" class="news-item__title"><?=$news['NAME']?></a>
                            <div class="news-item__text"><?//=$news['PREVIEW_TEXT']?></div>
                        </div>
                    </div>
                	<?//VK?>
		<?if($optionCode === "VK"):?>
			<?global $bShowVK, $bVKIndexClass;?>
			<?if($bShowVK):?>
				<div class="drag-block container <?=$optionCode?> <?=$bVKIndexClass;?> js-load-block loader_circle" data-class="<?=$subtype?>_drag" data-order="<?=++$key;?>" data-file="<?=SITE_DIR;?>include/mainpage/components/<?=$subtype;?>/<?=$strTemplateName;?>.php">
					<?=CMax::ShowPageType('mainpage', $subtype, $strTemplateName);?>
				</div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
            </div>
            <div class="section-footer text-center">
                <a href="/company/news/" class="knst-btn knst-btn--dark">Все новости</a>
            </div>
        </div>
    </div>
</section>
<?endif;?>



<div class="drag-block container">
    <section class="school">
        <div class="container">
            <div class="school__inner">
                <h3 class="school__title">Оснащение школ</h3>
                <img data-lazyload="" class="school__img lazyloaded" src="http://test.knst-ekb.ru/upload/resize_cache/iblock/109/1000_1000_0/school.png" data-src="http://test.knst-ekb.ru/upload/resize_cache/iblock/109/1000_1000_0/school.png" alt="">
                <p class="school__desc">Эффективная работа образовательного учреждения должна строиться на комплексном подходе, который помимо высокой квалификации педагогического состава -включает использование учебного оборудования и наглядных пособий, разработанных с учетом возрастных особенностей учащихся. Группа компаний «Новация» предлагает современные и технологичные решения по оснащению школ и детских садов, которые соответствуют методическим требованиям и стандартам, принятым в системе образования. Ассортимент учебного оборудования и наглядных пособий направлен на создание и реализацию успешных учебных программ, которые помогут ученикам приобрести интерес к изучаемым предметам, а преподавателям повысить уровень своей компетентности.<br><br>Мы предлагаем комплексные варианты оснащения и составляем индивидуальные проекты комплектации. Только нужные и полезные решения для образовательных учреждений, которые позволят реализовать уникальные методические разработки с максимальной функциональностью и информативностью.</p>
                <div class="school__bottom"><a href="company/" class="knst-btn knst-btn--dark">подробнее</a></div>
            </div>
        </div>
    </section>
</div>