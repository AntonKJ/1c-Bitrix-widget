<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/_area/styles.css');

$bEdit = ($_REQUEST['gdproducthtml'] == $id) && ($_REQUEST['edit']=='true') && ($arParams["PERMISSION"] > "R");
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['gdproducthtmlform'] == 'Y' && $_REQUEST['gdproducthtml'] == $id)
{
	$arGadget["USERDATA"] = Array("content"=>$_POST["html_content"]);
	$arGadget["FORCE_REDIRECT"] = true;
}
$arData = $arGadget["USERDATA"];

$arIBlocks = [];

if(!CModule::IncludeModule("iblock"))
    return false;

$arSortBy = array(
    "ID" => GetMessage("GD_IBEL_SORT_BY_ID"),
    "NAME" => GetMessage("GD_IBEL_SORT_BY_NAME"),
    "DATE_ACTIVE_FROM" => GetMessage("GD_IBEL_SORT_BY_DATE_ACTIVE_FROM"),
    "DATE_CREATE" => GetMessage("GD_IBEL_SORT_BY_DATE_CREATE"),
    "TIMESTAMP_X" => GetMessage("GD_IBEL_SORT_BY_TIMESTAMP_X")
);

$arSortOrder= array(
    "ASC" => GetMessage("GD_IBEL_SORT_ORDER_ASC"),
    "DESC" => GetMessage("GD_IBEL_SORT_ORDER_DESC")
);

$arSelect = array(
    "ID" => GetMessage("GD_IBEL_SELECT_ID"),
    "NAME" => GetMessage("GD_IBEL_SELECT_NAME"),
    "DATE_ACTIVE_FROM" => GetMessage("GD_IBEL_SELECT_DATE_ACTIVE_FROM"),
    "DATE_CREATE" => GetMessage("GD_IBEL_SELECT_DATE_CREATE"),
    "TIMESTAMP_X" => GetMessage("GD_IBEL_SELECT_TIMESTAMP_X"),
    "PREVIEW_PICTURE" => GetMessage("GD_IBEL_SELECT_PREVIEW_PICTURE"),
    "PREVIEW_TEXT" => GetMessage("GD_IBEL_SELECT_PREVIEW_TEXT"),
    "DETAIL_PICTURE" => GetMessage("GD_IBEL_SELECT_DETAIL_PICTURE"),
    "DETAIL_TEXT" => GetMessage("GD_IBEL_SELECT_DETAIL_TEXT")
);

$dbIBlock = CIBlock::GetList(
    array("SORT"=>"ASC", "NAME"=>"ASC"),
    array(
        "CHECK_PERMISSIONS" => "Y",
        "MIN_PERMISSION" => (IsModuleInstalled("workflow")?"U":"W")
    )
);
while($arIBlock = $dbIBlock->GetNext())
    $arIBlock_Types[$arIBlock["IBLOCK_TYPE_ID"]] = $arIBlock;

$arTypes = array("" => GetMessage("GD_IBEL_EMPTY"));
$rsTypes = CIBlockType::GetList(Array("SORT"=>"ASC"));
while($arType = $rsTypes->Fetch())
{
    if (is_array($arIBlock_Types) && array_key_exists($arType["ID"], $arIBlock_Types))
    {
        $arType = CIBlockType::GetByIDLang($arType["ID"], LANGUAGE_ID);
        $arTypes[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["NAME"];
    }
}

$arIBlocks = array("" => GetMessage("GD_IBEL_EMPTY"));
if (
    is_array($arAllCurrentValues)
    && array_key_exists("IBLOCK_TYPE", $arAllCurrentValues)
    && array_key_exists("VALUE", $arAllCurrentValues["IBLOCK_TYPE"])
    && $arAllCurrentValues["IBLOCK_TYPE"]["VALUE"] <> ''
)
{
    $dbIBlock = CIBlock::GetList(
        array("SORT" => "ASC"),
        array(
            "CHECK_PERMISSIONS" => "Y",
            "MIN_PERMISSION" => (IsModuleInstalled("workflow")?"U":"W"),
            "TYPE" => $arAllCurrentValues["IBLOCK_TYPE"]["VALUE"]
        )
    );
    while($arIBlock = $dbIBlock->GetNext())
        $arIBlocks[$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
}

$arIBlockProperties = array();
if (
    is_array($arAllCurrentValues)
    && array_key_exists("IBLOCK_ID", $arAllCurrentValues)
    && array_key_exists("VALUE", $arAllCurrentValues["IBLOCK_ID"])
    && intval($arAllCurrentValues["IBLOCK_ID"]["VALUE"]) > 0
    && array_key_exists($arAllCurrentValues["IBLOCK_ID"]["VALUE"], $arIBlocks)
)
{

    $dbIBlockProperties = CIBlockProperty::GetList(
        array("SORT" => "ASC"),
        array(
            "IBLOCK_ID" => $arAllCurrentValues["IBLOCK_ID"]["VALUE"],
            "ACTIVE" => "Y"
        )
    );
    while($arIBlockProperty = $dbIBlockProperties->GetNext())
        $arIBlockProperties["PROPERTY_".$arIBlockProperty["CODE"]] = "[".$arIBlockProperty["CODE"]."] ".$arIBlockProperty["NAME"];
}

$arParameters = Array(
    "PARAMETERS"=> Array(),
    "USER_PARAMETERS"=> Array(
        "IBLOCK_TYPE" => Array(
            "NAME" => GetMessage("GD_IBEL_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypes,
            "MULTIPLE" => "N",
            "DEFAULT" => "",
            "REFRESH" => "Y"
        )
    )
);

//IBloks
$count =count($arIBlocks);

// Products
$products = \CIBlockElement::GetList(
    array(),
    array(),
    false,
    false,
    array('ID', 'NAME', 'IBLOCK_ID')
);

$count = $products->SelectedRowsCount();

$content = $arData["content"] = $count ;

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

		echo '<a class="gdproductareachlink" >У вас <strong>'.$content.' </strong> объект'.$postfix.'.</a>';
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