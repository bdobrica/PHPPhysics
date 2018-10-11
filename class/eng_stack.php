<?php
/*
 * ENG_Stack is used to parse and evaluate:
 * - arithmetic expresions
 * - logic expressions
 */

class ENG_Stack {
	const Operand	= 1;
	const Operator	= 2;
	const LBraket	= 3;
	const RBraket	= 4;
	const Vars	= 5;

	private static $Operands	= array ('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.');
	private static $Operators	= array ('+', '-', '*', '/', '^', 'exp', 'log', 'sin', 'cos', 'tan', '&', '|', '!');
	private static $Arity		= array (2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 2, 2, 1);
	private static $LBrakets	= array ('(');
	private static $RBrakets	= array (')');
	private static $Vars		= array ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');

	private $S;
	private $V;

	private static function opfun ($token, $args) {
		if (self::arity ($token) != count ($args)) throw new ENG_Exception (ENG_Exception::Invalid_Operator_Arity);
		switch ($token) {
			case '+':
				return $args[0] + $args[1];
			case '-':
				return $args[1] - $args[0];
			case '*':
				return $args[0] * $args[1];
			case '/':
				if ($args[0] == 0) throw new ENG_Exception (ENG_Exception::Invalid_Operand);
				return $args[1] / $args[0];

			case '^':
				if ($args[0] == 0 && $args[1] == 0) throw new ENG_Exception (ENG_Exception::Invalid_Operand);
				return pow($args[0], $args[1]);

			case 'exp':
				return exp ($args[0]);
			case 'log':
				if ($args[0] <= 0) throw new ENG_Exception (ENG_Exception::Invalid_Operand);
				return log ($args[0]);

			case 'sin':
				return sin ($args[0]);
			case 'cos':
				return cos ($args[0]);
			case 'tan':
				return tan ($args[0]);

			case '&':
				return $args[0] && $args[1] ? 1 : 0;
			case '|':
				return $args[0] || $args[1] ? 1 : 0;
			case '!':
				return !$args[0] ? 1 : 0;
			}
		}

	private static function arity ($token) {
		if (($k = array_search ($token, self::$Operators)) === FALSE) return 0;
		return self::$Arity[$k];
		}

	private static function ttype ($token) {
		$type = null;
		$token = (string) $token;

		if (in_array ($token, self::$Operators))	return $type = self::Operator;
		if (in_array ($token, self::$LBrakets))		return $type = self::LBraket;
		if (in_array ($token, self::$RBrakets))		return $type = self::RBraket;
		if (isset ($token[0]) && in_array ($token[0], self::$Operands))	return $type = self::Operand;
		if (isset ($token[0]) && in_array ($token[0], self::$Vars))		return $type = self::Vars;

		return $type;
		}

	private static function token (&$string, &$token) {
		$string = trim ($string);
		$token = isset ($string[0]) ? $string[0] : null;
		$string = trim(substr ($string, 1));

		while (is_null ($type = self::ttype ($token)) && $string) {
			$token .= $string[0];
			$string = trim(substr ($string, 1));
			}

		switch ($type) {
			case self::Operator:
			case self::LBraket:
			case self::RBraket:
				return $type;
				break;
			case self::Operand:
				if (!isset($string[0])) return $type;
				while (in_array ($string[0], self::$Operands)) {	
					$token .= $string[0];
					$string = trim(substr ($string, 1));
					}
				return $type;
				break;
			case self::Vars:
				while (in_array ($string[0], self::$Vars)) {
					$token .= $string[0];
					$string = trim(substr ($string, 1));
					}
				return $type;
				break;
			}
		return $type;
		}

	private static function cmp ($opA, $opB) {
		return array_search ($opA, self::$Operators) > array_search ($opB, self::$Operators) ? 1 : 0;
		}

	private static function top ($S) {
		if (empty ($S)) return null;
		return $S[count($S)-1];
		}

	private static function pop (&$S) {
		return array_pop ($S);
		}

	private static function push (&$S, $v) {
		$S[] = $v;
		}

	public function __construct ($data = null) {
		$this->S = array ();
		$this->V = array ();
		$T = array ();
		$token = null;

		while (!is_null ($t = self::token ($data, $token))) {
			echo "data: $data\n";
			echo "token $token of type $t\n";
			switch ($t) {
				case self::Vars:
					$this->V[$token] = 0;
					self::push ($this->S, $token);
					break;

				case self::Operand:
					self::push ($this->S, (float) $token);
					break;

				case self::LBraket:
					self::push ($T, $token);
					break;

				case self::Operator:
					if (empty ($T))
						self::push ($T, $token);
					else {
						while (self::cmp (self::top ($T), $token))
							self::push ($this->S, self::pop ($T));
						self::push ($T, $token);
						}
					break;

				case self::RBraket:
					while (!empty($T) && !in_array (self::top ($T), self::$LBrakets))
						self::push ($this->S, self::pop ($T));
					if (!empty($T))
						self::pop ($T);
					break;
				}
			}
		while (!empty ($T)) self::push ($this->S, self::pop ($T));
		}

	public function assign ($key, $value = 0) {
		$vars = array_keys ($this->V);
		if (is_array ($key)) {
			foreach ($key as $k => $v)
				if (in_array ($k, $vars))
					$this->V[$k] = $v;
			}
		else
			if (in_array ($key, $vars))
				$this->V[$key] = $value;
		print_r($this->V);
		}

	public function compute () {
		$T = array ();
		foreach ($this->S as $token) {
			switch (self::ttype ($token)) {
				case self::Vars:
					if (is_null ($this->V[$token])) throw new ENG_Exception (ENG_Exception::Unknown_Variable);
					self::push ($T, $this->V[$token]);
					break;
				case self::Operand:
					self::push ($T, $token);
					break;
				case self::Operator:
					$a = self::arity ($token);
					$args = array ();
					if (!empty ($T))
					while ($a-- > 0) $args[] = self::pop ($T);
					self::push ($T, self::opfun ($token, $args));
					break;
				}
			}
		return self::pop ($T);
		}
	}
?>
