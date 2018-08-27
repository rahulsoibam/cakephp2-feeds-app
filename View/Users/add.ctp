<?php
$this->append('forms');
echo $this->Html->css('forms', null, array('inline' => false));
$this->end();
?>
<div class="text-center form-signin">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('User'); ?>
        <h1 class="h3 mb-3 font-weight-normal">Register</h1>
        <?php echo $this->Form->input('username', array(
            'class' => 'form-control',
        ));
        echo $this->Form->input('password', array(
            'class' => 'form-control'
        ));
    ?>
<?php echo $this->Form->button('Register', array('class' => 'submit btn btn-lg btn-primary btn-block')); ?>
<?php echo $this->Html->link('Already registered? Login here.', array('controller' => 'users', 'action' => 'login')); ?>
</div>
