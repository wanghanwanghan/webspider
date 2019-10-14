<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use QL\QueryList;

class XingZuoWu extends Command
{
    protected $signature = 'spider:xzw';

    protected $description = '爬取星座运势在星座屋 https://www.xzw.com/fortune/aries/';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $target = [
            'aries',// 白羊
            'taurus',// 金牛
            'gemini',// 双子
            'cancer',// 巨蟹
            'leo',// 狮子
            'virgo',// 处女
            'libra',// 天秤
            'scorpio',// 天蝎
            'sagittarius',// 射手
            'capricorn',// 摩羯
            'aquarius',// 水瓶
            'pisces',// 双鱼
        ];

        $day=Carbon::now()->format('Ymd');

        //今日取没取过
        if (is_dir(public_path("xingzuowu/{$day}"))) return true;

        //开始取
        foreach ($target as $one)
        {
            mkdir(public_path("xingzuowu/{$day}/{$one}"),0777,true);

            $obj=QueryList::get("https://www.xzw.com/fortune/{$one}/");
            $title=$obj->find('.c_cont strong')->texts();
            $texts=$obj->find('.c_cont span')->texts();

            //写入文件
            $fp2=@fopen(public_path("xingzuowu/{$day}/{$one}/yunshi.log"),"w+");

            //添一点别的
            $something=$obj->find('dl dd ul li')->slice(4,9)->texts();

            for ($i=0;$i<=4;$i++)
            {
                fwrite($fp2,$title[$i].'：');

                fwrite($fp2,$texts[$i].PHP_EOL);
            }

            for ($i=0;$i<=4;$i++)
            {
                fwrite($fp2,$something[$i].PHP_EOL);
            }

            fclose($fp2);
        }

        return true;
    }
}
