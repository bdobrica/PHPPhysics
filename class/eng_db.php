<?php
class ENG_DB extends mysqli {
	public $prefix;

	public function __construct ($dbname, $dbuser, $dbpass, $prefix = '') {
		$this->prefix = trim ((string) $prefix);
		parent::__construct ('localhost', $dbuser, $dbpass, $dbname);
		if (mysqli_connect_errno())
			throw new ENG_Exception (__CLASS__ . ' :: Database Error', ENG_Exception::Database_Error);
		}
	
	public function e (&$string) {
		if (!is_float ($string))
			$string = $this->real_escape_string ($string);
		}

	public function q ($query) {
		$result = $this->query ($query);
		if ($result) return $result;
		return false;
		}

	public function f ($result) {
		if (!is_object($result)) return false;
		if ($array = $result->fetch_array(MYSQLI_ASSOC)) return $array;
		return array();
		}

	public function r ($query) {
		$result = $this->q ($query);
		if ($row = $this->f ($result)) return $row;
		return false;
		}

	public function c ($query) {
		$cols = array ();
		$result = $this->q ($query);
		while ($col = $result->fetch_array(MYSQLI_NUM)) $cols[] = $col[0];
		return $cols;
		}

	public function a ($query) {
		$rows = array ();
		$result = $this->q ($query);
		while ($row = $this->f ($result)) $rows[] = $row;
		return $rows;
		}

	public function v ($query) {
		list ($result,) = $this->f ($this->q ($query));
		return $result;
		}

	public function p ($query, $args = null) {
		if (is_null ($query)) return;

		$args = func_get_args();
		array_shift ($args);
		if (isset ($args[0]) && is_array($args[0])) $args = $args[0];
		$query = str_replace ("'%s'", '%s', $query);
		$query = str_replace ('"%s"', '%s', $query);
		$query = preg_replace ('|(?<!%)%f|' , '%F', $query);
		$query = preg_replace ('|(?<!%)%s|', "'%s'", $query);
		array_walk ($args, array ($this, 'e'));
		return @vsprintf ($query, $args );
		}
	}
?>
