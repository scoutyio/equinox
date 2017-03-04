<?

class Mail extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	/*
	*/
	public 	function index() {
		
		$this->view->campaign = $this->model->fetchCampaigns();

		$this->view->title = 'Mass Email';
		
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('mail/index');
        $this->view->render('footer');
	}
	
	/*
	*/
	public function newcampaign() {

        $this->view->appendJsToHead('plugins/forms/chosen.jquery.min.js');
        $this->view->appendJsToHead('scripts/mail/newCampaign.php');

		if(isset($_POST['campaign'])){
			$this->model->createCampagin();
		}

		require "models/contacts_model.php";
		$contacts = new Contacts_model();

		$categories = $contacts->cats();
		$this->view->cat = json_encode($categories);
		
		$this->view->title = 'New Campaign';
		
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('mail/new');
        $this->view->render('footer');
	}

	/*
	*/
	public function view($id) {

		if(!isset($id)) { $eqApp->redirect(_SITEROOT_);}

		$d = $this->model->fetchCampaigns($id);
		
		if(isset($_POST['campaign'])){
			$this->model->createCampagin($id);
		}

        $this->view->appendJsToHead('scripts/mail/viewCampaign.php');

		require "models/contacts_model.php";
		$contacts = new Contacts_model();
		$categories = $contacts->cats();
		$this->view->cat = json_encode($categories);

		if($d[0]['status']==1){
			$render = 'mail/pending';
			$this->view->c = $d[0];
		} else {
			$render = 'mail/sent';	
		}

		
		$this->view->title = $d[0]['name'] . ' | Campaign';
		
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render($render);
        $this->view->render('footer');
	}


	public function send($id){

		if(!isset($id)) { $eqApp->redirect(_SITEROOT_);}	

		$this->model->send($id);
	}
}