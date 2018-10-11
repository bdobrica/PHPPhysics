<?php
include (dirname (__FILE__) . '/main.php');
$_SESSION['network'] = 1;

$net = new ENG_Network (1);
$tp->assign ('init_network', $net->get ('graft'));

$tp->parse ('main.header.menu');
$tp->parse ('main.header');
$tp->assign ('item_name', 'Object');
$tp->assign ('item_rel', 'object');
$tp->parse ('main.app.elements.item');
$tp->assign ('item_name', 'Morphism');
$tp->assign ('item_rel', 'morphism');
$tp->parse ('main.app.elements.item');
$tp->parse ('main.app.elements');
$tp->parse ('main.app');
$tp->parse ('main.footer');
$tp->parse ('main');
$tp->out ('main');
?>
