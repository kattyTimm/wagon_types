<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbasemy.php';

class wagon_types_db{
    public static   $dbhost = 'localhost',
                    $dbport = '3306',
                    $dbname = 'wagon_types_db',
                    $dbuser = 'types_share',
                    $dbpwd  = '12345678_0';
    
    public function _construct(){
      // session_start - инициализирует данные сессии.   
        if(strlen(trim(session_id())) == 0) // session_id - возвращает id текущей сессии
             session_start(); 
    }
    
    public function connectDB(){
       return _dbbasemy::connectDB(self::$dbhost, self::$dbuser, self::$dbpwd, self::$dbname, self::$dbport);
    }
    /*
 $dbhost = 'localhost',
                    $dbport = '3306',
                    $dbname = 'points_db',
                    $dbuser = 'points_share',
                    $dbpwd  = '12345678_0',
                 */
    
    
    public static function validateStr(string $tbl, string $fld, string &$str) {
        $str = _dbbasemy::validateStrLength(self::$dbhost, self::$dbuser, self::$dbpwd, self::$dbname, self::$dbport, $tbl, $fld, $str);
    }
    
    // ------------------------------------------
    // service function
    // ------------------------------------------
    
    public function table_ridExists(string $tbl, string $rid) : bool {
        $result = false;
        
        if (strlen($tbl) > 0) {
            $conn = $this->connectDB();

            if ($conn) {
                $db = new _dbbasemy();
                $result = $db->table_ridExists($conn, $tbl, $rid);
                unset($db);

                _dbbasemy::closeConnection($conn);
            }
        }
        
        return $result;
    }
    
    
    public function table_getRidRowNumber(string $table_name, string $order_by, string $rid) : int {
        $result = -1;
        
        if ($this->table_ridExists($table_name, $rid)) {
            $conn = $this->connectDB();

            if ($conn !== false) {
                $q = mysqli_prepare($conn, 
                                    "SELECT a.rown FROM (" .
                                        "SELECT ROW_NUMBER() OVER (ORDER BY " . $order_by . ") AS rown,rid FROM " . $table_name .
                                    ") a WHERE rid='" . $rid . "'");

                if ($q !== false) {
                    mysqli_stmt_execute($q);
                    mysqli_stmt_store_result($q);
                    mysqli_stmt_bind_result($q, $sel_rown);

                    if (mysqli_stmt_fetch($q))
                        $result = intval($sel_rown) - 1;    // because sql server numbered rows from 1, but we need from 0

                    mysqli_stmt_free_result($q);
                    mysqli_stmt_close($q);
                }

                _dbbasemy::closeConnection($conn);
                unset($conn);
            }
        }
        
        return $result;
    }
    
    public function table_getRowcount(string $tbl) : int {
        $result = -1;
        
        if (strlen($tbl) > 0) {
            $conn = $this->connectDB();

            if ($conn) {
                $db = new _dbbasemy();
                $result = $db->table_getRowcount($conn, $tbl);
                unset($db);

                _dbbasemy::closeConnection($conn);
            }
        }
        
        return $result;
    }
    
