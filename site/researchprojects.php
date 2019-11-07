<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

#require_once JPATH_COMPONENT . '/helpers/route.php';

// Get an instance of the controller prefixed by ResearchProjects
$controller = JControllerLegacy::getInstance('ResearchProjects');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();