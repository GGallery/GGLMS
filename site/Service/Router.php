<?php
defined('_JEXEC') or die;

/**
 * Joomla component com_gglms
 *
 * @package WebTV
 * Router.php
 */
class GGlmsRouter extends JComponentRouterBase
{

    function build(&$query)
    {
        $segments = array();

        if (isset($query['view'])) {
            $segments[] = $query['view'];
            unset($query['view']);
        }

        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        if (isset($query['type'])) {
            $segments[] = $query['type'];
            unset($query['type']);
        }

        if (isset($query['alias'])) {
            $segments[] = $query['alias'];
            unset($query['alias']);
        }

        if (isset($query['unit'])) {
            unset($query['unit']);
        }

        if (isset($query['coupondipenser'])) {
            unset($query['coupondipenser']);
        }

        if (isset($query['layout'])) {
            $segments[] = $query['layout'];
            unset($query['layout']);
        }

        return $segments;
    }

    function parse(&$segments)
    {
        $db = JFactory::getDbo();
        $vars = array();

        switch ($segments[0]) {
            case 'unita':
                $vars['view'] = 'unita';


                $alias = $segments[1];
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__gg_unit')
                    ->where('alias="' . $alias . '"');

                $db->setQuery($query);
                $vars['id'] = $db->loadResult();


                break;

            case 'contenuto':

                $vars['view'] = 'contenuto';

                if (strpos($segments[1], '-') === false) {
                    $alias = $segments[1];
                    $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__gg_contenuti')
                        ->where('alias="' . $alias . '"');

                    $db->setQuery($query);
                    $vars['id'] = $db->loadResult();
                } else {
                    list($id, $alias) = explode('-', $segments[1], 2);
                    $vars['id'] = $id;
                }

                break;

            case 'coupon':
                $vars['view'] = 'coupon';
                break;


            case 'coupondispenser':
                $vars['view'] = 'coupondispenser';

                $id_iscrizione = $segments[1];
                $query = $db->getQuery(true)
                    ->select('id')
                    ->from('#__gg_coupon_dispenser as c')
                    ->where('c.id_iscrizione="' . $id_iscrizione . '"');

                $db->setQuery($query);
                $vars['id'] = $db->loadResult();

                break;

            case 'dash':

                $vars['view'] = 'dash';
                break;

            default:
                $vars['view'] = 'gglms';
                break;


        }
        return $vars;
    }
}

function gglmsBuildRoute(&$query)
{

    $router = new GGlmsRouter;
    return $router->build($query);
}

function gglmsParseRoute($segments)
{
    $router = new GGlmsRouter;

    return $router->parse($segments);
}


