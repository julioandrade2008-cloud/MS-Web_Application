<?php
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
require_once __DIR__.'/../config/db.php';require_once __DIR__.'/../services/userService.php';
if($argc!==4){fwrite(STDERR,"Usage: php create-admin.php NAME EMAIL PASSWORD\n");exit(1);}
[, $name,$email,$password]=$argv;$name=trim($name);$email=strtolower(trim($email));$errors=array_merge(validateIdentity($name,$email),validatePassword($password));if($errors){fwrite(STDERR,implode(' ',$errors)."\n");exit(1);}
$pdo=openDatabase();if(findUserByEmail($pdo,$email)){fwrite(STDERR,"Email already registered.\n");exit(1);}
$id=insertUser($pdo,$name,$email,password_hash($password,PASSWORD_DEFAULT),'Administrator');echo "Administrator created with user ID $id\n";
