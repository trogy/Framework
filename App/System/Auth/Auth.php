<?php
/* 
TGNZ Auth
A Part of the Trogy.NZ Framework
(c) Marc Anderson (Trogy.NZ) 2021-2022
marc.anderson@trogy.nz
*/
namespace TGNZ;
use \PDO;
use Logger;
use OTPHP\TOTP;
logger::info('Loading!');
class Auth{
    /*-----------------------------------------------------
    TGNZ Auth
    Login Submit
    USAGE:
    $Login = new Auth();
    $Login->Login_Submit($Email(STRING), $Password(STRING), $TOTP(INT)[6 Characters]);
    RETURNS:
        Login_Status(BOOL)[True Means Login Succeeded]
        User_ID(INT)
        First_Name(STRING)
        Last_Name(STRING)
        Email(STRING)
    -----------------------------------------------------*/
    public function Login_Submit($Email, $Password, $TOTP){
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ?");
        $stmt->execute([$Email]);
        $user = $stmt->fetch();
        if(is_bool($user)){ 
            $this->Login_Status = false;
            return $this;
        }
        if ($user['Email'] == $Email){
            $this->Login_Status = "Username Found";
            if(password_verify($Password, $user['Password'])){
                if(!empty($user['TOTP_Secret'])){
                    $this->TwoFactor_Status = true;
                    $totp_obj = TOTP::create($user['TOTP_Secret']);
                    $OTP = $totp_obj->now();
                    if($OTP == $TOTP){
                        //OTP is correct
                        $this->Login_Status = true;
                        $this->User_ID = $user['ID'];
                        $this->First_Name = $user['First_Name'];
                        $this->Last_Name = $user['Last_Name'];
                        $this->Email = $user['Email'];
                        return $this;
                    }
                    else{
                        $this->Error = "TOTP Error";
                        $this->Login_Status = false;
                        return $this;
                    }
                }
                else{
                    //NO 2FA Present Continue
                    $this->TwoFactor_Status = false;
                    $this->Login_Status = true;
                    $this->User_ID = $user['ID'];
                    $this->First_Name = $user['First_Name'];
                    $this->Last_Name = $user['Last_Name'];
                    $this->Email = $user['Email'];
                    return $this;
                }
            }
            else{
                //PASSWORD ERROR
                $this->Error = "Username or Password Error";
                $this->Login_Status = false;
                return $this;
            }
        } 
        else{
            //USERNAME ERROR
            $this->Error = "Username or Password Error";
            $this->Login_Status = false;
            return $this->Login_Status;

        }
    }

    /*-------------------------------------------------
    TGNZ Auth
    User creation
    GLOBAL $pdo MUST BE SET
    Usage:
    Auth::Create_User($Username(STRING), $Password(STRING), $Email(STRING), $First_Name(STRING), $Last_Name(STRING))
    Returns: TRUE or FALSE    
    ---------------------------------------------------*/
    public static function Create_User($Username, $Password, $Email, $First_Name, $Last_Name){
        //Set Variables
        global $pdo;
        $Password = password_hash($Password, PASSWORD_DEFAULT);
        try{
            //Prepare SQL
            $stmt = $pdo->prepare("INSERT INTO users (Username, Password, Email, First_Name, Last_Name) VALUES (?, ?, ?, ?, ?");
            //Execute SQL
            $stmt->execute([$Username, $Password, $Email, $First_Name, $Last_Name]);
            //Check if Logger class exists
            if(class_exists('Logger')){
                //Log
                logger::info('User Created');
            }
            //Return True
            return true;
        }
        //Catch Error
        catch(PDOException $e){
            //Check if error is 1062 duplicate unique key
            if($e->errorInfo[1] == 1062){
                //Duplicate Username
                if(class_exists('Logger')){
                    logger::error('User Creation Failed: Duplicate Username');
                }
                return false;
            }
            else{
                //Other Error
                if(class_exists('Logger')){
                    logger::error('User Creation Failed: '.$e->errorInfo[2]);
                }
                return false;
            }
        }
    }

    /*-------------------------------------------------
    TGNZ Auth
    Delete User
    GLOBAL $pdo MUST BE SET
    Usage:
    Auth::Delete_User($Username(STRING))[$Username may be Username or Email]
    Returns: TRUE or FALSE
    ---------------------------------------------------*/
    public static function Delete_User($Username){
        //Set Variables
        global $pdo;
        try{
            //Prepare SQL
            $stmt = $pdo->prepare("DELETE FROM users WHERE Username OR Email = ?");
            //Execute SQL
            $stmt->execute([$Username]);
            //Check if Logger class exists
            if(class_exists('Logger')){
                //Log
                logger::info('User Deleted');
            }
            //Return True
            return true;
        }
        //Catch Errors
        catch(PDOException $e){
            //Log Error and return false
            if(class_exists('Logger')){
                logger::error('User Deletion Failed' . $e->getMessage());
            }
            return false;
        }
    }

    /*-------------------------------------------------
    TGNZ Auth
    Change User Password
    GLOBAL $pdo MUST BE SET
    Usage:
    Auth::Change_User_Password($Username(STRING), $Password(STRING))[$Username may be Username or Email]
    Returns: TRUE or FALSE
    ---------------------------------------------------*/
    public static function Change_User_Password($Username, $Password){
        //Set Variables
        global $pdo;
        $Password = password_hash($Password, PASSWORD_DEFAULT);
        try{
            //Prepare SQL
            $stmt = $pdo->prepare("UPDATE users SET Password = ? WHERE Username OR Email = ?");
            //Execute SQL
            $stmt->execute([$Password, $Username]);
            //Check if Logger class exists
            if(class_exists('Logger')){
                //Log
                logger::info('User Password Changed');
            }
            //Return True
            return true;
        }
        //Catch Errors
        catch(PDOException $e){
            //Log Error and return false
            if(class_exists('Logger')){
                logger::error('User Password Change Failed' . $e->getMessage());
            }
            return false;
        }
    }
    /*-------------------------------------------------
    TGNZ Auth
    Create New TOTP (2FA Secret)
    GLOBAL $pdo MUST BE SET
    OTPHP MUST BE INSTALLED
    Usage:
    $TOTP = new Auth->Create_New_TOTP($Username(STRING))[$Username may be Username or Email]
    Returns: 
    $this->Success(BOOLEAN)
    $this->TOTP_Provisioning_URI(STRING) [WILL RETURN 0 IF FAILED]
    ---------------------------------------------------*/
    public function Create_New_TOTP($Username){
        //Set Variables
        global $pdo;
        global $FW_APP_NAME;
        $TOTP = TOTP::create();
        $TOTP->setLabel( $FW_APP_NAME . '(' . $Username . ')');
        $TOTP_Secret = $TOTP->getSecret();
        try{
            //Prepare SQL
            $stmt = $pdo->prepare("UPDATE users SET TOTP_Secret = ? WHERE Username OR Email = ?");
            //Execute SQL
            $stmt->execute([$TOTP_Secret, $Username]);
            //Check if Logger class exists
            if(class_exists('Logger')){
                //Log
                logger::info('User TOTP Secret Created');
            }
            //Return True
            return $this->TOTP_Provisioning_URI = $TOTP->getProvisioningUri();
            return $this->Success = true;
        }
        //Catch Errors
        catch(PDOException $e){
            //Log Error and return false
            if(class_exists('Logger')){
                logger::error('User TOTP Secret Creation Failed' . $e->getMessage());
            }
            return $this->TOTP_Provisioning_URI = 0;
            return $this->Success = false;
        }
    }
}
