<?php
$this->append('forms');
echo $this->Html->css('forms', null, array('inline' => false));
$this->end();
?>
<div class="text-center form-signin">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('Feed'); ?>
        <h1 class="h3 mb-3 font-weight-normal">Create new feed data</h1>
        <?php echo $this->Form->input('feed_title', array(
            'class' => 'form-control',
        ));
        echo '<br />';
        echo 'Feed description';
        echo $this->Form->textarea('feed_description', array('class ' => 'form-control'));
    ?>
<?php echo $this->Form->button('Add post', array('class' => 'submit btn btn-lg btn-primary btn-block')); ?>
<?php echo $this->Html->link('Cancel and go back to feeds.', array('controller' => 'feeds', 'action' => 'index')); ?>
</div>
