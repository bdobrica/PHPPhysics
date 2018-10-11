<?php
class ENG_Spline {
	private $P;	/** matrix of points, on on each row */
	private $S;	/** the spline matrix */
	private $s;	/** the spline matrix free term */
	private $k;	/** derivatives @each interpolation point */
	private $n;	/** number of points */

	public function __construct ($data = null) {
		if (is_array ($data) && !isset ($data['points'])) $data['points'] = $data;
		if (is_array ($data['points'])) $this->P = new ENG_Matrix ($data['points']);
		if (is_object ($data['points']) && ($data['points'] instanceof ENG_Matrix)) $this->P = $data['points'];

		$this->n = $this->P->r;
		
		if (!isset ($data['spline']))
			$this->init ();
		else {
			if (is_array ($data['spline'])) $this->k = new ENG_Vector ($data['spline']);
			if (is_object ($data['spline'])) $this->k = $data['spline'];
			}
		}

	private function init () {
		$this->S = new ENG_Matrix ('Z' . $this->n);
		$this->s = new ENG_Vector ('Z' . $this->n);

		$this->S->M[0][0] = 2/($this->P->M[1][0] - $this->P->M[0][0]);
		$this->S->M[0][1] = 1/($this->P->M[1][0] - $this->P->M[0][0]);
		$this->s->V[0] = 3 * ($this->P->M[1][1] - $this->P->M[0][1]) / pow ($this->P->M[1][0] - $this->P->M[0][0], 2);

		for ($i = 1; $i<$this->n-1; $i++) {
			$this->S->M[$i][$i-1]	= 1/($this->P->M[$i][0] - $this->P->M[$i-1][0]);
			$this->S->M[$i][$i]	= 2/($this->P->M[$i][0] - $this->P->M[$i-1][0]) + 2/($this->P->M[$i+1][0] - $this->P->M[$i][0]);
			$this->S->M[$i][$i+1]	= 1/($this->P->M[$i+1][0] - $this->P->M[$i][0]);
			$this->s->V[$i]		= 3*($this->P->M[$i][1] - $this->P->M[$i-1][1]) / pow ($this->P->M[$i][0] - $this->P->M[$i-1][0], 2)
						+ 3*($this->P->M[$i+1][1] - $this->P->M[$i][1]) / pow ($this->P->M[$i+1][0] - $this->P->M[$i][0], 2);
			}

		$this->S->M[$this->n-1][$this->n-1] = 2/($this->P->M[$this->n-1][0] - $this->P->M[$this->n-2][0]);
		$this->S->M[$this->n-1][$this->n-2] = 1/($this->P->M[$this->n-1][0] - $this->P->M[$this->n-2][0]);
		$this->s->V[$this->n-1] = 3 * ($this->P->M[$this->n-1][1] - $this->P->M[$this->n-2][1]) / pow ($this->P->M[$this->n-1][0] - $this->P->M[$this->n-2][0], 2);

		$this->s = $this->s->col ();

		$I = $this->S->inv ();
		$this->k = $I->mult ($this->s);
		$this->k = $this->k->vector (0, ENG_Matrix::COL);
		}

	public function f ($x) {
		$i = 0;
		if ($x <= $this->P->M[0][0]) return $this->P->M[0][1];
		if ($x >= $this->P->M[$this->n-1][0]) return $this->P->M[$this->n-1][1];
		while (($x > $this->P->M[$i+1][0]) && ($i < $this->n)) $i++;

		$t = ($x - $this->P->M[$i][0]) / ($this->P->M[$i+1][0] - $this->P->M[$i][0]);
		$a = $this->k->V[$i] * ($this->P->M[$i+1][0] - $this->P->M[$i][0]) - ($this->P->M[$i+1][1] - $this->P->M[$i][1]);
		$b = ($this->P->M[$i+1][1] - $this->P->M[$i][1]) - $this->k->V[$i+1] * ($this->P->M[$i+1][0] - $this->P->M[$i][0]);

		return (1 - $t) * $this->P->M[$i][1] + $t * $this->P->M[$i+1][1] + $t * (1 - $t) * ($a * (1 - $t) + $b * $t);
		}

	public function out () {
		echo "array (\n\t";
		echo implode (",\n\t",$this->k->V);
		echo "\n)\n";
		}
	}
?>
