<?php

namespace Application\Controllers\User;

require_once('src/lib/database.php');
require_once('src/model/user.php');
require_once('src/env.php');

use Application\Lib\Database\DatabaseConnection;
use Application\Model\user\userRepository;
use Firebase\JWT\JWT;

class User
{
    public function One(string $identifier)
    {
        $connection = new DatabaseConnection();

        $userRepository = new userRepository();
        $userRepository->connection = $connection;
        $user = $userRepository->getuser($identifier);
        
        // echo json_encode(['token' => isValidToken($secret_Key,$domainName), 'users' => $users,'status' => true]);
        echo json_encode(['users' => $user,'status' => true]);
    }

    public function Register(array $input)
    {
        $isPassword = "/^(?=.*\d)(?=.*[a-z])(?=.*[$@$!%*?&])(?=.*[A-Z]).{8,20}$/";

        $user =  json_decode($input['user'], true);        
        extract($user);
        $created_at = date("Y-m-d H:i:s");

        if(!preg_match($isPassword, $password)){
                       
            $response = ['status' => 0, 'message' => 'Failed to create record Passsword.'];
            echo json_encode($response);            
        }
        elseif (empty($nom) || empty($prenom)) {
            $response = ['status' => 0, 'message' => 'Chmaps Obligatoire Vide !!!!'];
            echo json_encode($response); 
        } 
        else 
        {            

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0)
                $imageUrl= fileUpload($created_at,'uploads/profile/');
            else
                $imageUrl='imageUrl.jpg';                
            
            $user['isAdmin'] = 0;
            $user['imageUrl'] = $imageUrl;
            $user['created_at'] = $created_at;

            $UserRepository = new UserRepository();
            $UserRepository->connection = new DatabaseConnection();
            $success = $UserRepository->createUser($user);
            if (!$success) {                
                $response = ['status' => 0, 'message' => 'Failed to create record. Email existant ou Donnée non valide'];
            } else {
                $response = ['status' => 1, 'message' => 'Record created successfully.'];            
            }

            echo json_encode($response);
        }
    }

    public function Login(array $input,array $request_data, string $secret_Key)
    {
        $user =  json_decode($input['user'], true);        
        extract($user);
       
        if (empty($email) || empty($password)) {
            $response = ['status' => 0, 'message' => 'Chmaps Obligatoire Vide !!!!'];
            echo json_encode($response); 
        } 
        else 
        {            

            $UserRepository = new UserRepository();
            $UserRepository->connection = new DatabaseConnection();
            $userId = $UserRepository->loginUser($user);

            if ($userId===0) {                              
                // echo json_encode(['userId' => $userId]);
                $response = ['status' => 0, 'message' => 'Login ou password Problem.'];
            } else {
                $token = JWT::encode($request_data,$secret_Key,'HS512');
                $response = ['status' => 1, 'message' => 'Accees Autorisé','token' => $token, 'userId' => $userId];
                                          
            }

            echo json_encode($response);  

            
        }
    }

    public function DelOne(int $id)
    {
              
                  

            $UserRepository = new UserRepository();
            $UserRepository->connection = new DatabaseConnection();
            $success = $UserRepository->delelteUser($id);

            if($success)                  
                $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
            else
                $response = ['status' => 0, 'message' => 'Failed to delete record.']; 

                echo json_encode($response); 
        
    }

    public function UpdateOne(array $input)
    {
        $isPassword = "/^(?=.*\d)(?=.*[a-z])(?=.*[$@$!%*?&])(?=.*[A-Z]).{8,20}$/";

        $user =  json_decode($input['user'], true);        
        extract($user);
        $updated_at = date("Y-m-d H:i:s");

        if(!preg_match($isPassword, $password)){
                       
            $response = ['status' => 0, 'message' => 'Failed to create record Passsword.'];
            echo json_encode($response);            
        }
        elseif (empty($nom) || empty($prenom)) {
            $response = ['status' => 0, 'message' => 'Chmaps Obligatoire Vide !!!!'];
            echo json_encode($response); 
        } 
        else 
        {            

            $updateImage = false;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                $imageUrl= fileUpload($updated_at,'uploads/profile/');
                $updateImage = true;
            }
            else
                $imageUrl='imageUrl.jpg';
                
            
            $user['isAdmin'] = 0;
            $user['imageUrl'] = $imageUrl;
            $user['updated_at'] = $updated_at;

            $UserRepository = new UserRepository();
            $UserRepository->connection = new DatabaseConnection();
            $success = $UserRepository->updateUser($user,$updateImage);
            if ($success) {                
                $response = ['status' => 1, 'message' => 'Record updated successfully'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to update record.'];            
            }

            echo json_encode($response);
        }
    }

    
}
