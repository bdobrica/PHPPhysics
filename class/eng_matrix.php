<?php
class ENG_Matrix {
	const ROW	= 1;
	const COL	= 2;

	public $c;
	public $r;
	public $M;

	public function __construct ($M) {
		if (is_string ($M) && is_numeric ($n = str_replace ('I', '', $M))) {
			$M = array ();
			for ($c = 0; $c<$n; $c++)
				for ($d = 0; $d<$n; $d++)
					$M[$c][$d] = $c == $d ? 1 : 0;
			}
		if (is_string ($M) && is_numeric ($n = str_replace ('Z', '', $M))) {
			$M = array ();
			for ($c = 0; $c<$n; $c++)
				for ($d = 0; $d<$n; $d++)
					$M[$c][$d] = 0;
			}
		$this->M = $M;
		$this->r = count ($M);
		$this->c = count ($M[0]);
		}

	public function del ($what, $i) {
		$o = array ();
		switch ((int) $what) {
			case self::ROW:
				$N = array ();
				for ($j = 0; $j<$this->r; $j++) {
					if ($i == $j) {
						$o = array ($this->M[$j]);
						continue;
						}
					$N[$j < $i ? $j : $j-1] = $this->M[$j];
					}
				$this->M = $N;
				$this->r -= 1;
				break;
			case self::COL:
				$N = array ();
				for ($j = 0; $j<$this->r; $j++) {
					for ($k = 0; $k<$this->c; $k++) {
						if ($i == $k) {
							$o[$k] = array ($this->M[$j][$k]);
							continue;
							}
						$N[$j][$k < $i ? $k : $k-1] = $this->M[$j][$k];
						}
					}
				$this->M = $N;
				$this->c -= 1;
				break;
			}
		return $o;
		}

	public function trans () {
		$N = array ();
		for ($i = 0; $i<$this->r; $i++)
			for ($j = 0; $j<$this->c; $j++)
				$N[$j][$i] = $this->M[$i][$j];

		return new ENG_Matrix ($N);
		}

	public function mult ($A) {
		if (is_numeric ($A)) {
			$N = $this->M;
			for ($i = 0; $i<$this->r; $i++)
				for ($j = 0; $j<$this->c; $j++)
					$N[$i][$j] *= $A;
			return new ENG_Matrix ($N);
			}
		if (is_object ($A) && ($A instanceof ENG_Matrix)) {
			if ($this->c != $A->r) return false;
			$N = array ();
			for ($i = 0; $i<$this->r; $i++)
				for ($j = 0; $j<$this->c; $j++)
					for ($k = 0; $k<$A->c; $k++)
						$N[$i][$k] += $this->M[$i][$j] * $A->M[$j][$k];
			return new ENG_Matrix ($N);
			}
		return false;
		}

	public function sum ($A) {
		if (is_object ($A) && ($A instanceof ENG_Matrix)) {
			if (($this->r != $A->r) || ($this->c != $A->c)) return false;
			$N = array ();
			for ($i = 0; $i<$this->r; $i++)
				for ($j = 0; $j<$this->c; $j++)
					$N[$i][$j] = $this->M[$i][$j] + $A->M[$i][$j];
			return new ENG_Matrix ($N);
			}
		return false;
		}

	public function row ($i, $j, $a) {
		for ($k = 0; $k<$this->c; $k++)
			$this->M[$i][$k] = $this->M[$i][$k] + $a * $this->M[$j][$k];
		}

	public function col ($i, $j, $a) {
		for ($k = 0; $k<$this->r; $k++)
			$this->M[$k][$i] = $this->M[$k][$i] + $a * $this->M[$k][$j];
		}

	public function det () {
		if ($this->r != $this->c) return 0;
		if ($this->r == 1) return $this->M[0][0];
		if ($this->r == 2) return $this->M[0][0]*$this->M[1][1]-$this->M[0][1]*$this->M[1][0];

		$i = 0;
		while (($i < $this->r) && ($this->M[$i][0] == 0)) $i++;
		if ($i >= $this->r) return 0;

		$N = new ENG_Matrix ($this->M);

		for ($j = 0; $j<$this->r; $j++) {
			if ($j == $i) continue;
			$N->row ($j, $i, ((0 - $this->M[$j][0])/$this->M[$i][0]));
			}

		$N->del (self::ROW, $i);
		$N->del (self::COL, 0);
		
		return ($i%2 ? -1 : 1) * $this->M[$i][0] * $N->det ();
		}

	public function inv () {
		$a = $this->det ();
		if ($a == 0) return false;
		$I = array ();
		for ($i = 0; $i<$this->r; $i++)
			for ($j = 0; $j<$this->c; $j++) {
				$N = new ENG_Matrix ($this->M);
				$N->del (self::ROW, $i);
				$N->del (self::COL, $j);
				$I[$j][$i] = (($i+$j)%2 ? -1 : 1) * $N->det () / $a;
				unset ($N);
				}
		return new ENG_Matrix ($I);
		}

	public function vector ($i, $type = self::ROW) {
		if ($type == self::ROW) return new ENG_Vector ($this->M[$i]);
		if ($type == self::COL) {
			$C = array ();
			for ($c = 0; $c<$this->r; $c++)
				$C[$c] = $this->M[$c][$i];
			return new ENG_Vector ($C);
			}
		}

	public function replace ($i, $V, $type = self::ROW) {
		if ($type == self::ROW) {
			if ($this->c != $V->l) return false;
			$this->M[$i] = $V->V;
			}
		if ($type == self::COL) {
			if ($this->r != $V->l) return false;
			for ($c = 0; $c<$this->r; $c++)
				$this->M[$c][$i] = $V->V[$c];
			}
		return true;
		}

	public function out () {
		for ($i = 0; $i<$this->r; $i++) {
			for ($j = 0; $j<$this->c; $j++)
				echo $this->M[$i][$j]."\t";
			echo "\n";
			}
		}
	}
?>
