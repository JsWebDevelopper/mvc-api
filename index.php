<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

$request = explode('/', $_SERVER['REQUEST_URI']);
$Method = $_SERVER['REQUEST_METHOD'] ;

require_once('src/controllers/comment/add.php');
require_once('src/controllers/comment/update.php');
require_once('src/controllers/homepage.php');
require_once('src/controllers/post.php');
require_once('src/controllers/user.php');
require_once('src/env.php');

use Application\Controllers\Comment\Add\AddComment;
use Application\Controllers\Comment\Update\UpdateComment;
use Application\Controllers\Homepage\Homepage;
use Application\Controllers\Post\Post;
use Application\Controllers\User\User;
//use Firebase\JWT\JWT;

try {
    if (isset($_GET['action']) && $_GET['action'] !== '') {
        if ($_GET['action'] === 'post') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $identifier = $_GET['id'];

                (new Post())->execute($identifier);
            } else {
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        } 
        elseif ($_GET['action'] === 'user') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $identifier = $_GET['id'];

                (new User())->test($identifier);
            } else {
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        }elseif ($_GET['action'] === 'addComment') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $identifier = $_GET['id'];

                (new AddComment())->execute($identifier, $_POST);
            } else {
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        } elseif ($_GET['action'] === 'updateComment') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $identifier = $_GET['id'];
                // It sets the input only when the HTTP method is POST (ie. the form is submitted).
                $input = null;
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input = $_POST;
                }

                (new UpdateComment())->execute($identifier, $input);
            } else {
                throw new Exception('Aucun identifiant de commentaire envoyé');
            }
        } else {
            throw new Exception("La page que vous recherchez n'existe pas.");
        }
    } else {
        // 
        if(count($request)>=3)
        {
            switch ($request[2]) {                
                case "users":        
                    if(is_numeric($request[3]) && $Method === 'GET' && isValidToken($secret_Key,$domainName)) {
                        $identifier = $request[3];        
                        (new User())->One($identifier);
                    }
                    elseif($request[3] === 'register' && $Method === 'POST') {                                
                        (new User())->Register($_POST);
                    }
                    elseif($request[3] === 'login' && $Method === 'POST') {                                
                        (new User())->Login($_POST,$request_data,$secret_Key);
                    }
                    elseif(is_numeric($request[3]) && $Method === 'DELETE' && isValidToken($secret_Key,$domainName)) { 
                        $identifier = $request[3];                               
                        (new User())->DelOne($identifier);
                    }
                    elseif($request[3] === 'update' && $Method === 'POST' && isValidToken($secret_Key,$domainName)) { 
                        (new User())->UpdateOne($_POST);
                    }                     
                    else {
                        throw new Exception(' Aucun identifiant de billet envoyé ');
                    }
                break;
            
                
            
                default:
                    $errorMessage = '404';
                    require('templates/error.php');   
                    break;
                    }
        }
        else
            (new Homepage())->execute();
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();

    require('templates/error.php');
}
