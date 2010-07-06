<?php
class Log extends AppModel {

	var $name = 'Log';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Worklog' => array(
			'className' => 'Worklog',
			'foreignKey' => 'worklog_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function getLog($worklogId)
    {
        $logs = $this->find('all', array('fields' => array('content', 'time(created) as time'), 'conditions' => array('worklog_id' => $worklogId)));
        $data = array();

        foreach($logs as $log) {
            $temp = array();
            $temp['content'] = $log['Log']['content'];
            $temp['time'] = $log[0]['time'];
            $data[] = $temp;
        }
        return $data;
    }
}
?>
