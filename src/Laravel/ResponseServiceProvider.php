<?php
namespace EllipseSynergie\ApiResponse\Laravel;

use Input;
use Illuminate\Support\ServiceProvider;
use EllipseSynergie\ApiResponse\Laravel\Response;
use League\Fractal\Manager;

/**
 * Class ResponseServiceProvider
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package EllipseSynergie\ApiResponse\Laravel
 * @author Maxime Beaudoin <maxime.beaudoin@ellipse-synergie.com>
 */
class ResponseServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register response macro
        \Response::macro('api', function () {

            $manager = new Manager;

            // Are we going to try and include embedded data?
            $manager->setRequestedScopes(explode(',', Input::get('embed')));

            // Return the Response object
            return new Response($manager);
        });
    }
}