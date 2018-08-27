<?php
$this->append('pages');
echo $this->Html->css('pages.css', null, array('inline' => false));
$this->end();
?>
<?php 
    if(AuthComponent::user('username'))
        $user = AuthComponent::user('username'); 
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <span class="navbar-text">&nbsp;</span>
    <div class="navbar-collapse collapse" id="navbar8">
        <ul class="navbar-nav abs-center-x">
            <li class="nav-item">
                <a class="nav-link" href="/" style="font-weight: bold;">Feeds</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
      			<?php echo $this->Html->link('Add new feed', array('controller' => 'feeds', 'action' => 'add'), array('class' => 'nav-link')); ?>
            </li>
            <li class="nav-item">
      			<?php echo $this->Html->link('Logout '.$user, array('controller' => 'users', 'action' => 'logout'), array('class' => 'nav-link')); ?>
            </li>
        </ul>
    </div>
</nav>


<nav aria-label="...">
<ul class="pageno">
<?php
  echo $this->Paginator->first(__('First', true), array('tag' => 'li', 'escape' => false));
  echo $this->Paginator->prev('&laquo;Prev', array('tag' => 'li', 'escape' => false), '<a href="#">&laquo;Prev</a>', array('class' => 'prev disabled', 'tag' => 'li', 'escape' => false));
  echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'a'));
  echo $this->Paginator->next('Next&raquo;', array('tag' => 'li', 'escape' => false), '<a href="#">Next&raquo;</a>', array('class' => 'prev disabled', 'tag' => 'li', 'escape' => false));
  echo $this->Paginator->last(__('Last', true), array('tag' => 'li', 'escape' => false));
?>
</ul>
</nav>

<table class="table table-striped">
    <thead>
        <th scope="col">#</th>
        <th scope="col">Title</th>
        <th scope="col">Description</th>
        <th scope="col">Created</th>
    </thead>

    <!-- Loop through and print -->
    <?php foreach ($feeds as $feed): ?>
    <tr>
        <th scope="row"><?php echo $feed['Feed']['feed_id']; ?> </td>
        <td><?php echo $feed['Feed']['feed_title']; ?></td>
        <td><?php echo $feed['Feed']['feed_description']; ?></td>
        <td><?php echo $feed['Feed']['created_date']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($feed) ?>
</table>
