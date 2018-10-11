<?php
/*
 * ENG_Site should extend ENG_Object
 * describes a geographic location
 * sites can access weather data
 * - if weather data is not available, trying interpolation (GP&NM / SPLINE)
 */
class ENG_Site extends ENG_Object {
	private static $C = array (	/** describes `context` keys - contains weather parameters for the site */
		'TEMPERATURE'	=> 0,
		'PRESSURE'	=> 0,
		'HUMIDITY'	=> 0,
		'WINDSPD'	=> 0,
		'WINDDIR'	=> 0,
		'PRECIPITATION'	=> 0,
		'CLOUDS'	=> 0
		);

	public function __construct ($data = null) {
		parent::__construct ($data);
		}

	public function context ($stamp = null) {
		if (is_null ($stamp)) $stamp = time ();

		$context = array ();

		foreach ($C as $key => $value)
			$context[$key] = (float) $value;

		return $context;
		}

	public function __toString () {
		return json_encode (array (
			));
		}	
	}
?>
