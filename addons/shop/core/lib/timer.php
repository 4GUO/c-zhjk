<?php
namespace lib;
class timer
{
    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today()
    {
        return [
            mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            mktime(23, 59, 59, date('m'), date('d'), date('Y'))
        ];
    }
 
    /**
     * 返回昨日开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday()
    {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y'))
        ];
    }
 
    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime('this week Monday', $timestamp))),
            strtotime(date('Y-m-d 23:59:59', strtotime('this week Sunday', $timestamp))) + 24 * 3600 - 1
        ];
    }
 
    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime('last week Monday', $timestamp))),
            strtotime(date('Y-m-d 23:59:59', strtotime('last week Sunday', $timestamp))) + 24 * 3600 - 1
        ];
    }
 
    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month($everyDay = false)
    {
        return [
            mktime(0, 0, 0, date('m'), 1, date('Y')),
            mktime(23, 59, 59, date('m'), date('t'), date('Y'))
        ];
    }
 
    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth()
    {
        $begin = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $end = mktime(23, 59, 59, date('m') - 1, date('t', $begin), date('Y'));
 
        return [$begin, $end];
    }
    //获取下月开始时间和结束时间
	public static function nextMonth($date) {

		$timestamp = strtotime($date);

		$arr = getdate($timestamp);

		if($arr['mon'] == 12) {

			$year = $arr['year'] + 1;

			$month = $arr['mon'] - 11;

			$firstday = $year . '-0' . $month . '-01';

			$lastday = date('Y-m-d', strtotime($firstday . ' +1 month -1 day'));

		} else {
			
			$firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) + 1) . '-01'));

			$lastday = date('Y-m-d', strtotime($firstday . ' +1 month -1 day'));
		}
		return array(strtotime($firstday), strtotime($lastday));
	}
    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year()
    {
        return [
            mktime(0, 0, 0, 1, 1, date('Y')),
            mktime(23, 59, 59, 12, 31, date('Y'))
        ];
    }
 
    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear()
    {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year)
        ];
    }
 
    public static function dayOf()
    {
 
    }
 
    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow($day = 1, $now = true)
    {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }
 
        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end
        ];
    }
 
    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1)
    {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }
 
    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1)
    {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }
 
    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function daysToSecond($day = 1)
    {
        return $day * 86400;
    }
 
    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }
	/**
	  * 日期时间友好显示
	  * @param $time
	  * @return bool|string
	  */
	public static function friend_date($time)
	{
		if (!$time) {
			return false;
		}
		$fdate = '';
		$d = time() - intval($time);
		$ld = $time - mktime(0, 0, 0, 0, 0, date('Y')); //得出年
		$md = $time - mktime(0, 0, 0, date('m'), 0, date('Y')); //得出月
		$byd = $time - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
		$yd = $time - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
		$dd = $time - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
		$td = $time - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
		$atd = $time - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
		if ($d == 0) {
			$fdate = '刚刚';
		} else {
			switch ($d) {
				case $d < $atd:
					$fdate = date('Y年m月d日', $time);
				break;
				case $d < $td:
					$fdate = '后天' . date('H:i', $time);
				break;
				case $d < 0:
					$fdate = '明天' . date('H:i', $time);
				break;
				case $d < 60:
					$fdate = $d . '秒前';
				break;
				case $d < 3600:
					$fdate = floor($d / 60) . '分钟前';
				break;
				case $d < $dd:
					$fdate = floor($d / 3600) . '小时前';
				break;
				case $d < $yd:
					$fdate = '昨天' . date('H:i', $time);
				break;
				case $d < $byd:
					$fdate = '前天' . date('H:i', $time);
				break;
				case $d < $md:
					$fdate = date('m月d日 H:i', $time);
				break;
				case $d < $ld:
					$fdate = date('m月d日', $time);
				break;
				default:
					$fdate = date('Y年m月d日', $time);
				break;
			}
		}
		return $fdate;
	}
	public static function time2string($second)
    {
		$day = floor($second / (3600 * 24));
		$second = $second % (3600 * 24);//除去整天之后剩余的时间
		$hour = floor($second / 3600);
		$second = $second % 3600;//除去整小时之后剩余的时间 
		$minute = floor($second / 60);
		$second = $second % 60;//除去整分钟之后剩余的时间 
		//返回字符串
		return array(
			'day' => $day,
			'hour' => $hour,
			'minute' => $minute,
			'second' => $second,
		);
    }
    private static function startTimeToEndTime()
    {
 
    }
    /**
    *
    
    * 获取指定年月的开始和结束时间戳
    
    *
    
    * @param int $year 年份
    
    * @param int $month 月份
    
    * @return array(开始时间,结束时间)
    
    */
    
    public static function getMonthBeginAndEnd($year = 0, $month = 0) {
        $year = $year ? $year : date('Y');
        
        $month = $month ? $month : date('m');
        
        $d = date('t', strtotime($year . '-' . $month));
        
        return [strtotime($year . '-' . $month), mktime(23, 59, 59, $month, $d, $year)];
    }
    
    /**
    
    * 获取指定时间戳所在的月份的开始时间戳和结束时间戳
    
    *
    
    * @param int $timestamp
    
    * @return array(开始时间,结束时间)
    
    */
    
    public static function getMonthBeginAndEndByTamp($timestamp = 0) {
        $timestamp = $timestamp ? $timestamp : time();
        
        $year = date('Y', $timestamp);
        
        $month = date('m', $timestamp);
        
        $d = date('t', strtotime($year . '-' . $month));
        
        return [strtotime($year . '-' . $month), mktime(23, 59, 59, $month, $d, $year)];
    
    }
}