<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        // Some managed MySQL hosts (e.g. Aiven) enforce sql_require_primary_key
        // by default, which breaks this app's use of CREATE TEMPORARY TABLE with
        // only a plain INDEX (no PRIMARY KEY) for report/stats aggregation.
        // Older local MySQL versions don't know this session variable, so ignore
        // failures silently instead of breaking every request in dev.
        try {
            db_connect()->simpleQuery('SET SESSION sql_require_primary_key = 0');
        } catch (\Throwable $e) {
            // Ignore: variable not supported on this MySQL version.
        }
    }
}
