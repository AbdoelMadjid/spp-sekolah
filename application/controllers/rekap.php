<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rekap extends Alazka_Controller {

function __construct()
    {
        parent::__construct();
        $this->deny_group('ksr');

	    	$this->load->model('Unit_model');		
				$this->load->model('Rate_model');
				$this->load->model('Kelas_model');
				$this->load->model('Report_model');
				$this->load->model('TahunAkademik_model');
    }
 
	
	public function index()
	{
		$data['data_unit']=$this->Unit_model->get_all_unit();			
		$data['list_rate_category']=$this->Rate_model->get_all_category();			
		$data['page']='index';
		$this->load->view('site/header_view');
		$this->load->view('site/rekap_view',$data);
		 $this->load->view('site/footer_view');
	}
	
	/**
	 * Method untuk menambahkan javascript datepicker pada HEAD
	 *
	 * @author Rio Astamal <me@rioastamal.net>
	 *
	 * @return void
	 */
	public function add_more_javascript() {
		//adminique template already has datepicker support, please read the doc
		//printf('<script type="text/javascript" src="%s"></script>', base_url() . 'datepicker/datetimepicker_css.js');
	}

	public function cetak() {
		$tipe = $this->input->post('tipe');
		$persiswa = $this->Rate_model->get_all_category();
		if (in_array($tipe, $persiswa)) {
			$this->laporan_persiswa($tipe);
		}
	}
	protected function laporan_persiswa($kategori) {
	  $start = $this->input->post('tgl-mulai');
	  $end = $this->input->post('tgl-selesai');

		$jenjang = $this->input->post('tx_unit');
		$filename = strtolower(str_replace(array('Uang ', ' '), array('', '_'), 'tagihan_'.$kategori.$jenjang.$start) . '.pdf');
		$data['list_payment']=$this->Report_model->getAllPendingInvoice($kategori, $jenjang, $start, $end);
		if ($jenjang > -1) {
			$nm_jenjang=$this->Unit_model->get_all_unit(array('id'=>$jenjang));			
			$nm_jenjang=$nm_jenjang[0]->nama;
		} else {
			$nm_jenjang = array();
			foreach ($this->Unit_model->get_all_unit() as $j) { array_push($nm_jenjang, $j->nama); }

			$nm_jenjang = implode(', ', $nm_jenjang);
		}
		$data['kategori'] = $kategori;
		$data['ajaran'] = $this->TahunAkademik_model->berjalan->get_tahun();
		$data['start'] = $start;
		$data['end'] = $end;
		$data['nm_jenjang'] = $nm_jenjang;
		$data['page_title'] = 'Daftar Kewajiban Siswa';
		//$this->load->view('site/rekap_penerimaan_siswa_view', $data);
		$content = $this->load->view('site/detil_tunggakan_per_siswa_view', $data, true);

		$this->load->library('pdf');
    $this->pdf->SetSubject('Daftar Tunggakan');
    $this->pdf->SetKeywords('Laporan Tunggakan Tagihan Al Azhar Kelapa Gading Surabaya');      
		$this->pdf->SetHeaderData('alazka.jpg', 0, "                                        YAYASAN AL AZHAR KELAPA GADING", "                                             Jl. Taman Bhaskara Utara, Mulyorejo - Surabaya\n                                           Telp. (031) 5927420, 5927447, Fax. (031) 5938179 ");
		$this->pdf->setHeaderFont(Array('times', '', '14'));
		$this->pdf->setFooterFont(Array('times', '', '12'));
    $this->pdf->SetFont('times', '', 10);   
    $this->pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);    
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 5, PDF_MARGIN_RIGHT);
    $this->pdf->AddPage(); 
  	$this->pdf->writeHTML($content);
		$this->pdf->lastPage();		
		$this->pdf->Output($filename, 'I');  
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
