<?php
/**
 * HeydayXhprofController
 *
 * @category SilverStripe_Module
 * @package  Heyday
 * @author   Cam Spiers <cameron@heyday.co.nz>
 * @license  http://www.opensource.org/licenses/MIT MIT
 * @link     http://heyday.co.nz
 */

/**
 * HeydayXhprofController Provides sake callable actions for the xprof module
 *
 * @category SilverStripe_Module
 * @package  Heyday
 * @author   Cam Spiers <cameron@heyday.co.nz>
 * @license  http://www.opensource.org/licenses/MIT MIT
 * @link     http://heyday.co.nz
 */
class HeydayXhprofController extends Controller
{

    /**
     * Allowed actions
     * @var array
     */
    public static $allowed_actions = array(
        'enable',
        'disable'
    );

    /**
     * Init method to check permissions
     *
     * @return null
     */
    public function init()
    {
        if (!Director::is_cli() && !Permission::check('ADMIN')) {
            user_error('No access allowed');
            exit;
        }

        parent::init();
    }

    /**
     * Lists available commands
     *
     * @return null
     */
    public function index()
    {

        echo implode(
            PHP_EOL,
            array(
                'Commands available:',
                'sake xhprof/enable',
                'sake xhprof/disable'
            )
        ), PHP_EOL;

        exit;

    }

    /**
     * Enable or disable global profiling
     *
     * @param SS_HTTPRequest $request Request for action
     *
     * @return null
     */
    public function enable($request)
    {
        $backupFileName = XHPROF_BASE_PATH . '/code/GlobalProfile/backup/backup.htaccess';
        $htaccessFileName = BASE_PATH . '/.htaccess';

        if (!file_exists($backupFileName)) {
            rename($htaccessFileName, $backupFileName);
            file_put_contents($htaccessFileName, $this->globalIncludes() . file_get_contents($backupFileName));

            return 'Done' . PHP_EOL;
        } else {
            return "It appears that global profiling is already enabled as a backup file exists." . PHP_EOL;
        }

    }

    public function disable()
    {
        $backupFileName = XHPROF_BASE_PATH . '/code/GlobalProfile/backup/backup.htaccess';
        $htaccessFileName = BASE_PATH . '/.htaccess';

        if (file_exists($backupFileName)) {
            unlink($htaccessFileName);
            rename($backupFileName, $htaccessFileName);

            return 'Done' . PHP_EOL;
        } else {
            return "It appears that global profiling is not enabled as there is no backup file to restore from." . PHP_EOL;
        }
    }

    /**
     * Gets content for .htaccess file based on project directory
     *
     * @return string
     */
    public function globalIncludes()
    {

        $dir = realpath(__DIR__ . '/GlobalProfile');

        return <<<HTACCESS
php_value auto_prepend_file $dir/Start.php

HTACCESS;

    }

}
