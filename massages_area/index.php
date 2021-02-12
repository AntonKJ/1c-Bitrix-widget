<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/_area/styles.css');

$bEdit = ($_REQUEST['gdmassageshtml'] == $id) && ($_REQUEST['edit']=='true') && ($arParams["PERMISSION"] > "R");
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['gdmassageshtmlform'] == 'Y' && $_REQUEST['gdmassageshtml'] == $id)
{
	$arGadget["USERDATA"] = Array("content"=>$_POST["html_content"]);
	$arGadget["FORCE_REDIRECT"] = true;
}
$arData = $arGadget["USERDATA"];

$arIBlocks = [];

if(!CModule::IncludeModule("iblock"))
    return false;

$arSelect= Array("ID", "DATE_CREATE", "NAME", "IBLOCK_SECTION_ID");
//$arSelect= Array();
$arFilter = Array("IBLOCK_ID"=>3, "ACTIVE"=>"Y");
$arResult = [];
$res = CIBlockElement::GetList(Array("ID" => "DESC"), $arFilter, false, Array ("nTopCount" => 3), $arSelect);
while($arItem = $res->GetNext(true, false)){
    //$arResult['NEWS'][] = $arItem;
    $news_list .= <<<NEWS
    <li class="list__item"><div class="list__header">
            <div class="list__header-data">{$arItem['DATE_CREATE']}</div>
            <div class="list__header-name">Новости</div>
        </div><a class="list__link" href="/content/news/{$arItem['IBLOCK_SECTION_ID']}/{$arItem['ID']}/">{$arItem['NAME']}</a>
    </li>
NEWS;

}
?>

<?php

//echo "<pre>"; var_dump($res->SelectedRowsCount()); echo "</pre>";

$content = $arData["content"] = $news_list;

if (empty($count%2)) {
    $postfix = 'а';
} else if ($count == 1) {
    $postfix = '';
} else {
    $postfix = 'ов';
}

?>

<?if(!$bEdit):?>

<?
	if($content)
	{
		$parser = new CTextParser();
		$parser->allow = array(
			"HTML"=>($arParams["MODE"] != "AI" ? "N" : "Y"), 
			"ANCHOR"=>"Y", 
			"BIU"=>"Y", 
			"IMG"=>"Y", 
			"QUOTE"=>"Y", 
			"CODE"=>"Y", 
			"FONT"=>"Y", 
			"LIST"=>"Y", 
			"SMILES"=>"N", 
			"NL2BR"=>"N", 
			"VIDEO"=>"N", 
			"TABLE"=>"Y", 
			"CUT_ANCHOR"=>"N", 
			"ALIGN"=>"Y"
		);
		$parser->parser_nofollow = "Y";

		echo '<ul class="list">'.$content.'</ul>';
	}
	else
	{
		if($arParams["PERMISSION"]>"R")
			echo GetMessage("GD_HTML_AREA_NO_CONTENT");
	}
?>
    <BR/>
    <BR/>
<?endif?>