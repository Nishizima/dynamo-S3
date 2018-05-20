<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\MiddlewareFactory;


/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

use function Zend\Stratigility\doublePassMiddleware;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/', App\Handler\HomePageHandler::class, 'home');
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');


    //$app->get('/user/list', App\User\UserListAction::class, 'user.list');
    $app->post('/v1/user/login', App\User\UserLoginAction::class, 'user.login');
    $app->get('/v1/user/list', [Auth\Action\AuthAction::class, App\User\UserListAction::class], 'user.list');
    $app->get('/v1/user/get/{key}', [Auth\Action\AuthAction::class, App\User\UserGetAction::class], 'user.get');
    $app->post('/v1/user/create', [Auth\Action\AuthAction::class, App\User\UserCreateAction::class], 'user.create');
    $app->put('/v1/user/update/{key}', [Auth\Action\AuthAction::class, App\User\UserUpdateAction::class], 'user.update');
    $app->delete('/v1/user/delete/{key}', [Auth\Action\AuthAction::class, App\User\UserDeleteAction::class], 'user.delete');

    $app->post('/v1/s3/upload', [Auth\Action\AuthAction::class, App\S3\S3UploadAction::class], 's3.upload');
    $app->get('/v1/s3/makeurl/{key}', [Auth\Action\AuthAction::class, App\S3\S3UrlAction::class], 's3.makeurl');

    $app->post('/v1/s3/noauth/upload',  App\S3\S3UploadAction::class, 's3.uploadnoauth');
    $app->get('/v1/s3/noauth/makeurl/{key}',  App\S3\S3UrlAction::class, 's3.makeurlnoauth');


};
