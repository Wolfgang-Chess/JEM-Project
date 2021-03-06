<?php
/**
 * @version 2.0.0
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Contact select
 */
class JFormFieldModal_Contact extends JFormField
{
	/**
	 * field type
	 */
	protected $type = 'Modal_Contact';


	/**
	 * Method to get the field input markup
	 */
	protected function getInput()
	{
		// Load modal behavior
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script
		$script = array();
		$script[] = '    function jSelectContact_'.$this->id.'(id, name, object) {';
		$script[] = '        document.id("'.$this->id.'_id").value = id;';
		$script[] = '        document.id("'.$this->id.'_name").value = name;';
		$script[] = '        SqueezeBox.close();';
		$script[] = '    }';

		// Add to document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display
		$html = array();
		$link = 'index.php?option=com_jem&amp;view=contactelement&amp;tmpl=component&amp;function=jSelectContact_'.$this->id;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__contact_details');
		$query->where(array('id='.(int)$this->value));
		$db->setQuery($query);

		$contact = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			\Joomla\CMS\Factory::getApplication()->enqueueMessage($error, 'warning');
		}

		if (empty($contact)) {
			$contact = JText::_('COM_JEM_SELECTCONTACT');
		}
		$contact = htmlspecialchars($contact, ENT_QUOTES, 'UTF-8');

		// The current contact input field
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$contact.'" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The contact select button
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '    <a class="modal" title="'.JText::_('COM_JEM_SELECT').'" href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x:800, y:450}}">'.
					JText::_('COM_JEM_SELECT').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active contact id field
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
?>