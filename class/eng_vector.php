<?php
class ENG_Vector {
	public $V;
	public $l;

	public function __construct ($V) {
		if (is_string ($V) && is_numeric ($n = str_replace ('Z', '', $V))) {
			$V = array ();
			for ($c = 0; $c<$n; $c++)
				$V[$c] = 0;
			}
		if (is_string ($V) && preg_match ('/^E([0-9]+),([0-9]+)$/', $V)) {
			list ($i, $n) = explode (',', str_replace ('E', '', $V));
			$V = array ();
			for ($c = 0; $c<$n; $c++)
				$V[$c] = $c == $i ? 1 : 0;
			}
		$this->V = $V;
		$this->l = count ($V);
		}

	public function sum ($W) {
		if (is_object ($W) && ($W instanceof ENG_Vector) && ($this->l == $W->l)) {
			$S = array ();
			for ($c = 0; $c<$this->l; $c++)
				$S[$c] = $this->V[$c] + $W->V[$c];
			return new ENG_Vector ($S);
			}
		return false;
		}

	public function direct ($W) {
		if (is_object ($W) && ($W instanceof ENG_Vector) && ($this->l == $W->l)) {
			$P = array ();
			for ($c = 0; $c<$this->l; $c++)
				$P[$c] = $this->V[$c] * $W->V[$c];
			return new ENG_Vector ($P);
			}
		return false;
		}

	public function scalar ($a) {
		$S = array ();
		for ($c = 0; $c<$this->l; $c++)
			$S[$c] = $this->V[$c] * $a;
		return new ENG_Vector ($S);
		}

	public function dot ($W, $M = null) {
		if (is_null ($M)) $M = new ENG_Matrix ('I' . $this->l);
		if (is_object ($W) && ($W instanceof ENG_Vector) && ($this->l == $W->l)) {
			$D = $this->row ();
			$D = $D->mult ($M);
			$D = $D->mult ($W->col ());
			return $D->M[0][0];
			}
		return false;
		}

	public function norm ($M = null) {
		if (is_null ($M)) $M = new ENG_Matrix ('I' . $this->l);
		return sqrt ($this->dot ($this, $M));
		}

	public function tens ($W) {
		if (is_object ($W) && ($W instanceof ENG_Vector)) {
			$T = array ();
			for ($c = 0; $c<$this->l; $c++)
				$T[$c] = $this->V[$c];
			for ($c = $this->l; $c<$this->l+$W->l; $c++)
				$T[$c] = $W->V[$c - $this->l];
			return new ENG_Vector ($T);
			}
		return false;
		}

	public function col () {
		$C = array ();
		for ($c = 0; $c<$this->l; $c++)
			$C[$c] = array ($this->V[$c]);
		return new ENG_Matrix ($C);
		}

	public function row () {
		return new ENG_Matrix (array ($this->V));
		}

	public function del ($i) {
		$N = array ();
		for ($c = 0; $c<$this->l; $c++) {
			if ($c == $i) continue;
			$N[$c < $i ? $c : $c - 1] = $this->V[$c];
			}
		$this->V = $N;
		$this->l = count ($N);
		}

	public function avg () {
		$out = 0;
		if ($this->l == 0) return null;
		for ($c = 0; $c<$this->l; $c++) $out += $this->V[$c];
		return $out / $this->l;
		}

	public function std ($sqrt = true) {
		$out = 0;
		$avg = $this->avg ();
		if (is_null ($avg)) return null;
		for ($c = 0; $c<$this->l; $c++) $out += pow ($this->V[$c] - $avg, 2);
		return $sqrt ? sqrt ($out / $this->l) : ($out / $this->l);
		}

	public function rnk () {
		$W = $this->V;
		$R = array ();
		if ($this->l == 0) return null;
		sort ($W, SORT_NUMERIC);
		$c = 0;
		while ($c < $this->l) {
			$rank = $c;
			$d = $c + 1;
			while ($d < $this->l && $W[$c] == $W[$d]) $d++;
			if ($rank > $c)
				for ($e = $c; $e < $d; $e++) $R[$e] = $rank / ($d - $c);
			$c = $d;
			}
		return new ENG_Vector ($R);
		}

	public function out () {
		for ($c = 0; $c<$this->l; $c++)
			echo $this->V[$c] . "\t";
		echo "\n";
		}
	}
?>
