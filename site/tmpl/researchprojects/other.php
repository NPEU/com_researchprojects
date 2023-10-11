<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
#use Joomla\CMS\Layout\LayoutHelper;
#use Joomla\CMS\Layout\FileLayout;
#use Joomla\CMS\Language\Multilanguage;
#use Joomla\CMS\Session\Session;
#use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

#use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$language = JFactory::getLanguage();
$language->load('com_researchprojects', JPATH_ADMINISTRATOR . '/components/com_researchprojects');

$table_id = 'researchprojectsTable';

// Get the user object.
$user = Factory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
$can_edit       = $user->authorise('core.edit', 'com_researchprojects');
$can_create     = $user->authorise('core.create', 'com_researchprojects');
$can_edit_state = $user->authorise('core.edit.state', 'com_researchprojects');

?>
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<p>This content is coming from a template in the 'researchprojects' folder, and doesn't need it's own View to stuff.</p>