<?php 
$this->append('forms');
echo $this->Html->css('forms.css', null, array('inline' => false));
$this->end();
?>
<div class="text-center form-signin">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('User'); ?>
        <h1 class="h3 mb-3 font-weight-bold">Login</h1>
        <?php echo $this->Form->input('username', array(
            'class' => 'form-control',
        ));
        echo $this->Form->input('password', array(
            'class' => 'form-control'
        ));
    ?>
<?php echo $this->Form->button('Login', array('class' => 'submit btn btn-lg btn-primary btn-block')); ?> 
<?php echo $this->Html->link('Don\'t have an account? Register here.', array('controller' => 'users', 'action' => 'add')); ?>
</div>
