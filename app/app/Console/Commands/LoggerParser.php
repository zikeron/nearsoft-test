<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kassner\LogParser\LogParser;
use Jenssegers\Agent\Agent;
use UAParser\Parser;

class LoggerParser extends Command
{
    /** @var string $name */
    protected $name = 'logger-parser';

    /** @var string $signature */
    protected $signature = 'logger-parser';

    /** @var string $description */
    protected $description = 'Build a CSV from log access';

    /** @var LogParser $parser */
    protected $parser;

    /** @var Agent $agent */
    protected $agent;

    /** @var Parser $uaParser */
    protected $uaParser;

    /** @var string LOG_PATH */
    const LOG_PATH = "logs/access.log";

    /** @var string  CSV_PATH */
    const CSV_PATH = "storage/csv/";

    /** @var string BASE_GEO_URL */
    const BASE_GEO_URL = "http://www.geoplugin.net/php.gp?ip=";

    /**
     * LoggerParser constructor.
     * @param LogParser $logParser
     * @param Agent $agent
     * @throws \UAParser\Exception\FileNotFoundException
     */
    public function __construct(
        LogParser $logParser,
        Agent $agent
    )
    {
        parent::__construct();
        $this->parser = $logParser;
        $this->agent = $agent;
        $this->uaParser = Parser::create();
    }

    /**
     * @throws \Kassner\LogParser\FormatException
     * @throws \UAParser\Exception\FileNotFoundException
     */
    public function handle()
    {
        $data = [];
        if (file_exists(self::LOG_PATH)) {
            $file = file(self::LOG_PATH,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($file as $line) {
                $info = explode('"-"', $line);
                $objInfo = $this->parser->parse($info[0]);
                $host = $objInfo->host;
                $data[] = [$host, $info[1]];
            }
            $this->preparedFile($data);
        } else {
            $this->info("File not found");
        }
    }

    /**
     * @param array $data
     * @throws \UAParser\Exception\FileNotFoundException
     */
    protected function preparedFile(array $data)
    {
        $path = self::CSV_PATH . date("Ymd_His") . ".csv";
        $dataToCsv = [];

        foreach ($data as $item) {
            list($city, $country) = $this->getIpInfo($item[0]);
            $browser = $this->uaParser->parse($item[1])->ua->family;
            $model = $this->uaParser->parse($item[1])->device->model;
            $this->agent->setUserAgent($item[1]);
            $device = $this->agent->deviceType();
            $dataToCsv[] = [$country, $city, $browser, $device, $model];
        }
        $this->generateCsv($dataToCsv, $path,",");
    }

    protected function generateCsv(array $data, string $path, string $delimiter)
    {
        if ($path === "" || empty($data)) {
            return;
        }

        $file_handle = fopen($path, 'w');
        foreach ($data as $line) {
            fputcsv($file_handle, $line, $delimiter);
        }
        rewind($file_handle);
        fclose($file_handle);
    }

    protected function getIpInfo(string $host) :array
    {
        $geoPluginURL = self::BASE_GEO_URL . $host;
        $addrDetailsArr = unserialize(file_get_contents($geoPluginURL));
        return [
            $addrDetailsArr['geoplugin_city'] ?? "N/A",
            $addrDetailsArr['geoplugin_countryName'] ?? "N/A",
        ];
    }
}
