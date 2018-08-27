<?php
class FeedsController extends AppController {
/**    public $helper = array('Html', 'Form');

    public function index() {
        $this->set('feeds', $this->Feed->find('all'));
    }
*/
    public $components = array('Paginator');

    public $paginate = array(
        'fields' => array('Feed.feed_id', 'Feed.feed_title', 'Feed.feed_description', 'Feed.created_date'),
        'limit' => 25,
        'maxLimit' => 25,
        'prevPage' => true,
        'order' => array(
            'Feed.feed_id' => 'desc'
        )
    );

    public function index() {
        $this->Paginator->settings = $this->paginate;
        // Similar to find('all'), but fetches paged results
        $data = $this->Paginator->paginate('Feed');
        $this->set('feeds', $data);
    }

    public function add() {
        if ($this->Auth->login()) {
            if($this->request->is('post')) {
                if ($this->Feed->save($this->request->data)) {
                    $this->Flash->success(__('Your post has been saved.'));
                    return $this->redirect(array('action' => 'index'));
                }
            }
        }
    }
}
