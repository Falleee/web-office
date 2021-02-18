<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_email extends CI_Model
{
    public function import_data($dataemail)
    {
        $jumlah = count($dataemail);
        if ($jumlah > 0) {
            $this->db->replace('mail', $dataemail);
        }
    }

    public function getDataEmail()
    {
        return $this->db->get('mail')->result_array();
    }

    public function hapusemail()
    {
        $id = $this->uri->segment(3); 
        $this->db->where('id',$id);
        $this->db->delete('mail');
    }
}
