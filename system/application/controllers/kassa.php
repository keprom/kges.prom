<?php
class Kassa extends Controller
{
	function index()
	{
		$this->load->view("kassa/view");
	}
	function add_oplata()
	{
		$this->db->where("number",$_POST['number']);
		$abonent=$this->db->get("kassa.abonent");
		$data["abonent"]=
			($abonent->num_rows>0?$abonent:NULL);
		$data["abonent_number"]=$_POST['number'];
		
		$this->db->order_by('name');
		$data['street']=$this->db->get("kassa.street");
		
		$this->load->view("kassa/add_oplata",$data);
	}
	function adding_oplata()
	{
		$this->session->set_userdata(array('kassa_data'=>$_POST['data']));
		$this->db->insert('kassa.oplata',$_POST);
		redirect('kassa/index');
	}
	function view_oplata()
	{
		$data['data']=$_POST['data'];
		$this->db->where ('data',$_POST['data']);
		$data['oplata']=$this->db->get('kassa.oplata_view');
		$this->load->view('kassa/oplata_list',$data);
	}
	
	function delete_oplata()
	{
		$this->db->where('id',$this->uri->segment(3));
		$this->db->delete('kassa.oplata');
		redirect('kassa/index');
	}
		function obn_abon()
	{
		$this->db->where("number",$_POST['number']);
		$abonent=$this->db->get("kassa.abonent");
		$data["abonent"]=
			($abonent->num_rows>0?$abonent:NULL);
		$data["abonent_number"]=$_POST['number'];
		
		$this->db->order_by('name');
		$data['street']=$this->db->get("kassa.street");
		
		$this->load->view("kassa3/obn_abon",$data);
	}
	function obn_ab()
	{
	$this->db->where('number',$_POST['number']);
	$this->db->update('kassa.abonent',
			array(
			
			'fio'=>$_POST['fio'],
			'number'=>$_POST['number'],
			'street_id'=>$_POST['street_id'],
			'kv'=>$_POST['kv'],
				'dom'=>$_POST['dom']
				
			)
		);
		$this->db->where('number',$_POST['number']);
		$data['number']=$this->db->get('kassa.abonent')->row();
		$data['fio']=$_POST['fio'];
		$data['number']=$_POST['number'];
	$data['street_id']=$_POST['street_id'];
	$data['kv']=$_POST['kv'];	
	$data['dom']=$_POST['dom'];	
		redirect('kassa/index');}

function adding_abonent()
	{
		$this->session->set_userdata(array('kassa_data3'=>$_POST['data']));
		$this->db->insert('kassa.abonent',$_POST);
		redirect('kassa/index');
	}
		function add_abonent()
	{
		$this->db->where("number",$_POST['number']);
		$abonent=$this->db->get("kassa.abonent");
		$data["abonent"]=
			($abonent->num_rows>0?$abonent:NULL);
		$data["abonent_number"]=$_POST['number'];
		
		$this->db->order_by('name');
		$data['street']=$this->db->get("kassa.street");
		
		
		$this->load->view("kassa/add_abonent",$data);
	}
	
}
	


?>