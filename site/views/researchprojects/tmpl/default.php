<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

$table_id = 'researchprojectsTable';
// If you need specific JS/CSS for this view, add them here.
// Example included for DataTables (https://datatables.net/) delete if you don't want this.
// Make sure jQuery is loaded first:
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
// Get the doc object:
$doc = JFactory::getDocument();

/*
// Add a script tag with a src:
$doc->addScript("//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js");
#$doc->addScript("//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js");
// Add a CSS link tag:
$doc->addStyleSheet('//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css');
#$doc->addStyleSheet('//cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css');
// Add a script tag with content:
$js = '
jQuery(document).ready(function(){
    jQuery("#' . $table_id . '").DataTable();
});
';
$doc->addScriptDeclaration($js);
*/

function format_person($p) {
    $pp = ResearchProjectsHelper::parseCollaborator($p);
    return $pp['first_name'] . ' ' . $pp['last_name'] . (empty($pp['institution']) ? '' : ' (' . $pp['institution'] .')');
}

?>
<?php if (!empty($this->items)) : ?>
    <ul>
    <?php foreach ($this->items as $i => $row) :
        #$view_link = JRoute::_('index.php?option=com_researchprojects&task=researchproject.view&id=' . $row->id);
        $view_link = JRoute::_('index.php?option=com_researchprojects&task=researchproject.view');
        $view_link .= '/' . $row->id . '-' . $row->alias;
    ?>
        <li>
            <a href="<?php echo $view_link; ?>" class="c-card__full-link  u-fill-height--column">
                <b><?php echo $row->title; ?></b><br>
                <?php $has_pi_2 = !empty($row->pi_2); ?>
                <span>Lead<?php echo ($has_pi_2 ? 's' : '') ?>: <?php echo format_person($row->pi_1); ?><?php if ($has_pi_2) : ?> and <?php echo format_person($row->pi_2); ?><?php endif; ?><span><br>
                <span>Topics: <?php echo implode(", ", $row->topics); ?><span>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>