<?php
// This script and data application were generated by AppGini 23.16
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/shippers.php');
	include_once(__DIR__ . '/shippers_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('shippers');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'shippers';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`shippers`.`ShipperID`" => "ShipperID",
		"`shippers`.`CompanyName`" => "CompanyName",
		"`shippers`.`Phone`" => "Phone",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`shippers`.`ShipperID`',
		2 => 2,
		3 => 3,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`shippers`.`ShipperID`" => "ShipperID",
		"`shippers`.`CompanyName`" => "CompanyName",
		"`shippers`.`Phone`" => "Phone",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`shippers`.`ShipperID`" => "Shipper ID",
		"`shippers`.`CompanyName`" => "Company Name",
		"`shippers`.`Phone`" => "Phone",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`shippers`.`ShipperID`" => "ShipperID",
		"`shippers`.`CompanyName`" => "CompanyName",
		"`shippers`.`Phone`" => "Phone",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = [];

	$x->QueryFrom = "`shippers` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm['view'] == 0 ? 1 : 0);
	$x->AllowDelete = $perm['delete'];
	$x->AllowMassDelete = (getLoggedAdmin() !== false);
	$x->AllowInsert = $perm['insert'];
	$x->AllowUpdate = $perm['edit'];
	$x->SeparateDV = 0;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'shippers_view.php';
	$x->TableTitle = 'Shippers';
	$x->TableIcon = 'resources/table_icons/cart.png';
	$x->PrimaryKey = '`shippers`.`ShipperID`';
	$x->DefaultSortField = '2';
	$x->DefaultSortDirection = 'asc';

	$x->ColWidth = [400, 150, 100, ];
	$x->ColCaption = ['Company Name', 'Phone', 'Orders via shipper', ];
	$x->ColFieldName = ['CompanyName', 'Phone', '%orders.ShipVia%', ];
	$x->ColNumber  = [2, 3, -1, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/shippers_templateTV.html';
	$x->SelectedTemplate = 'templates/shippers_templateTVS.html';
	$x->TemplateDV = 'templates/shippers_templateDV.html';
	$x->TemplateDVP = 'templates/shippers_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: shippers_init
	$render = true;
	if(function_exists('shippers_init')) {
		$args = [];
		$render = shippers_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: shippers_header
	$headerCode = '';
	if(function_exists('shippers_header')) {
		$args = [];
		$headerCode = shippers_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: shippers_footer
	$footerCode = '';
	if(function_exists('shippers_footer')) {
		$args = [];
		$footerCode = shippers_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php'); 
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
