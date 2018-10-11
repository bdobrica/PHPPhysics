<?php
class ENG_Object extends ENG_Model {
	private static $ModelTypes = array (
		'sun' => '',
		);

	public static $T = 'objects';
	public static $K = array (
		'label',	# label (unique identifier)
		'nid',		# network id
		'type',		# object type
		'input',	# serialized input object
		'output',	# serialized output object
		'payload',	# data attached to this object
		'stamp'		# creation time
		);
	public static $Q = array (
		'`id` int(11) NOT NULL AUTO_INCREMENT',
		'`label` varchar(16) NOT NULL DEFAULT \'\'',
		'`nid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`input` text NOT NULL',
		'`output` text NOT NULL',
		'`payload` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'KEY `nid` (`nid`)',
		'UNIQUE KEY `label` (`label`,`nid`)'
		);

	protected $type;

	protected $input;
	protected $output;

	protected $I;
	protected $O;

	public function __construct ($data = null) {
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
		}

	public function __toString () {
		return json_encode (array (
			'arbor' => 'node',
			'label' => $this->data['label'],
			'input' => $this->input,
			'output' => $this->output
			));
		}	
	}
?>
