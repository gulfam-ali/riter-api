<?php
class Feed extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->itemPerpage;
  	}

    public function feed($cond = '')
    {
		$result = $this->select("posts");

		return $result;
    }
	
	public function story($story_id)
    {
		$result = $this->select("posts", " id='".$story_id."' ");
		return $result;
    }

}
