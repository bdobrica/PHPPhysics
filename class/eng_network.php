<?php
class ENG_Network extends ENG_Model {
	public static $T = 'networks';
	protected static $K = array (
		'begin',	# begining of simulation interval
		'end',		# the end of simulation interval
		'resolution',	# time resolution for simulation
		'stamp'		# network creation time
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL auto_increment',
		'`begin` int(11) NOT NULL default 0',
		'`end` int(11) NOT NULL default 0',
		'`resolution` int(11) NOT NULL default 3600',
		'`stamp` int(11) NOT NULL default 0'
		);

	private $objects;
	private $morphisms;

	public function __construct ($data = null) {
		parent::__construct ($data);
		$this->objects = null;
		$this->morphisms = null;
		}

	private function load () {
		if ($this->ID) {
			$this->objects = new ENG_List ('ENG_Object', array ('nid=' . $this->ID));
			$this->morphisms = new ENG_List ('ENG_Morphism', array ('nid=' . $this->ID));
			}
		}
	
	private function graft () {
		$nodes = array ();
		$edges = array ();
		
		if (!is_null ($this->objects))
			if (!$this->objects->is ('empty'))
			foreach ($this->objects->get () as $object) {
				$nodes[$object->get ('label')] = array (
					'label' => $object->get ('label')
					);
				}
		$nodes = (object) $nodes;
		if (!is_null ($this->morphisms))
			if (!$this->morphisms->is ('empty'))
			foreach ($this->morphisms->get () as $morphism) {
				if (!isset ($edges[$morphism->A->get ('label')])) $edges[$morphism->A->get ('label')] = array ();
				$edges[$morphism->A->get ('label')][$morphism->B->get ('label')] = (object) array ();
				}
		$edges = (object) $edges;
		
		return json_encode ((object) array ('nodes' => $nodes, 'edges' => $edges));
		}

	private function call () {
		if (is_null ($this->objects) || is_null ($this->morphisms)) $this->load ();
		
		}

	public function get ($key = null, $opts = null) {
		if ($key == 'graft') {
			$this->load ();
			return $this->graft ();
			}

		return parent::get ($key, $opts);
		}
	}
?>
