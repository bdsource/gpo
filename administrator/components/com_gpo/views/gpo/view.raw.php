<?

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 */
class GpoViewGpo extends JViewLegacy
{
   // Overwriting JView display method
   function display($tpl = null) 
   {
      /*
      // Assign data to the view
      $this->msg = $this->get('Msg');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) 
      {
         JError::raiseError(500, implode('<br />', $errors));
         return false;
      }
      */
      // Display the view
      // parent::display($tpl);
      
     //echo "RUNNING RAW FILE";
   }
}

?>