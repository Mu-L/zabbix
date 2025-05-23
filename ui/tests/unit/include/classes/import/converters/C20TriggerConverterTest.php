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


use PHPUnit\Framework\TestCase;

class C20TriggerConverterTest extends TestCase {

	public function dataProviderConvert() {
		return [
			['1#1', '1<>1'],

			['1|1', '1 or 1'],
			['1 |1', '1 or 1'],
			['1| 1', '1 or 1'],
			['1 | 1', '1 or 1'],
			['1  | 1', '1 or 1'],
			['1  |  1', '1 or 1'],

			['1&1', '1 and 1'],
			['1 &1', '1 and 1'],
			['1& 1', '1 and 1'],
			['1 & 1', '1 and 1'],
			['1  & 1',	'1 and 1'],
			['1  &  1', '1 and 1'],

			['{host:item.last()}      |      {host:item.str(#)}', '{host:item.last()} or {host:item.str(#)}'],
			['{host:item.last()} > {#MAX} - 5', '{host:item.last()} > {#MAX} - 5'],

			[
				'{host:item.last()}#{host:item.last()}&{host:item.last()}|{host:item.last()}',
				'{host:item.last()}<>{host:item.last()} and {host:item.last()} or {host:item.last()}'
			],
			[
				'{host:net.tcp.service[ntp, localhost, 123].last()}#{host:net.tcp.service["ntp", "локальныйхост", "123"].last()}&{host:net.tcp.service[ ntp , localhost , 123 ].last()}|{host:net.tcp.service[tcp].last()}',
				'{host:net.udp.service[ntp, localhost, 123].last()}<>{host:net.udp.service["ntp", "локальныйхост", "123"].last()} and {host:net.tcp.service[ ntp , localhost , 123 ].last()} or {host:net.tcp.service[tcp].last()}'
			],
			['{host:item.str(#)} # 1', '{host:item.str(#)} <> 1'],
			['{host:item.str(|)} | 1', '{host:item.str(|)} or 1'],
			['{host:item.str(&)} & 1', '{host:item.str(&)} and 1'],

			['{host:item[#].last()} # 1', '{host:item[#].last()} <> 1'],
			['{host:item[|].last()} | 1', '{host:item[|].last()} or 1'],
			['{host:item[&].last()} and 1', '{host:item[&].last()} and 1'],

			['{TRIGGER.VALUE}|{host:item[&].last()}', '{TRIGGER.VALUE} or {host:item[&].last()}'],
			[
				'({TRIGGER.VALUE}=0&{Template App Zabbix Server:zabbix[process,alerter,avg,busy].avg(10m)}>75)|({TRIGGER.VALUE}=1&{Template App Zabbix Server:zabbix[process,alerter,avg,busy].avg(10m)}>65)',
				'({TRIGGER.VALUE}=0 and {Template App Zabbix Server:zabbix[process,alerter,avg,busy].avg(10m)}>75) or ({TRIGGER.VALUE}=1 and {Template App Zabbix Server:zabbix[process,alerter,avg,busy].avg(10m)}>65)'
			],
			[
				'{host:log["/вар/лог/заббикс/заббикс_сервер.лог"].regexp("\<системная ошибка\>")} # 0',
				'{host:log["/вар/лог/заббикс/заббикс_сервер.лог"].regexp("\<системная ошибка\>")} <> 0'
			],

			// incorrect expressions are returned as is
			['{host:item.last()', '{host:item.last()'],

			// an already up-to-date expression
			['{host:item.last()} > 0', '{host:item.last()} > 0']
		];
	}

	/**
	 * @dataProvider dataProviderConvert
	 *
	 * @param $expression
	 * @param $expectedConvertedExpression
	 */
	public function testConvert($expression, $expectedConvertedExpression) {
		$converter = new C20TriggerConverter();
		$this->assertEquals($expectedConvertedExpression, $converter->convert($expression));
	}

}
