<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

 class PostTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll($limit = null, $offset = 0)
     {
         if (0 != (int)$limit) {
             $resultSet = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use ($limit, $offset) {
                 $select->limit($limit)->offset($offset);
             });
         } else {
             $resultSet = $this->tableGateway->select();
         }

         return $resultSet;
     }

     public function getPost($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function savePost(Post $post)
     {
         $data = array(
             'email' => $post->email,
             'name'  => $post->name,
             'message' => $post->message,
             'ip' => $post->ip,
             'agent' => $post->agent,
             'website' => $post->website
         );

         $id = (int) $post->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
             return $this->tableGateway->getLastInsertValue();
         } else {
             if ($this->getPost($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
                 return $id;
             } else {
                 throw new \Exception('Post id does not exist');
             }
         }
     }

     public function deletePost($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }