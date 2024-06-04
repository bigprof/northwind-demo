<?php

// Data functions (insert, update, delete, form) for table suppliers

// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

function suppliers_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('suppliers');
	if(!$arrPerm['insert']) return false;

	$data = [
		'CompanyName' => Request::val('CompanyName', ''),
		'ContactName' => Request::val('ContactName', ''),
		'ContactTitle' => Request::val('ContactTitle', ''),
		'Address' => br2nl(Request::val('Address', '')),
		'City' => Request::val('City', ''),
		'Region' => Request::val('Region', ''),
		'PostalCode' => Request::val('PostalCode', ''),
		'Country' => Request::val('Country', ''),
		'Phone' => Request::val('Phone', ''),
		'Fax' => Request::val('Fax', ''),
		'HomePage' => Request::val('HomePage', ''),
	];


	// hook: suppliers_before_insert
	if(function_exists('suppliers_before_insert')) {
		$args = [];
		if(!suppliers_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('suppliers', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('suppliers', $recID, calculated_fields()['suppliers']);

	// hook: suppliers_after_insert
	if(function_exists('suppliers_after_insert')) {
		$res = sql("SELECT * FROM `suppliers` WHERE `SupplierID`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!suppliers_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	// record owner is current user
	$recordOwner = getLoggedMemberID();
	set_record_owner('suppliers', $recID, $recordOwner);

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) suppliers_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function suppliers_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function suppliers_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('suppliers', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: suppliers_before_delete
	if(function_exists('suppliers_before_delete')) {
		$args = [];
		if(!suppliers_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: products
	$res = sql("SELECT `SupplierID` FROM `suppliers` WHERE `SupplierID`='{$selected_id}'", $eo);
	$SupplierID = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `products` WHERE `SupplierID`='" . makeSafe($SupplierID[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'products', $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'products', $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="btn btn-danger" value="' . html_attr($Translation['yes']) . '" onClick="window.location = \'suppliers_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1&csrf_token=' . urlencode(csrf_token(false, true)) . '\';">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="btn btn-success" value="' . html_attr($Translation[ 'no']) . '" onClick="window.location = \'suppliers_view.php?SelectedID=' . urlencode($selected_id) . '\';">', $RetMsg);
		return $RetMsg;
	}

	sql("DELETE FROM `suppliers` WHERE `SupplierID`='{$selected_id}'", $eo);

	// hook: suppliers_after_delete
	if(function_exists('suppliers_after_delete')) {
		$args = [];
		suppliers_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='suppliers' AND `pkValue`='{$selected_id}'", $eo);
}

function suppliers_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('suppliers', $selected_id, 'edit')) return false;

	$data = [
		'CompanyName' => Request::val('CompanyName', ''),
		'ContactName' => Request::val('ContactName', ''),
		'ContactTitle' => Request::val('ContactTitle', ''),
		'Address' => br2nl(Request::val('Address', '')),
		'City' => Request::val('City', ''),
		'Region' => Request::val('Region', ''),
		'PostalCode' => Request::val('PostalCode', ''),
		'Country' => Request::val('Country', ''),
		'Phone' => Request::val('Phone', ''),
		'Fax' => Request::val('Fax', ''),
		'HomePage' => Request::val('HomePage', ''),
	];

	// get existing values
	$old_data = getRecord('suppliers', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: suppliers_before_update
	if(function_exists('suppliers_before_update')) {
		$args = ['old_data' => $old_data];
		if(!suppliers_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'suppliers', 
		backtick_keys_once($set), 
		['`SupplierID`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="suppliers_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('suppliers', $data['selectedID'], calculated_fields()['suppliers']);

	// hook: suppliers_after_update
	if(function_exists('suppliers_after_update')) {
		$res = sql("SELECT * FROM `suppliers` WHERE `SupplierID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['SupplierID'];
		$args = ['old_data' => $old_data];
		if(!suppliers_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update record update timestamp
	set_record_owner('suppliers', $selected_id);
}

function suppliers_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	$noSaveAsCopy = true;

	// mm: get table permissions
	$arrPerm = getTablePermissions('suppliers');
	if(!$arrPerm['insert'] && $selected_id == '')
		// no insert permission and no record selected
		// so show access denied error unless TVDV
		return $separateDV ? $Translation['tableAccessDenied'] : '';
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if(strlen($selected_id) && Request::val('dvprint_x') != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: Country
	$combo_Country = new Combo;
	$combo_Country->ListType = 0;
	$combo_Country->MultipleSeparator = ', ';
	$combo_Country->ListBoxHeight = 10;
	$combo_Country->RadiosPerLine = 1;
	if(is_file(__DIR__ . '/hooks/suppliers.Country.csv')) {
		$Country_data = addslashes(implode('', @file(__DIR__ . '/hooks/suppliers.Country.csv')));
		$combo_Country->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions($Country_data))));
		$combo_Country->ListData = $combo_Country->ListItem;
	} else {
		$combo_Country->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions("Afghanistan;;Albania;;Algeria;;American Samoa;;Andorra;;Angola;;Anguilla;;Antarctica;;Antigua, Barbuda;;Argentina;;Armenia;;Aruba;;Australia;;Austria;;Azerbaijan;;Bahamas;;Bahrain;;Bangladesh;;Barbados;;Belarus;;Belgium;;Belize;;Benin;;Bermuda;;Bhutan;;Bolivia;;Bosnia, Herzegovina;;Botswana;;Bouvet Is.;;Brazil;;Brunei Darussalam;;Bulgaria;;Burkina Faso;;Burundi;;Cambodia;;Cameroon;;Canada;;Canary Is.;;Cape Verde;;Cayman Is.;;Central African Rep.;;Chad;;Channel Islands;;Chile;;China;;Christmas Is.;;Cocos Is.;;Colombia;;Comoros;;Congo, D.R. Of;;Congo;;Cook Is.;;Costa Rica;;Croatia;;Cuba;;Cyprus;;Czech Republic;;Denmark;;Djibouti;;Dominica;;Dominican Republic;;Ecuador;;Egypt;;El Salvador;;Equatorial Guinea;;Eritrea;;Estonia;;Ethiopia;;Falkland Is.;;Faroe Is.;;Fiji;;Finland;;France;;French Guiana;;French Polynesia;;French Territories;;Gabon;;Gambia;;Georgia;;Germany;;Ghana;;Gibraltar;;Greece;;Greenland;;Grenada;;Guadeloupe;;Guam;;Guatemala;;Guernsey;;Guinea-bissau;;Guinea;;Guyana;;Haiti;;Heard, Mcdonald Is.;;Honduras;;Hong Kong;;Hungary;;Iceland;;India;;Indonesia;;Iran;;Iraq;;Ireland;;Israel;;Italy;;Ivory Coast;;Jamaica;;Japan;;Jersey;;Jordan;;Kazakhstan;;Kenya;;Kiribati;;Korea, D.P.R Of;;Korea, Rep. Of;;Kuwait;;Kyrgyzstan;;Lao Peoples D.R.;;Latvia;;Lebanon;;Lesotho;;Liberia;;Libyan Arab Jamahiriya;;Liechtenstein;;Lithuania;;Luxembourg;;Macao;;Macedonia, F.Y.R Of;;Madagascar;;Malawi;;Malaysia;;Maldives;;Mali;;Malta;;Mariana Islands;;Marshall Islands;;Martinique;;Mauritania;;Mauritius;;Mayotte;;Mexico;;Micronesia;;Moldova;;Monaco;;Mongolia;;Montserrat;;Morocco;;Mozambique;;Myanmar;;Namibia;;Nauru;;Nepal;;Netherlands Antilles;;Netherlands;;New Caledonia;;New Zealand;;Nicaragua;;Niger;;Nigeria;;Niue;;Norfolk Island;;Norway;;Oman;;Pakistan;;Palau;;Palestinian Terr.;;Panama;;Papua New Guinea;;Paraguay;;Peru;;Philippines;;Pitcairn;;Poland;;Portugal;;Puerto Rico;;Qatar;;Reunion;;Romania;;Russian Federation;;Rwanda;;Samoa;;San Marino;;Sao Tome, Principe;;Saudi Arabia;;Senegal;;Seychelles;;Sierra Leone;;Singapore;;Slovakia;;Slovenia;;Solomon Is.;;Somalia;;South Africa;;South Georgia;;South Sandwich Is.;;Spain;;Sri Lanka;;St. Helena;;St. Kitts, Nevis;;St. Lucia;;St. Pierre, Miquelon;;St. Vincent, Grenadines;;Sudan;;Suriname;;Svalbard, Jan Mayen;;Swaziland;;Sweden;;Switzerland;;Syrian Arab Republic;;Taiwan;;Tajikistan;;Tanzania;;Thailand;;Timor-leste;;Togo;;Tokelau;;Tonga;;Trinidad, Tobago;;Tunisia;;Turkey;;Turkmenistan;;Turks, Caicoss;;Tuvalu;;Uganda;;Ukraine;;United Arab Emirates;;United Kingdom;;United States;;Uruguay;;Uzbekistan;;Vanuatu;;Vatican City;;Venezuela;;Viet Nam;;Virgin Is. British;;Virgin Is. U.S.;;Wallis, Futuna;;Western Sahara;;Yemen;;Yugoslavia;;Zambia;;Zimbabwe"))));
		$combo_Country->ListData = $combo_Country->ListItem;
	}
	$combo_Country->SelectName = 'Country';

	if($selected_id) {
		if(!check_record_permission('suppliers', $selected_id, 'view'))
			return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = check_record_permission('suppliers', $selected_id, 'edit');

		// can delete?
		$AllowDelete = check_record_permission('suppliers', $selected_id, 'delete');

		$res = sql("SELECT * FROM `suppliers` WHERE `SupplierID`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'suppliers_view.php', false);
		}
		$combo_Country->SelectedData = $row['Country'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_Country->SelectedText = (isset($filterField[1]) && $filterField[1] == '9' && $filterOperator[1] == '<=>' ? $filterValue[1] : entitiesToUTF8(''));
	}
	$combo_Country->Render();

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/suppliers_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/suppliers_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($arrPerm['insert'] && !$selected_id) { // allow insert and no record selected?
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return suppliers_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return suppliers_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate)
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return suppliers_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

		if($AllowDelete)
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		// if not in embedded mode and user has insert only but no view/update/delete,
		// remove 'back' button
		if(
			$arrPerm['insert']
			&& !$arrPerm['update'] && !$arrPerm['delete'] && !$arrPerm['view']
			&& !Request::val('Embedded')
		)
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
		elseif($separateDV)
			$templateCode = str_replace(
				'<%%DESELECT_BUTTON%%>', 
				'<button
					type="submit" 
					class="btn btn-default" 
					id="deselect" 
					name="deselect_x" 
					value="1" 
					onclick="' . $backAction . '" 
					title="' . html_attr($Translation['Back']) . '">
						<i class="glyphicon glyphicon-chevron-left"></i> ' .
						$Translation['Back'] .
				'</button>',
				$templateCode
			);
		else
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#CompanyName').replaceWith('<div class=\"form-control-static\" id=\"CompanyName\">' + (jQuery('#CompanyName').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#ContactName').replaceWith('<div class=\"form-control-static\" id=\"ContactName\">' + (jQuery('#ContactName').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#ContactTitle').replaceWith('<div class=\"form-control-static\" id=\"ContactTitle\">' + (jQuery('#ContactTitle').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Address').replaceWith('<div class=\"form-control-static\" id=\"Address\">' + (jQuery('#Address').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#City').replaceWith('<div class=\"form-control-static\" id=\"City\">' + (jQuery('#City').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Region').replaceWith('<div class=\"form-control-static\" id=\"Region\">' + (jQuery('#Region').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#PostalCode').replaceWith('<div class=\"form-control-static\" id=\"PostalCode\">' + (jQuery('#PostalCode').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Country').replaceWith('<div class=\"form-control-static\" id=\"Country\">' + (jQuery('#Country').val() || '') + '</div>'); jQuery('#Country-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#Phone').replaceWith('<div class=\"form-control-static\" id=\"Phone\">' + (jQuery('#Phone').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#Fax').replaceWith('<div class=\"form-control-static\" id=\"Fax\">' + (jQuery('#Fax').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#HomePage').replaceWith('<div class=\"form-control-static\" id=\"HomePage\">' + (jQuery('#HomePage').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#HomePage, #HomePage-edit-link').hide();\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif(($AllowInsert && !$selected_id) || ($AllowUpdate && $selected_id)) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(Country)%%>', $combo_Country->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(Country)%%>', $combo_Country->SelectedData, $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = [];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if(($pt_perm['view'] && isDetailViewEnabled($ptfc[0])) || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(SupplierID)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(CompanyName)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ContactName)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ContactTitle)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Address)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(City)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Region)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(PostalCode)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Country)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Phone)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(Fax)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(HomePage)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(SupplierID)%%>', safe_html($urow['SupplierID']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(SupplierID)%%>', html_attr($row['SupplierID']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(SupplierID)%%>', urlencode($urow['SupplierID']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CompanyName)%%>', safe_html($urow['CompanyName']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CompanyName)%%>', html_attr($row['CompanyName']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CompanyName)%%>', urlencode($urow['CompanyName']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ContactName)%%>', safe_html($urow['ContactName']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ContactName)%%>', html_attr($row['ContactName']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ContactName)%%>', urlencode($urow['ContactName']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ContactTitle)%%>', safe_html($urow['ContactTitle']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ContactTitle)%%>', html_attr($row['ContactTitle']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ContactTitle)%%>', urlencode($urow['ContactTitle']), $templateCode);
		if($dvprint || (!$AllowUpdate && !$AllowInsert)) {
			$templateCode = str_replace('<%%VALUE(Address)%%>', safe_html($urow['Address']), $templateCode);
		} else {
			$templateCode = str_replace('<%%VALUE(Address)%%>', safe_html($urow['Address'], true), $templateCode);
		}
		$templateCode = str_replace('<%%URLVALUE(Address)%%>', urlencode($urow['Address']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(City)%%>', safe_html($urow['City']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(City)%%>', html_attr($row['City']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(City)%%>', urlencode($urow['City']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Region)%%>', safe_html($urow['Region']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Region)%%>', html_attr($row['Region']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Region)%%>', urlencode($urow['Region']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(PostalCode)%%>', safe_html($urow['PostalCode']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(PostalCode)%%>', html_attr($row['PostalCode']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(PostalCode)%%>', urlencode($urow['PostalCode']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Country)%%>', safe_html($urow['Country']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Country)%%>', html_attr($row['Country']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Country)%%>', urlencode($urow['Country']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Phone)%%>', safe_html($urow['Phone']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Phone)%%>', html_attr($row['Phone']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Phone)%%>', urlencode($urow['Phone']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(Fax)%%>', safe_html($urow['Fax']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(Fax)%%>', html_attr($row['Fax']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Fax)%%>', urlencode($urow['Fax']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(HomePage)%%>', safe_html($urow['HomePage']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(HomePage)%%>', html_attr($row['HomePage']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(HomePage)%%>', urlencode($urow['HomePage']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(SupplierID)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(SupplierID)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(CompanyName)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CompanyName)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(ContactName)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ContactName)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(ContactTitle)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ContactTitle)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Address)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Address)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(City)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(City)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Region)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Region)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(PostalCode)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(PostalCode)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Country)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Country)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Phone)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Phone)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(Fax)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(Fax)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(HomePage)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(HomePage)%%>', urlencode(''), $templateCode);
	}

	// process translations
	$templateCode = parseTemplate($templateCode);

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if(Request::val('dvprint_x') == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
			$templateCode.="\n\tif(document.getElementById('HomePageEdit')) { document.getElementById('HomePageEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('HomePageEditLink')) { document.getElementById('HomePageEditLink').style.display='none'; }";
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields
	$filterField = Request::val('FilterField');
	$filterOperator = Request::val('FilterOperator');
	$filterValue = Request::val('FilterValue');

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('suppliers');
	if($selected_id) {
		$jdata = get_joined_record('suppliers', $selected_id);
		if($jdata === false) $jdata = get_defaults('suppliers');
		$rdata = $row;
	}
	$templateCode .= loadView('suppliers-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: suppliers_dv
	if(function_exists('suppliers_dv')) {
		$args = [];
		suppliers_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}