<?php
class ENG_GP {
	const Sf		= 1;
	const Sn		= 1.5;
	const L			= 1.2;

	private $X;		# training inputs n x d
	private $y;		# training targets n x 1

	private $Sf;		# variance of the sample
	private $K;		# covariance matrix n x n

	private function mean () {
		$u = new ENG_Vector ('Z' . $this->X->r);
		for ($c = 0; $c<$this->X->c; $c++)
			$u = $u->add ($this->X->vector ($c, ENG_Matrix::COL));
		return $u->scalar (1/$this->X->c);
		}

	private function varr ($X) {
		$v = new ENG_Vector ('Z', $this->X->r);
		$u = $this->mean ($X);
		for ($c = 0; $c<$this->X->c; $c++) {
			$c = $this->X->vector ($c, ENG_Matrix::COL);
			$d = $c->add ($u->scalar (-1));
			$v = $v->add (pow($d->norm (), 2));
			$v = pow ($v->add ($X[0][$c] - $u), 2);
			}
		return $v/$l;
		}
	
	private static function covv ($a, $b, $i, $j) {
		return pow(self::Sf,2) * exp (pow($a - $b, 2)/(-2 * self::L)) + ($i == $j ? pow(self::Sn, 2) : 0);
		}

	private static function covm ($X) {
		$K = array ();
		$l = count ($X[0]);
		for ($c = 0; $c<$l; $c++)
			for ($d = 0; $d<$l; $d++)
				$K[$c][$d] = self::covv ($X[0][$c], $X[0][$d], $c, $d);
		return $K;
		}

	private static function covx ($X, $x) {
		$K = array (array ());
		$l = count ($X[0]);
		for ($c = 0; $c<$l; $c++)
			$K[0][$c] = self::covv ($x, $X[0][$c], -1, $c);
		return $K;
		}

	public function __construct ($X, $y) {
		$this->X = new ENG_Matrix ($X);
		$this->y = new ENG_Matrix ($y);

		$this->K = new ENG_Matrix (self::covm ($this->X->M));
		}

	public function y ($x) {
		$k = new ENG_Matrix (self::covx ($this->X->M, $x));
		$Ki = $this->K->inv ();
		$y = $k->mult ($Ki);
		$y = $y->mult ($this->y->trans ());
		return $y->M[0][0];
		}

	public function s ($x) {
		$k = new ENG_Matrix (covx ($this->X->M, $x));
		$v = $k->mult ($this->K->inv ());
		$v = $v->mult ($this->K->trans ());
		return self::covv ($x, $x, 0, 0) - $v->M[0][0];
		}
	}
?>
