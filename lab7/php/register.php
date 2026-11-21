<?php
require_once __DIR__.'/../config/db.php';require_once __DIR__.'/../services/userService.php';require_once __DIR__.'/../includes/auth.php';
startSecureSession();$error='';$success='';if($_SERVER['REQUEST_METHOD']==='POST'){verifyCsrfToken($_POST);try{$id=registerUser(openDatabase(),$_POST);$success='Account created. You may now log in.';}catch(InvalidArgumentException $e){$error=$e->getMessage();}catch(Throwable $e){error_log($e->getMessage());$error='Unable to create account.';}}
require __DIR__.'/../includes/header.php';require __DIR__.'/../includes/nav.php';?>
<main class="container py-4"><h1>Create account</h1><?php if($error):?><div class="alert alert-danger"><?=htmlspecialchars($error,ENT_QUOTES,'UTF-8')?></div><?php endif;?><?php if($success):?><div class="alert alert-success"><?=$success?> <a href="login.php">Log in</a></div><?php endif;?>
<form method="post"><?=csrfField()?>
<label class="form-label">Full name<input class="form-control" name="full_name" required maxlength="120"></label><br>
<label class="form-label">Email<input class="form-control" type="email" name="email" required></label><br>
<label class="form-label">Password (12+ characters)<input class="form-control" type="password" name="password" minlength="12" required></label><br>
<label class="form-label">Confirm password<input class="form-control" type="password" name="confirm_password" required></label><br>
<label class="form-label">Account type<select class="form-select" name="role"><option value="Student">Student</option><option value="Tutor">Tutor</option></select></label><br><button class="btn btn-primary">Create account</button></form></main><?php require __DIR__.'/../includes/footer.php';?>
