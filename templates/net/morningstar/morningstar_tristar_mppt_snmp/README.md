
# Morningstar TriStar MPPT by SNMP

## Overview

This template is designed for the effortless deployment of Morningstar TriStar MPPT monitoring by Zabbix via SNMP and doesn't require any external scripts.

## Requirements

Zabbix version: 8.0 and higher.

## Tested versions

This template has been tested on:
- Morningstar TriStar MPPT

## Configuration

> Zabbix should be configured according to the instructions in the [Templates out of the box](https://www.zabbix.com/documentation/8.0/manual/config/templates_out_of_the_box) section.

## Setup

Refer to the vendor documentation.

### Macros used

|Name|Description|Default|
|----|-----------|-------|
|{$BATTERY.TEMP.MIN.WARN}|<p>Battery low temperature warning value</p>|`0`|
|{$BATTERY.TEMP.MAX.WARN}|<p>Battery high temperature warning value</p>|`45`|
|{$BATTERY.TEMP.MIN.CRIT}|<p>Battery low temperature critical value</p>|`-20`|
|{$BATTERY.TEMP.MAX.CRIT}|<p>Battery high temperature critical value</p>|`60`|
|{$VOLTAGE.MIN.WARN}|||
|{$VOLTAGE.MAX.WARN}|||
|{$VOLTAGE.MIN.CRIT}|||
|{$VOLTAGE.MAX.CRIT}|||
|{$CHARGE.STATE.WARN}|<p>disconnect</p>|`2`|
|{$CHARGE.STATE.CRIT}|<p>fault</p>|`4`|
|{$LOAD.STATE.WARN:"lvdWarning"}|<p>lvdWarning</p>|`2`|
|{$LOAD.STATE.WARN:"disconnect"}|<p>disconnect</p>|`5`|
|{$LOAD.STATE.WARN:"override"}|<p>override</p>|`7`|
|{$LOAD.STATE.CRIT:"lvd"}|<p>lvd</p>|`3`|
|{$LOAD.STATE.CRIT:"fault"}|<p>fault</p>|`4`|

### Items

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Status: Uptime (network)|<p>The time (in hundredths of a second) since the network management portion of the system was last re-initialized.</p>|SNMP agent|status.net.uptime<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.01`</p></li></ul>|
|Status: Uptime (hardware)|<p>The amount of time since this host was last initialized. Note that this is different from sysUpTime in the SNMPv2-MIB [RFC1907] because sysUpTime is the uptime of the network management portion of the system.</p>|SNMP agent|status.hw.uptime<p>**Preprocessing**</p><ul><li><p>Check for not supported value: `any error`</p><p>⛔️Custom on fail: Set value to: `0`</p></li><li><p>Custom multiplier: `0.01`</p></li></ul>|
|Array: Voltage|<p>MIB: TRISTAR-MPPT</p><p>Description:Array Voltage</p><p>Scaling Factor:0.0054931640625</p><p>Units:V</p><p>Range:[-10, 180]</p><p>Modbus address:0x001b</p>|SNMP agent|array.voltage[arrayVoltage.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Array: Array Current|<p>MIB: TRISTAR-MPPT</p><p>Description:Array Current</p><p>Scaling Factor:0.00244140625</p><p>Units:A</p><p>Range:[-10, 80]</p><p>Modbus address:0x001d</p>|SNMP agent|array.current[arrayCurrent.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.00244140625`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Array: Sweep Vmp|<p>MIB: TRISTAR-MPPT</p><p>Description:Vmp (last sweep)</p><p>Scaling Factor:0.0054931640625</p><p>Units:V</p><p>Range:[-10, 180.0]</p><p>Modbus address:0x003d</p>|SNMP agent|array.sweep_vmp[arrayVmpLastSweep.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Array: Sweep Voc|<p>MIB: TRISTAR-MPPT</p><p>Description:Voc (last sweep)</p><p>Scaling Factor:0.0054931640625</p><p>Units:V</p><p>Range:[-10, 180.0]</p><p>Modbus address:0x003e</p>|SNMP agent|array.sweep_voc[arrayVocLastSweep.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Array: Sweep Pmax|<p>MIB: TRISTAR-MPPT</p><p>Description:Pmax (last sweep)</p><p>Scaling Factor:0.10986328125</p><p>Units:W</p><p>Range:[-10, 5000]</p><p>Modbus address:0x003c</p>|SNMP agent|array.sweep_pmax[arrayPmaxLastSweep.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.1098632813`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Battery: Charge State|<p>MIB: TRISTAR-MPPT</p><p>Description:Charge State</p><p>Modbus address:0x0032</p><p></p><p>0: Start</p><p>1: NightCheck</p><p>2: Disconnect</p><p>3: Night</p><p>4: Fault</p><p>5: Mppt</p><p>6: Absorption</p><p>7: Float</p><p>8: Equalize</p><p>9: Slave</p>|SNMP agent|charge.state[chargeState.0]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|Battery: Battery Voltage discovery|<p>MIB: TRISTAR-MPPT</p>|SNMP agent|battery.voltage.discovery[batteryVoltage.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li></ul>|
|Battery: Target Voltage|<p>MIB: TRISTAR-MPPT</p><p>Description:Target Voltage</p><p>Scaling Factor:0.0054931640625</p><p>Units:V</p><p>Range:[-10, 180.0]</p><p>Modbus address:0x0033</p>|SNMP agent|target.voltage[targetRegulationVoltage.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Battery: Charge Current|<p>MIB: TRISTAR-MPPT</p><p>Description:Battery Current</p><p>Scaling Factor:0.00244140625</p><p>Units:A</p><p>Range:[-10, 80]</p><p>Modbus address:0x001c</p>|SNMP agent|charge.current[batteryCurrent.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.00244140625`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Battery: Output Power|<p>MIB: TRISTAR-MPPT</p><p>Description:Output Power</p><p>Scaling Factor:0.10986328125</p><p>Units:W</p><p>Range:[-10, 5000]</p><p>Modbus address:0x003a</p>|SNMP agent|charge.output_power[ outputPower.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.1098632813`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|
|Temperature: Battery|<p>MIB: TRISTAR-MPPT</p><p>Description:Batt. Temp</p><p>Scaling Factor:1.0</p><p>Units:C</p><p>Range:[-40, 80]</p><p>Modbus address:0x0025</p>|SNMP agent|temp.battery[batteryTemperature.0]|
|Temperature: Heatsink|<p>MIB: TRISTAR-MPPT</p><p>Description:HS Temp</p><p>Scaling Factor:1.0</p><p>Units:C</p><p>Range:[-40, 80]</p><p>Modbus address:0x0023</p>|SNMP agent|temp.heatsink[heatsinkTemperature.0]|
|Counter: Charge Amp-hours|<p>MIB: TRISTAR-MPPT</p><p>Description:Ah Charge Resettable</p><p>Scaling Factor:0.1</p><p>Units:Ah</p><p>Range:[0.0, 5000]</p><p>Modbus addresses:H=0x0034 L=0x0035</p>|SNMP agent|counter.charge_amp_hours[ahChargeResetable.0]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.1`</p></li></ul>|
|Counter: Charge KW-hours|<p>MIB: TRISTAR-MPPT</p><p>Description:kWh Charge Resettable</p><p>Scaling Factor:0.1</p><p>Units:kWh</p><p>Range:[0.0, 65535.0]</p><p>Modbus address:0x0038</p>|SNMP agent|counter.charge_kw_hours[kwhChargeResetable.0]|
|Status: Faults|<p>MIB: TRISTAR-MPPT</p><p>Description:Faults</p><p>Modbus address:0x002c</p>|SNMP agent|status.faults[faults.0]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|
|Status: Alarms|<p>MIB: TRISTAR-MPPT</p><p>Description:Faults</p><p>Modbus address:0x002c</p>|SNMP agent|status.alarms[alarms.0]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Triggers

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Morningstar TriStar MPPT: Status: Device has been restarted|<p>Uptime is less than 10 minutes.</p>|`(last(/Morningstar TriStar MPPT by SNMP/status.hw.uptime)>0 and last(/Morningstar TriStar MPPT by SNMP/status.hw.uptime)<10m) or (last(/Morningstar TriStar MPPT by SNMP/status.hw.uptime)=0 and last(/Morningstar TriStar MPPT by SNMP/status.net.uptime)<10m)`|Info|**Manual close**: Yes|
|Morningstar TriStar MPPT: Status: Failed to fetch data|<p>Zabbix has not received data for items for the last 5 minutes.</p>|`nodata(/Morningstar TriStar MPPT by SNMP/status.net.uptime,5m)=1`|Warning|**Manual close**: Yes|
|Morningstar TriStar MPPT: Battery: Device charge in warning state||`last(/Morningstar TriStar MPPT by SNMP/charge.state[chargeState.0])={$CHARGE.STATE.WARN}`|Warning|**Depends on**:<br><ul><li>Morningstar TriStar MPPT: Battery: Device charge in critical state</li></ul>|
|Morningstar TriStar MPPT: Battery: Device charge in critical state||`last(/Morningstar TriStar MPPT by SNMP/charge.state[chargeState.0])={$CHARGE.STATE.CRIT}`|High||
|Morningstar TriStar MPPT: Temperature: Low battery temperature||`max(/Morningstar TriStar MPPT by SNMP/temp.battery[batteryTemperature.0],5m)<{$BATTERY.TEMP.MIN.WARN}`|Warning|**Depends on**:<br><ul><li>Morningstar TriStar MPPT: Temperature: Critically low battery temperature</li></ul>|
|Morningstar TriStar MPPT: Temperature: Critically low battery temperature||`max(/Morningstar TriStar MPPT by SNMP/temp.battery[batteryTemperature.0],5m)<{$BATTERY.TEMP.MIN.CRIT}`|High||
|Morningstar TriStar MPPT: Temperature: High battery temperature||`min(/Morningstar TriStar MPPT by SNMP/temp.battery[batteryTemperature.0],5m)>{$BATTERY.TEMP.MAX.WARN}`|Warning|**Depends on**:<br><ul><li>Morningstar TriStar MPPT: Temperature: Critically high battery temperature</li></ul>|
|Morningstar TriStar MPPT: Temperature: Critically high battery temperature||`min(/Morningstar TriStar MPPT by SNMP/temp.battery[batteryTemperature.0],5m)>{$BATTERY.TEMP.MAX.CRIT}`|High||
|Morningstar TriStar MPPT: Status: Device has "overcurrent" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","overcurrent")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "fetShort" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","fetShort")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "softwareFault" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","softwareFault")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "batteryHvd" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","batteryHvd")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "arrayHvd" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","arrayHvd")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "dipSwitchChange" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","dipSwitchChange")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "customSettingsEdit" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","customSettingsEdit")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "rtsShorted" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","rtsShorted")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "rtsDisconnected" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","rtsDisconnected")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "eepromRetryLimit" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","eepromRetryLimit")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "slaveControlTimeout" faults flag||`count(/Morningstar TriStar MPPT by SNMP/status.faults[faults.0],#3,"like","slaveControlTimeout")=2`|High||
|Morningstar TriStar MPPT: Status: Device has "rtsShorted" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","rtsShorted")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "rtsDisconnected" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","rtsDisconnected")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "heatsinkTempSensorOpen" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","heatsinkTempSensorOpen")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "heatsinkTempSensorShorted" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","heatsinkTempSensorShorted")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "highTemperatureCurrentLimit" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","highTemperatureCurrentLimit")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "currentLimit" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","currentLimit")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "currentOffset" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","currentOffset")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "batterySense" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","batterySense")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "batterySenseDisconnected" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","batterySenseDisconnected")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "uncalibrated" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","uncalibrated")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "rtsMiswire" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","rtsMiswire")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "highVoltageDisconnect" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","highVoltageDisconnect")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "systemMiswire" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","systemMiswire")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "mosfetSOpen" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","mosfetSOpen")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "p12VoltageReferenceOff" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","p12VoltageReferenceOff")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "highArrayVCurrentLimit" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","highArrayVCurrentLimit")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "maxAdcValueReached" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","maxAdcValueReached")=2`|Warning||
|Morningstar TriStar MPPT: Status: Device has "controllerWasReset" alarm flag||`count(/Morningstar TriStar MPPT by SNMP/status.alarms[alarms.0],#3,"like","controllerWasReset")=2`|Warning||

