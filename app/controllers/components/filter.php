<?php
// file: /app/controllers/components/filter.php
/**
 * Filter component
 *
 * @original concept by Nik Chankov - http://nik.chankov.net
 * @modified and extended by Maciej Grajcarek - http://blog.uplevel.pl
 * @modified again by James Fairhurst - http://www.jamesfairhurst.co.uk
 * @version 0.1
 */
class FilterComponent extends Object {
    /**
     * fields which will replace the regular syntax in where i.e. field = 'value'
     */
    var $fieldFormatting = array(
		"string"	=> "LIKE '%%%s%%'",
		"text"		=> "LIKE '%%%s%%'",
		"datetime"	=> "LIKE '%%%s%%'"
	);

	/**
	 * Paginator params sent in URL
	 */
   	var $paginatorParams = array(
		'page',
		'sort',
		'direction'
   	);

   	/**
   	 *  Url variable used in paginate helper (array('url'=>$url));
   	 */
   	var $url = '';

    /**
     * Function which will change controller->data array
     * @param object $controller the class of the controller which call this component
     * @param array $whiteList contains list of allowed filter attributes
     * @access public
     */
	function process($controller, $whiteList = null){
        $controller = $this->_prepareFilter($controller);
        $ret = array();
        if(isset($controller->data)){
            // loop models
            foreach($controller->data as $key=>$value) {
				// get fieldnames from database of model
				$columns = array();
                if(isset($controller->{$key})) {
                    $columns = $controller->{$key}->getColumnTypes();
				} elseif (isset($controller->{$controller->modelClass}->belongsTo[$key])) {
                    $columns = $controller->{$controller->modelClass}->{$key}->getColumnTypes();
				}

				// if columns exist
				if(!empty($columns)) {
					// loop through filter data
                    foreach($value as $k=>$v) {
						// JF: deal with datetime filter
						if(is_array($v) && $columns[$k]=='datetime') {
							$v = $this->_prepare_datetime($v);
						}

						// if filter value has been entered
                        if($v != '') {
							// if filter is in whitelist
                        	if(is_array($whiteList) && !in_array($k,$whiteList) ){
                        		continue;
                        	}

							// check if there are some fieldFormatting set
							if(isset($this->fieldFormatting[$columns[$k]])) {
								// insert value into fieldFormatting
								$tmp = sprintf($this->fieldFormatting[$columns[$k]], $v);
								// don't put key.fieldname as array key if a LIKE clause
								if (substr($tmp,0,4)=='LIKE') {
									$ret[] = $key.'.'.$k . " " .  $tmp;
								} else {
									$ret[$key.'.'.$k] = $tmp;
								}
							} else {
								// build up where clause with field and value
								$ret[$key.'.'.$k] = $v;
							}

							// save the filter data for the url
							$this->url .= '/'.$key .'.'.$k.':'.$v;
                        }
                    }

                    //unsetting the empty forms
                    if(count($value) == 0){
                        unset($controller->data[$key]);
                    }
				}
            }
        }

	return $ret;
    }


    /**
     * function which will take care of the storing the filter data and loading after this from the Session
	 * JF: modified to not htmlencode, caused problems with dates e.g. -05-
	 * @param object $controller the class of the controller which call this component
     */
    function _prepareFilter($controller) {
		$filter = array();
        if(isset($controller->data)) {
			//pr($controller);
            foreach($controller->data as $model=>$fields) {
				if(is_array($fields)) {
					foreach($fields as $key=>$field) {
						if($field == '') {
							unset($controller->data[$model][$key]);
						}
					}
				}
            }

            App::import('Sanitize');
            $sanit = new Sanitize();
            $controller->data = $sanit->clean($controller->data, array('encode' => false));
            $filter = $controller->data;
        }

        if (empty($filter)) {
      		$filter = $this->_checkParams($controller);
        }
        $controller->data = $filter;
		
	return $controller;
    }


    /**
     * function which will take care of filters from URL
	 * JF: modified to not encode, caused problems with dates
	 * @param object $controller the class of the controller which call this component
     */
     function _checkParams($controller) {
     	if (empty($controller->params['named'])) {
     		$filter = array();
     	}

        App::import('Sanitize');
        $sanit = new Sanitize();
        $controller->params['named'] = $sanit->clean($controller->params['named'],array('encode' => false));

     	foreach($controller->params['named'] as $field => $value) {
     		if(!in_array($field, $this->paginatorParams)) {
				$fields = explode('.',$field);
				if (sizeof($fields) == 1) {
	     			$filter[$controller->modelClass][$field] = $value;
				} else {
	     			$filter[$fields[0]][$fields[1]] = $value;
				}
     		}
     	}

     	if (!empty($filter))
     		return $filter;
     	else
     		return array();
     }


	/**
	 * Prepares a date array for a Mysql where clause
	 * @author James Fairhurst
	 * @param array $arr
	 * @return string
	 */
	function _prepare_datetime($date) {
		// init
		$str = '';
		// reverse array so that dd-mm-yyyy becomes yyyy-mm-dd
		$date = array_reverse($date);
		// loop through date
		foreach($date as $key=>$value) {
			// if d/m/y has been entered
			if(!empty($value)) {
				// seperate with '-'
				$str .= '-'.$value;
				// remove first '-'
				if($key=='year') {
					$str = str_replace('-', '', $str);
				}
				// only add if day is empty
				if($key=='month' && empty($date['day'])) {
					$str .= '-';
				}
				// add final space
				if($key=='day') {
					$str.=' ';
				}
			}
		}

	return $str;
	}
}

?>
