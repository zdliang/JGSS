<?php

    $dj = new SaeDeferredJob();
    $taskID = $_GET["taskID"];
    $fileName = $_GET["fileName"];
    
    if(is_numeric($taskID)){
        //echo $taskID;
        删除任务
        $ret=$dj->deleteTask($taskID);
        if($ret===false){
            var_dump($dj->errno(), $dj->errmsg());    
        }
    }else if(!empty($fileName)){
        //echo $fileName;
        添加任务
        $s = new SaeStorage();
        if( $s->file_exists("stock",$fileName)){
            $taskID=$dj->addTask("import","mysql","stock",$fileName,"app_zdliang","close_predict_new","");
            if($taskID===false){
                var_dump($dj->errno(), $dj->errmsg());
            }
            else{
                var_dump($taskID);
            }
        }
    }    
?>