<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_email');
    }



    public function index()
    {
        $data['title'] = 'Email';
        $data['semuaemail'] = $this->M_email->getDataEmail();
        $this->load->view('index', $data);
    }

    public function uploaddata()
    {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['file_name'] = 'doc' . time();
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('importexcel')) {
            $file = $this->upload->data();
            $reader = ReaderEntityFactory::createXLSXReader();

            $reader->open('uploads/' . $file['file_name']);
            foreach ($reader->getSheetIterator() as $sheet) {
                $numRow = 1;
                foreach ($sheet->getRowIterator() as $row) {
                    if ($numRow > 1) {
                        $dataemail = array(
                            // 'kode_barang'  => $row->getCellAtIndex(1),
                            'email'  => $row->getCellAtIndex(1),
                            // 'jumlah'       => $row->getCellAtIndex(3),
                            // 'date_created' => time(),
                            // 'date_modified' => time(),
                        );
                        $this->M_email->import_data($dataemail);
                    }
                    $numRow++;
                }
                $reader->close();
                unlink('uploads/' . $file['file_name']);
                $this->session->set_flashdata('pesan', '<div class="alert alert-dark alert-dismissible fade show" role="alert">
                Import Data Berhasil !
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>');
                redirect('auth');
            }
        } else {
            echo "Error :" . $this->upload->display_errors();
        };
    }
    public function uploademail()
    {
        //penaturan email
        $this->load->library('email'); // panggil library email codeigniter
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => 465,
            'smtp_user' => 'kpm.hmit@gmail.com',//alamat email gmail
            'smtp_pass' => 'kpmhmitkpm',//password email 
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );
        $message = $this->input->post('isi'); //isi email
        $subject = $this->input->post('subject');

        $data = $this->M_email->getDataEmail();
        foreach ($data as $a){ 
        $email = $a['email']; //email penerima
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from($config['smtp_user']);
        $this->email->to($email);
        $this->email->subject($subject);//subjectemail
        $this->email->message($message);

        //proses kirim email
        if ($this->email->send()) {
            $this->session->set_flashdata('pesan','<div class="alert alert-dark alert-dismissible fade show" role="alert">
            Sukses Kirim Email !
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>');
            
        }
        else{
            $this->session->set_flashdata('pesan', $this->email->print_debugger());

        }
        }
        redirect('auth');
    }
    public function deleteemail($id)
    {
        $this->load->model('m_email');
        $where = array('id' => $id);
        $this->m_email->hapusemail($where,'mail');
        redirect('auth');
    }
}
