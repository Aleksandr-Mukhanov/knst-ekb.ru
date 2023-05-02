<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowMeta("viewport");?>
    <?$APPLICATION->ShowMeta("HandheldFriendly");?>
    <?$APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes");?>
    <?$APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style");?>

    <link href="/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?$APPLICATION->ShowMeta("SKYPE_TOOLBAR");?>
    <?$APPLICATION->ShowHead();?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/styles/jquery.fancybox.css?ver=1241241244" type="text/css" />
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/styles/slick.css?ver=1241241244" type="text/css" />
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/styles/style.css?ver=1241241244" type="text/css" />
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/styles/responsive.css?ver=1241241244" type="text/css" />
    <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH;?>/styles/custom.css" type="text/css" />

</head>

<?
    $res = \Bitrix\Sale\Location\LocationTable::getList(array(
        'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_CODE' => 'CITY'),
        'order' => array('NAME_RU' => 'ASC'),
        'select' => array('*', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE')
    ));
    while($item = $res->fetch())
    {
        $cities[$item['ID']] = $item['NAME_RU'];
        if ($item['ID'] == $_SESSION['city'])
            $current_city = $item['NAME_RU'];
    }
?>

<body>
    <? $APPLICATION->ShowPanel(); ?>
    <div class="menu-modal">
        <div class="menu-modal__close"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/close.svg"></i></div>

        <div class="menu-modal__logo"><img src="<?=SITE_TEMPLATE_PATH;?>/images/svg/logo.svg" alt=""></div>

        <nav class="menu-modal__nav">
            <ul>
                <li class="active"><a href="#">Оплата</a></li>
                <li><a href="#">Доставка</a></li>
                <li><a href="#">Контакты</a></li>
                <li><a href="#">Госзаказ</a></li>
            </ul>
        </nav>

        <div class="menu-modal__bottom">
            <div class="cities">
                <span><?=($current_city ? $current_city : 'Екатеринбург');?> <i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/chevron-d.svg"></i></span>
            </div>

            <div class="header__contacts">
                <div class="header__phone"><a href="tel:8 800 355 55 55">8 800 355 55 55</a></div>
                <div class="header__callback"><a href="#">заказать звонок</a></div>
            </div>
        </div>

    </div>

    <div class="cats-modal">
        <div class="cats-modal__title-m">КАТАЛОГ <b class="cats-modal__close"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/close.svg"></i></b></div>
        <div class="cats-modal__block">
            <div class="wrap">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.section.list",
                    "catalog_menu",
                    array(
                        "ADDITIONAL_COUNT_ELEMENTS_FILTER" => "additionalCountFilter",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "CACHE_FILTER" => "Y",
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
                        "TOP_DEPTH" => "3",
                        "VIEW_MODE" => "LINE",
                        "COMPONENT_TEMPLATE" => "catalog_menu"
                    ),
                    false
                );?>
            </div>
        </div>
    </div>


    <div class="cities-modal">
        <div class="cities-modal__inner">
            <div class="search">
                <form class="search_form">
                    <input type="search" placeholder="Поиск по городам">
                    <button type="submit" class="search-btn"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/search.svg"></i></button>
                </form>
            </div>
            <div class="cities-modal__close"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/close.svg"></i></div>

            <div class="cities-modal__content">
                <div class="cities-modal__chosen">сейчас: <?=($current_city ? $current_city : 'Екатеринбург');?></div>
                <div class="cities-modal__list">
                    <ul>
                        <? foreach ($cities as $city_id => $city): ?>
                            <li><a href="<?=$APPLICATION->GetCurPageParam('city=' . $city_id, ['city']);?>"><?=$city; ?></a></li>
                       <? endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <header class="header">
        <div class="wrap">
            <div class="header__top">
                <div class="header__serch-btn"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/search.svg"></i></div>

                <div class="logo">
                    <a href="/"><img src="<?=SITE_TEMPLATE_PATH;?>/images/svg/logo.svg" alt=""></a>
                </div>

                <nav class="header__nav">
                    <ul>
                        <li class="active"><a href="#">Оплата</a></li>
                        <li><a href="#">Доставка</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Госзаказ</a></li>
                    </ul>
                </nav>

                <div class="cities">
                    <span><?=($current_city ? $current_city : 'Екатеринбург');?> <i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/chevron-d.svg"></i></span>
                </div>

                <div class="header__contacts">
                    <div class="header__phone"><a href="tel:8 800 355 55 55">8 800 355 55 55</a></div>
                    <div class="header__callback"><a href="#">заказать звонок</a></div>
                </div>

                <div class="header__hamburger"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/menu2.svg"></i></div>
            </div>

            <div class="header__bottom">
                <div class="header__cat-btn"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/menu.svg" class="cat-btn-open"></i><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/close-sm.svg" class="cat-btn-close"></i> КАТАЛОГ</div>

                <div class="search">
                     <form class="search_form">
                        <input type="search" placeholder="Поиск товаров по категориям">
                        <button type="submit" class="search-btn"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/search.svg"></i></button>
                    </form>
                </div>

                <div class="personal">
                    <a href="#"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/doc.svg"></i></a>
                    <a href="/basket/">
                        <?/*$APPLICATION->IncludeComponent(
                            "bitrix:sale.basket.basket.line",
                            "",
                            Array(
                                "HIDE_ON_BASKET_PAGES" => "N",
                                "PATH_TO_AUTHORIZE" => "",
                                "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
                                "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
                                "PATH_TO_PERSONAL" => SITE_DIR."personal/",
                                "PATH_TO_PROFILE" => SITE_DIR."personal/",
                                "PATH_TO_REGISTER" => SITE_DIR."login/",
                                "POSITION_FIXED" => "N",
                                "SHOW_AUTHOR" => "N",
                                "SHOW_EMPTY_VALUES" => "Y",
                                "SHOW_NUM_PRODUCTS" => "Y",
                                "SHOW_PERSONAL_LINK" => "N",
                                "SHOW_PRODUCTS" => "N",
                                "SHOW_REGISTRATION" => "N",
                                "SHOW_TOTAL_PRICE" => "Y"
                            )
                        );*/?>
                        <i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/cart.svg">3</i>
                    </a>
                    <? if ($USER->IsAuthorized()): ?>
                        <a href="/personal/"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/person.svg"></i> <span>Личный кабинет</span></a>
                    <? else: ?>
                        <a href="/auth/"><i data-svg="<?=SITE_TEMPLATE_PATH;?>/images/svg/icons/person.svg"></i> <span>Войти</span></a>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="main">