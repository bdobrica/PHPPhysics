<?php
class ENG_Sun extends ENG_Object {
	const Irradiance = 0;
/*
input = null
output = irradiance
*/
	public function __construct () {
		$this->input = array (
			new ENG_Port (ENG_Port::Null)				/** No input required */
			);
		$this->output = array (
			self::Irradiance => new ENG_Port (ENG_Port::Light)	/** Irradiance */
			);
		}

	protected function transfer () {
		$spa = new ENG_SPA ();
		$spa->calculate ();

		$E0 = 1328;							/** solar constant */
		$E = $E0 * cos ($spa->incidence);				/** solar irradiance */

		($this->output[self::Irradiance])->set ('value', $E);
		}
	}
?>
