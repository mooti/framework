<?php
/*
* Main xizlr framework class
*
* @author Ken Lalobo
*
*/

namespace Mooti\Xizlr\Core;

use \Mooti\Xizlr\Core\Interfaces\Config;
use \Mooti\Xizlr\Core\Interfaces\Framework as FrameworkInterface;

class Framework implements FrameworkInterface
{
    /**
     * @var \Pimple The dependancy injection container
     */
    private $container;

    /**
     * @var \Mooti\Xizlr\Core\Interfaces\Config The config object
     */
    public $config;

    /**
     * @param \Mooti\Xizlr\Core\Interfaces\Config $config      The config object
     * @param array                               $serverVars  The server variables
     * @param array                               $requestVars The request variables
     */
    public function __construct(Config $config, $serverVars = array(), $requestVars = array())
    {
        
        $this->config = $config;

        $congfigModule = $config->get('module');

        $this->container = new \Pimple($config);

        /*$this->dic['mooti.service.logger'] = function ($config) {
            $configServices = $config->get('services');
            $logger = new $configServices['logger']['class']($config);//'\Mooti\Xizlr\Core\Logger');
            $logger->setModuleName($congfigModule['name']);
            return new $logger;
        };*/

        //$request       = $this->instantiate($configServices['request']['class'], $config, $serverVars, $requestVars);//'\Mooti\Xizlr\Core\Request'
        //$response      = $this->instantiate($configServices['response']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$cache         = $this->instantiate($configServices['cache']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$session       = $this->instantiate($configServices['session']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$rdbms         = $this->instantiate($configServices['rdbms']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$documentStore = $this->instantiate($configServices['documentStore']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$search        = $this->instantiate($configServices['search']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$fileService   = $this->instantiate($configServices['fileService']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
    }

    /**
     * @param string $configName The name of the config
     */
    public function getConfig($configName)
    {
        return $this->config->getConfig($configName);
    }

    /**
     * @param string $configName  The name of the config
     * @param array  $configValue  The value of the config
     */
    public function setConfig($configName, array $configValue)
    {
        $this->config->setConfig($configName, $configValue);
    }
}