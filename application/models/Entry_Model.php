<?php

class Entry_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    public function search_for($content)
    {
        if($content == "")
        {
            return;
        } else
        {
            $sql = "SELECT id_entry, name FROM entry 
                WHERE name LIKE '%" . $content . "%';";

            $query = $this->db->query($sql);

            return $query;
        }
    }
}

?>
