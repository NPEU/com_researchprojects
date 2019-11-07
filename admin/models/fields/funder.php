<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;


/*
    IMPORTANT = this field has an UNPROTECTED dependency on the FirstLastNames plugin.
    This extension will break of that's not installed and enabled.
*/


#JFormHelper::loadFieldClass('groupedlist');
JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of funders.
 */
#class JFormFieldFunders extends JFormFieldGroupedList
class JFormFieldFunders extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Funders';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        $input = parent::getInput();
        
        $attr = 'class="chzn-custom-value" '
        . 'data-custom_group_text="' . JText::_('COM_RESEARCHPROJECTS_FUNDERS_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . JText::_('COM_RESEARCHPROJECTS_FUNDERS_ADD_NEW') . '"';
        #. 'data-placeholder="' . JText::_('COM_RESEARCHPROJECTS_FUNDERS_TYPE_SELECT') . '"';
        
        $input = str_replace('<select', '<select ' . $attr,  $input);
        return $input;
        #echo '<pre>'; var_dump($input); echo '</pre>'; exit;
        
        /*'class="chzn-custom-value" '
        . 'data-no_results_text="' . JText::_('COM_MODULES_ADD_CUSTOM_POSITION') . '" '
        . 'data-placeholder="' . JText::_('COM_MODULES_TYPE_OR_SELECT_POSITION') . '" '*/

    }
    
    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options = array();
        $db = JFactory::getDBO();
        $q  = 'SELECT * FROM `#__researchprojects_funders`';
        $q .= 'ORDER BY funder;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $funders = $db->loadAssocList();

        $i = 0;
        foreach ($funders as $funder) {
            $val = $funder;
            $options[] = JHtml::_('select.option', $val, $val);
            $i++;
        }
        
        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_SITEAREAS_CONTACT_DEFAULT_NO_STAFF');
        }
        return $options;
    }
    
    /**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	/*protected function getGroups()
    {
        $groups = array();
        
        // Add the staff group:
        $groups['NPEU Staff'] = array();
        
        $db = JFactory::getDBO();
        $q  = 'SELECT u.id, u.name, up1.profile_value AS first_name, up2.profile_value AS last_name FROM `#__users` u ';
        $q .= 'JOIN `#__user_usergroup_map` ugm ON u.id = ugm.user_id ';
        $q .= 'JOIN `#__usergroups` ug ON ugm.group_id = ug.id ';
        $q .= 'JOIN `#__user_profiles` up1 ON u.id = up1.user_id AND up1.profile_key = "firstlastnames.firstname"';
        $q .= 'JOIN `#__user_profiles` up2 ON u.id = up2.user_id AND up2.profile_key = "firstlastnames.lastname"';
        $q .= 'WHERE ug.title = "Staff" ';
        $q .= 'AND u.block = 0 ';
        $q .= 'ORDER BY last_name, first_name;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $staff_members = $db->loadAssocList();

        $i = 0;
        foreach ($staff_members as $staff_member) {
            $groups['NPEU Staff'][] = JHtml::_('select.option', $staff_member['id'], $staff_member['name']);
            $i++;
        }
        
        /*if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_SITEAREAS_CONTACT_DEFAULT_NO_STAFF');
        }
        return $options;*
        
        
		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
    }*/
}