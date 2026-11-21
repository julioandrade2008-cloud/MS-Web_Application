<?php
require_once __DIR__.'/../config/db.php';
$pdo=openDatabase();
$pdo->exec("CREATE TABLE IF NOT EXISTS users (user_id INTEGER PRIMARY KEY, full_name TEXT NOT NULL, email TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, role TEXT NOT NULL CHECK(role IN ('Student','Tutor','Administrator')), status TEXT NOT NULL DEFAULT 'Active' CHECK(status IN ('Active','Inactive')), created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)");
$columns=$pdo->query('PRAGMA table_info(students)')->fetchAll();
if (!in_array('user_id',array_column($columns,'name'),true)) $pdo->exec('ALTER TABLE students ADD COLUMN user_id INTEGER REFERENCES users(user_id)');
$pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_students_user_id ON students(user_id)');
echo "Migration complete.\n";
