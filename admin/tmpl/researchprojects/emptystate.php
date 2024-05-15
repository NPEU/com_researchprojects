<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_RESEARCHPROJECTS',
    'formURL'    => 'index.php?option=com_researchprojects',
];

/*
$displayData = [
    'textPrefix' => 'COM_RESEARCHPROJECTS',
    'formURL'    => 'index.php?option=com_researchprojects',
    'helpURL'    => '',
    'icon'       => 'icon-globe researchprojects',
];
*/

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_researchprojects') || count($user->getAuthorisedCategories('com_researchprojects', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_researchprojects&task=researchproject.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);