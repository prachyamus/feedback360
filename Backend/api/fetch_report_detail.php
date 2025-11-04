<?php
include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

if(isset($_GET['sub_id']) && isset($_GET['person_id'])) {
    $sub_id = $_GET['sub_id'];
    $person_id = $_GET['person_id'];
    $res['employee'] = get_data($person_id);
    $res['group'] = report_group($sub_id);
    $res['averange'] = averange($person_id, $sub_id);
    $res['feedback'] = countfeedback($person_id, $sub_id);
    $res['spider'] = spider($person_id, $sub_id);
    $res['success'] = true;
    $res['message'] = 'ดึงข้อมูลสำเร็จ';
    echo json_encode($res);
}
function get_data($person_id){
    global $conn;
    $sql = "SELECT emp_id,emp_fname,emp_lname,emp_nickname,emp_position FROM user_list WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = $result->fetch_assoc();
    return $res;
}

function report_group($sub_id){
    global $conn;
    $sql = "SELECT * FROM question_group JOIN s_subject ON question_group.group_report = s_subject.type_group WHERE s_subject.sub_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function averange($person_id, $sub_id){
    global $conn;
    $sql = " SELECT AVG(answer_save) AS Avarage  
            FROM s_answer 
            WHERE  person_id = ? AND sub_id = ? AND que_id NOT IN 
            (SELECT que_id FROM s_question WHERE sub_id = ? AND que_type != 1)  
            AND who_id !=? AND answer_save!= 0 ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $person_id, $sub_id, $sub_id, $person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return isset($row['Avarage']) ? (float)$row['Avarage'] : 0.0;
}
function countfeedback($person_id,$sub_id){
    global $conn;
    $sql = "SELECT COUNT(DISTINCT(who_id)) AS total ,
    COUNT(DISTINCT(CASE WHEN whotype_id = '1' THEN (who_id) END)) AS self,
    COUNT(DISTINCT(CASE WHEN whotype_id = '2' THEN (who_id) END)) AS friend,
    COUNT(DISTINCT(CASE WHEN whotype_id = '3' THEN (who_id) END)) AS team
    FROM s_answer 
    WHERE person_id = ? AND sub_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $person_id, $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row;
}

function spider($person_id,$sub_id){
    global $conn;
    $sql = " SELECT whotype_id,  
    AVG(case when s_question.que_group = 1 then s_answer.answer_save else null end ) AS a, 
    AVG(case when s_question.que_group = 2 then s_answer.answer_save else null end ) AS b, 
    AVG(case when s_question.que_group = 3 then s_answer.answer_save else null end ) AS c, 
    AVG(case when s_question.que_group = 4 then s_answer.answer_save else null end ) AS d, 
    AVG(case when s_question.que_group = 5 then s_answer.answer_save else null end ) AS e 
    FROM s_answer JOIN s_question ON s_answer.que_id = s_question.que_id 
    WHERE s_answer.sub_id=? AND s_answer.person_id =? AND s_answer.answer_save!= 0 AND s_answer.que_id 
    NOT in ( SELECT que_id FROM s_question WHERE sub_id = ? AND que_type != 1 ) 
    GROUP BY s_answer.whotype_id 
    ORDER BY s_answer.whotype_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sub_id, $person_id, $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = array();
    while($row = $result->fetch_assoc()){
        switch ($row['whotype_id']) {
            case '1':
                $res['self']=$row;
                array_shift($res['self']);
                $res['self'] = array_values(array_filter($res['self']));
                break;
            case '2':
                $res['friend']=$row;
                array_shift($res['friend']);
                $res['friend']= array_values(array_filter($res['friend']));
                break;
            case '3':
                $res['team']=$row;
                array_shift($res['team']);
                $res['team']= array_values(array_filter($res['team']));
                break;
            
        }
    }
    return $res;
}