### LLD rule Battery voltage discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Battery voltage discovery|<p>Discovery for battery voltage triggers</p>|Dependent item|battery.voltage.discovery<p>**Preprocessing**</p><ul><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Item prototypes for Battery voltage discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Battery: Voltage{#SINGLETON}|<p>MIB: TRISTAR-MPPT</p><p>Description:Battery voltage</p><p>Scaling Factor:0.0054931640625</p><p>Units:V</p><p>Range:[-10, 180.0]</p><p>Modbus address:0x0018</p>|SNMP agent|battery.voltage[batteryVoltage.0{#SINGLETON}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.005493164063`</p></li><li><p>Regular expression: `^(\d+)(\.\d{1,2})? \1\2`</p></li></ul>|

### Trigger prototypes for Battery voltage discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|Morningstar TriStar MPPT: Battery: Low battery voltage||`max(/Morningstar TriStar MPPT by SNMP/battery.voltage[batteryVoltage.0{#SINGLETON}],5m)<{#VOLTAGE.MIN.WARN}`|Warning|**Depends on**:<br><ul><li>Morningstar TriStar MPPT: Battery: Critically low battery voltage</li></ul>|
|Morningstar TriStar MPPT: Battery: Critically low battery voltage||`max(/Morningstar TriStar MPPT by SNMP/battery.voltage[batteryVoltage.0{#SINGLETON}],5m)<{#VOLTAGE.MIN.CRIT}`|High||
|Morningstar TriStar MPPT: Battery: High battery voltage||`min(/Morningstar TriStar MPPT by SNMP/battery.voltage[batteryVoltage.0{#SINGLETON}],5m)>{#VOLTAGE.MAX.WARN}`|Warning|**Depends on**:<br><ul><li>Morningstar TriStar MPPT: Battery: Critically high battery voltage</li></ul>|
|Morningstar TriStar MPPT: Battery: Critically high battery voltage||`min(/Morningstar TriStar MPPT by SNMP/battery.voltage[batteryVoltage.0{#SINGLETON}],5m)>{#VOLTAGE.MAX.CRIT}`|High||

## Feedback

Please report any issues with the template at [`https://support.zabbix.com`](https://support.zabbix.com)

You can also provide feedback, discuss the template, or ask for help at [`ZABBIX forums`](https://www.zabbix.com/forum/zabbix-suggestions-and-feedback)

