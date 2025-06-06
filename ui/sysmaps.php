<?php
/*
** Copyright (C) 2001-2025 Zabbix SIA
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/


require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/include/maps.inc.php';
require_once dirname(__FILE__).'/include/forms.inc.php';

$page['title'] = _('Configuration of network maps');
$page['file'] = 'sysmaps.php';
$page['type'] = detect_page_type(PAGE_TYPE_HTML);

// VAR	TYPE	OPTIONAL	FLAGS	VALIDATION	EXCEPTION
$fields = [
	'maps' =>					[T_ZBX_INT, O_OPT, P_SYS|P_ONLY_ARRAY,	DB_ID,	null],
	'sysmapid' =>				[T_ZBX_INT, O_OPT, P_SYS,	DB_ID,
		'isset({form}) && ({form} === "update" || {form} === "clone")'
	],
	'name' =>					[T_ZBX_STR, O_OPT, null,	NOT_EMPTY, 'isset({add}) || isset({update})', _('Name')],
	'width' =>					[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 65535), 'isset({add}) || isset({update})', _('Width')],
	'height' =>					[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 65535), 'isset({add}) || isset({update})', _('Height')],
	'backgroundid' =>			[T_ZBX_INT, O_OPT, null,	DB_ID,			'isset({add}) || isset({update})'],
	'background_scale' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	'isset({add}) || isset({update})'],
	'iconmapid' =>				[T_ZBX_INT, O_OPT, null,	DB_ID,			'isset({add}) || isset({update})'],
	'expandproblem' =>			[T_ZBX_INT, O_OPT, null,
		IN([SYSMAP_PROBLEMS_NUMBER, SYSMAP_SINGLE_PROBLEM, SYSMAP_PROBLEMS_NUMBER_CRITICAL]),	null
	],
	'markelements' =>			[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	null],
	'show_unack' =>				[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 2),	null],
	'highlight' =>				[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	null],
	'label_format' =>			[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	null],
	'label_type_host' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL, MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_type_hostgroup' =>	[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL, MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_type_trigger' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL, MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_type_map' =>			[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL, MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_type_image' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL, MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_string_host' =>		[T_ZBX_STR, O_OPT, null,	null,			'isset({add}) || isset({update})'],
	'label_string_hostgroup' =>	[T_ZBX_STR, O_OPT, null,	null,			'isset({add}) || isset({update})'],
	'label_string_trigger' =>	[T_ZBX_STR, O_OPT, null,	null,			'isset({add}) || isset({update})'],
	'label_string_map' =>		[T_ZBX_STR, O_OPT, null,	null,			'isset({add}) || isset({update})'],
	'label_string_image' =>		[T_ZBX_STR, O_OPT, null,	null,			'isset({add}) || isset({update})'],
	'label_type' =>				[T_ZBX_INT, O_OPT, null,	BETWEEN(MAP_LABEL_TYPE_LABEL,MAP_LABEL_TYPE_CUSTOM), 'isset({add}) || isset({update})'],
	'label_location' =>			[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 3),	'isset({add}) || isset({update})'],
	'show_element_label' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	'isset({add}) || isset({update})'],
	'show_link_label' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	'isset({add}) || isset({update})'],
	'urls' =>					[T_ZBX_STR, O_OPT, P_ONLY_TD_ARRAY,	null,	null],
	'severity_min' =>			[T_ZBX_INT, O_OPT, null,	IN('0,1,2,3,4,5'), null],
	'show_suppressed' =>		[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	null],
	'userid' =>					[T_ZBX_INT, O_OPT, P_SYS,	DB_ID,			null],
	'private' =>				[T_ZBX_INT, O_OPT, null,	BETWEEN(0, 1),	null],
	'users' =>					[T_ZBX_INT, O_OPT, P_ONLY_TD_ARRAY,	null,	null],
	'userGroups' =>				[T_ZBX_INT, O_OPT, P_ONLY_TD_ARRAY,	null,	null],
	// actions
	'action' =>					[T_ZBX_STR, O_OPT, P_SYS|P_ACT, IN('"map.export","map.massdelete"'),		null],
	'add' =>					[T_ZBX_STR, O_OPT, P_SYS|P_ACT, null,		null],
	'update' =>					[T_ZBX_STR, O_OPT, P_SYS|P_ACT, null,		null],
	'delete' =>					[T_ZBX_STR, O_OPT, P_SYS|P_ACT, null,		null],
	'cancel' =>					[T_ZBX_STR, O_OPT, P_SYS,	null,			null],
	// form
	'form' =>					[T_ZBX_STR, O_OPT, P_SYS,	null,			null],
	'form_refresh' =>			[T_ZBX_INT, O_OPT, P_SYS,	null,			null],
	// filter
	'filter_set' =>				[T_ZBX_STR, O_OPT, P_SYS,	null,			null],
	'filter_rst' =>				[T_ZBX_STR, O_OPT, P_SYS,	null,			null],
	'filter_name' =>			[T_ZBX_STR, O_OPT, P_NO_TRIM,	null,		null],
	// sort and sortorder
	'sort' =>					[T_ZBX_STR, O_OPT, P_SYS, IN('"height","name","width"'),				null],
	'sortorder' =>				[T_ZBX_STR, O_OPT, P_SYS, IN('"'.ZBX_SORT_DOWN.'","'.ZBX_SORT_UP.'"'),	null]
];
check_fields($fields);

/*
 * Permissions
 */
