<?php
abstract class Model {
	protected $registry;

    protected $tableName;
    protected $id;
    protected $defaultSort;
    protected $defaultJoins;
    protected $tableAlias;

    public function setTableAlias($tableAlias)
    {
        $this->tableAlias = $tableAlias;
    }

	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    public function getOne($id,$select = false)
    {
        $sql = 'SELECT ';

        if($select)
        {
            $sql .= $select;
        }
        else
        {
            $sql .= ' * ';
        }

        $sql .= ' FROM '.DB_PREFIX.$this->tableName;


        $sql .= " WHERE `".$this->id."` = '".(int)$id."' ";

        $q = $this->db->query($sql);

        return $q->rows;

    }



    public function getMany($params = false,$limit = false,$sort = false,$joins = array(), $select = false)
    {
        $sql = 'SELECT ';

        if($select)
        {
            $sql .= $select;
        }
        else
        {
            $sql .= ' * ';
        }

        $sql .= ' FROM '.DB_PREFIX.'`'.$this->tableName.'` '.$this->tableAlias;

        if($this->defaultJoins)
        {
            foreach($this->defaultJoins as $join)
            {
                $joins[] = $join;
            }
        }

        if(!empty($joins))
        {
            foreach($joins as $join)
            {
                $sql .= ' '.$join['type'].' JOIN `'.$join['table'].'` '.$join['alias'].' ON('.$this->tableAlias.'.'.$join['field'].'='.$join['alias'].'.'.$join['field'].') ';
            }
        }

        if(is_array($params))
        {
            foreach($params as $key => $param)
            {
                if($key === 0)
                {
                    $sql .= ' WHERE ';


                }
                else
                {
                    $sql .= ' AND ';
                }

                if(isset($param['alias']))
                {
                    $param['column'] = $param['alias'].".`".$param['column']."`";
                }
                else
                {
                    $param['column'] = "`".$param['column']."`";
                }

                if($param['type'] == 'string')
                {
                    $sql .= " ".$param['column']." ".$param['relation']." '".$this->db->escape($param['value'])."' ";
                }
                elseif($param['type'] == 'float')
                {
                    $sql .= " ".$param['column']." ".$param['relation']." '".(float)$param['value']."' ";
                }
                else
                {
                    $sql .= " ".$param['column']." ".$param['relation']." '".(int)$param['value']."' ";
                }
            }
        }



        if($this->defaultSort)
        {
            $sort[] = $this->defaultSort;
        }

        if($sort)
        {

            foreach($sort as $key => $s)
            {

                if(isset($s['alias']))
                {
                    $s['column'] = $s['alias'].".`".$s['column']."`";
                }
                else
                {
                    $s['column'] = "`".$s['column']."`";
                }

                if($key === 0 )
                {
                    $sql .= " ORDER BY ".$s['column']." ".$s['order'];
                }
                else
                {
                    $sql .= ", ".$s['column']." ".$s['order'];
                }
            }

        }

        if($limit)
        {
            $sql .= " LIMIT " . (int)$limit['start'] . "," . (int)$limit['stop'];
        }


        $q = $this->db->query($sql);

        return $q->rows;
    }

    public function setDefaultSort($defaultSort)
    {
        $this->defaultSort = $defaultSort;
    }

    public function setDefaultJoins($defaultJoins)
    {
        $this->defaultJoins = $defaultJoins;
    }



}
?>