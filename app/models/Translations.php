<?php

namespace models;

use models\Model;
use PDO;

class Translations extends Model {

    public function getAllTranslations() {
       
        $query = "select originalMessage.id, originalMessage.originalText, corporateMessage.newText 
                 FROM originalMessage
                 LEFT JOIN corporateMessage ON originalMessage.id = corporateMessage.originalMessageID";
        return $this->query($query);
    }

    public function getTranslationsById($id){
        $query = "select originalMessage.id, originalMessage.originalText, corporateMessage.newText 
                 FROM originalMessage
                 LEFT JOIN corporateMessage ON originalMessage.id = corporateMessage.originalMessageID
                 WHERE originalMessage.id = :id";
        return $this->query($query, ['id' => $id]);
    }

   public function saveTranslations($inputData){
     
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER);
        
        $con->beginTransaction();
        
        try {
            // First insert the original message
            $stmt1 = $con->prepare("INSERT INTO originalMessage (originalText) VALUES (:originalText)");
            $stmt1->execute(['originalText' => $inputData['originalText']]);
            
            // Get the last inserted ID directly from the connection
            $lastId = $con->lastInsertId();
            
            // Then insert the corporate message
            $stmt2 = $con->prepare("INSERT INTO corporateMessage (newText, originalMessageID) VALUES (:newText, :originalMessageID)");
            $stmt2->execute([
                'newText' => $inputData['newText'],
                'originalMessageID' => $lastId
            ]);
            
           
            $con->commit();
            return true;
            
        } catch (\Exception $e) {
            // Roll back the transaction if something failed
            $con->rollBack();
            return false;
        }
    }

    public function getLastInsertId() {
        
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER);
        return $con->lastInsertId();
    }

    public function deleteTranslations($inputData){
        
        $query1 = "DELETE FROM corporateMessage WHERE originalMessageID = :id";
        $this->query($query1, ['id' => $inputData['id']]);
        
        $query2 = "DELETE FROM originalMessage WHERE id = :id";
        return $this->query($query2, ['id' => $inputData['id']]);
    }
}
