<?php

    /** @var Router $router */


    /*
    |--------------------------------------------------------------------------
    | Application Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register all of the routes for an application.
    | It is a breeze. Simply tell Lumen the URIs it should respond to
    | and give it the Closure to call when that URI is requested.
    |
    */

    use App\UncleStepan\Entity\Bicycle;
    use App\UncleStepan\Entity\Bus;
    use App\UncleStepan\Entity\Car;
    use App\UncleStepan\Entity\Cross;
    use App\UncleStepan\Entity\Motocycle;
    use Laravel\Lumen\Routing\Router;

    $cross = new Cross();
    $transportClasses = [
        Bus::class => 0.1,
        Motocycle::class => 0.02,
        Bicycle::class => 0.08,
        Car::class => 0.8,
    ];
    $crossDirections = [
        'top' => 0.25,
        'left' => 0.25,
        'bottom' => 0.25,
        'right' => 0.25,
    ];

    $cross->setChanceDirection($crossDirections);
    $cross->setTransportSet($transportClasses);

    $router->get('/', function(){
        return view('crossway');
    });
    $router->get('/snap', function () use ($cross) {
        return response()->json(
            $cross->snap()
        );
    });

    $router->get('/addTransport', function (Cross $cross) {
        return '';
    });
