<?php
class ENG_Str {
	public static function c ( $str ) { return preg_replace ('/[^a-z0-9]+/','-',substr($str)); }
	public static function i ( $str, $what = '' ) { switch ($what) { case 'hash': return preg_match('/^[a-fA-F0-9]+$/',$str); break; case 'web': return preg_match('/^(http[s]?://)?[A-z0-9.-]+\.[A-z]{2,4}$/',$str); break; case 'email': return preg_match ('/^[A-z0-9._]{2,}@[A-z0-9.-]{3,}\.[A-z]{2,4}$/',$str); break; case 'number': return preg_match ('/^[0-9][0-9.]*$/',$str); break; default: return $str?true:false; }; }
	public static function e ( $str ) { global $_log; if ($_log) echo $str; }
	public static function v ( $v, $f = false ) { echo "<!--\n"; if ($f) print_r($v); else echo $v; echo "\n-->"; }
	}
?>
