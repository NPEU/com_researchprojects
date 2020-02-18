<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2019.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;
use Joomla\Registry\Registry;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
#$listOrder = $this->escape($this->filter_order);
#$listDirn  = $this->escape($this->filter_order_Dir);
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_researchprojects&view=researchprojects'); ?>" method="post" id="adminForm" name="adminForm">
    <div id="j-main-container">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        <div class="clearfix"> </div>
        <?php if (empty($this->items)) : ?>
        <div class="researchproject researchproject-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); #COM_RESEARCHPROJECTS_NO_RECORDS ?>
        </div>
        <?php else : ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="2%"><?php echo JText::_('COM_RESEARCHPROJECTS_NUM'); ?></th>
                    <th width="4%">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="40%" style="max-width: 600px;">
                        <?php echo JHtml::_('searchtools.sort', 'COM_RESEARCHPROJECTS_RECORDS_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="24%">
                        <?php echo JText::_('COM_RESEARCHPROJECTS_RECORDS_TOPICS'); ?>
                    </th>
                    <th width="14%">
                        <?php echo JHtml::_('searchtools.sort', 'COM_RESEARCHPROJECTS_RECORDS_OWNER', 'owner_name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%">
                        <?php echo JHtml::_('searchtools.sort', 'COM_RESEARCHPROJECTS_PUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%">
                        <?php echo JHtml::_('searchtools.sort', 'COM_RESEARCHPROJECTS_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <?php // Format topics: 
                    $item->topics = str_replace("\n", "<br>\n", $item->topic_names);
                ?>
                <?php $canCreate      = $user->authorise('core.create',     'com_researchprojects.' . $item->id); ?>
                <?php $canEdit        = $user->authorise('core.edit',       'com_researchprojects.' . $item->id); ?>
                <?php $canCheckin     = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0; ?>
                <?php $canEditOwn     = $user->authorise('core.edit.own',   'com_researchprojects.' . $item->id) && $item->created_by == $user->id; ?>
                <?php $canChange      = $user->authorise('core.edit.state', 'com_researchprojects.' . $item->id) && $canCheckin; ?>

                <tr>
                    <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                    <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class=" has-context">
                    <?php #echo '<pre>'; var_dump($item); echo '</pre>'; ?>
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'researchprojects.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit || $canEditOwn) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_researchprojects&task=researchproject.edit&id=' . (int) $item->id); ?>" title="<?php echo JText::_('COM_RESEARCHPROJECTS_EDIT_RECORD'); ?>">
                                <?php echo $this->escape($item->title); ?></a>
                        <?php else : ?>
                                <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                        <br><span class="small">
                            <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                        </span>
                    </td>
                    <td align="center">
                        <?php echo $item->topics; ?>
                    </td>
                    <td align="center">
                        <a href="mailto:<?php echo $item->owner_email; ?>"><?php echo $item->owner_name; ?></a>
                    </td>
                    <td align="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'researchprojects.', true, 'cb'); ?>
                    </td>
                    <td align="center">
                        <?php echo $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
