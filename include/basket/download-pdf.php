<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина в Excel");
?>
<?$APPLICATION->IncludeComponent(
	"aspro:basket.file.max",
	"pdf",
	Array()
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>