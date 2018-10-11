<?php
class ENG_Port {
	/* Types of energy: */
	const Null		= 0;
	const Kintetic		= 1;
	const Potential		= 2;
	const Thermal		= 3;
	const Chemical		= 4;
	const Electrical	= 5;
	const Nuclear		= 6;
	const Light		= 7;

	protected $type;
	protected $value;

	public function __construct ($type) {
		$this->type = (int) $type;
		$this->value = 0;
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'value':
				return $this->value;
				break;
			}
		return $this->type;
		}

	public function set ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'value':
				$this->value  = (double) $opts;
				return TRUE;
				break;
			case 'type':
				$this->type = (int) $opts;
				return TRUE;
				break;
			}
		return FALSE;
		}
	}
?>
