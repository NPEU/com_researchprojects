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
 * Form field for a list of collaborators.
 */
#class JFormFieldCollaborators extends JFormFieldGroupedList
class JFormFieldCollaborators extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Collaborators';
    
	/**
	 * Method parse a collaborator string into an array
	 *
     * @param string    The collaborator string.
	 * @return  array   The field input markup.
	 */
    protected function parseCollaborator($str)
    {
        // E.g. Abalos, Edgardo (Queensland Institute of Medical Research, Brisbane, Australia)
        // Also has the option to have URL in [], so lets extract that first:
        $url1 = strpos($str, '[');
        $url2 = strrpos($str, ']');
        
        $url = '';
        if ($url1 && $url2) {
            $url = substr($str, $url1 + 1, ($url2 - $url1 - 1));
            $str = trim(str_replace('[' . $url . ']', '', $str));
        }
        
        // Look for the first open bracket after the name, as determined by the first space after
        // the first comma.
        $institution1 = strpos($str, '(', strpos($str, ' ', strpos($str, ',')));
        $institution2 = strrpos($str, ')');
        
        // Extract the institution:
        $institution = '';
        if ($institution1 && $institution2) {
            
            $institution = substr($str, $institution1 + 1, ($institution2 - $institution1 - 1));
            $str = trim(str_replace('(' . $institution . ')', '', $str));
        }
        
        $name = explode(', ', $str);
        return array(
            'first_name'  => $name[1],
            'last_name'   => $name[0],
            'institution' => $institution,
            'url'         => $url
        );
        
    }

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
        . 'data-custom_group_text="' . JText::_('COM_RESEARCHPROJECTS_COLLABORATOR_GROUP_LABEL') . '" '
        . 'data-no_results_text="' . JText::_('COM_RESEARCHPROJECTS_COLLABORATOR_ADD_NEW') . '"';
        #. 'data-placeholder="' . JText::_('COM_RESEARCHPROJECTS_COLLABORATOR_TYPE_SELECT') . '"';

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
        $collaborators = array();
        $options = array();
        $db = JFactory::getDBO();
        
        
        // Get Staff members:
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
        foreach ($staff_members as $staff_member) {
            $val  = $staff_member['last_name'] . ', ' . $staff_member['first_name'] . ' (NPEU)';
            $text = strtoupper($staff_member['last_name']) . ', ' . $staff_member['first_name'] . ' (NPEU)';
            #$text = $staff_member['name'] . ' (NPEU)';
            $collaborators[$val] = $text;
        }
        
        // Get non staff members:
        $q  = 'SELECT collaborator FROM `#__researchprojects_collaborators`;';

        $db->setQuery($q);
        if (!$db->execute($q)) {
            JError::raiseError( 500, $db->stderr() );
            return false;
        }

        $nonstaff_members = $db->loadColumn();
        foreach ($nonstaff_members as $nonstaff_member) {
            $val  = $nonstaff_member;
            $t = $this->parseCollaborator($val);
            $text = strtoupper($t['last_name']) . ', ' . $t['first_name'] . (empty($t['institution']) ? '' : ' (' . $t['institution'] .')') . (empty($t['url']) ? '' : ' [' . $t['institution'] . ']');
            $collaborators[$val] = $text;
        }
        
        #echo '<pre>'; var_dump($collaborators); echo '</pre>'; exit;
        ksort($collaborators);
        
        $i = 0;
        foreach ($collaborators as $val => $text) {
            $options[] = JHtml::_('select.option', $val, $text);
            $i++;
        }
        

        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_RESEARCHPROJECTS_COLLABORATORS_EMPTY');
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