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
