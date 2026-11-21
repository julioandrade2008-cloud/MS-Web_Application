<?php
require_once __DIR__.'/../config/db.php';require_once __DIR__.'/../services/requestService.php';require_once __DIR__.'/../includes/auth.php';$pdo=openDatabase();$user=requireLogin($pdo);requireRole($user,['Student','Administrator']);
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('POST required');}
verifyCsrfToken($_POST);$id=filter_input(INPUT_POST,'request_id',FILTER_VALIDATE_INT);if(!$id||$id<1){http_response_code(400);exit('Invalid ID');}
try{$r=requireRequestAccess($pdo,$user,$id);[$errors,$data]=validateSubmission($_POST,false);if($errors)throw new InvalidArgumentException(implode(' ',$errors));$ok=$user['role']==='Administrator'?updateOpenRequest($pdo,$id,$data):updateOwnedOpenRequest($pdo,$id,(int)$user['user_id'],$data);if(!$ok){http_response_code(409);exit('Request not found or not Open');}header('Location: request-details.php?request_id='.$id,true,303);exit;}
catch(InvalidArgumentException $e){http_response_code(422);exit(escapeHtml($e->getMessage()));}
catch(Throwable $e){error_log($e->getMessage());http_response_code(500);exit('Unable to update request');}
