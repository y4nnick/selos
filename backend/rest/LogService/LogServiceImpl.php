<?php

class LogServiceImpl implements LogService{

    private static $TABLENAME = "log";

    private function getBasicLog($team,$level,$type){
        $log = R::dispense(LogServiceImpl::$TABLENAME);
        $log->date = R::isoDateTime();
        $log->level = $level;
        $log->type = $type;
        $log->team = $team;

        return $log;
    }

    public function logImport($team) {
        $log = $this->getBasicLog($team,"info","import");
        $log->msg = "Erfolgreich importiert";
        R::store($log);
    }
}


