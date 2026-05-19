<?php
defined('_JEXEC') or die;

class ModEventsCalendarHelper
{
    /**
     * Получить данные для календаря
     */
    public function getCalendarData($events)
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        // Группируем события по датам
        $eventsByDate = $this->groupEventsByDate($events);
        
        // Получаем данные календаря
        $calendar = $this->getCalendar($currentMonth, $currentYear);
        
        // Получаем события для каждого дня месяца
        $daysWithEvents = $this->getDaysWithEvents($calendar['daysInMonth'], $currentMonth, $currentYear, $eventsByDate);
        
        return array(
            'monthName' => $this->getMonthName($currentMonth, $currentYear),
            'weekDays' => $this->getWeekDays(),
            'daysInMonth' => $calendar['daysInMonth'],
            'startDayOfWeek' => $calendar['startDayOfWeek'],
            'emptyCells' => $calendar['emptyCells'],
            'daysWithEvents' => $daysWithEvents,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'remainingCells' => $calendar['remainingCells']
        );
    }
    
    /**
     * Группировка событий по датам
     */
    private function groupEventsByDate($events)
    {
        $eventsByDate = array();
        
        if (!empty($events)) {
            foreach ($events as $event) {
                $date = $event->date;
                if (!empty($date)) {
                    if (!isset($eventsByDate[$date])) {
                        $eventsByDate[$date] = array();
                    }
                    $eventsByDate[$date][] = array(
                        'title' => $event->title,
                        'url' => $event->url
                    );
                }
            }
        }
        
        return $eventsByDate;
    }
    
    /**
     * Получить данные календаря
     */
    private function getCalendar($month, $year)
    {
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDayOfMonth);
        $startDayOfWeek = date('N', $firstDayOfMonth);
        $emptyCells = $startDayOfWeek - 1;
        
        $totalCells = $emptyCells + $daysInMonth;
        $remainingCells = (7 - ($totalCells % 7)) % 7;
        
        return array(
            'firstDayOfMonth' => $firstDayOfMonth,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'emptyCells' => $emptyCells,
            'remainingCells' => $remainingCells
        );
    }
    
    /**
     * Получить массив дней с событиями
     */
    private function getDaysWithEvents($daysInMonth, $month, $year, $eventsByDate)
    {
        $daysWithEvents = array();
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $hasEvents = isset($eventsByDate[$dateKey]);
            $formattedDate = $this->formatDate($year, $month, $day);
            
            $daysWithEvents[$day] = array(
                'hasEvents' => $hasEvents,
                'isToday' => ($day == date('j') && $month == date('n') && $year == date('Y')),
                'events' => $hasEvents ? $eventsByDate[$dateKey] : array(),
                'formattedDate' => $formattedDate,
                'fullDate' => $dateKey
            );
        }
        
        return $daysWithEvents;
    }
    
    /**
     * Форматирование даты для отображения
     */
    private function formatDate($year, $month, $day)
    {
        $monthsRu = array(
            1 => 'Января',
            2 => 'Февраля',
            3 => 'Марта',
            4 => 'Апреля',
            5 => 'Мая',
            6 => 'Июня',
            7 => 'Июля',
            8 => 'Августа',
            9 => 'Сентября',
            10 => 'Октября',
            11 => 'Ноября',
            12 => 'Декабря'
        );
        
        return $day . ' ' . $monthsRu[$month] . ' ' . $year;
    }
    
    /**
     * Получить название месяца на русском
     */
    private function getMonthName($month, $year)
    {
        $monthsRu = array(
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        );
        
        return $monthsRu[$month] . ' ' . $year;
    }
    
    /**
     * Получить массив дней недели на русском
     */
    private function getWeekDays()
    {
        return array(
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб',
            7 => 'Вс'
        );
    }
    
    /**
     * Получить стили для ячейки календаря
     */
    public function getCellStyles($dayData)
    {
        $bgStyle = '';
        $colorStyle = '';
        $additionalClass = '';
        
        if ($dayData['isToday']) {
            $bgStyle = 'background: #fff3cd;';
            $colorStyle = 'color: #d39e00;';
            $additionalClass = 'today';
        } elseif ($dayData['hasEvents']) {
            $bgStyle = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;';
            $additionalClass = 'has-event';
        }
        
        return array(
            'bgStyle' => $bgStyle,
            'colorStyle' => $colorStyle,
            'additionalClass' => $additionalClass
        );
    }
}