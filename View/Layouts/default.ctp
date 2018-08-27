<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

	//	echo $this->Html->css('cake.generic');

        $this->Html->css('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css', array('inline' => false));
        $this->Html->css('main.css');
        
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
        $this->start('forms');
        $this->end();
        $this->start('pages');
        $this->end();
	?>
</head>

<body>
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
<?php
echo $this->Html->script('https://code.jquery.com/jquery-3.3.1.slim.min.js');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js');
echo $this->Html->script('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
?>
</body>
</html>
