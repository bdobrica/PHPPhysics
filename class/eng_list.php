<?php
class ENG_List {
	protected $list;
	private $filter;
	/*
	TODO: use grouping
	*/
	private $group;
	private $class;

	public function __construct ($class, $filter = null) {
		$this->class = $class;
		$this->filter = $filter;
		$this->list = null;
		}

	private function load () {
		global $db;
		$class = $this->class;
		$sql = $db->p ('select id from `' . $db->prefix . $class::$T . '` where ' . (empty($this->filter) ? 1 : implode (' and ', $this->filter)));
		$ids = $db->c ($sql);
		if (!empty($ids))
			foreach ($ids as $id)
				if (!isset($this->list[$id])) {
					try {
						$this->list[$id] = new $this->class ((int) $id);
						}
					catch (ENG_Exception $eng_exception) {
						}
					}
		$this->sort ('stamp', 'desc');
		if (!empty($this->list))
			reset ($this->list);
		}

	/*
	INFO: compare functions
	*/
	private static function stamp_compare ($a, $b) {
		if ($a->get ('stamp') < $b->get ('stamp')) return -1;
		if ($a->get ('stamp') > $b->get ('stamp')) return  1;
		return 0;
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'size':
			case 'count':
				if (is_null($this->list)) $this->load ();
				return count($this->list);
				break;
			case 'class':
			case 'type':
				return $this->class;
				break;
			case 'first':
				if (is_null($this->list)) $this->load ();
				if (!empty($this->list))
					reset ($this->list);
				$first = current ($this->list);
				return $first;
				break;
			case 'last':
				if (is_null($this->list)) $this->load ();
				$last = end ($this->list);
				if (!empty($this->list))
					reset ($this->list);
				return $last;
				break;
			case 'json':
				$out = array ();
				if (is_null($this->list)) $this->load ();

				foreach ($this->list as $object) $out[] = array (
					'id' => $object->get (),
					'name' => $object->get ('name')
					);

				return json_encode ($out);
				break;
			}
		if (is_null($this->list)) $this->load ();
		return $this->list;
		}

	public function sort ($by = 'stamp', $opts = 'asc') {
		switch ((string) $by) {
			case 'time':
			case 'stamp':
				if (!empty($this->list))
					usort ($this->list, array ('ENG_List', 'stamp_compare'));
				break;
			}
		if ($opts == 'desc' && (!empty($this->list)))
			array_reverse ($this->list);
		if (!empty($this->list))
			reset ($this->list);
		}

	public function reset () {
		$this->list = array ();
		}

	public function is ($key = null) {
		switch ((string) $key) {
			case 'empty':
				if (is_null($this->list)) $this->load ();
				return empty($this->list) ? TRUE : FALSE;
				break;
			}
		}

	public function __destruct () {
		}
	}
?>
