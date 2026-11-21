<?php
function insertTutoringRequest($pdo, $studentId, $data) {
    $stmt=$pdo->prepare('INSERT INTO tutoring_requests(student_id,course,topic,preferred_date,preferred_time) VALUES(?,?,?,?,?)');
    $stmt->execute([$studentId,$data['course'],$data['topic'],$data['preferred_date'],$data['preferred_time']]);
    return (int)$pdo->lastInsertId();
}
function findRequest($pdo, $id) {
    $stmt=$pdo->prepare('SELECT r.*,s.student_name,s.student_email FROM tutoring_requests r JOIN students s ON r.student_id=s.student_id WHERE r.request_id=?');
    $stmt->execute([$id]);return $stmt->fetch();
}
function listRequests($pdo, $course = '', $status = '') {
    $sql='SELECT r.*,s.student_name FROM tutoring_requests r JOIN students s ON r.student_id=s.student_id WHERE 1=1';$args=[];
    if($course!==''){$sql.=' AND r.course = ?';$args[]=$course;}
    if($status!==''){$sql.=' AND r.status = ?';$args[]=$status;}
    $sql.=' ORDER BY r.request_id DESC';$stmt=$pdo->prepare($sql);$stmt->execute($args);return $stmt->fetchAll();
}
function updateOpenRequest($pdo, $id, $data) {
    $stmt=$pdo->prepare("UPDATE tutoring_requests SET course=?,topic=?,preferred_date=?,preferred_time=? WHERE request_id=? AND status='Open'");
    $stmt->execute([$data['course'],$data['topic'],$data['preferred_date'],$data['preferred_time'],$id]);return $stmt->rowCount()===1;
}
function cancelOpenRequest($pdo, $id) {
    $stmt=$pdo->prepare("UPDATE tutoring_requests SET status='Cancelled' WHERE request_id=? AND status='Open'");
    $stmt->execute([$id]);return $stmt->rowCount()===1;
}

function findOwnedRequest(PDO $pdo,int $id,int $userId) { $s=$pdo->prepare('SELECT r.*,st.student_name,st.student_email FROM tutoring_requests r JOIN students st ON r.student_id=st.student_id WHERE r.request_id=? AND st.user_id=?');$s->execute([$id,$userId]);return $s->fetch(); }
function listOwnedRequests(PDO $pdo,int $userId,string $course='',string $status=''): array { $sql='SELECT r.*,st.student_name FROM tutoring_requests r JOIN students st ON r.student_id=st.student_id WHERE st.user_id=?';$args=[$userId];if($course!==''){$sql.=' AND r.course=?';$args[]=$course;}if($status!==''){$sql.=' AND r.status=?';$args[]=$status;}$sql.=' ORDER BY r.request_id DESC';$s=$pdo->prepare($sql);$s->execute($args);return $s->fetchAll(); }
function updateOwnedOpenRequest(PDO $pdo,int $id,int $userId,array $data): bool { $s=$pdo->prepare("UPDATE tutoring_requests SET course=?,topic=?,preferred_date=?,preferred_time=? WHERE request_id=? AND status='Open' AND student_id IN (SELECT student_id FROM students WHERE user_id=?)");$s->execute([$data['course'],$data['topic'],$data['preferred_date'],$data['preferred_time'],$id,$userId]);return $s->rowCount()===1; }
function cancelOwnedOpenRequest(PDO $pdo,int $id,int $userId): bool { $s=$pdo->prepare("UPDATE tutoring_requests SET status='Cancelled' WHERE request_id=? AND status='Open' AND student_id IN (SELECT student_id FROM students WHERE user_id=?)");$s->execute([$id,$userId]);return $s->rowCount()===1; }
