<?

class Error extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {

		global $equrl;

		// echo $this->view->missing = $equrl[1];die;
		
		$this->view->title = '404';
		
		$this->view->render('header');
		$this->view->render('404/index');
	}
}