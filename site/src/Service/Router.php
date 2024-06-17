<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_researchprojects
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Researchprojects\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;


class Router extends RouterView
{
    use MVCFactoryAwareTrait;

    private $categoryFactory;

    private $categoryCache = [];

    private $db;

    /**
     * Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
        $this->db = \Joomla\CMS\Factory::getContainer()->get('DatabaseDriver');

        $researchprojects = new RouterViewConfiguration('researchprojects');

        $this->registerView($researchprojects);

        $researchproject = new RouterViewConfiguration('researchproject');
        $researchproject->setKey('id')->setParent($researchprojects);

        $this->registerView($researchproject);


        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));

        $this->attachRule(new CustomRouterRules($this));
    }


    /**
     * Method to get the id for an researchprojects item from the segment
     *
     * @param   string  $segment  Segment of the researchprojects to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getResearchprojectId(string $segment, array $query): bool|string
    {
        return $segment;
    }

    /**
     * Method to get the segment(s) for a researchprojects item
     *
     * @param   string  $id     ID of the researchprojects to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getResearchprojectSegment(string $id, array $query): array
    {
        $id = (int) $id;
        $db = $this->db;

        $dbQuery = $db->getQuery(true)
            ->select($db->quoteName('alias'))
            ->from($db->quoteName('#__researchprojects'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $alias = $db->setQuery($dbQuery)->loadResult() ?: null;
        if ($alias === null) {
            return [];
        }
        return [(int) $id => $id . '-' . $alias];
    }

}
