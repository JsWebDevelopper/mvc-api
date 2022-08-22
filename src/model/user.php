<?php

namespace Application\Model\User;
require_once('src/lib/database.php');
use Application\Lib\Database\DatabaseConnection;

class User
{
    public int $id ;
    public string $nom ;
    public string $prenom ;
    public string $email ;
    public string $password ;
    public boolean $isAdmin ;
    public string $imageUrl ;
    public string $created_at ;
    public string $updated_at;
}

class UserRepository
{
    public DatabaseConnection $connection;
    public function getUser(string $identifier) : User
    {
        require_once "src/env.php";
        $statement = $this->connection->getConnection()->prepare(
            "SELECT * FROM Users WHERE id = ?"
        );
        $statement->execute([$identifier]);

        if ($row = $statement->fetch())  {  
            extract($row);       
            $user = new User();
            $user->nom = $nom; 
            $user->prenom = $prenom;
            $user->email = encrypt_decrypt("decrypt", $email);   
            $user->imageUrl = $imageUrl;
            
            return $user;
        }          
        else
            echo json_encode( ['message' => false]);
    }

    public function getUsers(): array
    {
        $statement = $this->connection->getConnection()->query(
            "SELECT id, title, content, DATE_FORMAT(creation_date, '%d/%m/%Y à %Hh%imin%ss') AS french_creation_date FROM Posts ORDER BY creation_date DESC LIMIT 0, 5"
        );
        $Users = [];
        while (($row = $statement->fetch())) {
            $User = new User();
            $User->title = $row['title'];
            $User->frenchCreationDate = $row['french_creation_date'];
            $User->content = $row['content'];
            $User->identifier = $row['id'];

            $Users[] = $User;
        }

        return $Users;
    }

    public function createUser(array $user): bool
    {
        require_once "src/env.php";
        extract($user);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $email = encrypt_decrypt("encrypt", $email);

        $sql = ("INSERT INTO users(id, nom, prenom, email,password,isAdmin,imageUrl, created_at) 
            VALUES(null, '$nom', '$prenom', '$email','$hash', '$isAdmin','$imageUrl', '$created_at')");
        $statement = $this->connection->getConnection()->prepare($sql);
        $affectedLines = $statement->execute();

        return ($affectedLines > 0);
    }

    public function loginUser(array $user): string
    {
        require_once "src/env.php";
        extract($user);
        $sendPass = $password;
        $email = encrypt_decrypt("encrypt", $email);

        $sql = "SELECT * FROM users WHERE email = '$email' limit 0,1" ;
        $statement = $this->connection->getConnection()->prepare($sql);
        $statement->execute();

        if ($row = $statement->fetch())  {  
            extract($row);
            if (password_verify($sendPass,$password))
                return $id;
            else
                return 0;
        }
        else
        {
            return 0;
        }
        
        
    }

    public function delelteUser(int $id): bool
    {
       $sql = "DELETE FROM users WHERE id = $id" ;
        $statement = $this->connection->getConnection()->prepare($sql);
        $affectedLines = $statement->execute();

        return ($affectedLines > 0);
    }

    public function updateUser(array $user, bool $updateImage ): bool
    {
        extract($user);

        if($updateImage)
            $sql = ("update users set nom = '$nom',imageUrl ='$imageUrl', updated_at = '$updated_at' where id = $id");
        else
             $sql = ("update users set nom = '$nom', updated_at = '$updated_at' where id = $id");
        
        $statement = $this->connection->getConnection()->prepare($sql);
        $affectedLines = $statement->execute();

        return ($affectedLines > 0);
    }

    
}
