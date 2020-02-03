<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * ResearchProjectsHelper component helper.
 */
class ResearchProjectsHelper extends JHelperContent
{
    /**
     * Configure the Submenu. Delete if component has only one view.
     *
     * @param   string  The name of the active view.
     */
    public static function addSubmenu($vName = 'researchprojects')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_RESEARCHPROJECTS_MANAGER_SUBMENU_RECORDS'),
            'index.php?option=com_researchprojects&view=researchprojects',
            $vName == 'researchprojects'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_RESEARCHPROJECTS_MANAGER_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&view=categories&extension=com_researchprojects',
            $vName == 'categories'
        );
    }
    
    /**
	 * Method parse a collaborator string into an array
	 *
     * @param string    The collaborator string.
	 * @return  array   The field input markup.
	 */
    public static function parseCollaborator($str)
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
        
        // Really, there should be a comma in the remaining text, separating last name, first name,
        // but this isn't (and shouldn't be) enforced, so check for presence of comma first:
        if (strpos($str, ',')) {
            $name = explode(',', $str);
        } else {
            $name = array('', $str);
        }
        return array(
            'first_name'  => trim($name[1]),
            'last_name'   => trim($name[0]),
            'institution' => $institution,
            'url'         => $url
        );
        
    }

    /**
     * Get the actions
     */
     /*
    public static function getActions($itemId = 0, $model = null)
    {
        jimport('joomla.access.access');
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($itemId)) {
            $assetName = 'comresearchprojects';
        }
        else {
            $assetName = 'com_researchprojects.researchproject.'.(int) $itemId;
        }

        $actions = JAccess::getActions('com_researchprojects', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        // Check if user belongs to assigned category and permit edit if so:
        if ($model) {
            $item  = $model->getItem($itemId);

            if (!!($user->authorise('core.edit', 'com_researchprojects')
            || $user->authorise('core.edit', 'com_content.category.' . $item->catid))) {
                $result->set('core.edit', true);
            }
        }

        return $result;
    }*/

}
