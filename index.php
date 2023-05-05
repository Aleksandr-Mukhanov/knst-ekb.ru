<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("viewed_show", "Y");
$APPLICATION->SetTitle("Константа - интернет-магазин");
?>    <section class="hero">
        <div class="wrap">
            <div class="hero__inner" style="background-color: #365EDC;">
                <div class="hero__text">
                    <div class="hero__title">Комплексное оснащение школ по ФГОС</div>
                    <div class="hero__desc">Доставляем по РФ за 2 недели</div>
                    <div class="button"><a href="/catalog/" class="btn btn-yellow">подобрать оборудование</a></div>
                </div>
                <div class="hero__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/hero.png" alt=""></div>
            </div>
        </div>
    </section>

    <?php
        global $homeFilter;
        $homeFilter = [
            '>CATALOG_PRICE_1' => 0,
            '!PREVIEW_PICTURE' => false
        ];
    ?>
    <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"popularHome", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/basket/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPATIBLE_MODE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
		"DETAIL_URL" => "catalog/#ELEMENT_CODE#/",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_COMPARE" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"ENLARGE_PRODUCT" => "STRICT",
		"FILTER_NAME" => "homeFilter",
		"HIDE_NOT_AVAILABLE" => "Y",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"IBLOCK_ID" => "128",
		"IBLOCK_TYPE" => "aspro_max_catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => array(
		),
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "3",
		"LOAD_ON_SCROLL" => "N",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_LAZY_LOAD" => "Показать ещё",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "8",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'1','BIG_DATA':false}]",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTY_CODE_MOBILE" => array(
		),
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"RCM_TYPE" => "personal",
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "catalog/#SECTION_CODE_PATH#/",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_FROM_SECTION" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_SLIDER" => "N",
		"SLIDER_INTERVAL" => "3000",
		"SLIDER_PROGRESS" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"COMPONENT_TEMPLATE" => "popularHome",
		"SEF_RULE" => "",
		"SECTION_CODE_PATH" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SHOW_RATING" => "Y"
	),
	false
);?>

    <section class="instock-main">
        <div class="wrap">
            <div class="instock-main__inner">
                <div class="constanta">
                    <div class="constanta__logo">
                        <img src="<?=SITE_TEMPLATE_PATH;?>/images/svg/logo.svg" alt="">
                        <div class="button m-show"><a href="#" class="btn">скачать каталог</a></div>
                        <div class="constanta__caption m-show">Скачали уже 557 школ</div>
                    </div>
                    <div class="constanta__list">
                        <ul>
                            <li>— Более 10 лет опыта поставок</li>
                            <li>— Собственное производство</li>
                            <li>— Товары соответствуют ФГОС</li>
                            <li>— Гарантия на все оборудование</li>
                            <li>— Кажду позицию проверяем перед поставкой</li>
                            <li>— Реализуем проекты разного масштаба: от одного кабинета до национальных проектов</li>
                        </ul>
                    </div>
                    <div class="button"><a href="#" class="btn">скачать каталог</a></div>
                    <div class="constanta__caption">Скачали уже 557 школ</div>
                </div>

                <div class="instock-cats">
                    <h3>В наличии 15.000 позиций</h3>
                    <?
                        global $sectionsFilter;
                        $sectionsFilter = [
                            'UF_POPULAR' => 1
                        ];
                    ?>
                    <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list", 
	"homeSections", 
	array(
		"ADDITIONAL_COUNT_ELEMENTS_FILTER" => "additionalCountFilter",
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COUNT_ELEMENTS" => "Y",
		"COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
		"FILTER_NAME" => "sectionsFilter",
		"HIDE_SECTIONS_WITH_ZERO_COUNT_ELEMENTS" => "N",
		"IBLOCK_ID" => "128",
		"IBLOCK_TYPE" => "aspro_max_catalog",
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_ID" => "",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_PARENT_NAME" => "Y",
		"TOP_DEPTH" => "1",
		"VIEW_MODE" => "LINE",
		"COMPONENT_TEMPLATE" => "homeSections"
	),
	false
);?>
                </div>
            </div>
        </div>
    </section>

    <section class="economy">
        <div class="wrap">
            <h2>Экономим вам более двух<br> дней работы и нервы</h2>
            <div class="subtitle">Закрываем все вопросы “До поставки” и “После поставки”</div>

            <div class="economy__inner">
                <div class="economy__block">
                    <div class="economy__image"><img src="/bitrix/templates/aspro_max_knst/images/economy1.jpg" alt=""></div>
                    <div class="economy__content">
                        <div class="economy__title">До поставки</div>
                        <div class="economy__text">
                            <ul>
                                <li>1) Подбираем оборудование с лучшими характеристиками под ваш бюджет</li>
                                <li>2) Готовим коммерческие предложения</li>
                                <li>3) Согласовываем с департаментом образования</li>
                                <li>4) Привозим в любую точку России точно в срок</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="economy__block">
                    <div class="economy__image"><img src="/bitrix/templates/aspro_max_knst/images/economy2.jpg" alt=""></div>
                    <div class="economy__content">
                        <div class="economy__title">После поставки</div>
                        <div class="economy__text">
                            <ul>
                                <li>1) Самостоятельно разгружаем, проводим сборку и монтаж</li>
                                <li>2) Обучаем учителей правилам пользования техникой</li>
                                <li>3) Предоставляем отчетные документы</li>
                                <li>4) Отвечаем на вопросы, возникшие во время эксплуатации</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="price-about">
        <div class="wrap">
            <div class="price-about__inner">
                <h2>Цены ниже на 30%</h2>
                <div class="subtitle">У нас получается делать цену ниже без потери<br> качества благодаря следующим факторам:</div>

                <div class="price-about__blocks">
                    <div class="price-about__block">
                        <div class="price-about__content">
                            <div class="price-about__icon"><img src="<?=SITE_TEMPLATE_PATH;?>/images/price-about/price-about-i1.svg" alt=""></div>
                            <div class="price-about__desc">
                                <div class="price-about__title">Собственное производство мебели</div>
                                <div class="price-about__text">Контролируем качество продукции, выполняем сложные проекты, а также проводим ремонт в случае необходимости</div>
                            </div>
                        </div>

                        <div class="price-about__slider">
                            <div class="price-about__slider-js price-about__slider-js1">
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about1.jpg" data-fancybox="price-about1"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about1.jpg" alt=""></a></div>
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about2.jpg" data-fancybox="price-about1"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about2.jpg" alt=""></a></div>
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about3.jpg" data-fancybox="price-about1"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about3.jpg" alt=""></a></div>
                            </div>

                            <div class="price-about__nav">
                                <div class="price-about__prev1 slick-arrow slick-prev"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/prev.svg"></i></div>
                                <div class="price-about__next1 slick-arrow slick-next"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/next.svg"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="price-about__block">
                        <div class="price-about__content">
                            <div class="price-about__icon"><img src="<?=SITE_TEMPLATE_PATH;?>/images/price-about/price-about-i2.svg" alt=""></div>
                            <div class="price-about__desc">
                                <div class="price-about__title">Собственные монтажники</div>
                                <div class="price-about__text">Вы экономите на услуге монтажа. Мы не только привезем необходимые позиции, а еще выгрузим и проведем сборку.</div>
                            </div>
                        </div>

                        <div class="price-about__slider">
                            <div class="price-about__slider-js price-about__slider-js2">
                                <div class="price-about__slide"><a href="<?=SITE_TEMPLATE_PATH;?>/images/price-about/price-about2.jpg" data-fancybox="price-about2"><img src="<?=SITE_TEMPLATE_PATH;?>/images/price-about/price-about2.jpg" alt=""></a></div>
                            </div>

                            <div class="price-about__nav">
                                <div class="price-about__prev2 slick-arrow slick-prev"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/prev.svg"></i></div>
                                <div class="price-about__next2 slick-arrow slick-next"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/next.svg"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="price-about__block">
                        <div class="price-about__content">
                            <div class="price-about__icon"><img src="<?=SITE_TEMPLATE_PATH;?>/images/price-about/price-about-i3.svg" alt=""></div>
                            <div class="price-about__desc">
                                <div class="price-about__title">Прямые поставки</div>
                                <div class="price-about__text">Мы получаем дополнительные скидки от производителей и поставщиков за счет больших объемов закупок. Убедитесь сами.</div>
                            </div>
                        </div>

                        <div class="price-about__slider">
                            <div class="price-about__slider-js price-about__slider-js3">
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg1.jpg" data-fancybox="price-about3"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg1.jpg" alt=""></a></div>
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg2.jpg" data-fancybox="price-about3"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg2.jpg" alt=""></a></div>
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg3.jpg" data-fancybox="price-about3"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg3.jpg" alt=""></a></div>
                                <div class="price-about__slide"><a href="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg4.jpg" data-fancybox="price-about3"><img src="/bitrix/templates/aspro_max_knst/images/price-about/price-about-lg4.jpg" alt=""></a></div>
                            </div>

                            <div class="price-about__nav">
                                <div class="price-about__prev3 slick-arrow slick-prev"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/prev.svg"></i></div>
                                <div class="price-about__next3 slick-arrow slick-next"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/next.svg"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feedback feedback__price">
        <div class="wrap">
            <div class="feedback__inner">
                <div class="feedback__content">
                    <div class="feedback__text">
                        <div class="feedback__title">Узнайте стоимость</div>
                        <div class="feedback__desc">Оставьте номер, наш специалист перезвонит вам и уточнит нюансы, необходимые для КП</div>
                    </div>
                    <div class="feedback__form">
                        <div class="form style2">
                            <form class="send_form">
                                <input type="hidden" value="1" name="send_form" />
                                <div class="form__row">
                                    <input name="fio" type="text" class="inputbox required" placeholder="Имя">
                                </div>
                                <div class="form__row">
                                    <input name="phone" type="tel" class="inputbox required" placeholder="Телефон">
                                </div>
                                <div class="form__row form__submit">
                                    <input type="submit" class="btn btn-yellow" value="связаться">
                                </div>
                                <div class="form__row form__accept"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/check.svg"></i><span>Согласен с <a href="#">политикой конфиденциальности</a> и <a href="#">пользовательским соглашением</a></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="feedback__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/feedback.png" alt=""></div>
                <div class="feedback__image-m"><img src="<?=SITE_TEMPLATE_PATH;?>/images/feedback-m.png" alt=""></div>
            </div>
        </div>
    </section>

    <section class="purchase-var">
        <div class="wrap">
            <h2>Работаем со всеми формами закупок</h2>

            <div class="purchase-var__inner">
                <div class="purchase-var__left">
                    <div class="purchase-var__block">
                        <div class="purchase-var__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/purchase-var1.png" alt=""></div>
                        <div class="purchase-var__title">Прямой договор</div>
                        <div class="purchase-var__text">Составим договор, впишемся в нужный бюджет, согласуем с Управлением Образования</div>
                    </div>
                </div>

                <div class="purchase-var__right">
                    <div class="purchase-var__block">
                        <div class="purchase-var__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/purchase-var2.png" alt=""></div>
                        <div class="purchase-var__title">Малая закупка</div>
                        <div class="purchase-var__text">Подготовим договор контракта из КТРУ</div>
                    </div>
                    <div class="purchase-var__block">
                        <div class="purchase-var__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/purchase-var3.png" alt=""></div>
                        <div class="purchase-var__title">Тендер</div>
                        <div class="purchase-var__text">Работаем по 223 ФЗ и 44 ФЗ. Готовим документы и познакомим вас с нюансами закупки через аукцион</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="accredited">
        <div class="wrap">
            <div class="accredited__inner">
                <div class="accredited__title">
                    <h3>Аккредитованы на всех основных тендерных площадках</h3>
                    <img src="<?=SITE_TEMPLATE_PATH;?>/images/svg/logo-lg.svg" alt="">
                </div>

                <div class="accredited__items">
                    <span><i><img src="<?=SITE_TEMPLATE_PATH;?>/images/accredited1.png" alt=""></i></span>
                    <span><i><img src="<?=SITE_TEMPLATE_PATH;?>/images/accredited2.png" alt=""></i></span>
                    <span><i><img src="<?=SITE_TEMPLATE_PATH;?>/images/accredited3.png" alt=""></i></span>
                    <span><i><img src="<?=SITE_TEMPLATE_PATH;?>/images/accredited4.png" alt=""></i></span>
                    <span><i><img src="<?=SITE_TEMPLATE_PATH;?>/images/accredited5.png" alt=""></i></span>
                </div>
            </div>
        </div>
    </section>

    <section class="cases">
        <div class="wrap">
            <h2>За 11 лет укомплектовали свыше<br> 2000 гос. учереждений</h2>
            <div class="subtitle">Каждый день мы упрощаем жизнь директорам, завхозам и учителям</div>

            <div class="cases__inner">
                <div class="case">
                    <div class="case__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/cases/case1.png" alt=""></div>
                    <div class="case__title">Нацпроект Точка Роста</div>
                    <div class="case__text">
                        <p>Поставка оборудования в Сеяхинскую школуболее 17 позицийот жалюзи до верстаков. Сумма поставки 932 тыс.руб.</p>
                        <p>Выполнили заказ за 2 месяца от заявки до монтажа с учетом удаленной доставки.</p>
                    </div>
                </div>
                <div class="case">
                    <div class="case__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/cases/case2.png" alt=""></div>
                    <div class="case__title">Нацпроект - Доступная среда</div>
                    <div class="case__text">
                        <p>Поставка инклюзивного оборудования в Детский сад города Петропавловск-Камчатск, Стол СИ-1, кресло-колсяки, эвакуационный лестичный стул.Сумма поставки 360 тыс. руб.</p>
                        <p>Выполнили заказ за 3 месяца от заявки до монтажа с учетом удаленной доставки.</p>
                    </div>
                </div>
                <div class="case">
                    <div class="case__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/cases/case3.png" alt=""></div>
                    <div class="case__title">Нацпроект Точка Роста</div>
                    <div class="case__text">
                        <p>Поставка Антитеррор оборудования в школу города Пыть-Яха под срочную закупку металлодетекторы, барьеры, ограждения. Сумма поставки 3,5 млн.</p>
                        <p>Выполнили заказ за 3 месяца от заявки до монтажа.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feedback2">
        <div class="wrap">
            <div class="feedback2__inner">
                <div class="feedback2__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/feedback2.png" alt=""></div>
                <div class="feedback2__text">
                    <div class="feedback2__title">Вам нужно укомплектовать класс или группу?</div>
                    <div class="feedback2__desc">Отправьте нам список нужного оборудования или опишите задачу, мы подготовим вам КП и сориентируем в какие сроки она может быть выполнена</div>
                </div>
                <div class="button"><a href="#" class="btn">заказать расчет</a></div>
            </div>
        </div>
    </section>

    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "home_letters",
        Array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "FIELD_CODE" => array("", ""),
            "FILTER_NAME" => "",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "IBLOCK_ID" => "139",
            "IBLOCK_TYPE" => "content",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "20",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Новости",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "PROPERTY_CODE" => array("", ""),
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SORT_BY1" => "SORT",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER1" => "ASC",
            "SORT_ORDER2" => "DESC",
            "STRICT_SECTION_CHECK" => "N"
        )
    );?>
 
    <section class="feedback feedback-ask">
        <div class="wrap">
            <div class="feedback__inner">
                <div class="feedback__content">
                    <div class="feedback__text">
                        <div class="feedback__title">Появился вопрос?</div>
                        <div class="feedback__desc">Оставьте свой номер, наш специалист перезвонит вам и ответит на ваши вопросы.</div>
                    </div>
                    <div class="feedback__form">
                        <div class="form style2">
                            <form class="send_form">
                                <input type="hidden" value="1" name="send_form" />
                                <div class="form__row">
                                    <input name="fio" type="text" class="inputbox required" placeholder="Имя">
                                </div>
                                <div class="form__row">
                                    <input name="phone" type="tel" class="inputbox required" placeholder="Телефон">
                                </div>
                                <div class="form__row form__submit">
                                    <input type="submit" class="btn btn-yellow" value="связаться">
                                </div>
                                <div class="form__row form__accept"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/check.svg"></i><span>Согласен с <a href="#">политикой конфиденциальности</a> и <a href="#">пользовательским соглашением</a></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="feedback__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/feedback3.png" alt=""></div>
                <div class="feedback__image-m"><img src="<?=SITE_TEMPLATE_PATH;?>/images/feedback3-m.png" alt=""></div>
            </div>
        </div>
    </section>

    <section class="about">
        <div class="wrap">
            <div class="about__inner">
                <div class="about__text">
                    <h1>Комплексное оснащение школ</h1>
                    <p class="about__image-m"><img src="/bitrix/templates/aspro_max_knst/images/about.jpg" alt=""></p>
                    <p> Полная комплектация СОШ «ПОД КЛЮЧ» - это направление, в котором мы работаем уже на протяжении 10 лет и накопили за это время большой опыт. В 2018 году укомплектовали более 2000 учреждений по России, включая города Сибири, Урала, Дальнего Востока и Республики Крым.</p>
                    <p>Наши специалисты проведут персональные консультации по оснащению, подберут рациональный вариант, возьмут на себя все организационные вопросы, позаботятся о своевременных поставках, произведут установку и монтаж.</p>

                    <ul>
                        <li>— Комплектация СОШ от А до Я</li>
                        <li>— Весь ассортимент соответствует ФГОС</li>
                        <li>— Оптовая продажа оборудования для кабинетов, мебели, физкультурного оборудования, интерактивных систем и т.д.</li>
                        <li>— Помощь в оформлении документов, в том числе тендеров для СОШ.</li>
                    </ul>

                    <p>Комплектация школ включает в себя подбор мебели и оборудования, соответственно профилю учреждения и возрастным потребностям в каждом кабинете. Возможна комплектация как всего учреждения, так и отдельного класса.</p>
                    <p>Мы совершенно бесплатно проконсультируем вас по ФГОС и новым нормам комплектации образовательных учреждений. Обращаем ваше внимание, что наша компания работает не только с государственными учреждениями, но и с частными школами, детскими домами, домами культуры, оптовыми компаниями.</p>
                </div>

                <div class="about__image"><img src="<?=SITE_TEMPLATE_PATH;?>/images/about.jpg" alt=""></div>
            </div>
        </div>
    </section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>