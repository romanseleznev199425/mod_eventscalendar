<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

$events = $params->get('events', array());
$helper = new ModEventsCalendarHelper();
$calendarData = $helper->getCalendarData($events);

require ModuleHelper::getLayoutPath('mod_eventscalendar');