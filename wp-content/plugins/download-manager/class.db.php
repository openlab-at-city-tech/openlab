<?php

class DMDB{
    
    
    function AddNew($table, $data){
        mysql_query("insert into $table set ".self::MakeQuery($data));
    }
    
    function Update($table, $data, $cond){
        mysql_query("update $table set ".self::MakeQuery($data)." where ".$cond);
    }
    
    function Delete($table, $cond){
        $d = mysql_fetch_assoc(mysql_query("select * from $table where $cond"));
        @unlink(UPLOAD_DIR.$d['file']);
        mysql_query("delete from $table where ".$cond);
    }
    
    function getById($table, $id){        
        return mysql_fetch_assoc(mysql_query("select * from $table where id='$id'"));
    }
    
    function getData($table, $where = '', $limit=''){
      $req =  mysql_query("select * from $table $where $limit");
      while($r = mysql_fetch_assoc($res)){
          $rows[] = $r;
      }
      return $rows;
    }
    
    function MakeQuery($data){
        foreach($data as $k=>$d){
            $qry[] = "`$k`='$d'";
        }
        return implode(",", $qry);
    }
    
}

?>