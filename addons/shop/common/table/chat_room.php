<?php
namespace table;
use base;
class chat_room extends base\db {
    protected $tableName = 'chat_room';
    public function add($data) {
        $this->beginTransaction();
        try {
    		$room_id = $this->insert($data);
		    $room_counter = array(
    	        'room_id' => $room_id,
    	        'records' => 0,
    	    );
    	    $this->table('chat_room_msg_counter')->insert($room_counter);
    		$this->commit();
        } catch (\Exception $e) {
			$this->rollBack();
			\lib\logging::write(var_export($e->getMessage(), true));
			return callback(false, $e->getMessage());
		}
		return callback(true, '', array('room_id' => $room_id));
	}
}