<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><script>window.basketJSParams = window.basketJSParams || [];</script>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket",
	"v3",
	array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_PICT_PROP_22" => "-",
		"ADDITIONAL_PICT_PROP_26" => "-",
		"ADDITIONAL_PICT_PROP_28" => "-",
		"AJAX_MODE_CUSTOM" => "Y",
		"AUTO_CALCULATION" => "Y",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"BASKET_WITH_ORDER_INTEGRATION" => "Y",
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "PROPS",
			3 => "DELETE",
			4 => "DELAY",
			5 => "TYPE",
			6 => "PRICE",
			7 => "QUANTITY",
			8 => "SUM",
		),
		"COLUMNS_LIST_EXT" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "TYPE",
			4 => "SUM",
		),
		"COLUMNS_LIST_MOBILE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "TYPE",
			4 => "SUM",
		),
		"COMPATIBLE_MODE" => "Y",
		"COMPONENT_TEMPLATE" => "v2",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CORRECT_RATIO" => "Y",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"DEFERRED_REFRESH" => "N",
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",
		"DISPLAY_MODE" => "extended",
		"EMPTY_BASKET_HINT_PATH" => SITE_DIR."catalog/",
		"GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_CONVERT_CURRENCY" => "N",
		"GIFTS_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_HIDE_NOT_AVAILABLE" => "N",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MESS_BTN_DETAIL" => "Подробнее",
		"GIFTS_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_PLACE" => "BOTTOM",
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_TEXT_LABEL_GIFT" => "Подарок",
		"HIDE_COUPON" => "Y",
		"LABEL_PROP" => array(
		),
		"OFFERS_PROPS" => array(
			0 => "COLOR_REF",
			1 => "SIZES",
		),
		"PATH_TO_BASKET" => "basket/",
		"PATH_TO_ORDER" => SITE_DIR."order/#zakaz",
		"PICTURE_HEIGHT" => "100",
		"PICTURE_WIDTH" => "100",
		"PRICE_DISPLAY_MODE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "Y",
		"PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_FAST_ORDER_BUTTON" => "Y",
		"SHOW_FILTER" => "Y",
		"SHOW_FULL_ORDER_BUTTON" => "Y",
		"SHOW_MEASURE" => "Y",
		"SHOW_RESTORE" => "N",
		"TEMPLATE_THEME" => "blue",
		"TOTAL_BLOCK_DISPLAY" => array(
			0 => "top",
		),
		"USE_DYNAMIC_SCROLL" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_GIFTS" => "N",
		"USE_PREPAYMENT" => "N",
		"USE_PRICE_ANIMATION" => "Y",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"ADDITIONAL_PICT_PROP_3" => "-",
		"ADDITIONAL_PICT_PROP_128" => "-",
		"ADDITIONAL_PICT_PROP_130" => "-",
		"ADDITIONAL_PICT_PROP_137" => "-"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>

<!-- <div class="row">
	<div class="col-md-9">
		<form action="" class="order-form">
			<div class="form_title">
				Введите данные
			</div>
			<div class="form_fields">
				<input type="text" name="orderInn" value="" placeholder="ИНН">
				<input type="text" name="orderName" value="" placeholder="Имя">
				<input type="phone" name="orderPhone" value="" placeholder="Телефон">
				<input type="email" name="orderEmail" value="" placeholder="E-mail">
			</div>
		</form>
	</div>
