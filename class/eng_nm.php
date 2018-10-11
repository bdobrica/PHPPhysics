<?php
class ENG_NM {
	const alpha		= 1;
	const gamma		= 2;
	const rho		= -0.5;
	const sigma		= 0.5;

	const VALUE		= 1;
	const VECTOR		= 2;
	


	private $X;		# the X matrix of X{i} vectors
	private $f;		# the function to be optimized
	private $V;		# V{i} = f(X{i});

	private $d;		# dimension

	public function __construct ($f, $X) {
		$this->f = $f;	# f should be callable, of argument ENG_Vector
		$this->X = $X;
		$this->d = $X->r;

		$this->V = array ();
		for ($c = 0; $c<$this->X->c; $c++)
			$this->V[$c] = call_user_func ($this->f, $this->X->vector ($c, ENG_Matrix::COL));
		}

	public function sort () {
		asort ($this->V);
		}

	public function center () {
		$G = new ENG_Vector ('Z' . $this->d);
		$c = 0;
		foreach ($this->V as $i => $V) {
			if ($c < $this->X->c -1) $G = $G->sum ($this->X->vector ($i, ENG_Matrix::COL));
			$c++;
			}
		$G = $G->scalar (1/($this->X->c-1));
		return $G;
		}

	public function iteration () {
		$this->sort ();

		$keys = array_keys ($this->V);

		$X0 = $this->center ();
		$Xn = $this->X->vector ($keys[$this->X->c -1], ENG_Matrix::COL);

		$XR = $X0->scalar (self::alpha + 1);
		$XR = $XR->sum ($Xn->scalar (-self::alpha));

		$VR = call_user_func ($this->f, $XR);
		echo "VR: ".log($VR)."\n";

		if ($VR >= $this->V[$keys[0]] && $this->V[$keys[$this->X->c -2]] > $VR) {
			$this->V[$keys[$this->X->c -1]] = $VR;
			$this->X->replace ($keys[$this->X->c -1], $XR, ENG_Matrix::COL);
			$this->sort ();
			return true;
			}
		if ($VR < $this->V[$keys[0]]) {
			$XE = $X0->scalar (self::gamma + 1);
			$XE = $XE->sum ($Xn->scalar (-self::gamma));

			$VE = call_user_func ($this->f, $XE);

			if ($VR < $VE) {
				$this->V[$keys[$this->X->c -1]] = $VR;
				$this->X->replace ($keys[$this->X->c -1], $XR, ENG_Matrix::COL);
				$this->sort ();
				return true;
				}
			else {
				$this->V[$keys[$this->X->c -1]] = $VE;
				$this->X->replace ($keys[$this->X->c -1], $XE, ENG_Matrix::COL);
				$this->sort ();
				return true;
				}
			}
		$XC = $X0->scalar (self::rho + 1);
		$XC = $XC->sum ($Xn->scalar (-self::rho));

		$VC = call_user_func ($this->f, $XC);

		if ($VC < $this->V[$keys[$this->X->c -2]]) {
			$this->V[$keys[$this->X->c -1]] = $VC;
			$this->X->replace ($keys[$this->X->c -1], $XC, ENG_Matrix::COL);
			$this->sort ();
			return true;
			}
		
		$Xm = $this->X->vector ($keys[0], ENG_Matrix::COL);
		for ($c = 1; $c<$this->X->c; $c++) {
			$Xi = $this->X->vector ($keys[$c], ENG_Matrix::COL);
			$Xr = $Xm->scalar (1 - self::sigma);
			$Xr = $Xr->sum ($Xi->scalar (self::sigma));
			$Vr = call_user_func ($this->f, $Xr);

			$this->X->replace ($keys[$c], $Xr, ENG_Matrix::COL);	
			}
		$this->sort ();
		return true;
		}

	public function best ($what = self::VALUE) {
		$keys = array_keys ($this->V);
		if ($what == self::VALUE) return $this->V[$keys[0]];
		if ($what == self::VECTOR) return $this->X->vector ($keys[0], ENG_Matrix::COL);
		}
	}
?>
