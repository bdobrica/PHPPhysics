<?php
class ENG_WindData extends ENG_Data {
	public static $T = 'winddata';
	public static $K = array (
		'stamp',			/** date */
		'height',			/** height */
		'site',				/** where? */
		'wind_speed',			/** wind speed */
		'wind_direction',		/** wind direction */
		'temperature',			/** temperature */
		'pressure',			/** pressure */
		'offset'			/** date diff */
		);
	public static $Q = array (
		);
	}
?>
