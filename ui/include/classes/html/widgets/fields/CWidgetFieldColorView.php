<?php declare(strict_types = 0);
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


use Zabbix\Widgets\Fields\CWidgetFieldColor;

class CWidgetFieldColorView extends CWidgetFieldView {

	public function __construct(CWidgetFieldColor $field) {
		$this->field = $field;
	}

	public function getFocusableElementId(): string {
		return 'lbl_'.$this->field->getName();
	}

	public function getView(): CColorPicker {
		return (new CColorPicker($this->field->getName()))
			->setColor($this->field->getValue())
			->setHasDefault($this->field->hasAllowInherited());
	}

	public function getJavaScript(): string {
		return '
			CWidgetForm.addField(
				new CWidgetFieldColor('.json_encode([
					'name' => $this->field->getName(),
					'form_name' => $this->form_name
				]).')
			);
		';
	}
}
