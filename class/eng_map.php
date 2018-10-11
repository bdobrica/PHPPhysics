<?php
/*
 * stored functions acting on input / outputs
 * string contains a serialized array, entries coresponds to outputs
 * each output expression uses I0, I1, etc as inputs
 */
class ENG_Map extends ENG_Model {
	public static $T = 'maps';
	public static $K = array (
		'label',	# label (unique identifier)
		'description',	# description
		'expressions',	# object type
		'input',	# serialized input object
		'output',	# serialized output object
		'stamp'		# creation time
		);
	public static $Q = array (
		'`id` int(11) NOT NULL AUTO_INCREMENT',
		'`label` varchar(16) NOT NULL DEFAULT \'\'',
		'`description` text NOT NULL',
		'`expressions` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'UNIQUE KEY `label` (`label`)'
		);


	private $maps;
	private $context;

	public function __construct ($data = null) {
		parent::__construct ($data);

		$expressions = unserialize ($this->data['expressions']);

		$this->maps = array ();
		$this->context = array ();
		foreach ($expressions as $key => $expression) {
			$this->maps[$key] = new ENG_Stack ($expression);
			}
		}

	public function context ($params = null) {
		if (!empty($params))
		foreach ($params as $param => $value)
			$vars[$param] = $value;
		}

	public function call ($inputs, &$outputs) {
		$c = 0;
		$vars = $this->context;
		foreach ($inputs as $input) {
			$vars['I' . $c] = $input->get ('value');
			$c++;
			}

		foreach ($outputs as $key => $output) {
			$this->maps[$key]->assign ($vars);
			$outputs[$key]->set ('value', $this->maps[$key]->compute ());
			}
		}
	}
?>
