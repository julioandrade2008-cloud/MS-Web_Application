<?php
require_once dirname(__DIR__).'/services/userService.php';
require_once dirname(__DIR__).'/services/requestService.php';
$pdo=new PDO('sqlite::memory:');$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);$pdo->exec('PRAGMA foreign_keys=ON');
$pdo->exec(file_get_contents(dirname(__DIR__).'/database/setup-database.sql'));
$pdo->exec('CREATE TABLE users (user_id INTEGER PRIMARY KEY, full_name TEXT NOT NULL, email TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, role TEXT NOT NULL CHECK(role IN (\'Student\',\'Tutor\',\'Administrator\')), status TEXT NOT NULL DEFAULT \'Active\' CHECK(status IN (\'Active\',\'Inactive\')), created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
$pdo->exec('ALTER TABLE students ADD COLUMN user_id INTEGER REFERENCES users(user_id)');$pdo->exec('CREATE UNIQUE INDEX idx_students_user_id ON students(user_id)');
$pass=0;$fail=0;function testCheck($condition,$label){global $pass,$fail;if($condition){$pass++;echo "PASS $label\n";}else{$fail++;echo "FAIL $label\n";}}
function rejected(callable $fn): bool {try{$fn();return false;}catch(InvalidArgumentException|DomainException $e){return true;}}
$student=registerUser($pdo,['full_name'=>'Test Student','email'=>'student@example.org','password'=>'StrongPassword123!','confirm_password'=>'StrongPassword123!','role'=>'Student']);
$u=findUserById($pdo,$student);testCheck($u['role']==='Student'&&$u['status']==='Active','UM-01 student active');
testCheck($u['password_hash']!=='StrongPassword123!'&&password_verify('StrongPassword123!',$u['password_hash']),'UM-01 password hashed');
testCheck(rejected(fn()=>registerUser($pdo,['full_name'=>'Bad Admin','email'=>'bad@example.org','password'=>'StrongPassword123!','confirm_password'=>'StrongPassword123!','role'=>'Administrator'])),'UM-01 public administrator denied');
testCheck(rejected(fn()=>registerUser($pdo,['full_name'=>'Duplicate','email'=>'student@example.org','password'=>'StrongPassword123!','confirm_password'=>'StrongPassword123!','role'=>'Student'])),'UM-01 duplicate rejected');
testCheck((int)$pdo->query("SELECT COUNT(*) FROM students WHERE user_id=$student")->fetchColumn()===1,'UM-01 linked student profile');
$tutor=registerUser($pdo,['full_name'=>'Test Tutor','email'=>'tutor@example.org','password'=>'StrongPassword123!','confirm_password'=>'StrongPassword123!','role'=>'Tutor']);testCheck(findUserById($pdo,$tutor)['role']==='Tutor','UM-01 tutor registration');
testCheck(authenticateUser($pdo,'student@example.org','StrongPassword123!')!==false,'UM-09 valid login');testCheck(authenticateUser($pdo,'student@example.org','wrong')===false,'UM-09 invalid password denied');
changeOwnProfile($pdo,$student,'Updated Student','updated@example.org');testCheck(findUserById($pdo,$student)['email']==='updated@example.org','UM-04 profile update');testCheck((int)$pdo->query("SELECT COUNT(*) FROM students WHERE student_email='updated@example.org' AND user_id=$student")->fetchColumn()===1,'UM-04 linked profile synchronized');
testCheck(rejected(fn()=>changeOwnProfile($pdo,$student,'Updated Student','tutor@example.org')),'UM-04 duplicate email denied');
changeOwnPassword($pdo,$student,'StrongPassword123!','NewPassword456!','NewPassword456!');testCheck(authenticateUser($pdo,'updated@example.org','StrongPassword123!')===false&&authenticateUser($pdo,'updated@example.org','NewPassword456!')!==false,'UM-05 new password only');
testCheck(rejected(fn()=>changeOwnPassword($pdo,$student,'wrong','NewPassword789!','NewPassword789!')),'UM-05 incorrect current password denied');
$admin=insertUser($pdo,'Admin','admin@example.org',password_hash('AdminPassword123!',PASSWORD_DEFAULT),'Administrator');testCheck(count(searchUsers($pdo,'tutor'))===1,'UM-03 administrator search repository');
adminChangeStatus($pdo,$tutor,'Inactive');testCheck(authenticateUser($pdo,'tutor@example.org','StrongPassword123!')===false,'UM-07 inactive login denied');adminChangeStatus($pdo,$tutor,'Active');
adminChangeRole($pdo,$tutor,'Administrator');testCheck(findUserById($pdo,$tutor)['role']==='Administrator','UM-06 role update');
testCheck(rejected(fn()=>adminDeleteUser($pdo,$student)),'UM-08 deletion blocked pending policy');
testCheck(rejected(fn()=>adminChangeStatus($pdo,$admin,'Inactive'))===false,'UM-07 multiple administrators allow deactivation');
testCheck(rejected(fn()=>adminChangeStatus($pdo,$tutor,'Inactive')),'UM-07 last active admin protected');
$input=['course'=>'IS 6465','reason'=>'Help with PHP arrays and forms','topics'=>['Exam review'],'preferreddate'=>(new DateTimeImmutable('+10 days'))->format('Y-m-d'),'preferredtime'=>'Morning'];
$id=submitAuthenticatedRequest($pdo,findUserById($pdo,$student),$input);testCheck(findOwnedRequest($pdo,$id,$student)!==false,'Student owns created request');testCheck(findOwnedRequest($pdo,$id,$tutor)===false,'Another user cannot read request');
testCheck(rejected(fn()=>submitAuthenticatedRequest($pdo,findUserById($pdo,$tutor),$input)),'Non-student cannot create request');
[$errors,$data]=validateSubmission($input,false);testCheck(updateOwnedOpenRequest($pdo,$id,$tutor,$data)===false,'Other user cannot edit request');testCheck(cancelOwnedOpenRequest($pdo,$id,$tutor)===false,'Other user cannot cancel request');
testCheck(count(listOwnedRequests($pdo,$student))===1&&count(listOwnedRequests($pdo,$tutor))===0,'Ownership-filtered listing');
echo "Results: $pass passed, $fail failed\n";exit($fail?1:0);