</div> -->

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax",
	"v3",
	array(
		"ACTION_VARIABLE" => "soa-action",
		"ADDITIONAL_PICT_PROP_22" => "-",
		"ADDITIONAL_PICT_PROP_26" => "-",
		"ADDITIONAL_PICT_PROP_28" => "-",
		"ALLOW_APPEND_ORDER" => "Y",
		"ALLOW_AUTO_REGISTER" => "Y",
		"ALLOW_NEW_PROFILE" => "N",
		"ALLOW_USER_PROFILES" => "N",
		"BASKET_IMAGES_SCALING" => "standard",
		"BASKET_POSITION" => "after",
		"COMPATIBLE_MODE" => "Y",
		"COMPONENT_TEMPLATE" => "v2_custom",
		"COUNT_DELIVERY_TAX" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"DELIVERIES_PER_PAGE" => "20",
		"DELIVERY_FADE_EXTRA_SERVICES" => "N",
		"DELIVERY_NO_AJAX" => "N",
		"DELIVERY_NO_SESSION" => "Y",
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"DISABLE_BASKET_REDIRECT" => "N",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"HIDE_ORDER_DESCRIPTION" => "Y",
		"MESS_ADDITIONAL_PROPS" => "Дополнительные свойства",
		"MESS_AUTH_BLOCK_NAME" => "Авторизация",
		"MESS_AUTH_REFERENCE_1" => "Символом \"звездочка\" (*) отмечены обязательные для заполнения поля.",
		"MESS_AUTH_REFERENCE_2" => "После регистрации вы получите информационное письмо.",
		"MESS_AUTH_REFERENCE_3" => "Личные сведения, полученные в распоряжение интернет-магазина при регистрации или каким-либо иным образом, не будут без разрешения пользователей передаваться третьим организациям и лицам за исключением ситуаций, когда этого требует закон или судебное решение.",
		"MESS_BACK" => "Назад",
		"MESS_BASKET_BLOCK_NAME" => "Товары в заказе",
		"MESS_BUYER_BLOCK_NAME" => "Введите данные",
		"MESS_COUPON" => "Купон",
		"MESS_DELIVERY_BLOCK_NAME" => "Доставка",
		"MESS_DELIVERY_CALC_ERROR_TEXT" => "Вы можете продолжить оформление заказа, а чуть позже менеджер магазина свяжется с вами и уточнит информацию по доставке.",
		"MESS_DELIVERY_CALC_ERROR_TITLE" => "Не удалось рассчитать стоимость доставки.",
		"MESS_ECONOMY" => "Экономия",
		"MESS_EDIT" => "Изменить",
		"MESS_FAIL_PRELOAD_TEXT" => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически.<br />Обратите внимание на развернутый блок с информацией о заказе. Здесь вы можете внести необходимые изменения илиоставить как есть и нажать кнопку \"#ORDER_BUTTON#\".",
		"MESS_FURTHER" => "Далее",
		"MESS_INNER_PS_BALANCE" => "На вашем пользовательском счете:",
		"MESS_MORE_DETAILS" => "Подробнее",
		"MESS_NAV_BACK" => "Назад",
		"MESS_NAV_FORWARD" => "Вперед",
		"MESS_NEAREST_PICKUP_LIST" => "Ближайшие пункты:",
		"MESS_ORDER" => "Оформить",
		"MESS_ORDER_DESC" => "Комментарии к заказу:",
		"MESS_PAYMENT_BLOCK_NAME" => "Способ оплаты",
		"MESS_PAY_SYSTEM_PAYABLE_ERROR" => "Вы сможете оплатить заказ после того, как менеджер проверит наличие полного комплекта товаров на складе. Сразу после проверки вы получите письмо с инструкциями по оплате. Оплатить заказ можно будет в персональном разделе сайта.",
		"MESS_PERIOD" => "Срок доставки",
		"MESS_PERSON_TYPE" => "Тип плательщика",
		"MESS_PICKUP_LIST" => "",
		"MESS_PRICE" => "Стоимость",
		"MESS_PRICE_FREE" => "бесплатно",
		"MESS_REGION_BLOCK_NAME" => "Тип покупателя и регион доставки",
		"MESS_REGION_REFERENCE" => "Выберите свой город в списке. Если вы не нашли свой город, выберите \"другое местоположение\", а город впишите в поле \"Город\"",
		"MESS_REGISTRATION_REFERENCE" => "Если вы впервые на сайте, и хотите, чтобы мы вас помнили и все ваши заказы сохранялись, заполните регистрационную форму.",
		"MESS_REG_BLOCK_NAME" => "Регистрация",
		"MESS_SELECT_PICKUP" => "Выбрать",
		"MESS_SELECT_PROFILE" => "Выберите профиль",
		"MESS_SUCCESS_PRELOAD_TEXT" => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически.<br />Если все заполнено верно, нажмите кнопку \"#ORDER_BUTTON#\".",
		"MESS_USE_COUPON" => "Применить купон",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"PATH_TO_AUTH" => "/auth/",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PATH_TO_PERSONAL" => "/personal/order/",
		"PAY_FROM_ACCOUNT" => "N",
		"PAY_SYSTEMS_PER_PAGE" => "20",
		"PICKUPS_PER_PAGE" => "5",
		"PICKUP_MAP_TYPE" => "yandex",
		"PRODUCT_COLUMNS_HIDDEN" => array(
		),
		"PRODUCT_COLUMNS_VISIBLE" => array(
		),
		"PROPS_FADE_LIST_1" => "",
		"PROPS_FADE_LIST_2" => array(
		),
		"PROP_1" => array(
			0 => "4",
			1 => "6",
		),
		"PROP_2" => array(
			0 => "16",
			1 => "17",
		),
		"SEND_NEW_USER_NOTIFY" => "Y",
		"SERVICES_IMAGES_SCALING" => "standard",
		"SET_TITLE" => "N",
		"SHOW_BASKET_HEADERS" => "Y",
		"SHOW_COUPONS" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "Y",
		"SHOW_COUPONS_PAY_SYSTEM" => "Y",
		"SHOW_DELIVERY_INFO_NAME" => "N",
		"SHOW_DELIVERY_LIST_NAMES" => "N",
		"SHOW_DELIVERY_PARENT_NAMES" => "N",
		"SHOW_MAP_IN_PROPS" => "N",
		"SHOW_NEAREST_PICKUP" => "N",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "Y",
		"SHOW_ORDER_BUTTON" => "final_step",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "N",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "N",
		"SHOW_PICKUP_MAP" => "N",
		"SHOW_STORES_IMAGES" => "N",
		"SHOW_TOTAL_ORDER_BUTTON" => "Y",
		"SHOW_VAT_PRICE" => "N",
		"SKIP_USELESS_BLOCK" => "N",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"TEMPLATE_LOCATION" => "popup",
		"TEMPLATE_THEME" => "blue",
		"USER_CONSENT" => "Y",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "Y",
		"USE_CUSTOM_ERROR_MESSAGES" => "Y",
		"USE_CUSTOM_MAIN_MESSAGES" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PHONE_NORMALIZATION" => "N",
		"USE_PRELOAD" => "N",
		"USE_PREPAYMENT" => "N",
		"USE_YM_GOALS" => "N",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"ADDITIONAL_PICT_PROP_3" => "-",
		"ADDITIONAL_PICT_PROP_128" => "-",
		"ADDITIONAL_PICT_PROP_130" => "-",
		"ADDITIONAL_PICT_PROP_137" => "-"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"basket",
	Array(
		"AREA_FILE_RECURSIVE" => "Y",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"BIG_DATA_RCM_TYPE" => "personal",
		"COMPONENT_TEMPLATE" => "basket",
		"EDIT_TEMPLATE" => "standard.php",
		"PATH" => SITE_DIR."include/comp_basket_bigdata.php",
		"PRICE_CODE" => array(0=>"BASE",1=>"OPT",),
		"SALE_STIKER" => "SALE_TEXT",
		"STIKERS_PROP" => "HIT",
		"STORES" => array(0=>"1",1=>"2",2=>"",)
	)
);?>
<script>
	setInterval(function() {

		var startProductPriceVal = $(".bx-soa-cart-total-line-totals .bx-soa-cart-d").first().text().replace(' ', '').replace('₽', '');
		var startProductPrice = parseInt(startProductPriceVal);
		var deliveryTop = $(".bx-soa-pp-company.bx-selected").data('id') === 48;

		if (startProductPrice < 0) {
      $(".btn-order-save").addClass("order-disable");
			$(".min-price-error").show();
		} else {
			$(".btn-order-save").removeClass('order-disabled');
			$(".min-price-error").hide();
		};

		if (deliveryTop) {
			$(".btn-order-save").addClass('order-disable');
		} else {
			$(".btn-order-save").removeClass('order-disable');
		};
	}, 1000);

	$('.basket-item-actions-remove').click(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000);
	});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
