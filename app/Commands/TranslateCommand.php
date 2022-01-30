<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\VarExporter\VarExporter;

class TranslateCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'translate {--name=message} {--lang=cn}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Translate languages';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $toLang = explode(',', $this->option('lang'));
        $lookup = [
            'cn' => 'zh-CN',
            'zh' => 'zh-TW',
            'af' => 'af',
            'sq' => 'sq',
            'am' => 'am',
            'ar' => 'ar',
            'hy' => 'hy',
            'az' => 'az',
            'eu' => 'eu',
            'be' => 'be',
            'bn' => 'bn',
            'bs' => 'bs',
            'bg' => 'bg',
            'ca' => 'ca',
            'ceb' => 'ceb',
            'ny' => 'ny',
            'co' => 'co',
            'hr' => 'hr',
            'cs' => 'cs',
            'da' => 'da',
            'nl' => 'nl',
            'en' => 'en',
            'eo' => 'eo',
            'et' => 'et',
            'tl' => 'tl',
            'fi' => 'fi',
            'fr' => 'fr',
            'fy' => 'fy',
            'gl' => 'gl',
            'ka' => 'ka',
            'de' => 'de',
            'el' => 'el',
            'gu' => 'gu',
            'ht' => 'ht',
            'ha' => 'ha',
            'haw' => 'haw',
            'iw' => 'iw',
            'hi' => 'hi',
            'hmn' => 'hmn',
            'hu' => 'hu',
            'is' => 'is',
            'ig' => 'ig',
            'id' => 'id',
            'ga' => 'ga',
            'it' => 'it',
            'ja' => 'ja',
            'jw' => 'jw',
            'kn' => 'kn',
            'kk' => 'kk',
            'km' => 'km',
            'rw' => 'rw',
            'ko' => 'ko',
            'ku' => 'ku',
            'ky' => 'ky',
            'lo' => 'lo',
            'la' => 'la',
            'lv' => 'lv',
            'lt' => 'lt',
            'lb' => 'lb',
            'mk' => 'mk',
            'mg' => 'mg',
            'ms' => 'ms',
            'ml' => 'ml',
            'mt' => 'mt',
            'mi' => 'mi',
            'mr' => 'mr',
            'mn' => 'mn',
            'my' => 'my',
            'ne' => 'ne',
            'no' => 'no',
            'or' => 'or',
            'ps' => 'ps',
            'fa' => 'fa',
            'pl' => 'pl',
            'pt' => 'pt',
            'pa' => 'pa',
            'ro' => 'ro',
            'ru' => 'ru',
            'sm' => 'sm',
            'gd' => 'gd',
            'sr' => 'sr',
            'st' => 'st',
            'sn' => 'sn',
            'sd' => 'sd',
            'si' => 'si',
            'sk' => 'sk',
            'sl' => 'sl',
            'so' => 'so',
            'es' => 'es',
            'su' => 'su',
            'sw' => 'sw',
            'sv' => 'sv',
            'tg' => 'tg',
            'ta' => 'ta',
            'tt' => 'tt',
            'te' => 'te',
            'th' => 'th',
            'tr' => 'tr',
            'tk' => 'tk',
            'uk' => 'uk',
            'ur' => 'ur',
            'ug' => 'ug',
            'uz' => 'uz',
            'vi' => 'vi',
            'cy' => 'cy',
            'xh' => 'xh',
            'yi' => 'yi',
            'yo' => 'yo',
            'zu' => 'zu',
        ];
        if(count(array_diff($toLang,array_keys($lookup))) > 0) {
            $this->error('No supported languages found for : '. implode(', ', array_diff($toLang,array_keys($lookup))));
            return true;
        }
        $translated = [];
        $filename = $this->option('name');
        $data = include(resource_path('lang/en/' . $filename . '.php'));
        foreach ($toLang as $lang) {
            if (!File::exists(resource_path('lang/' . $lang . '/'))) {
                File::makeDirectory(resource_path('lang/' . $lang), 0755, true, true);
            }
            $this->browse(function ($browser) use ($lookup, $lang, $data, $filename) {
                $browser->visit('https://translate.google.com/?sl=auto&tl=' . $lookup[$lang] . '&op=translate');
                foreach ($data as $key => $item) {
                    if (gettype($item) === 'array') {
                        foreach ($item as $k => $i) {
                            $browser->type('textarea', $i);
                            $browser->pause(2000);
                            $translated[$key][$k] = $browser->text('span.JLqJ4b');
                        }
                    } else {
                        $browser->type('textarea', $item);
                        $browser->pause(2000);
                        $translated[$key] = $browser->text('span.JLqJ4b');
                    }
                    File::put(resource_path('lang/' . $lang . '/' . $filename . '.php'), "<?php " . PHP_EOL . PHP_EOL . " return " . VarExporter::export($translated) . ';');
                    $data = include(resource_path('lang/' . $lang . '/' . $filename . '.php'));
                }
            });
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
