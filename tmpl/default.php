<?php
defined('_JEXEC') or die;

$calendarData = $calendarData ?? array();
$moduleId = $module->id;

// Подключаем CSS файл
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true) . '/modules/mod_eventscalendar/tmpl/default.css');
$document->addScript(JUri::base(true) . '/modules/mod_eventscalendar/tmpl/default.js');
?>

<div class="mod-events-calendar" id="mod-events-calendar">
    <div class="calendar-header">
        <h3><?php echo $calendarData['monthName']; ?></h3>
    </div>
    
    <table class="calendar-table">
        <thead>
            <tr class="weekday">
                <?php foreach ($calendarData['weekDays'] as $day): ?>
                    <th><?php echo $day; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalDays = $calendarData['daysInMonth'];
            $startOffset = $calendarData['startDayOfWeek'] - 1;
            $totalCells = $startOffset + $totalDays;
            $rows = ceil($totalCells / 7);
            
            for ($row = 0; $row < $rows; $row++):
            ?>
            <tr>
                <?php for ($col = 0; $col < 7; $col++):
                    $cellIndex = $row * 7 + $col;
                    
                    if ($cellIndex < $startOffset || $cellIndex >= $startOffset + $totalDays):
                        // Пустая ячейка
                ?>
                    <td class="calendar-cell empty">
                        <div>&nbsp;</div>
                    </td>
                <?php
                    else:
                        $dayNum = $cellIndex - $startOffset + 1;
                        $dayData = $calendarData['daysWithEvents'][$dayNum] ?? null;
                        $hasEvents = $dayData && $dayData['hasEvents'];
                        $isToday = $dayData && $dayData['isToday'];
                        $eventsCount = $hasEvents ? count($dayData['events']) : 0;
                        $cellClass = 'calendar-cell';
                        if ($isToday) $cellClass .= ' today';
                        if ($hasEvents) $cellClass .= ' has-event';
                        
                        // Склонение слова "мероприятие"
                        $eventWord = 'мероприятий';
                        if ($eventsCount % 10 == 1 && $eventsCount % 100 != 11) {
                            $eventWord = 'мероприятие';
                        } elseif (($eventsCount % 10 >= 2 && $eventsCount % 10 <= 4) && ($eventsCount % 100 < 10 || $eventsCount % 100 >= 20)) {
                            $eventWord = 'мероприятия';
                        }
                ?>
                    <td class="<?php echo $cellClass; ?>"
                        data-date="<?php echo $dayData ? $dayData['fullDate'] : ''; ?>"
                        data-formatted-date="<?php echo $dayData ? $dayData['formattedDate'] : ''; ?>"
                        data-events='<?php echo $dayData ? json_encode($dayData['events']) : '[]'; ?>'>
                        <div>
                            <span class="day-number"><?php echo $dayNum; ?></span>
                            <?php if ($hasEvents): ?>
                                <div class="events-count">
                                    <span class="events-count-number"><?php echo $eventsCount; ?></span>
                                    <span class="events-count-text"><?php echo $eventWord; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                <?php
                    endif;
                endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    
    <!-- Popup контейнер -->
    <div id="event-popup" class="event-popup"></div>
</div>