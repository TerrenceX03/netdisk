<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->group(['prefix' => 'api'], function () use ($router) {
  $router->get('authors',  ['uses' => 'AuthorController@showAllAuthors']);

  $router->get('authors/{id}', ['uses' => 'AuthorController@showOneAuthor']);

  $router->post('authors', ['uses' => 'AuthorController@create']);

  $router->delete('authors/{id}', ['uses' => 'AuthorController@delete']);

  $router->put('authors/{id}', ['uses' => 'AuthorController@update']);
});


$router->get('/subgrouptest', ['as'=>'test', function(){
  echo 'subgroup test';
}]);

$router->get('/hello/{name}', function($name){
	echo 'hello '.$name.'<br>';
});

// $router->get('/{folderName}/{name}', function($args1, $args2){
// 	echo $args1.' '.$args2;
// });

$router->post('/files', 'fileController@upload');

$router->get('/files', 'fileController@download');
//$router->get('/fileDownload/{uid}?version=102', 'fileController@idDownload');

$router->delete('/files', 'fileController@delete');
//$router->delete('/fileDelete/{id}', 'fileController@idDelete');

$router->get('/files/list', 'fileController@list');

$router->put('/files/{id}', 'fileController@update');