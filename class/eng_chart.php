<?php
class ENG_Chart extends ENG_Model {
	public static $T = 'charts';
	public static $K = array (
		'label',	# label (unique identifier)
		'nid',		# network id
		'type',		# object type
		'data',		# serialized input object
		);
	public static $Q = array (
		'`id` int(11) NOT NULL AUTO_INCREMENT',
		'`label` varchar(16) NOT NULL DEFAULT \'\'',
		'`nid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`data` text NOT NULL',
		'KEY `nid` (`nid`)',
		'UNIQUE KEY `label` (`label`,`nid`)'
		);

	protected $data;

	public function __construct ($data = null) {
		}

	public function out () {
		}
	}
?>
