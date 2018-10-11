<?php
abstract class ENG_Model {
	public static $T;
	protected static $K;
	protected static $F = array (
		'new' => array (
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q;

	protected $ID;
	protected $data;
	protected $group;

	public function __construct ($data = null) {
		global $db;

		if (is_null($data)) {
			}
		else
		if (is_numeric($data)) {
			$row = $db->r ($db->p ('select * from `' . $db->prefix . static::$T . '` where id=%d;', (int) $data));
			if (!empty($row)) {
				$this->ID = (int) $data;
				$this->data = $row;
				}
			else
				throw new ENG_Exception (__CLASS__ . ' :: Invalid ID', ENG_Exception::Invalid_ID);
			}
		else
		if (is_string($data) && in_array ('label', static::$K) && session_id() && isset($_SESSION['network'])) {
			$row = $db->r ($db->p ('select * from `' . $db->prefix . static::$T . '` where label=%s and nid=%d;', array (
				(string) $data,
				(int) $_SESSION['network']
				)));
			if (!empty($row)) {
				$this->ID = (int) $row->id;
				$this->data = $row;
				}
			else
				throw new ENG_Exception (__CLASS__ . ' :: Invalid ID', ENG_Exception::Invalid_ID);
			}
		else
		if (is_array($data)) {
			foreach (static::$K as $key)
				if (isset($data[$key]))
					$this->data[$key] = $data[$key];

			if (isset($data['id'])) $this->ID = (int) $data['id'];
			}
		}

	public static function slug ($key) {
		return str_replace(array(' ', '-'), '_', strtolower(trim($key)));
		}

	public function get ($key = null, $opts = null) {
		if (is_null($key)) return $this->ID;
		$slug = static::slug ($key);
		if ($slug == 'keys') return static::$K;
		if (isset($this->data[$slug]))
			return $this->data[$slug];
		#if (in_array ($slug, static::$K))
		#	return (string) $this->data[$slug];
		return $this->ID;
		}

	public function set ($key = null, $value = null) {
		global $db;

		if (is_array ($key)) {
			if (!empty ($key)) {
				$keys = $key;
				$update = array ();
				$values = array ();
			
				foreach ($keys as $key => $value) {
					$slug = static::slug ($key);
					if (!in_array($slug, static::$K))
						continue;
						//throw new ENG_Exception (__CLASS__ . ' :: Invalid Assignment', ENG_Exception::Invalid_Assignment);
					$update[] = $slug . '=%s';
					$values[] = $value;
					$this->data[$slug] = $value;
					}

				$values[] = $this->ID;
				$db->q ($db->p (
					'update `' . $db->prefix . static::$T . '` set ' . implode (',', $update) . ' where id=%d;',
					$values
					));
				}
			}
		else {
			$slug = static::slug ($key);
			if (!in_array($slug, static::$K)) throw new ENG_Exception (__CLASS__ . ' :: Invalid Assignment', ENG_Exception::Invalid_Assignment);
			$this->data[$slug] = $value;

			if ($this->ID) {
				if (is_object ($value) && ($value instanceof WP_CRM_Model))
					$value = $value->get();
				$db->q ($db->p ('update `' . $db->prefix . static::$T . '` set '.$slug.'=%s where id=%d;', $value, $this->ID));
				}
			}
		}

	public function json ($data = FALSE) {
		$out = array (
			'type' => 'object',
			'class' => get_class ($this),
			'id' => $this->ID
			);

		if ($data)
			$out['data'] = $this->data;

		return json_encode ($out);
		}

	public function save () {
		global $db;
		if ($this->ID) throw new ENG_Exception (__CLASS__ . ' :: Object Exists', ENG_Exception::Object_Exists);
		$formats = array ();
		$values = array ();
		foreach (static::$K as $key) {
			$formats[] = '%s';
			$values[] = $this->data[$key];
			}
		$sql = $db->p ('insert into `' . $db->prefix . static::$T . '` (' . implode(',', static::$K) . ') values (' . implode (',', $formats) . ');', $values);
		$db->q ($sql);
		if (!($this->ID = $db->insert_id)) throw new ENG_Exception (__CLASS__ . ' :: Saving Failure SQL: ' . "\n" . $sql . "\n", ENG_Exception::Saving_Failure);
		}

	public static function parse ($key = null, $from = null) {
		switch ($key) {
			case 'series':
				return trim(preg_replace('/[^A-Z]+/','',strtoupper($from)));
				break;
			case 'number':
				return intval(preg_replace('/[^0-9]+/','',$from));
				break;
			case 'spell number':
				$words = array (
					1 => array ('unu', 'doi', 'trei', 'patru', 'cinci', 'sase', 'sapte', 'opt', 'noua'),
					10 => array ('zece', 'douazeci', 'treizeci', 'patruzeci', 'cincizeci', 'saizeci', 'saptezeci', 'optzeci', 'nouazeci'),
					100 => array ('o suta', 'doua sute', 'trei sute', 'patru sute', 'cinci sute', 'sase sute', 'sapte sute', 'opt sute', 'noua sute'),
					1000 => array ('o mie', 'doua mii', 'trei mii', 'patru mii', 'cinci mii', 'sase mii', 'sapte mii', 'opt mii', 'noua mii')
					);

				$integer = intval($number);
				$decimal = intval(100 * ($number - $integer));

				$out = '';

				$value = $integer%100;

				if ($value) {
					if ($value < 10) $out = $words[1][$value - 1] . ' ' . $out;
					else
					if ($value == 10) $out = $words[10][0] . $out;
					else
					if ($value < 20) $out = $words[1][$value%10 - 1] . 'sprezece ' . $out;
					else {
						if ($value % 10)
							$out = $words[10][intval($value/10) - 1] . ' si ' . $words[1][$value%10 - 1] . ' ' . $out;
						else
							$out = $words[10][intval($value/10) - 1] . ' ' . $out;
						}
					}

				if ($integer) $out .= ($value > 0 || $value < 20) ? 'lei' : 'de lei';

				$integer = intval($integer/100);
				$value = $integer%10;

				if ($value) $out = $words[100][$value - 1] . ' ' . $out;
				$integer = intval ($integer/10);
				$value = $integer%10;

				if ($value) $out = $words[1000][$value - 1] . ' ' . $out;

				if ($decimal) {
					if ($decimal < 10) $out .= ' si ' . $words[1][$decimal - 1];
					else
					if ($decimal == 10) $out .= ' si ' . $words[10][0];
					else
					if ($decimal < 20) $out .= ' si ' . $words[1][$decimal%10 - 1] . 'sprezece';
					else {
						if ($decimal % 10)
							$out .= ' si ' . $words[10][intval($decimal/10) - 1] . ' si ' . $words[1][$decimal%10 - 1];
						else
							$out .= ' si ' . $words[10][intval($decimal/10) - 1];
						}
					$out .= ($decimal < 20) ? ' bani' : ' de bani';
					}

				return str_replace ('unusprezece', 'unsprezece', $out);
				break;
			default:
				return null;
			}
		}

	public function install ($uninstall = FALSE) {
		global $db;

		$sql = $uninstall ?
			'drop table `' . $db->prefix . static::$T . '`;' :
			'create table `' . $db->prefix . static::$T . '` (' . implode (',', static::$Q) . ') engine=MyISAM default charset=utf8;';

		echo $sql;
		$db->q ($sql);
		}

	public function delete () {
		global $db;
		if (!$this->ID) throw new ENG_Exception (__CLASS__ . ' :: Forgettable Object', ENG_Exception::Forgettable_Object);
		$db->q ($db->p ('delete from `' . $db->prefix . static::$T . '` where id=%d;', (int) $this->ID));
		}

	public function __clone () {
		}

	public function __destruct () {
		}
	};
?>
