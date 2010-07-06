<?php
class ApiController extends AppController {

	var $name = 'Api';
    var $uses = array('User');

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->User->recursive = -1;
        Configure::write('debug', 0);
        $this->autoRender = false;
    }

    function login()
    {
        // variable initialization
        $isAuthenticated = false;
        $serverTime = date('Y-m-d H:i:s');
        $todayStartTime = date('H:i:s');
        $lateTime = false;
        $worklog_id = null;
        $user_id = null;
        $activeTime = '00:00:00';

        $username = $_POST['username'];
        $password = $this->Auth->password( $_POST['password']);
        $user = $this->User->find('first', array('conditions' => array('username' => $username, 'password' => $password)));
        if($user) {
            $this->loadModel('Worklog');
            $user_id = $user['User']['id'];
            $isAuthenticated = true;
            $todayLog = $this->Worklog->find(array("date(Worklog.start)"=>date("Y-m-d"),"User.id" => $user['User']['id']));

            if($todayLog) {
                $todayStartTime = substr($todayLog['Worklog']['start'], 11, 8);
                $worklog_id = $todayLog['Worklog']['id'];
                $activeTime = $todayLog['Worklog']['active_time'];
            }else {
        		$data['Worklog']['user_id'] = $user['User']['id'];
        		$data['Worklog']['start'] = $todayStartTime;
        		$this->Worklog->save($data);
                $worklog_id = $this->Worklog->id;
            }

            $late = $this->Worklog->getLateByUser($user['User']['id']);
        }
        echo json_encode(array('isAuthenticated' => $isAuthenticated, 'serverTime' => $serverTime, 'startTime' => $todayStartTime, 'lateTime' => $late, 'worklogId' => $worklog_id, 'userId' => $user_id, 'activeTime' => $activeTime));
    }

    function addLog()
    {
        $worklogId = $_POST['worklogId'];
        $content = $_POST['content'];
        $time = date('Y-m-d') . ' ' . $_POST['time'];
        $status = ClassRegistry::init('Log')->save(array(
            'worklog_id' => $worklogId,
            'content' => $content,
            'created' => $created
        ));
        echo json_encode(array('status' => $status, 'time' => $_POST['time'], 'content' => $content));
    }
    function getLog()
    {
        $this->loadModel('Log');
        $this->Log->recursive = -1;
        $worklogId = $_POST['worklogId'];
        $logs = $this->Log->getLog($worklogId);
        echo json_encode($logs);
    }

    function updateActiveTime()
    {
        $worklogId = $_POST['worklogId'];
        $time = $_POST['time'];
        $this->loadModel('Worklog');
        if (isset($_POST['endwork'])) {
            $this->Worklog->save(array('id' => $worklogId, 'active_time' => $time, 'end' => date("Y-m-d H:i:s")));
        } else {
            $this->Worklog->save(array('id' => $worklogId, 'active_time' => $time));
        }
    }
}
?>
