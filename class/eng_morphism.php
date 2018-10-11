<?php
/**
 * ENG_Morphisms are arrows in a network algebra model. Each morphism has a source (A) and a sink (B) object.
 */
class ENG_Morphism extends ENG_Model {
	public static $T = 'morphisms';
	public static $K = array (
		'label',	# label (unique identifier)
		'nid',		# network id
		'type',		# object type
		'input',	# serialized input object
		'output',	# serialized output object
		'aid',		# source node
		'bid',		# destination node
		'stamp'		# creation time
		);
	public static $Q = array (
		'`id` int(11) NOT NULL AUTO_INCREMENT',
		'`label` varchar(16) NOT NULL DEFAULT \'\'',
		'`nid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`input` text NOT NULL',
		'`output` text NOT NULL',
		'`aid` int(11) NOT NULL DEFAULT 0',
		'`bid` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'KEY `nid` (`nid`)',
		'UNIQUE KEY `label` (`label`,`nid`)'
		);

	protected $type;
	protected $map;
	protected $input;
	protected $output;

	public $A;
	public $B;
	
	public function __construct ($data = null) {
		if (is_array ($data) && isset ($data['A'])) {
			$this->A = new ENG_Object ($data['A']);
			$data['aid'] = $this->A->get('id');
			}
		if (is_array ($data) && isset ($data['B'])) {
			$this->B = new ENG_Object ($data['B']);
			$data['bid'] = $this->B->get('id');
			}

		parent::__construct ($data);

		if ($this->data['input'])
			$this->input = unserialize ($this->data['input']);
		else {
			$this->data['input'] = serialize (array());
			$this->input = array ();
			}
		if ($this->data['output'])
			$this->output = unserialize ($this->data['output']);
		else {
			$this->data['output'] = serialize (array());
			$this->output = array ();
			}

		$this->map = new ENG_Map ($this->data['type']);

		if (!is_object ($this->A) && $this->data['aid']) $this->A = new ENG_Object ((int) $this->data['aid']);
		if (!is_object ($this->B) && $this->data['bid']) $this->B = new ENG_Object ((int) $this->data['bid']);
		}

	public function map () {
		#$this->map->context ();
		$this->map->call ($this->input, $this->output);
		}

	public function __toString () {
		return json_encode (array (
			'arbor' => 'edge',
			'label' => $this->data['label'],
			'input' => $this->input,
			'output' => $this->output,
			'from' => $this->A->get('label'),
			'to' => $this->B->get('label')
			));
		}
	}
?>