if (hasRequest('sysmapid')) {
	$sysmap = API::Map()->get([
		'sysmapids' => getRequest('sysmapid'),
		'editable' => true,
		'output' => API_OUTPUT_EXTEND,
		'selectUrls' => API_OUTPUT_EXTEND,
		'selectUsers' => ['userid', 'permission'],
		'selectUserGroups' => ['usrgrpid', 'permission']
	]);
	if (empty($sysmap)) {
		access_deny();
	}
	else {
		$sysmap = reset($sysmap);
	}
}
else {
	$sysmap = [];
}

$allowed_edit = CWebUser::checkAccess(CRoleHelper::ACTIONS_EDIT_MAPS);

if (!$allowed_edit && array_filter([
		hasRequest('add') || hasRequest('update'),
		hasRequest('delete') && hasRequest('sysmapid'),
		hasRequest('action') && getRequest('action') == 'map.massdelete',
		hasRequest('form')
])) {
	access_deny(ACCESS_DENY_PAGE);
}

require_once dirname(__FILE__).'/include/page_header.php';

/*
 * Actions
 */
if (hasRequest('add') || hasRequest('update')) {
	$map = [
		'name' => getRequest('name'),
		'width' => getRequest('width'),
		'height' => getRequest('height'),
		'backgroundid' => getRequest('backgroundid'),
		'background_scale' => getRequest('background_scale'),
		'iconmapid' => getRequest('iconmapid'),
		'highlight' => getRequest('highlight', 0),
		'markelements' => getRequest('markelements', 0),
		'expandproblem' => getRequest('expandproblem', DB::getDefault('sysmaps', 'expandproblem')),
		'label_format' => getRequest('label_format', 0),
		'label_type_host' => getRequest('label_type_host', 2),
		'label_type_hostgroup' => getRequest('label_type_hostgroup', 2),
		'label_type_trigger' => getRequest('label_type_trigger', 2),
		'label_type_map' => getRequest('label_type_map', 2),
		'label_type_image' => getRequest('label_type_image', 2),
		'label_string_host' => getRequest('label_string_host', ''),
		'label_string_hostgroup' => getRequest('label_string_hostgroup', ''),
		'label_string_trigger' => getRequest('label_string_trigger', ''),
		'label_string_map' => getRequest('label_string_map', ''),
		'label_string_image' => getRequest('label_string_image', ''),
		'label_type' => getRequest('label_type'),
		'label_location' => getRequest('label_location'),
		'show_element_label' => getRequest('show_element_label'),
		'show_link_label' => getRequest('show_link_label'),
		'show_unack' => getRequest('show_unack', 0),
		'severity_min' => getRequest('severity_min', TRIGGER_SEVERITY_NOT_CLASSIFIED),
		'show_suppressed' => getRequest('show_suppressed', 0),
		'urls' => getRequest('urls', []),
		'userid' => getRequest('userid', ''),
		'private' => getRequest('private', PRIVATE_SHARING),
		'users' => getRequest('users', []),
		'userGroups' => getRequest('userGroups', [])
	];

	foreach ($map['urls'] as $unum => $url) {
		if (zbx_empty($url['name']) && zbx_empty($url['url'])) {
			unset($map['urls'][$unum]);
		}
	}

	DBstart();

	if (hasRequest('update')) {
		// TODO check permission by new value.
		$map['sysmapid'] = getRequest('sysmapid');

		// Only administrators can set map owner.
		if (CWebUser::getType() == USER_TYPE_ZABBIX_USER) {
			unset($map['userid']);
		}
		// Map update with inaccessible user.
		elseif (CWebUser::getType() == USER_TYPE_ZABBIX_ADMIN && $map['userid'] === '') {
			$user_exist = API::User()->get([
				'output' => ['userid'],
				'userids' => [$sysmap['userid']]
			]);

			if (!$user_exist) {
				unset($map['userid']);
			}
		}

		$result = API::Map()->update($map);

		$messageSuccess = _('Network map updated');
		$messageFailed = _('Cannot update network map');
	}
	else {
		if (getRequest('form') === 'clone') {
			$maps = API::Map()->get([
				'output' => [],
				'selectSelements' => ['selementid', 'elements', 'elementtype', 'iconid_off', 'iconid_on', 'label',
					'label_location', 'show_label', 'x', 'y', 'iconid_disabled', 'iconid_maintenance', 'elementsubtype',
					'areatype', 'width', 'height', 'viewtype', 'use_iconmap', 'urls', 'tags', 'evaltype', 'zindex'
				],
				'selectShapes' => ['type', 'x', 'y', 'width', 'height', 'text', 'font', 'font_size', 'font_color',
					'text_halign', 'text_valign', 'border_type', 'border_width', 'border_color', 'background_color',
					'zindex'
				],
				'selectLines' => ['x1', 'y1', 'x2', 'y2', 'line_type', 'line_width', 'line_color', 'zindex'],
				'selectLinks' => ['selementid1', 'selementid2', 'drawtype', 'color', 'label', 'show_label',
					'indicator_type', 'linktriggers', 'itemid', 'thresholds', 'highlights'
				],
				'sysmapids' => $sysmap['sysmapid']
			]);

			if ($maps) {
				$map['selements'] = $maps[0]['selements'];
				$map['shapes'] = $maps[0]['shapes'];
				$map['lines'] = $maps[0]['lines'];
				$map['links'] = $maps[0]['links'];
			}
		}

		$result = API::Map()->create($map);

		$messageSuccess = _('Network map added');
		$messageFailed = _('Cannot add network map');
	}

	if ($result) {
		unset($_REQUEST['form']);
	}

	$result = DBend($result);

	if ($result) {
		uncheckTableRows();
	}
	show_messages($result, $messageSuccess, $messageFailed);
}
elseif ((hasRequest('delete') && hasRequest('sysmapid'))
		|| (hasRequest('action') && getRequest('action') == 'map.massdelete')) {
	$sysmapIds = getRequest('maps', []);

	if (hasRequest('sysmapid')) {
		$sysmapIds[] = getRequest('sysmapid');
	}

	$sysmap_count = count($sysmapIds);

	DBstart();

	$maps = API::Map()->get([
		'sysmapids' => $sysmapIds,
		'output' => ['sysmapid', 'name'],
		'editable' => true
	]);
	$result = API::Map()->delete($sysmapIds);

	if ($result) {
		unset($_REQUEST['form']);
	}

	$result = DBend($result);

	if ($result) {
		uncheckTableRows();
	}
	else {
		uncheckTableRows(null, zbx_objectValues($maps, 'sysmapid'));
	}

	$messageSuccess = _n('Network map deleted', 'Network maps deleted', $sysmap_count);
	$messageFailed = _n('Cannot delete network map', 'Cannot delete network maps', $sysmap_count);

	show_messages($result, $messageSuccess, $messageFailed);
}

