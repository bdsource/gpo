<?php
defined('_JEXEC') or die();

class GpoControllerTwitteroauths extends GpoController
{
	function __construct()
	{
		parent::__construct();
		$this->oUser	= & JFactory::getUser();
		
        $groupsUserIsIn = JAccess::getGroupsByUser($this->oUser->id);
        $this->can_publish = (in_array(7, $groupsUserIsIn) || in_array(8, $groupsUserIsIn)) ? true : false;
	
        if ($this->can_publish === false) {
            $msg = 'Your account doesnt have high enough access';
            $link = JRoute::_('index.php?option=com_gpo', false);
            $this->setRedirect($link, $msg);
            $this->redirect();
        }
        $this->cookie_name_topic = 'twitteroauth_id';
        $this->registerTask('', 'viewall');
    }

    /*
	 * 
	 */
	function edit()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );

        $model =& $this->getModel( 'Twitteroauths' );

        $item = $model->getById($id);

		$view =& $this->getView( 'Twitteroauths', 'html' );
		$view->item=&$item;
		$view->connect();
	}
	
	
	
	function view()
	{
		$id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );
		
		$model =& $this->getModel( 'Twitteroauths' );
		
		$item = $model->getById( $id );
		if( $item === false )
		{
			$msg ="We are unable to find that twitteroauth.";
			$link = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths', false  );
			$this->setRedirect($link, $msg);
			$this->redirect();
		}
		
		$view =& $this->getView( 'Twitteroauths', 'html' );
		$view->twitteroauth=&$item;
		$view->view();
	}

	
	
	function viewall()
	{
        $model = & $this->getModel('Twitteroauths');
        $items = $model->getAll();

		$view =& $this->getView( 'Twitteroauths', 'html' );

        $twitterHome = "Home Page!";
        $view->twitterHome=&$twitterHome;
        $view->items=&$items;
		$view->all();
	}

    function oauth_connect()
    {
        $message = "Twitter Connect";

        $view =& $this->getView( 'Twitteroauths', 'html' );
        $view->message = &$message;
        $view->connect();
    }

    function create()
    {
        $message = "Twitter Connect";

        $view =& $this->getView( 'Twitteroauths', 'html' );
        $view->message=&$message;
        $view->connect();
    }

    function delete(){
        $id = Joomla\CMS\Factory::getApplication()->getInput()->get( 'id' );

        $model =& $this->getModel( 'Twitteroauths' );
        $model->delete($id);

        $href = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths',false );
        header( 'Location: ' . $href );


    }

    function save(){

        if(!empty($_POST['id'])){
            $data['id'] = $_POST['id'];
            $data['owner'] = $_POST['owner'];
            $data['client_name'] = $_POST['client_name'];
            $data['consumer_key'] = $_POST['consumer_key'];
            $data['consumer_secret'] = $_POST['consumer_secret'];
            $data['user_token'] = $_POST['user_token'];
            $data['user_secret'] = $_POST['user_secret'];

            $model = & $this->getModel('Twitteroauths');
            $model->update($data);

            $href = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths',false );
            header( 'Location: ' . $href );

        }else{

            $data['owner'] = $_POST['owner'];
            $data['client_name'] = $_POST['client_name'];
            $data['consumer_key'] = $_POST['consumer_key'];
            $data['consumer_secret'] = $_POST['consumer_secret'];
            $data['user_token'] = $_POST['user_token'];
            $data['user_secret'] = $_POST['user_secret'];

            $model = & $this->getModel('Twitteroauths');
            $model->save($data);

            $href = JRoute::_( 'index.php?option=com_gpo&controller=twitteroauths',false );
            header( 'Location: ' . $href );
        }

    }

}
?>
