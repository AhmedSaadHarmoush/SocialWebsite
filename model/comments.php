<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of comments
 *
 * @author omar
 */
class comments {
    public $link;
    public $functions;
    public $date;
    public function __construct() {
        include_once 'connect.php';
        include_once 'functions.php';
        $this->functions=new db_functions();
        $this->link=$this->functions->link;
        $this->functions->table_name="comments";
        $this->date=date('Y/m/d h:i:s', time());
    }
    public function add_comment($user_id,$post_id,$comment_body,$post_type=false){
        $comment['user_id']=$user_id;
        $comment['post_id']=$post_id;
        $comment['body']=$comment_body;
        if($post_type){
            $comment['post_type']=$post_type;
        }else{
            $comment['post_type']=0;
        }
        $comment['created']=$this->date;
        try{
            $comment_id=$this->functions->insert($comment);
            return $comment_id;
        } catch (Exception $ex) {
            echo mysqli_errno($this->link);
        }
    }
    public function delete_comment($user_id,$comment_id){
        try{
            $specific_row['cols']['user_id']=$user_id;
            $check=$this->functions->select(array('id'), $comment_id, false, false, $specific_row);
            if($check){
                return $this->functions->delete($comment_id);
            }else{
                die("User Not Valid !");
            }
        } catch (Exception $ex) {
            echo mysqli_errno($this->link);
        }
    }
    public function edit_comment($user_id,$comment_id,$comment_body){
        try{
            $specific_row['cols']['user_id']=$user_id;
            $check=$this->functions->select(array('id'), $comment_id, false, false, $specific_row);
            if($check){
                $comment_edit['body']=$comment_body;
                $comment_edit['last_edit']=$this->date;
                return $this->functions->update($comment_id);
            }else{
                die("User Not Valid !");
            }
        } catch (Exception $ex) {
            echo mysqli_errno($this->link);
        }
    }
    public function count_comments($post_id){
        $specific_row['cols']['post_id']=$post_id;
        $this->functions->select(array('id'), false, FALSE, FALSE, $specific_row);
        return mysqli_affected_rows($this->link);
    }
    public function view_comments($post_id,$limit,$post_type=false){
//        $order['date']='DESC';
//        $limit[$set_no]=5;
//        $specific_row['cols']['post_id']=$post_id;
//        try{
//            $comments=$this->functions->select(false, false, $order, $limit, $specific_row);
//            if($comments!=0){
//                return $comments;
//            }
//        } catch (Exception $ex) {
//            echo mysqli_errno($this->link);
//        }
        foreach ($limit as $key=>$value){
            $items_no=$key;
            if($value==1){
                $set_no=0;
            }
            else{
                $set_no=($key*$value)-$key;
            }
        }
        if(!$post_type){
            $post_type=0;
        }
        $query="SELECT comments.*,users.username,users.id AS users_id "
//        . ", COUNT(rating.id) AS count_likes "
        . "FROM comments "
        . "LEFT JOIN users ON users.id=comments.user_id "
//        . "LEFT JOIN rating ON rating.type=2 AND rating.post_id=comments.id "
        . "WHERE comments.post_id=$post_id AND comments.post_type=$post_type "
        . "ORDER BY comments.created ASC "
        . "LIMIT $set_no,$items_no";
        try{
            $sql= mysqli_query($this->link, $query);
            if(mysqli_affected_rows($this->link)>0){
                $comments=array();
                while($row=mysqli_fetch_array($sql)){
                    $comments[]=$row;
                }
                return $comments;
            }else{
                return mysqli_affected_rows($this->link);
            }
        }catch(Exception $ex){
            echo mysqli_error($this->link);
        }
    }
}
