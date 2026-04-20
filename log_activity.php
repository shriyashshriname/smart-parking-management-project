<?php
function log_activity($conn, $user_id, $action, $detail=''){
    $user_id = $user_id ? intval($user_id) : 'NULL';
    $action = $conn->real_escape_string($action);
    $detail = $conn->real_escape_string($detail);
    $ip = $conn->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $conn->query("INSERT INTO activity_logs (user_id,action,detail,ip_address) VALUES ($user_id,'$action','$detail','$ip')");
}
?>
