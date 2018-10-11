<?php
class ENG_Corr {
	private $pearson;
	private $spearman;

	private /*ENG_Vector*/ $X;
	private /*ENG_Vector*/ $Y;
	private /*ENG_Vector*/ $RX;
	private /*ENG_Vector*/ $RY;

	public function __construct ($X = null, $Y = null) {
		$this->X = null;
		$this->Y = null;
		$this->RX = null;
		$this->RY = null;

		if ($X instanceof ENG_Vector) $this->X = $X;
		else
		if (is_array ($X)) $this->X = new ENG_Vector ($X);

		if ($Y instanceof ENG_Vector) $this->Y = $Y;
		else
		if (is_array ($Y)) $this->Y = new ENG_Vector ($Y);
	
		if (!is_null ($this->X)) $this->RX = $this->X->rnk ();
		if (!is_null ($this->Y)) $this->RY = $this->Y->rnk ();

		if ($this->X->l != $this->Y->l)
		}
	}
?>
