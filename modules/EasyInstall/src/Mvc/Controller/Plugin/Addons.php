<?php
namespace EasyInstall\Mvc\Controller\Plugin;

use DOMDocument;
use DOMXPath;
use Omeka\Stdlib\Message;
use Zend\Http\Client as HttpClient;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;
use Zend\Uri\Http as HttpUri;

/**
 * List addons for Omeka.
 */
class Addons extends AbstractPlugin
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Source of data and destination of addons.
     *
     * @var array
     */
    protected $data = [
        'omekamodule' => [
            'source' => 'https://omeka.org/s/modules/',
            'destination' => '/modules',
        ],
        'omekatheme' => [
            'source' => 'https://omeka.org/s/themes/',
            'destination' => '/themes',
        ],
        'module' => [
            'source' => 'https://raw.githubusercontent.com/Daniel-KM/UpgradeToOmekaS/master/_data/omeka_s_modules.csv',
            'destination' => '/modules',
        ],
        'theme' => [
            'source' => 'https://raw.githubusercontent.com/Daniel-KM/UpgradeToOmekaS/master/_data/omeka_s_themes.csv',
            'destination' => '/themes',
        ],
    ];

    /**
     * Expiration seconds.
     *
     * @var int
     */
    protected $expirationSeconds = 3600;

    /**
     * Expiration hops.
     *
     * @var int
     */
    protected $expirationHops = 10;

    /**
     * Cache for the list of addons.
     *
     * @var array
     */
    protected $addons;

    /**
     * @param HTTPClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Return the addon list.
     *
     * @return string
     */
    public function __invoke()
    {
        // Build the list of addons only once.
        if (!$this->isEmpty()) {
            return $this->addons;
        }

        // Check the cache.
        $container = new Container('EasyInstall');
        if (isset($container->addons)) {
            $this->addons = $container->addons;
            if (!$this->isEmpty()) {
                return $this->addons;
            }
        }

        $addons = [];
        foreach ($this->types() as $addonType) {
            $addons[$addonType] = $this->listAddonsForType($addonType);
        }

        $this->addons = $addons;
        $this->cacheAddons();
        return $this->addons;
    }

    /**
     * Helper to save addons in the cache.
     */
    protected function cacheAddons()
    {
        $container = new Container('EasyInstall');
        $container->setExpirationSeconds($this->expirationSeconds);
        $container->setExpirationHops($this->expirationHops);
        $container->addons = $this->addons;
    }

    /**
     * Check if the lists of addons are empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        if (empty($this->addons)) {
            return true;
        }
        foreach ($this->addons as $addons) {
            if (!empty($addons)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the list of default types.
     *
     * @return array
     */
    public function types()
    {
        return array_keys($this->data);
    }

    /**
     * Get addon data.
     *
     * @param string $url
     * @param string $type
     * @return array
     */
    public function dataForUrl($url, $type)
    {
        return $this->addons && isset($this->addons[$type][$url])
            ? $this->addons[$type][$url]
            : [];
    }

    /**
     * Check if an addon is installed.
     *
     * @param array $addon
     * @return bool
     */
    public function dirExists($addon)
    {
        $destination = OMEKA_PATH . $this->data[$addon['type']]['destination'];
        $existings = $this->listDirsInDir($destination);
        $existings = array_map('strtolower', $existings);
        return in_array(strtolower($addon['dir']), $existings)
            || in_array(strtolower($addon['basename']), $existings);
    }

    /**
     * Helper to list the addons from a web page.
     *
     * @param string $type
     * @return array
     */
    protected function listAddonsForType($type)
    {
        if (!isset($this->data[$type]['source'])) {
            return [];
        }
        $source = $this->data[$type]['source'];

        $content = $this->fileGetContents($source);
        if (empty($content)) {
            return [];
        }

        switch ($type) {
            case 'module':
            case 'theme':
                return $this->extractAddonList($content, $type);
            case 'omekamodule':
            case 'omekatheme':
                return $this->extractAddonListFromOmeka($content, $type);
        }
    }

    /**
     * Helper to get content from an external url.
     *
     * @param string $url
     * @return string
     */
    protected function fileGetContents($url)
    {
        $uri = new HttpUri($url);
        $client = $this->httpClient;
        $client->reset();
        $client->setUri($uri);
        $response = $client->send();
        $response = $response->isOk() ? $response->getBody() : null;

        if (empty($response)) {
            $this->getController()->messenger()->addError(
                new Message('Unable to fetch the url %s.', $url) // @translate
            );
        }

        return $response;
    }

    /**
     * Helper to parse a csv file to get urls and names of addons.
     *
     * @param string $csv
     * @param string $type
     * @return array
     */
    protected function extractAddonList($csv, $type)
    {
        $list = [];

        $addons = array_map('str_getcsv', explode(PHP_EOL, $csv));
        $headers = array_flip($addons[0]);

        foreach ($addons as $key => $row) {
            if ($key == 0 || empty($row) || !isset($row[$headers['Url']])) {
                continue;
            }

            $url = $row[$headers['Url']];
            $name = $row[$headers['Name']];
            $version = $row[$headers['Last version']];
            $addonName = preg_replace('~[^A-Za-z0-9]~', '', $name);
            $server = strtolower(parse_url($url, PHP_URL_HOST));
            $dependencies = empty($headers['Dependencies']) || empty($row[$headers['Dependencies']])
                ? []
                : array_filter(array_map('trim', explode(',', $row[$headers['Dependencies']])));

            $zip = $row[$headers['Last released zip']];
            if (!$zip) {
                switch ($server) {
                    case 'github.com':
                        $zip = $url . '/archive/master.zip';
                        break;
                    case 'gitlab.com':
                        $zip = $url . '/repository/archive.zip';
                        break;
                    default:
                        $zip = $url . '/master.zip';
                        break;
                }
            }

            $addon = [];
            $addon['type'] = $type;
            $addon['server'] = $server;
            $addon['name'] = $name;
            $addon['basename'] = basename($url);
            $addon['dir'] = $addonName;
            $addon['version'] = $version;
            $addon['zip'] = $zip;
            $addon['server'] = $server;
            $addon['dependencies'] = $dependencies;

            $list[$url] = $addon;
        }

        return $list;
    }

    /**
     * Helper to parse html to get urls and names of addons.
     *
     * @todo Manage dependencies for addon from omeka.org.
     *
     * @param string $html
     * @param string $type
     * @return array
     */
    protected function extractAddonListFromOmeka($html, $type)
    {
        $list = [];

        libxml_use_internal_errors(true);
        $htmlDom = new DOMDocument();
        $htmlDom->loadHTML($html);
        $xpath = new DOMXPath($htmlDom);

        // New format is the one of Github: /TagVersion/NameGivenByAuthor.zip.
        switch ($type) {
            case 'omekamodule':
                $type = 'module';
                $query = '//div[@id="module-list"]/div[@class="module"]/div[@class="download"]/a[@class="button"]/@href';
                break;
            case 'omekatheme':
                $type = 'theme';
                $query = '//div[@id="theme-list"]/div[@class="theme"]/div[@class="download"]/a[@class="button"]/@href';
                break;
            default:
                return [];
        }

        $rows = $xpath->query($query);
        if ($rows->length <= 0) {
            $htmlDom = new DOMDocument();
            $htmlDom->loadHTML($html);
            $xpath = new DOMXPath($htmlDom);
            $rows = $xpath->query($query);
            if ($rows->length <= 0) {
                return [];
            }
        }

        foreach ($rows as $row) {
            $url = $row->nodeValue;
            // $filename = basename(parse_url($url, PHP_URL_PATH));
            $query = '//a[@href="' . $url . '"]/../../div/h4/a';
            $nameRows = $xpath->query($query);
            if (empty($nameRows)) {
                continue;
            }
            $name = $nameRows->item(0)->nodeValue;

            $query = '//a[@href="' . $url . '"]/../span[@class="version"]';
            $versionRows = $xpath->query($query);
            $version = $versionRows->item(0)->nodeValue;
            $version = trim(str_replace('Latest Version:', '', $version));

            $query = '//a[@href="' . $url . '"]/../../div/h4/a/@href';
            $addonRows = $xpath->query($query);
            $addonName = $addonRows->item(0)->nodeValue;

            $server = strtolower(parse_url($url, PHP_URL_HOST));
            $zip = $url;

            $addon = [];
            $addon['type'] = $type;
            $addon['server'] = 'omeka.org';
            $addon['name'] = $name;
            $addon['basename'] = $addonName;
            $addon['dir'] = $addonName;
            $addon['version'] = $version;
            $addon['zip'] = $zip;
            $addon['server'] = $server;
            $addon['dependencies'] = [];

            $list[$url] = $addon;
        }

        return $list;
    }

    /**
     * List directories in a directory, not recursively.
     *
     * @param string $dir
     * @return array
     */
    protected function listDirsInDir($dir)
    {
        static $dirs;

        if (isset($dirs[$dir])) {
            return $dirs[$dir];
        }

        if (empty($dir) || !file_exists($dir) || !is_dir($dir) || !is_readable($dir)) {
            return [];
        }

        $list = array_filter(array_diff(scandir($dir), ['.', '..']), function ($file) use ($dir) {
            return is_dir($dir . DIRECTORY_SEPARATOR . $file);
        });

        $dirs[$dir] = $list;
        return $dirs[$dir];
    }
}
