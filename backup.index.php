<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("viewed_show", "Y");
$APPLICATION->SetTitle("Константа - интернет-магазин");
?><?$APPLICATION->IncludeComponent(
	"aspro:com.banners.max",
	"top_big_banner_4",
	Array(
		"BANNER_TYPE_THEME" => "TOP",
		"BANNER_TYPE_THEME_CHILD" => "",
		"BG_POSITION" => "center",
		"CACHE_GROUPS" => "N",
		"COMPONENT_TEMPLATE" => "top_big_banner_4",
		"CONVERT_CURRENCY" => "N",
		"FILTER_NAME" => "arRegionLink",
		"IBLOCK_ID" => "129",
		"IBLOCK_TYPE" => "aspro_max_adv",
		"NEWS_COUNT" => "10",
		"NEWS_COUNT2" => "20",
		"NEWS_COUNT3" => "20",
		"NO_MARGIN" => "Y",
		"PRICE_CODE" => array(),
		"SECTION_ID" => "",
		"SHOW_MEASURE" => "Y",
		"SIZE_IN_ROW" => "1",
		"STORES" => array(0=>"",1=>"",),
		"TYPE_BANNERS_IBLOCK_ID" => "103",
		"WIDE" => "N",
		"WIDE_BANNER" => "N"
	)
);?>&nbsp;<br>
 <br>
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.recommended.products",
	"bootstrap_v4",
	Array(
		"ACTION_VARIABLE" => "action_crp",
		"ADDITIONAL_PICT_PROP_128" => "CA_MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_130" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_137" => "",
		"ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO",
		"ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CODE" => "",
		"COMPONENT_TEMPLATE" => "bootstrap_v4",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"ELEMENT_SORT_FIELD" => "SORT",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER" => "ASC",
		"ELEMENT_SORT_ORDER2" => "DESC",
		"HIDE_NOT_AVAILABLE" => "N",
		"IBLOCK_ID" => "137",
		"IBLOCK_TYPE" => "catalog",
		"ID" => "",
		"LABEL_PROP_128" => "-",
		"LABEL_PROP_137" => "-",
		"LABEL_PROP_2" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"OFFERS_PROPERTY_LINK" => "RECOMMEND",
		"PAGE_ELEMENT_COUNT" => "30",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(0=>"BASE",),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_LINK" => "RECOMMEND",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_IMAGE" => "Y",
		"SHOW_NAME" => "Y",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_PRODUCTS_128" => "N",
		"SHOW_PRODUCTS_137" => "N",
		"SHOW_PRODUCTS_2" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_PRODUCT_QUANTITY" => "N"
	)
);?><br>
<br>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>