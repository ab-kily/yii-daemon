<?php

require_once dirname(__FILE__) . '/lib/System/Daemon.php';

class SystemDaemon extends \CApplicationComponent
{
    const LOG_FILE_NAME = 'daemon.log';

    public $appName;

    public $sysMaxExecutionTime = 0;

    public $sysMaxInputTime = 0;

    public $sysMemoryLimit = '128M';

    public $logFile = '';

    public $runAsGID = 0;

    public $runAsUID = 0;

    public function init()
    {
        if(!$this->appName) {
            throw new CException('Invalid application name for daemon.');
        }

        if ($this->logFile=='') {
            $this->logFile = implode(DIRECTORY_SEPARATOR,
                array(Yii::getPathOfAlias('application'),
                    'runtime',
                    self::LOG_FILE_NAME
                ));
        }

        if(!file_exists($this->logFile)) {
            if (!touch($this->logFile)) {
                throw new CException('Invalid log file ' . $this->logFile);
            }
        }

        Yii::registerAutoloader(array('System_Daemon', 'autoload'));

        System_Daemon::setOptions(array(
            'appName' => $this->appName,
            'appDir' => Yii::getPathOfAlias('application'),
            'sysMaxExecutionTime' => $this->sysMaxExecutionTime,
            'sysMaxInputTime' => $this->sysMaxInputTime,
            'sysMemoryLimit' => $this->sysMemoryLimit,
            'appRunAsGID' => $this->runAsGID,
            'appRunAsUID' => $this->runAsUID,
            'logLocation' => $this->logFile
        ));
    }

    /**
     * Any requests to set or get attributes or call methods on this class that
     * are not found are redirected to the {@link System_Daemon} object.
     *
     * @param string $name the method name
     * @param array $parameters
     * @return mixed
     */
    public function __call($name, $parameters) {
        if(method_exists('System_Daemon', $name)) {
            return call_user_func_array(array('System_Daemon', $name), $parameters);
        } else {
            return parent::__call($name, $parameters);
        }
    }
}