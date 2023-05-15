<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $arTheme;
$bPrintButton = isset($arTheme['PRINT_BUTTON']) ? ($arTheme['PRINT_BUTTON']['VALUE'] == 'Y' ? true : false) : false;
?>

<footer class="footer">
    <div class="wrap">
        <div class="footer__menu">
            <nav class="footer__nav">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "bottom",
                    array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "left",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "1",
                        "MENU_CACHE_GET_VARS" => array(""),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "bottom",
                        "USE_EXT" => "N"
                    )
                ); ?>
            </nav>

            <div class="cities">
                <span>Екатеринбург <i data-svg="<?= SITE_TEMPLATE_PATH; ?>/images/svg/icons/chevron-d.svg"></i></span>
            </div>
        </div>

        <div class="footer__inner">
            <div class="footer__form">
                <div class="footer__title">Связаться с нами</div>
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
		"WEB_FORM_ID" => "20",
		"COMPONENT_TEMPLATE" => ".default",
		"VARIABLE_ALIASES" => array(
			"action" => "action",
		)
	),
	false
); ?>
            </div>

            <div class="footer__contacts">
                <div class="footer__phone"><a href="tel:88003011341">8 800 301 13 41</a></div>
                <div class="footer__email"><a href="mailto:zakaz@td-const.ru">zakaz@td-const.ru</a></div>
                <div class="footer__adress">г. Екатеринбург, ул.Репина 42А, офис 50</div>
            </div>

            <div class="footer__text">
                <div class="footer__info">*Информация о товаре не является публичной офертой, определяемой Статьей 437 ГК РФ. Изображение товара может отличаться от полученного Вами товара. Производитель оставляет за собой право изменять комплектацию и технические характеристики без предварительного уведомления заказчика, при этом функционал и качество товара остается прежним.</div>

                <div class="copyright">2023 © knst.ru <a href="#" target="_blank">Политика конфиденциальности</a></div>
            </div>
        </div>
    </div>
</footer>