    public function mdls_getRowcount(string $rid_type = '', $from = '', $to = '', $country = '', $factory = '') : int { //
        $result = -1;
        $q_str = '';
        /*
        if(strlen($from) > 0 && strlen($to) > 0)

           $q_str = "SELECT count(*) FROM ( SELECT DISTINCT a.rid from (
                                                SELECT m.rid, e.prod_ys from mdls m left join executions e ON m.rid=e.rid_mdl 
                                               ) a WHERE a.prod_ys>='".$from."' AND a.prod_ys<='".$to."'
                                           )a"; 
        else if(mb_strlen($country) > 0){
            $q_str = "SELECT count(*) FROM ( SELECT DISTINCT a.rid from (
                                                SELECT m.rid, e.country from mdls m left join executions e ON m.rid=e.rid_mdl 
                                               ) a WHERE a.country like('%".$country."%')
                                        )a"; 
        }
        
        else if(mb_strlen($factory) > 0){
            $q_str = "SELECT count(*) FROM ( SELECT DISTINCT a.rid from (
                                                SELECT m.rid, e.factory from mdls m left join executions e ON m.rid=e.rid_mdl 
                                               ) a WHERE a.factory like('%".$factory."%')
                                        )a"; 
        }
        */
        if(strlen($country) > 0 || strlen($factory) > 0 || strlen($from) > 0 || strlen($to) > 0 ){
            $and_str = '';
    
             if(strlen($country) > 0){      
            $and_str = "AND (e.country='".$country."'";
            }
            if (strlen($factory) > 0){
               if(strlen($and_str) > 0) $and_str .= " AND e.factory='".$factory."'";

               else $and_str = "AND (e.factory='".$factory."'"; 
            } 
            if(strlen($from) > 0 && strlen($to) > 0 || strlen($from) > 0 || strlen($to) > 0 ){

               if(strlen($and_str) > 0){
                      if(!strlen($from) > 0){
                           $and_str .= " AND e.prod_ys <='".$to."'"; 
                      } else if(!strlen($to) > 0){
                         $and_str .= " AND e.prod_ys>='".$from."'";  
                      } else if(strlen($from) > 0 && strlen($to) > 0){
                         $and_str .= " AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."')";                      
                      }   
                  }    
                  else {
                       if(!strlen($from) > 0){
                           $and_str .= "AND (e.prod_ys <='".$to."'"; 
                      } else if(!strlen($to) > 0){
                         $and_str .= "AND (e.prod_ys>='".$from."'";  
                      } else if(strlen($from) > 0 && strlen($to) > 0){
                         $and_str = "AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."'"; 
                      }   
                  }   
            }

         if(strlen($and_str) > 0)
            $and_str .= ')'; 
           
           $q_str = "SELECT count(*) FROM (
                           SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                SELECT DISTINCT m.rid from mdls m left join executions e ON m.rid=e.rid_mdl 
                                WHERE m.rid=e.rid_mdl ".$and_str."))a";  
        }   
           
        else {
            $where_str = (strlen($rid_type) > 0) ? "WHERE rid_type='".$rid_type."'" : '';
            $q_str = "SELECT count(*) FROM mdls ".$where_str;
        }
        
        $conn = $this->connectDB();
        
        if($conn !== false){
            $q = mysqli_prepare($conn, $q_str);

            if ($q !== false) {
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_cnt);

                if (mysqli_stmt_fetch($q))
                    $result = intval($sel_cnt);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
        }
    
        return $result;
    }
    
    // не пользуюсь ей
    public function mdls_getRowcount_with_years_table($from, $to){
        $result = -1;
        
        $conn = $this->connectDB();
        
        if($conn !== false){

            $q = mysqli_prepare($conn, "SELECT count(*) FROM (
                                                SELECT DISTINCT a.nm,a.rid,a.rid_type,a.prod_ys from (
                                                SELECT m.nm, m.rid,m.rid_type,e.prod_ys from mdls m left join executions e ON m.rid=e.rid_mdl 
                                               ) a WHERE a.prod_ys>='".$from."' AND a.prod_ys<='".$to."'
                                           )a");

            if ($q !== false) {
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_cnt);

                if (mysqli_stmt_fetch($q))
                    $result = intval($sel_cnt);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
        }
    
        return $result;
    }
    
    
     /*  
    public function table_pidExists_ONLY_docs(string $tbl, string $pid) : bool {
        $result = false;
        
        $tbl_ = trim($tbl);
        $pid_ = trim($pid);
        
        if ($conn && strlen($tbl_) > 0 && strlen($rid_) > 0) {
            $q = mysqli_prepare($conn, "SELECT pid FROM " . $tbl_ . " WHERE pid=? LIMIT 1");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 's', $rid_);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid);
                
                if (mysqli_stmt_fetch($q))
                    $result = true;

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
        }
        
        return $result;
    }
    */
    // ------------------------------------------
    // types
    // ------------------------------------------
    
    public function add_orEdit_type($rid, $nm, $flg = 0){

        $nm_ = trim($nm);
        
        $result = '';
        
        if(strlen($nm_) > 0) {        
            $conn = $this->connectDB();

            //иногда требуется переопределить передданный параметр как в строке ниже:		
            $rid_= $rid;   

            if($conn){
                       mysqli_autocommit($conn, false);

                            if (strlen($rid_) > 0) {
                                $q = mysqli_prepare($conn, "UPDATE types SET nm=?,flg=? WHERE rid=?");

                                 mysqli_stmt_bind_param($q, 'sis', $nm_, $flg, $rid_);
                            }
                            else {
                                $q = mysqli_prepare($conn, "INSERT INTO types(rid,nm,flg) VALUES(?,?,?)");

                                $rid_ = _dbbase::gen_uuid();
                                mysqli_stmt_bind_param($q, 'ssi', $rid_, $nm_, $flg);
                            }

                            //if (mysqli_stmt_execute($q) !== false) //mysqli_stmt_execute($q) !== false может возвращать false, когда все ок, и из-за этого запрос не выполнится!!!
                            mysqli_stmt_execute($q);
                                $result = $rid_;

                           if (is_string($result)) {
                                mysqli_stmt_close($q);
                                mysqli_commit($conn);
                            }

                        _dbbasemy::closeConnection($conn);
            }
        }
        return $result;
    }
    
    public function typesList_Whole(){ // whole[həʊl] - весь, целый, общий
        $result = [];
        
        $conn = $this->connectDB();
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,flg FROM types");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'flg' => $sel_flg
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            
            _dbbasemy::closeConnection($conn);
        }        
        return $result;
    }
    
    public function type_record_by_self_rid($rid){
        $result = [];
        $conn = $this->connectDB();
        $rid_ = trim($rid);
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,flg FROM types WHERE rid='".$rid_."'");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg);
                
                if (mysqli_stmt_fetch($q))
                    $result = ['rid' => $sel_rid,
                               'nm' => $sel_nm,
                               'flg' => $sel_flg
                              ];
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function delete_type_by_rid($rid){
        $result = false;
        
         if ($this->table_ridExists('types', $rid)) {
            $conn = $this->connectDB();
            
            if($conn !== false){
                mysqli_autocommit($conn, false);
                
                /* Здесь надо будет удалять связанные документы , и поле rid_type в таблице mdls у моделей с этим типом
                
                $q = mysqli_prepare($conn, "UPDATE clnf SET org='' WHERE org=?");
                if (mysqli_stmt_bind_param($q, 's', $rid) &&
                    mysqli_stmt_execute($q) &&
                    mysqli_stmt_close($q))
                {                 */
                
                $q = mysqli_prepare($conn, "DELETE FROM types WHERE rid=?");
                
                if (mysqli_stmt_bind_param($q, 's', $rid) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                    mysqli_commit($conn);
                    $result = true;
                }
                                       
                _dbbasemy::closeConnection($conn);                
            }
         }
        return $result;
    }
    
    // ------------------------------------------
    // mdls // models
    // ------------------------------------------
    
    
    public function mdlsList_Subset_by_multi($offset, $rows, $country, $factory, $from, $to){
        $result = [];
        $conn = $this->connectDB();
        $and_str = '';
    
         if(strlen($country) > 0){      
            $and_str = "AND (e.country='".$country."'";
         }
         if (strlen($factory) > 0){
            if(strlen($and_str) > 0) $and_str .= " AND e.factory='".$factory."'";

            else $and_str = "AND (e.factory='".$factory."'"; 
         } 
         if(strlen($from) > 0 && strlen($to) > 0 || strlen($from) > 0 || strlen($to) > 0 ){
             
            if(strlen($and_str) > 0){
                   if(!strlen($from) > 0){
                        $and_str .= " AND e.prod_ys <='".$to."'"; 
                   } else if(!strlen($to) > 0){
                      $and_str .= " AND e.prod_ys>='".$from."'";  
                   } else if(strlen($from) > 0 && strlen($to) > 0){
                      $and_str .= " AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."')";                      
                   }   
               }    
               else {
                    if(!strlen($from) > 0){
                        $and_str .= "AND (e.prod_ys <='".$to."'"; 
                   } else if(!strlen($to) > 0){
                      $and_str .= "AND (e.prod_ys>='".$from."'";  
                   } else if(strlen($from) > 0 && strlen($to) > 0){
                      $and_str = "AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."'"; 
                   }   
               }   
         }

         if(strlen($and_str) > 0)
            $and_str .= ')';              
         
        if($conn !== false){
                      
           $q_str = "SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT m.rid from mdls m left join executions e ON m.rid=e.rid_mdl 
                                            WHERE m.rid=e.rid_mdl ".$and_str.") ORDER BY nm LIMIT ? OFFSET ?";   
            
           $q = mysqli_prepare($conn, $q_str);
           
           if($q !== false){
                mysqli_stmt_bind_param($q, 'ii', $rows, $offset); 
               
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
                 
        return $result;
    }
    
    public function mdlsList_whole_by_multi($country, $factory, $from, $to){
        $result = [];
        $conn = $this->connectDB();
        $and_str = '';
    
         if(strlen($country) > 0){      
            $and_str = "AND (e.country='".$country."'";
         }
         if (strlen($factory) > 0){
            if(strlen($and_str) > 0) $and_str .= " AND e.factory='".$factory."'";

            else $and_str = "AND (e.factory='".$factory."'"; 
         } 
         
         if(strlen($from) > 0 && strlen($to) > 0 || strlen($from) > 0 || strlen($to) > 0 ){
             
            if(strlen($and_str) > 0){
                   if(!strlen($from) > 0){
                        $and_str .= " AND e.prod_ys <='".$to."'"; 
                   } else if(!strlen($to) > 0){
                      $and_str .= " AND e.prod_ys>='".$from."'";  
                   } else if(strlen($from) > 0 && strlen($to) > 0){
                      $and_str .= " AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."')";                      
                   }   
               }    
               else {
                    if(!strlen($from) > 0){
                        $and_str .= "AND (e.prod_ys <='".$to."'"; 
                   } else if(!strlen($to) > 0){
                      $and_str .= "AND (e.prod_ys>='".$from."'";  
                   } else if(strlen($from) > 0 && strlen($to) > 0){
                      $and_str = "AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."'"; 
                   }     
               }        
             
           /* if(strlen($and_str) > 0){
                $and_str .= " AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."')"; 
            } else $and_str = "AND (e.prod_ys>='".$from."' AND e.prod_ys <='".$to."'"; */
         }

         if(strlen($and_str) > 0)
            $and_str .= ')';              
         
        if($conn !== false){
           $q_str = "SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT m.rid from mdls m left join executions e ON m.rid=e.rid_mdl 
                                            WHERE m.rid=e.rid_mdl ".$and_str.") ORDER BY nm";   
            
           $q = mysqli_prepare($conn, $q_str);
           
           if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
                 
        return $result;
    }
    
    public function mdlsList_Whole_by_years($from, $to){
        $result = [];
        $conn = $this->connectDB();
       
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.prod_ys>='".$from."' AND e.prod_ys<='".$to."' 
                                          ) ORDER BY nm");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;
    }
    
    public function mdlsList_Subset_by_years($offset, $rows, $from, $to){
        $result = [];        
        $conn = $this->connectDB();         
        /*
            SELECT DISTINCT a.nm,a.rid,a.rid_type,a.prod_ys from (
                                 здесь модели повторялись по кол-ву исполнений потому, что во внутреннем селекте ты ищешь по немкольким полям и потому что
                                 должен быть IN !!!!  IN без разницы сколько раз повторится  запись
                                     SELECT m.nm, m.rid,m.rid_type,e.prod_ys from mdls m left join executions e ON m.rid=e.rid_mdl 
                                    ) a WHERE a.prod_ys>='".$from."' AND a.prod_ys<='".$to."' 
                                   ORDER BY a.nm LIMIT ? OFFSET ?"
          */

        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.prod_ys>='".$from."' AND e.prod_ys<='".$to."' 
                                          ) ORDER BY nm LIMIT ? OFFSET ?");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 'ii', $rows, $offset);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);

                while (mysqli_stmt_fetch($q))
                    array_push($result, [   'rid' => $sel_rid,
                                            'nm' => $sel_nm,
                                            'rid_type' => $sel_rid_type]);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }

           _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function mdlsList_Subset_by_country($offset, $rows, $country){
        $result = [];        
        $conn = $this->connectDB();         

        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.country LIKE('%".$country."%')
                                          ) ORDER BY nm LIMIT ? OFFSET ?");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 'ii', $rows, $offset);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);

                while (mysqli_stmt_fetch($q))
                    array_push($result, [   'rid' => $sel_rid,
                                            'nm' => $sel_nm,
                                            'rid_type' => $sel_rid_type]);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }

           _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function mdlsList_Whole_by_country($country){
        $result = [];        
        $conn = $this->connectDB();         

        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.country LIKE('%".$country."%')
                                          ) ORDER BY nm");
            
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;

    }
    
    public function mdlsList_Subset_by_factory($offset, $rows, $factory){
        $result = [];        
        $conn = $this->connectDB();         

        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.factory LIKE('%".$factory."%')
                                          ) ORDER BY nm LIMIT ? OFFSET ?");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 'ii', $rows, $offset);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);

                while (mysqli_stmt_fetch($q))
                    array_push($result, [   'rid' => $sel_rid,
                                            'nm' => $sel_nm,
                                            'rid_type' => $sel_rid_type]);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }

           _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function mdlsList_Whole_by_factory($factory){
        $result = [];        
        $conn = $this->connectDB();         

        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,rid_type from mdls WHERE rid IN (
                                            SELECT DISTINCT  m.rid from mdls m left join executions e ON m.rid=e.rid_mdl WHERE e.factory LIKE('%".$factory."%')
                                          ) ORDER BY nm");
            
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;

    }
    
    public function mdlsList_Whole($rid_type = ''){
        $result = [];
        $conn = $this->connectDB();
        
        $where = (strlen($rid_type) > 0) ? "WHERE rid_type='".$rid_type."'" : '';
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,flg,rid_type FROM mdls ".$where." ORDER BY nm");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg, $sel_rid_type);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'flg' => $sel_flg,
                         'rid_type' => $sel_rid_type
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;
    }
    
    public function mdlsList_Subset($offset, $rows, $rid_type = ''){
        $result = [];        
        $conn = $this->connectDB();
        
        $where = (strlen($rid_type) > 0) ? "WHERE rid_type='".$rid_type."'" : '';
        
        if ($conn !== false) {
            $q = mysqli_prepare($conn,"SELECT rid,nm,flg,rid_type FROM mdls ".$where." ORDER BY nm LIMIT ? OFFSET ?");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 'ii', $rows, $offset);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg, $sel_rid_type);

                while (mysqli_stmt_fetch($q))
                    array_push($result, [  'rid' => $sel_rid,
                                            'nm' => $sel_nm,
                                            'flg' => $sel_flg,
                                            'rid_type' => $sel_rid_type]);

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }

           _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
 /*   
    public function add_orEdit_mdl($rid, $nm, $rid_type, $flg) : int {
        
        $nm_ = trim($nm);   
        $rid_type_ = trim($rid_type);
        
        $result = 0;
        
        if(strlen($nm_) > 0) {        
            $conn = $this->connectDB();

            //иногда требуется переопределить передданный параметр как в строке ниже:		
            $rid_= trim($rid);   

            if($conn !== false){
                mysqli_autocommit($conn, false);
                     
                if(!$this->verify_nm_mdl($nm_)){
                     if (strlen($rid_) > 0) {
                         $q = mysqli_prepare($conn, "UPDATE mdls SET nm=?,flg=?,rid_type=? WHERE rid=?");

                          mysqli_stmt_bind_param($q, 'siss', $nm_, $flg, $rid_type_, $rid_);
                     }
                     else {
                         $q = mysqli_prepare($conn, "INSERT INTO mdls(rid,nm,flg,rid_type) VALUES(?,?,?,?)");
                         
                         $rid_ = _dbbase::gen_uuid();                       
                         mysqli_stmt_bind_param($q, 'ssis', $rid_, $nm_, $flg, $rid_type_); 

                     }

                    if(mysqli_stmt_execute($q) !== false) //mysqli_stmt_execute($q) !== false может возвращать false, когда все ок, и из-за этого запрос не выполнится!!!
                            $result = 1;
                            
                            mysqli_stmt_close($q);
                            mysqli_commit($conn);
               } else $result = 2;
                 _dbbasemy::closeConnection($conn);
            }
        }
        return $result;
    }
 */   
    public function verify_nm_mdl($nm){
        $result = false;
        $nm = trim($nm);
        
        if(strlen($nm) > 0){
            $conn = $this->connectDB();
            
             if($conn !== false){
                 $q = mysqli_prepare($conn, "SELECT nm from mdls WHERE nm=?");
              
                 mysqli_stmt_bind_param($q, 's', $nm);
                 
                 mysqli_stmt_execute($q);
                 mysqli_stmt_store_result($q);
                 mysqli_stmt_bind_result($q, $sel_rid);
                 
                 if(mysqli_stmt_fetch($q))
                     $result = $sel_rid;
  
                 mysqli_stmt_free_result($q);
                 mysqli_stmt_close($q);
             }
              _dbbasemy::closeConnection($conn);
        }
        return $result;
    }
    
  
    public function add_orEdit_mdl($rid, $nm, $rid_type, $flg) : string {
        
        $nm_ = trim($nm);   
        $rid_type_ = trim($rid_type);
        
        $result = '';       
        
        if(strlen($nm_) > 0) {        
            $conn = $this->connectDB();
	
            $rid_= trim($rid);   

            if($conn !== false){
                mysqli_autocommit($conn, false);

                     if (strlen($rid_) > 0) {
                            $q = mysqli_prepare($conn, "UPDATE mdls SET nm=?,flg=?,rid_type=? WHERE rid=?");
                             
                             mysqli_stmt_bind_param($q, 'siss', $nm_, $flg, $rid_type_, $rid_);
                     }
                     else {
                         $q = mysqli_prepare($conn, "INSERT INTO mdls(rid,nm,flg,rid_type) VALUES(?,?,?,?)");

                         $rid_ = _dbbase::gen_uuid();                       
                         mysqli_stmt_bind_param($q, 'ssis', $rid_, $nm_, $flg, $rid_type_);
                     }

                     //if (mysqli_stmt_execute($q) !== false) //mysqli_stmt_execute($q) !== false может возвращать false, когда все ок, и из-за этого запрос не выполнится!!!
                     if(mysqli_stmt_execute($q) !== false)
                            $result = $rid_;

                         mysqli_stmt_close($q);
                         mysqli_commit($conn);

                 _dbbasemy::closeConnection($conn);
            }
        }
        return $result;
    }
   
    public function mdl_record_by_self_rid($rid){
        $result = [];
        $conn = $this->connectDB();
        $rid_ = trim($rid);
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,flg,rid_type FROM mdls WHERE rid='".$rid_."'");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg, $rid_type);
                
                if (mysqli_stmt_fetch($q))
                    $result = ['rid' => $sel_rid,
                               'nm' => $sel_nm,
                               'flg' => $sel_flg,
                               'rid_type' => $rid_type
                              ];
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function mdl_list_by_rid_type($type_rid){
        $result = [];
        $conn = $this->connectDB();
        $type_rid_ = trim($type_rid);
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,flg,rid_type FROM mdls WHERE rid_type='".$type_rid_."'");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_flg, $type_rid_);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, ['rid' => $sel_rid,
                                          'nm' => $sel_nm,
                                          'flg' => $sel_flg,
                                          'rid_type' => $type_rid_
                                         ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;
    } 
    
    public function delete_mdl_by_rid($rid){
        $result = false;
        
         if ($this->table_ridExists('mdls', $rid)) {
            $conn = $this->connectDB();
            
            if($conn !== false){
                mysqli_autocommit($conn, false);
  
                if($this->delete_exe_by_mdl($rid)){
                
                    $q = mysqli_prepare($conn, "DELETE FROM mdls WHERE rid=?");

                    if (mysqli_stmt_bind_param($q, 's', $rid) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                        mysqli_commit($conn);
                        $result = true;
                    }
                }                       
                _dbbasemy::closeConnection($conn);                
            }
         }
        return $result;
    }
    
    public function searching_coincidence_mdls($context){
       $result = [];
        
       $context = trim($context);
       $conn = $this->connectDB();
       
       if($conn !== false){
           $q = mysqli_prepare($conn, "SELECT a.dt, a.val FROM (
                                             SELECT rid AS dt, nm as val FROM mdls
                                        ) a WHERE a.val like '%".$context."%' ORDER BY a.val");
           
           if($q !== false){
               mysqli_stmt_execute($q);
               mysqli_stmt_store_result($q);
               mysqli_stmt_bind_result($q, $sel_dt, $sel_val);
               
               while(mysqli_stmt_fetch($q))
                   array_push($result, [
                       'data' => $sel_dt,
                       'value' => $sel_val
                   ]);
               
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
           }
          _dbbasemy::closeConnection($conn);     
       }
        
        return $result;

    }
    
    
    // ------------------------------------------
    // executions
    // ------------------------------------------
    
    
    public function get_prod_ys(){
        $result = [];
        $conn = $this->connectDB();
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT DISTINCT prod_ys from executions WHERE prod_ys<>'' ORDER BY prod_ys");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_prod_ys);
                
                while (mysqli_stmt_fetch($q))
                    array_push( $result, ['prod_ys' => $sel_prod_ys]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function get_countries (){
        $result = [];
        $conn = $this->connectDB();
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT DISTINCT country from executions WHERE country<>'' ORDER BY country");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_country);
                
                while (mysqli_stmt_fetch($q))
                    array_push( $result, ['country' => $sel_country]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function get_factories (){
        $result = [];
        $conn = $this->connectDB();
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT DISTINCT factory from executions WHERE factory<>'' ORDER BY factory");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_factory);
                
                while (mysqli_stmt_fetch($q))
                    array_push( $result, ['factory' => $sel_factory]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function add_orEdit_execution($rid, $rid_mdl, $nm,$factory,$country, $flg, $spec_n, $desigDraw_techCon, $comm_draw, $w_base, $w_length, $b_length, $b_width, $b_height, $composition,
            $layout, $type_coup_dev, $tM_cart, $weight_tare, $payload, $size_bC, $quan_sit_plcs, $constr_speed, $existType_EHTK, $type_brake, $type_trans_dev, 
            $type_B_dev, $systemElectr, $type_gen, $DGY, $syfle, $video, $type_drive, $vent_cond, $air_decon, $aqua_decon, $F_MV_B_C, $BRISS,
            $sys_heat, $cert, $serv_life, $runs, $prod_ys)
    {
    //validateStr(string $tbl, string $fld, string &$str)  
        $nm_ = trim($nm, '\"\' ');
        $factory_ = trim($factory, '\"\' ');
        $country_ = trim($country, '\"\'');
        $desigDraw_techCon_ = trim($desigDraw_techCon, '\"\' ');
        $comm_draw_ = trim($comm_draw, '\"\' ');
        $w_base_ = trim($w_base, '\"\' ');
        $w_length_ = trim($w_length, '\"\' ');
        $b_length_ = trim($b_length, '\"\' ');
        $b_width_ = trim($b_width, '\"\' ');
        $b_height_ = trim($b_height, '\"\' ');
        $composition_ = trim($composition, '\"\' ');
        
        $layout_ = trim($layout, '\"\' ');
        $type_coup_dev_ = trim($type_coup_dev, '\"\' ');
        $tM_cart_ = trim($tM_cart, '\"\' ');
        $weight_tare_ = trim($weight_tare, '\"\' ');
        $payload_ = trim($payload, '\"\' ');
        
        $size_bC_ = trim($size_bC, '\"\' ');
        $quan_sit_plcs_ = trim($quan_sit_plcs, ' \"\' ');
        $constr_speed_ = trim($constr_speed, '\"\' ');
        $existType_EHTK_ = trim($existType_EHTK, '\"\' ');
        $type_brake_ = trim($type_brake, '\"\' ');
        $type_trans_dev_ = trim($type_trans_dev, '\"\' ');
        
        $type_B_dev_ = trim($type_B_dev, '\"\' ');
        $systemElectr_ = trim($systemElectr,'\"\' ');
        $type_gen_ = trim($type_gen, '\"\' ');
        
        $DGY_ = trim($DGY, '\"\' ');
        $syfle_ = trim($syfle, '\"\' ');
        $video_ = trim($video, '\"\' ');
        
        $type_drive_ = trim($type_drive, '\"\' ');
        $vent_cond_ = trim($vent_cond, '\"\' ');
        $air_decon_ = trim($air_decon, '\"\' ');
        
        $aqua_decon_ = trim($aqua_decon, '\"\' ');
        $F_MV_B_C_ = trim($F_MV_B_C, '\"\' ');
        $BRISS_ = trim($BRISS, '\"\' ');
        
        $sys_heat_ = trim($sys_heat, '\"\' ');
        $cert_ = trim($cert, '\"\' ');
        $serv_life_ = trim($serv_life, '\"\' ');
        $runs_ = trim($runs, '\"\' ');
        $prod_ys_ = trim($prod_ys);
        
        $date = date('Y.m.d H:i:s');
        
        $result = '';
        
        self::validateStr("executions", "type_coup_dev", $type_coup_dev_); 
        self::validateStr("executions", "tM_cart", $tM_cart_);
        self::validateStr("executions", "weight_tare", $weight_tare_); 
        self::validateStr("executions", "payload", $payload_);
        
        self::validateStr("executions", "size_bC", $size_bC_); 
        self::validateStr("executions", "existType_EHTK", $existType_EHTK_);
        self::validateStr("executions", "sys_heat", $sys_heat_); 
        self::validateStr("executions", "vent_cond", $vent_cond_);
        
        self::validateStr("executions", "quan_sit_plcs", $quan_sit_plcs_);
        self::validateStr("executions", "layout", $layout_);
        self::validateStr("executions", "composition", $composition_);
        self::validateStr("executions", "runs", $runs_);

        if(strlen($nm_) > 0) {        
            $conn = $this->connectDB();	            
            $rid_= $rid;   

            if($conn){
                mysqli_autocommit($conn, false);

                     if (strlen($rid_) > 0) {
                         $q = mysqli_prepare($conn, "UPDATE executions SET nm=?,factory=?,country=?,flg=?,spec_n=?,desigDraw_techCon=?,comm_draw=?,w_base=?,w_length=?,
                             
                                                                           b_length=?,b_width=?,b_height=?,composition=?,layout=?,type_coup_dev=?,
                                                                           
                                                                           tM_cart=?,weight_tare=?,payload=?,size_bC=?,quan_sit_plcs=?,constr_speed=?,existType_EHTK=?,
                                                                           
                                                                           type_brake=?,type_trans_dev=?,type_B_dev=?,systemElectr=?,type_gen=?,
                                                                           
                                                                           DGY=?,syfle=?,video=?,type_drive=?,vent_cond=?,air_decon=?,
                                                                           
                                                                           aqua_decon=?,F_MV_B_C=?,BRISS=?,
                                                                           
                                                                           sys_heat=?,cert=?,serv_life=?,runs=?,prod_ys=? WHERE rid=?");

                          mysqli_stmt_bind_param($q, 'sssissssssssssssssssssssssssssssssssssssss', $nm_,$factory_,$country_, $flg, $spec_n, $desigDraw_techCon_, $comm_draw_, $w_base_, $w_length_, 
                                                                 $b_length_, $b_width_, $b_height_, $composition_, $layout_, $type_coup_dev_,
                                $tM_cart_,$weight_tare_,$payload_,$size_bC_,$quan_sit_plcs_,$constr_speed_,$existType_EHTK_,$type_brake_, $type_trans_dev_, 
                                  
                                $type_B_dev_,$systemElectr_,$type_gen_,$DGY_,$syfle_,$video_, 
                                  
                                $type_drive_, $vent_cond_, $air_decon_, $aqua_decon_, $F_MV_B_C_, $BRISS_, 
                                $sys_heat_, $cert_, $serv_life_, $runs_, $prod_ys_, $rid_ );
                     }
                     else {
                         $q = mysqli_prepare($conn, "INSERT INTO executions(rid,rid_mdl,nm,factory,country,flg,spec_n,desigDraw_techCon,comm_draw,w_base,w_length,b_length,b_width,b_height,composition,
                                                     layout,type_coup_dev,tM_cart,weight_tare,payload,size_bC,quan_sit_plcs,constr_speed,existType_EHTK,type_brake,type_trans_dev,
                                                     type_B_dev,systemElectr,type_gen,DGY,syfle,video,type_drive,vent_cond,air_decon,aqua_decon,F_MV_B_C,BRISS,sys_heat,cert,serv_life,
                                                     runs,prod_ys,date_create) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                         $rid_ = _dbbase::gen_uuid();
                         mysqli_stmt_bind_param($q, 'sssssissssssssssssssssssssssssssssssssssssss', $rid_, $rid_mdl, $nm_,$factory_,$country_, $flg, $spec_n, $desigDraw_techCon_, $comm_draw_, $w_base_, $w_length_,
                                                                     $b_length_, $b_width_, $b_height_, $composition_,
                                                                     $layout_, $type_coup_dev_, $tM_cart_, $weight_tare_, 
                                                                     $payload_, $size_bC_, $quan_sit_plcs_, $constr_speed_, 
                                                                     $existType_EHTK_, $type_brake_, $type_trans_dev_,
                                                                     $type_B_dev_,$systemElectr_,$type_gen_,$DGY_,$syfle_,$video_,
                                                                     $type_drive_, $vent_cond_, $air_decon_, $aqua_decon_, $F_MV_B_C_, $BRISS_,
                                                                     $sys_heat_, $cert_, $serv_life_, $runs_, $prod_ys_,$date);
                     }

                     //if (mysqli_stmt_execute($q) !== false) //mysqli_stmt_execute($q) !== false может возвращать false, когда все ок, и из-за этого запрос не выполнится!!!
                     mysqli_stmt_execute($q);
                         $result = $rid_;

                    if (is_string($result)) {
                         mysqli_stmt_close($q);
                         mysqli_commit($conn);
                     }

                 _dbbasemy::closeConnection($conn);
            }
        }
        return $result;
    }

    public function get_executions_list($mdl_rid){
        $result = [];
        $conn = $this->connectDB();
        $mdl_rid_ = trim($mdl_rid);
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,nm,factory,country,flg,spec_n,desigDraw_techCon,comm_draw,w_base,w_length,b_length,b_width,b_height,composition,
                                        layout,type_coup_dev,tM_cart,weight_tare,payload,size_bC,quan_sit_plcs,constr_speed,existType_EHTK,type_brake,
                                        type_trans_dev,type_B_dev,systemElectr,type_gen,DGY,syfle,video,type_drive,vent_cond,
                                        air_decon,aqua_decon,F_MV_B_C,BRISS,sys_heat,cert,serv_life,runs,prod_ys FROM executions WHERE rid_mdl='".$mdl_rid_."' ORDER BY date_create DESC");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_nm, $sel_factory,$sel_country,$sel_flg, $sel_spec, $sel_desigDraw_techCon, $sel_comm_draw, $sel_w_base, $sel_w_length, 
                                            $sel_b_length, $sel_b_width, $sel_b_height, $sel_composition, $sel_layout, $sel_type_coup_dev,
                                            $sel_tM_cart,$sel_weight_tare,$sel_payload,$sel_size_bC,$sel_quan_sit_plcs,$sel_constr_speed,
                                            $sel_existType_EHTK,$sel_type_brake, $sel_type_trans_dev, $sel_type_B_dev, $sel_systemElectr,
                                            $sel_type_gen,$sel_DGY, $sel_syfle,$sel_video,
                                            $sel_type_drive, $sel_vent_cond, $sel_air_decon, $sel_aqua_decon, $sel_F_MV_B_C, $sel_BRISS,
                                            $sel_sys_heat, $sel_cert, $sel_serv_life, $sel_runs, $sel_prod_ys);
                
                while(mysqli_stmt_fetch($q))
                     array_push($result, [
                         'rid' => $sel_rid,
                         'nm' => $sel_nm,
                         'factory' => $sel_factory,
                         'country' => $sel_country,
                         'flg' => $sel_flg,
                         'spec_n' => $sel_spec,
                         'desigDraw_techCon' => $sel_desigDraw_techCon,
                         'comm_draw' => $sel_comm_draw,
                         'w_base' => $sel_w_base,
                         'w_length' => $sel_w_length,
                         'b_length' => $sel_b_length,
                         'b_width' => $sel_b_width,
                         'b_height' => $sel_b_height,
                         'composition' => $sel_composition,
                         
                         'layout' => $sel_layout,
                         'type_coup_dev' => $sel_type_coup_dev,
                         'tM_cart' => $sel_tM_cart,
                         'weight_tare' => $sel_weight_tare,
                         'payload' => $sel_payload,
                         'size_bC' => $sel_size_bC,
                         
                         'quan_sit_plcs' => $sel_quan_sit_plcs,
                         'constr_speed' => $sel_constr_speed,
                         'existType_EHTK' => $sel_existType_EHTK,
                         'type_brake' => $sel_type_brake,
                         'type_trans_dev' => $sel_type_trans_dev,
                         
                         'type_B_dev' => $sel_type_B_dev,
                         'systemElectr' => $sel_systemElectr,
                         'type_gen' => $sel_type_gen,
                         'DGY' => $sel_DGY,
                         'syfle' => $sel_syfle,
                         'video' => $sel_video,
                         
                         'type_drive' => $sel_type_drive,
                         'vent_cond' => $sel_vent_cond,
                         'air_decon' => $sel_air_decon,
                         'aqua_decon' => $sel_aqua_decon,
                         'F_MV_B_C' => $sel_F_MV_B_C,
                         'BRISS' => $sel_BRISS,
                         
                         'sys_heat' => $sel_sys_heat,
                         'cert' => $sel_cert,
                         'serv_life' => $sel_serv_life,
                         'runs' => $sel_runs,
                         'prod_ys' => $sel_prod_ys
                     ]);
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        return $result;
    }
    
    public function get_execution_record_by_self_rid($rid){
        $result = [];
        $conn = $this->connectDB();
        $rid_ = trim($rid);
        
        if($conn !== false){
            $q = mysqli_prepare($conn, "SELECT rid,rid_mdl,nm,factory,country,flg,spec_n,desigDraw_techCon,comm_draw,w_base,w_length,b_length,b_width,b_height,composition,                
                                        layout,type_coup_dev,tM_cart,weight_tare,payload,size_bC,quan_sit_plcs,constr_speed,existType_EHTK,type_brake,
                                        type_trans_dev,type_B_dev,systemElectr,type_gen,DGY,syfle,video,type_drive,vent_cond,
                                        air_decon,aqua_decon,F_MV_B_C,BRISS,sys_heat,cert,serv_life,runs,prod_ys FROM executions WHERE rid='".$rid_."'");
            
            if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_rid_mdl, $sel_nm,$sel_factory,$sel_country, $sel_flg, $sel_spec, $sel_desigDraw_techCon, $sel_comm_draw, $sel_w_base, $sel_w_length,
                                            $sel_b_length, $sel_b_width, $sel_b_height, $sel_composition, $sel_layout, $sel_type_coup_dev,
                                            $sel_tM_cart,$sel_weight_tare,$sel_payload,$sel_size_bC,$sel_quan_sit_plcs,$sel_constr_speed,
                                            $sel_existType_EHTK,$sel_type_brake, $sel_type_trans_dev,$sel_type_B_dev, $sel_systemElectr,
                                            $sel_type_gen,$sel_DGY, $sel_syfle,$sel_video,
                                            $sel_type_drive, $sel_vent_cond, $sel_air_decon, $sel_aqua_decon, $sel_F_MV_B_C, $sel_BRISS,
                                            $sel_sys_heat, $sel_cert, $sel_serv_life, $sel_runs, $sel_prod_ys);
                
                if (mysqli_stmt_fetch($q))
                    $result = ['rid' => $sel_rid,
                               'rid_mdl' => $sel_rid_mdl,
                               'nm' => $sel_nm,
                                'factory' => $sel_factory,
                                'country' => $sel_country,
                               'flg' => $sel_flg,
                               'spec_n' => $sel_spec,
                                'desigDraw_techCon' => $sel_desigDraw_techCon,
                                'comm_draw' => $sel_comm_draw,
                                'w_base' => $sel_w_base,
                                'w_length' => $sel_w_length,
                                'b_length' => $sel_b_length,
                                'b_width' => $sel_b_width,
                                'b_height' => $sel_b_height,
                                'composition' => $sel_composition,
                        
                                'layout' => $sel_layout,
                                 'type_coup_dev' => $sel_type_coup_dev,
                                 'tM_cart' => $sel_tM_cart,
                                 'weight_tare' => $sel_weight_tare,
                                 'payload' => $sel_payload,
                                 'size_bC' => $sel_size_bC,

                                 'quan_sit_plcs' => $sel_quan_sit_plcs,
                                 'constr_speed' => $sel_constr_speed,
                                 'existType_EHTK' => $sel_existType_EHTK,
                                 'type_brake' => $sel_type_brake,
                                 'type_trans_dev' => $sel_type_trans_dev,
                        
                                'type_B_dev' => $sel_type_B_dev,
                                'systemElectr' => $sel_systemElectr,
                                'type_gen' => $sel_type_gen,
                                'DGY' => $sel_DGY,
                                'syfle' => $sel_syfle,
                                'video' => $sel_video,
                        
                                'type_drive' => $sel_type_drive,
                                 'vent_cond' => $sel_vent_cond,
                                 'air_decon' => $sel_air_decon,
                                 'aqua_decon' => $sel_aqua_decon,
                                 'F_MV_B_C' => $sel_F_MV_B_C,
                                 'BRISS' => $sel_BRISS,
                        
                                'sys_heat' => $sel_sys_heat,
                                'cert' => $sel_cert,
                                'serv_life' => $sel_serv_life,
                                'runs' => $sel_runs,
                                'prod_ys' => $sel_prod_ys];
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
            _dbbasemy::closeConnection($conn);
        }
        
        return $result;
    }
    
    public function get_last_exe_by_mdl($rid_mdl){
        $result = [];
        $conn = $this->connectDB();
        
        if($conn !== false){
           $q = mysqli_prepare($conn, "SELECT rid,rid_mdl,nm,factory,country,flg,spec_n,desigDraw_techCon,comm_draw,w_base,w_length,b_length,b_width,b_height,composition,                
                                        layout,type_coup_dev,tM_cart,weight_tare,payload,size_bC,quan_sit_plcs,constr_speed,existType_EHTK,type_brake,
                                        type_trans_dev,type_B_dev,systemElectr,type_gen,DGY,syfle,video,type_drive,vent_cond, 
                                        air_decon,aqua_decon,F_MV_B_C,BRISS,sys_heat,cert,serv_life,runs,prod_ys FROM executions WHERE rid_mdl='".$rid_mdl."' 
                                        ORDER BY date_create DESC LIMIT 1"); 
           
           if($q !== false){
                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid, $sel_rid_mdl, $sel_nm,$sel_factory,$sel_country, $sel_flg, $sel_spec, $sel_desigDraw_techCon, $sel_comm_draw, $sel_w_base, $sel_w_length,
                                            $sel_b_length, $sel_b_width, $sel_b_height, $sel_composition, $sel_layout, $sel_type_coup_dev,
                                            $sel_tM_cart,$sel_weight_tare,$sel_payload,$sel_size_bC,$sel_quan_sit_plcs,$sel_constr_speed,
                                            $sel_existType_EHTK,$sel_type_brake, $sel_type_trans_dev,$sel_type_B_dev, $sel_systemElectr,
                                            $sel_type_gen,$sel_DGY, $sel_syfle,$sel_video,
                                            $sel_type_drive, $sel_vent_cond, $sel_air_decon, $sel_aqua_decon, $sel_F_MV_B_C, $sel_BRISS,
                                            $sel_sys_heat, $sel_cert, $sel_serv_life, $sel_runs, $sel_prod_ys);
                
                if (mysqli_stmt_fetch($q))
                    $result = ['rid' => $sel_rid,
                               'rid_mdl' => $sel_rid_mdl,
                               'nm' => $sel_nm,
                                'factory' => $sel_factory,
                                'country' => $sel_country,
                               'flg' => $sel_flg,
                               'spec_n' => $sel_spec,
                                'desigDraw_techCon' => $sel_desigDraw_techCon,
                                'comm_draw' => $sel_comm_draw,
                                'w_base' => $sel_w_base,
                                'w_length' => $sel_w_length,
                                'b_length' => $sel_b_length,
                                'b_width' => $sel_b_width,
                                'b_height' => $sel_b_height,
                                'composition' => $sel_composition,
                        
                                'layout' => $sel_layout,
                                 'type_coup_dev' => $sel_type_coup_dev,
                                 'tM_cart' => $sel_tM_cart,
                                 'weight_tare' => $sel_weight_tare,
                                 'payload' => $sel_payload,
                                 'size_bC' => $sel_size_bC,

                                 'quan_sit_plcs' => $sel_quan_sit_plcs,
                                 'constr_speed' => $sel_constr_speed,
                                 'existType_EHTK' => $sel_existType_EHTK,
                                 'type_brake' => $sel_type_brake,
                                 'type_trans_dev' => $sel_type_trans_dev,
                        
                                'type_B_dev' => $sel_type_B_dev,
                                'systemElectr' => $sel_systemElectr,
                                'type_gen' => $sel_type_gen,
                                'DGY' => $sel_DGY,
                                'syfle' => $sel_syfle,
                                'video' => $sel_video,
                        
                                'type_drive' => $sel_type_drive,
                                 'vent_cond' => $sel_vent_cond,
                                 'air_decon' => $sel_air_decon,
                                 'aqua_decon' => $sel_aqua_decon,
                                 'F_MV_B_C' => $sel_F_MV_B_C,
                                 'BRISS' => $sel_BRISS,
                        
                                'sys_heat' => $sel_sys_heat,
                                'cert' => $sel_cert,
                                'serv_life' => $sel_serv_life,
                                'runs' => $sel_runs,
                                'prod_ys' => $sel_prod_ys];
                
                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);  
           }
            _dbbasemy::closeConnection($conn);   
        }
        return $result;
    }
    
    public function delete_exe_by_rid($rid){
         $result = false;
        
         if ($this->table_ridExists('executions', $rid)) {
            $conn = $this->connectDB();
            
            if($conn !== false){  
                mysqli_autocommit($conn, false);
                
                if($this->delete_doc_by_pid($rid, 'executions')){
                
                    $q = mysqli_prepare($conn, "DELETE FROM executions WHERE rid=?");

                    if (mysqli_stmt_bind_param($q, 's', $rid) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                        mysqli_commit($conn);
                        $result = true;
                    }
                }                       
                _dbbasemy::closeConnection($conn);                
            }
         }
        return $result;
    }
            
    
    public function delete_exe_by_mdl($rid_mdl){
         $result = false;
         $conn = $this->connectDB();
            
            if($conn !== false){
                mysqli_autocommit($conn, false);
                
                $q = mysqli_prepare($conn, "DELETE FROM executions WHERE rid_mdl=?");
                
                if (mysqli_stmt_bind_param($q, 's', $rid_mdl) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                    mysqli_commit($conn);
                    $result = true;
                }
                                       
                _dbbasemy::closeConnection($conn);                
            }
        
        return $result;
    }
    
    public function is_exe_exists_for_actual_mdl($mdl){
        $result = false;
        
        $mdl_ = trim($mdl);
        $conn = $this->connectDB();
        if ($conn !== false) {
            $q = mysqli_prepare($conn, "SELECT rid FROM executions WHERE rid_mdl=?");

            if ($q !== false) {
                mysqli_stmt_bind_param($q, 's', $mdl_);

                mysqli_stmt_execute($q);
                mysqli_stmt_store_result($q);
                mysqli_stmt_bind_result($q, $sel_rid);
                
                if (mysqli_stmt_fetch($q))
                    $result = true;

                mysqli_stmt_free_result($q);
                mysqli_stmt_close($q);
            }
        }
        
        return $result;
    }
    
    // ------------------------------------------
    // docs
    // ------------------------------------------
    
    public function docs_addFile($tbl, $pid, $fnm, $nm, $flg, $rdat){
         $result = "";           
        
        if ($this->table_ridExists($tbl, $pid) && mb_strlen($fnm) > 0 && strlen($rdat) > 0) {
                    
            if ($flg > 0) {
                $conn = $this->connectDB();

                if ($conn !== false) {
                    mysqli_autocommit($conn, false);

                    $q = mysqli_prepare($conn, "INSERT INTO docs(rid,pid,flg,fnm,nm,rdat) VALUES (?,?,?,?,?,?)");

                    $rid_  = _dbbase::gen_uuid();
                    mysqli_stmt_bind_param($q, 'ssissb', $rid_, $pid, $flg, $fnm, $nm, $rdat);

                    if ($q !== false) {
                        mysqli_stmt_send_long_data($q, 5, gzcompress($rdat, 9));

                        if (mysqli_stmt_execute($q) !== false)
                            $result = $rid_; 
                    }

                    mysqli_stmt_close($q);
                    if ($result)
                        mysqli_commit($conn);
                }

                _dbbasemy::closeConnection($conn);
            }
        }

        return $result;
    }
    
    public function get_docs_by_pid($pid){
        $result = [];
        
         $pid_ = trim($pid);
        
        if (strlen($pid_) > 0) {
            $conn = $this->connectDB();

            if ($conn) {                                                                                             // OCTET_LENGTH возвращает длину blob
                $q = mysqli_prepare($conn, "SELECT rid,pid,flg,fnm,nm,rdat,SUBSTRING_INDEX(fnm, '.', -1) AS ftp,COALESCE(OCTET_LENGTH(rdat),0) AS rdatlen FROM docs WHERE pid=? ");

                if ($q !== false) {
                    
                // mysqli_stmt_bind_param используется для того чтобы не передавать парамеьтр напрямую в запрос: "WHERE rid=".$rid."", а
                // привязать параметр через mysqli_stmt_bind_param($q, 's', $rid_), и в запросе тогда будет так: WHERE rid=?                   
                    mysqli_stmt_bind_param($q, 's', $pid_);
                    
                    mysqli_stmt_execute($q);
                    mysqli_stmt_store_result($q);
                    mysqli_stmt_bind_result($q, $sel_rid, $sel_pid, $sel_flg, $sel_fnm, $sel_nm, $sel_rdat, $sel_ftp, $sel_rdatlen);

                    while(mysqli_stmt_fetch($q))
                    array_push($result,
                                    [ 'rid' => $sel_rid,
                                    'pid' => $sel_pid,
                                    'flg' => $sel_flg,
                                    'fnm' => $sel_fnm,
                                    'nm'  => $sel_nm,
                                    'ftp' => $sel_ftp,    // file extension

                                    'rdatlen' => $sel_rdatlen,
                                    'rdat'    => ($sel_rdatlen > 0 ? gzuncompress($sel_rdat) : "")
                              ]);

                    mysqli_stmt_free_result($q);
                    mysqli_stmt_close($q);
                }

                _dbbasemy::closeConnection($conn);
            }
        }

        return $result;
    }
    
    public function get_document ($rid){
        $result = [];

            $rid_ = trim($rid);

            if (strlen($rid_) > 0) {
                $conn = $this->connectDB();

                if ($conn) {                                                                                             // OCTET_LENGTH возвращает длину blob
                    $q = mysqli_prepare($conn, "SELECT rid,pid,fnm,nm,rdat,SUBSTRING_INDEX(fnm, '.', -1) AS ftp,COALESCE(OCTET_LENGTH(rdat),0) AS rdatlen FROM docs WHERE rid=? LIMIT 1");

                    if ($q !== false) {
                        mysqli_stmt_bind_param($q, 's', $rid_);

                        mysqli_stmt_execute($q);
                        mysqli_stmt_store_result($q);
                        mysqli_stmt_bind_result($q, $sel_rid, $sel_pid, $sel_fnm, $sel_nm, $sel_rdat, $sel_ftp, $sel_rdatlen);

                        if (mysqli_stmt_fetch($q))
                            $result = [ 'rid' => $sel_rid,
                                        'pid' => $sel_pid,
                                        'fnm' => $sel_fnm,
                                        'nm'  => $sel_nm,
                                        'ftp' => $sel_ftp,    // file ext

                                        'rdatlen' => $sel_rdatlen,
                                        'rdat'    => ($sel_rdatlen > 0 ? gzuncompress($sel_rdat) : ""),
                                      ];

                        mysqli_stmt_free_result($q);
                        mysqli_stmt_close($q);
                    }

                    _dbbasemy::closeConnection($conn);
                }
            }

            return $result;
    }
    
    public function delete_doc_by_rid($rid){
        $result = false;
        
         if ($this->table_ridExists('docs', $rid)) {
            $conn = $this->connectDB();
            
            if($conn !== false){
                mysqli_autocommit($conn, false);
                
                $q = mysqli_prepare($conn, "DELETE FROM docs WHERE rid=?");
                
                if (mysqli_stmt_bind_param($q, 's', $rid) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                    mysqli_commit($conn);
                    $result = true;
                }
                                       
                _dbbasemy::closeConnection($conn);                
            }
         }
        return $result;
    }
    
    public function delete_doc_by_pid($pid, $table){
        $result = false;
        
         if ($this->table_ridExists($table, $pid)) {
            $conn = $this->connectDB();
            
            if($conn !== false){
                mysqli_autocommit($conn, false);
                
                $q = mysqli_prepare($conn, "DELETE FROM docs WHERE pid=?");
                
                if (mysqli_stmt_bind_param($q, 's', $pid) && mysqli_stmt_execute($q) && mysqli_stmt_close($q)){
                    mysqli_commit($conn);
                    $result = true;
                }
                                       
                _dbbasemy::closeConnection($conn);                
            }
         }
        return $result;
    }
};

/*
$db = new wagon_types_db();
$result =  $db->mdlsList_whole_by_multi('Германия', 'Торжокский завод', '', '');
var_dump($result);
*/

/*
$db = new wagon_types_db();
var_dump($db-> get_factories ());
*/




?>

