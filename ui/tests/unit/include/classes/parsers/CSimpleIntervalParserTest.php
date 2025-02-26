﻿<?php
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

class CSimpleIntervalParserTest extends TestCase {

	/**
	 * An array of simple intervals and parsed results.
	 */
	public static function dataProvider() {
		return [
			// success
			[
				'5', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '5'
				]
			],
			[
				'10s', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '10s'
				]
			],
			[
				'30m', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '30m'
				]
			],
			[
				'604800', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '604800'
				]
			],
			[
				'5h', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '5h'
				]
			],
			[
				'3d', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '3d'
				]
			],
			[
				'2w', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '2w'
				]
			],
			[
				'3550w', 0, ['negative' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '3550w'
				]
			],
			[
				'{$M}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{$M}'
				]
			],
			[
				'{{$M}.regsub("^([0-9]+)", "{$M}: \1")}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{{$M}.regsub("^([0-9]+)", "{$M}: \1")}'
				]
			],
			[
				'{$M: "context"}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{$M: "context"}'
				]
			],
			[
				'{{$M: "context"}.regsub("^([0-9]+)", "{$M}: \1")}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{{$M: "context"}.regsub("^([0-9]+)", "{$M}: \1")}'
				]
			],
			[
				'{$M: ";"}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{$M: ";"}'
				]
			],
			[
				'{$M: "/"}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{$M: "/"}'
				]
			],
			[
				'{#M}', 0, ['lldmacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{#M}'
				]
			],
			[
				'{{#M}.regsub("^([0-9]+)", "{#M}: \1")}', 0, ['lldmacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '{{#M}.regsub("^([0-9]+)", "{#M}: \1")}'
				]
			],
			[
				'-2w', 0, ['negative' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '-2w'
				]
			],
			[
				'-3600', 0, ['negative' => true],
				[
					'rc' => CParser::PARSE_SUCCESS,
					'match' => '-3600'
				]
			],
			// partial success
			[
				'02', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '0'
				]
			],
			[
				'00', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '0'
				]
			],
			[
				'00h', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '0'
				]
			],
			[
				'random text.....10s....text', 16, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '10s'
				]
			],
			[
				'2ww', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '2w'
				]
			],
			[
				'9z', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '9'
				]
			],
			[
				'9/', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '9'
				]
			],
			[
				'10sm', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '10s'
				]
			],
			[
				'300;', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '300'
				]
			],
			[
				'1y', 0, [],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '1'
				]
			],
			[
				'{$M};', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '{$M}'
				]
			],
			[
				'{{$M}.regsub("^([0-9]+)", \1)};', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '{{$M}.regsub("^([0-9]+)", \1)}'
				]
			],
			[
				'{#M};', 0, ['lldmacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '{#M}'
				]
			],
			[
				'{{#M}.regsub("^([0-9]+)", "{#M}: \1")};', 0, ['lldmacros' => true],
				[
					'rc' => CParser::PARSE_SUCCESS_CONT,
					'match' => '{{#M}.regsub("^([0-9]+)", "{#M}: \1")}'
				]
			],
			// fail
			[
				'', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				's', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'qwerty', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				' 10s', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'-10s', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			// User macros are not enabled.
			[
				'{$M}', 0, ['lldmacros' => true],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'{$M: "context"}', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'{$M: ";"}', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'{$M: "/"}', 0, [],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			// LLD macros are not enabled.
			[
				'{#M}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			],
			[
				'{{#M}.regsub("^([0-9]+)", "{#M}: \1")}', 0, ['usermacros' => true],
				[
					'rc' => CParser::PARSE_FAIL,
					'match' => ''
				]
			]
		];
	}

	/**
	 * @dataProvider dataProvider
	 *
	 * @param string $source
	 * @param int    $pos
	 * @param array  $options
	 * @param array  $expected
	*/
	public function testParse($source, $pos, $options, $expected) {
		$parser = new CSimpleIntervalParser($options);

		$this->assertSame($expected, [
			'rc' => $parser->parse($source, $pos),
			'match' => $parser->getMatch()
		]);
		$this->assertSame(strlen($expected['match']), $parser->getLength());
	}
}