/*
 * Display
 */
if (hasRequest('form')) {
	$current_userid = CWebUser::$data['userid'];
	$userids[$current_userid] = $current_userid;
	$user_groupids = [];

	if (!hasRequest('sysmapid') || hasRequest('form_refresh')) {
		// Map owner
		$map_owner = getRequest('userid', $current_userid);
		$userids[$map_owner] = $map_owner;

		foreach (getRequest('users', []) as $user) {
			$userids[$user['userid']] = $user['userid'];
		}

		foreach (getRequest('userGroups', []) as $user_group) {
			$user_groupids[$user_group['usrgrpid']] = $user_group['usrgrpid'];
		}
	}
	else {
		// Map owner.
		$userids[$sysmap['userid']] = $sysmap['userid'];

		foreach ($sysmap['users'] as $user) {
			$userids[$user['userid']] = $user['userid'];
		}

		foreach ($sysmap['userGroups'] as $user_group) {
			$user_groupids[$user_group['usrgrpid']] = $user_group['usrgrpid'];
		}
	}

	$data['users'] = API::User()->get([
		'output' => ['userid', 'username', 'name', 'surname'],
		'userids' => $userids,
		'preservekeys' => true
	]);

	$data['user_groups'] = API::UserGroup()->get([
		'output' => ['usrgrpid', 'name'],
		'usrgrpids' => $user_groupids,
		'preservekeys' => true
	]);

	if (!hasRequest('sysmapid') || hasRequest('form_refresh')) {
		$data['sysmap'] = [
			'sysmapid' => getRequest('sysmapid'),
			'name' => getRequest('name', ''),
			'width' => getRequest('width', 800),
			'height' => getRequest('height', 600),
			'backgroundid' => getRequest('backgroundid', 0),
			'background_scale' => getRequest('background_scale', SYSMAP_BACKGROUND_SCALE_COVER),
			'iconmapid' => getRequest('iconmapid', 0),
			'label_format' => getRequest('label_format', 0),
			'label_type_host' => getRequest('label_type_host', 2),
			'label_type_hostgroup' => getRequest('label_type_hostgroup', 2),
			'label_type_trigger' => getRequest('label_type_trigger', 2),
			'label_type_map' => getRequest('label_type_map', 2),
			'label_type_image' => getRequest('label_type_image', 2),
			'label_string_host' => getRequest('label_string_host', ''),
			'label_string_hostgroup' => getRequest('label_string_hostgroup', ''),
			'label_string_trigger' => getRequest('label_string_trigger', ''),
			'label_string_map' => getRequest('label_string_map', ''),
			'label_string_image' => getRequest('label_string_image', ''),
			'label_type' => getRequest('label_type', 0),
			'label_location' => getRequest('label_location', 0),
			'show_element_label' => getRequest('show_element_label', 1),
			'show_link_label' => getRequest('show_link_label', 1),
			'highlight' => getRequest('highlight', 0),
			'markelements' => getRequest('markelements', 0),
			'expandproblem' => getRequest('expandproblem', DB::getDefault('sysmaps', 'expandproblem')),
			'show_unack' => getRequest('show_unack', 0),
			'severity_min' => getRequest('severity_min', TRIGGER_SEVERITY_NOT_CLASSIFIED),
			'show_suppressed' => getRequest('show_suppressed', 0),
			'urls' => getRequest('urls', []),
			'userid' => getRequest('userid', hasRequest('form_refresh') ? '' : $current_userid),
			'private' => getRequest('private', PRIVATE_SHARING),
			'users' => getRequest('users', []),
			'userGroups' => getRequest('userGroups', [])
		];
	}
	else {
		$data['sysmap'] = $sysmap;
	}

	$data['current_user_userid'] = $current_userid;
	$data['form_refresh'] = getRequest('form_refresh', 0);

	// advanced labels
	$data['labelTypes'] = sysmapElementLabel();
	$data['labelTypesLimited'] = $data['labelTypes'];
	unset($data['labelTypesLimited'][MAP_LABEL_TYPE_IP]);
	$data['labelTypesImage'] = $data['labelTypesLimited'];
	unset($data['labelTypesImage'][MAP_LABEL_TYPE_STATUS]);

	// images
	$data['images'] = API::Image()->get([
		'output' => ['imageid', 'name'],
		'filter' => ['imagetype' => IMAGE_TYPE_BACKGROUND]
	]);
	order_result($data['images'], 'name');

	// icon maps
	$data['iconMaps'] = API::IconMap()->get([
		'output' => ['iconmapid', 'name'],
		'preservekeys' => true
	]);
	order_result($data['iconMaps'], 'name');

	// render view
	echo (new CView('monitoring.sysmap.edit', $data))->getOutput();
}
else {
	CProfile::delete('web.maps.sysmapid');

	$sortField = getRequest('sort', CProfile::get('web.'.$page['file'].'.sort', 'name'));
	$sortOrder = getRequest('sortorder', CProfile::get('web.'.$page['file'].'.sortorder', ZBX_SORT_UP));

	CProfile::update('web.'.$page['file'].'.sort', $sortField, PROFILE_TYPE_STR);
	CProfile::update('web.'.$page['file'].'.sortorder', $sortOrder, PROFILE_TYPE_STR);

	if (hasRequest('filter_set')) {
		CProfile::update('web.sysmapconf.filter_name', getRequest('filter_name', ''), PROFILE_TYPE_STR);
	}
	elseif (hasRequest('filter_rst')) {
		DBStart();
		CProfile::delete('web.sysmapconf.filter_name');
		DBend();
	}

	$data = [
		'filter' => [
			'name' => CProfile::get('web.sysmapconf.filter_name', '')
		],
		'sort' => $sortField,
		'sortorder' => $sortOrder,
		'profileIdx' => 'web.sysmapconf.filter',
		'active_tab' => CProfile::get('web.sysmapconf.filter.active', 1),
		'allowed_edit' => $allowed_edit
	];

	// get maps
	$limit = CSettingsHelper::get(CSettingsHelper::SEARCH_LIMIT) + 1;
	$data['maps'] = API::Map()->get([
		'output' => ['sysmapid', 'name', 'width', 'height'],
		'sortfield' => $sortField,
		'limit' => $limit,
		'search' => [
			'name' => ($data['filter']['name'] === '') ? null : $data['filter']['name']
		],
		'preservekeys' => true
	]);

	order_result($data['maps'], $sortField, $sortOrder);

	// pager
	if (hasRequest('page')) {
		$data['page'] = getRequest('page');
	}
	elseif (isRequestMethod('get') && !hasRequest('cancel')) {
		$data['page'] = 1;
	}
	else {
		$data['page'] = CPagerHelper::loadPage($page['file']);
	}

	CPagerHelper::savePage($page['file'], $data['page']);

	$data['paging'] = CPagerHelper::paginate($data['page'], $data['maps'], $sortOrder, new CUrl('sysmaps.php'));

	if (CWebUser::getType() != USER_TYPE_SUPER_ADMIN) {
		$editable_maps = API::Map()->get([
			'output' => [],
			'sysmapids' => array_keys($data['maps']),
			'editable' => true,
			'preservekeys' => true
		]);

		foreach ($data['maps'] as &$map) {
			$map['editable'] = array_key_exists($map['sysmapid'], $editable_maps);
		}
		unset($map);
	}

	// render view
	echo (new CView('monitoring.sysmap.list', $data))->getOutput();
}

require_once dirname(__FILE__).'/include/page_footer.php';
