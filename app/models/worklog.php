<?php
class Worklog extends AppModel {

	var $name = 'Worklog';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'worklog_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'Log.created DESC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

    public function getLateByUser($user_id, $month = null)
    {
        $whereMonth = 'MONTH(NOW())';
        if ($month) {
            $whereMonth = $month;
        }
        $sql = "
            SELECT
            SEC_TO_TIME(
                SUM( TIME_TO_SEC( TIME(START ) ) - TIME_TO_SEC(  '08:00:00' ) )
            ) as late
            FROM worklogs
            WHERE MONTH(START) = $whereMonth
            AND user_id = ?
            AND TIME( START ) >  '08:00:00'
            GROUP BY user_id
        ";
        $result = $this->query($sql, array($user_id));
        if(isset($result[0])) {
            return $result[0][0]['late'];
        } else {
            return "00:00:00";
        }
    }

}
?>
