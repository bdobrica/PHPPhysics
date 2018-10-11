<?php
class ENG_Controller extends ENG_Model {
	public static $T = 'controllers';
	public static $K = array (
		'label',	# label (unique identifier)
		'nid',		# network id
		'type',		# object type
		'input',	# serialized input object
		'output',	# serialized output object
		'stamp'		# creation time
		);
	public static $Q = array (
		'`id` int(11) NOT NULL AUTO_INCREMENT',
		'`label` varchar(16) NOT NULL DEFAULT \'\'',
		'`nid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`input` text NOT NULL',
		'`output` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'KEY `nid` (`nid`)',
		'UNIQUE KEY `label` (`label`,`nid`)'
		);

	}
?>
