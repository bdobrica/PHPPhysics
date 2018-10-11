<?php
include (dirname(dirname(__FILE__)) . '/main.php');
#$net = new ENG_Network ((int) $_SESSION['network']);
#print_r($_POST);
if ($_POST['label']) {
	switch ($_POST['add']) {
		case 'object':
			$object = new ENG_Object (array (
				'nid'	=> 1,
				'label'	=> $_POST['label']
				));
			$object->save ();
			echo '@' . ((string) $object);
			exit (0);
			break;
		case 'morphism':
			$morphism = new ENG_Morphism (array (
				'nid'	=> 1,
				'label' => $_POST['label'],
				'A'	=> $_POST['A'],
				'B'	=> $_POST['B']
				));
			$morphism->save ();
			echo '@' . ((string) $morphism);
			exit (0);
			break;
		}
	}
?>
<form action="/ajax/element.php" method="post">
<?php
switch ($_POST['add']) {
	case 'object': ?>
	<label>Label:</label>
	<input type="text" name="label" value="<?php echo $_POST['label']; ?>" />
<?php		break;
	case 'morphism': ?>
	<label>Label:</label>
	<input type="text" name="label" value="<?php echo $_POST['label']; ?>" />
	<label>From object:</label>
	<input type="text" name="A" value="<?php echo $_POST['A']; ?>" />
	<label>To object:</label>
	<input type="text" name="B" value="<?php echo $_POST['B']; ?>" />
<?php		break;
	}
?>
	<input type="hidden" name="add" value="<?php echo $_POST['add']; ?>" />
	<button>Add</button>
</form>
