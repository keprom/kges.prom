<?php

class Billing extends Controller
{
    function datetostring($date)
    {
        $d = explode("-", $date);
        return $d['2'] . '.' . $d['1'] . '.' . $d['0'];
    }

    function shortdate($date)
    {
        $d = explode("-", $date);
        return $d['1'] . '.' . substr($d['0'], 2, 2);
    }

    function f_d_graph($var)
    {
        if ($var == 0) return "0"; else
            return trim(sprintf("%22.2f", $var));
    }

    function d2($date)
    {
        $d = explode("-", $date);
        return $d['2'] . '.' . $d['1'] . '.' . substr($d['0'], 2, 2);
    }

    function execute($function)
    {
        if (($this->session->userdata('billing/' . $function) == 't') or
            ($this->session->userdata('login') == 'programmist') or ($this->session->userdata('login') == 'admin'))
            eval('$this->' . $function . '();');
    }

    public function narpoture()
    {
        $data['narzad'] = $this->db->get("industry.nar_zad")->result();
        #$this->left();
        $this->load->view('nar_zad', $data);
        #$this->load->view('right');
        #$this->load->view('right');
    }

    function Billing()
    {
        parent::Controller();
        set_time_limit(0);

        $is_login = $this->session->userdata('is_login');
        if ($is_login != TRUE) {
            redirect("login/billing");
            die();
        }

        $class_method = $this->uri->segment(1) . '/' . $this->uri->segment(2);
        $user_id = $this->session->userdata('id');
        date_default_timezone_set('Asia/Almaty');

        $action = array(
            "user_id" => $user_id,
            "class_method" => $class_method,
            "action_time" => date("Y-m-d H:i:s"),
            "ip_address" => $_SERVER['REMOTE_ADDR']
        );

        $first_arg = $this->uri->segment(3);
        $second_arg = $this->uri->segment(4);

        if (!empty($first_arg)) {
            $action['first_arg'] = $first_arg;
            if (!empty($second_arg)) {
                $action['second_arg'] = $second_arg;
            }
        }

        $this->db->insert("industry.user_action", $action);
        if ($class_method == '/') redirect("billing");

        if (($this->session->userdata('login') == 'programmist') or (($this->session->userdata('login') == 'admin') and ($class_method != 'billing/oplata'))) {
            if (($class_method != 'billing/jpeg') and ($this->session->userdata('admin') == 'programmist'))
                $this->output->enable_profiler(TRUE);
        } else {
            $class_method = $this->uri->segment(1) . '/' . $this->uri->segment(2);
            if ($this->session->userdata($class_method) != 't') {
                if (($class_method == 'billing/') or ($class_method == 'billing/index')) {
                    redirect("login");
                    die('<h1>Доступ запрещен</h1>');
                }
                redirect("billing");
                die('<h1>Доступ запрещен</h1>');
            }
        }

        #$this->output->enable_profiler(TRUE);
    }

    function left($title = NULL)
    {
        $data['title'] = !is_null($title) ? $title : 'Учет электроэнергии. Промышленный отдел';
        #added
        $data['month_to_look'] = $this->db->query("select * from industry.current_period()")->row()->current_period;
        #end of added
        $data['poisk'] = $this->session->userdata('poisk');
        if ($this->session->userdata('poisk') == NULL) $data['poisk'] = '1';
        $this->load->view("left", $data);
        $this->load->view("messages");
    }

    function phpinfo()
    {
        echo phpinfo();
    }

    function index()
    {
        $this->left("Выбор фирмы");
        $this->db->order_by('dogovor');
        $data['query'] = $this->db->get("industry.firm_overview");
        $this->load->view("billing_view", $data);
        $this->load->view("right");
    }

    function my_firm()
    {
        $this->left();
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('dogovor');
        $data['query'] = $this->db->get("industry.firm_overview");
        $this->load->view("billing_view", $data);
        $this->load->view("right");
    }

    function my_firm_not_closed()
    {
        $this->left();
        $this->db->where('is_closed is null', null, FALSE);
        $this->db->where('firm_closed', "FALSE");
        $this->db->where('user_id', $this->session->userdata('id'));
        $this->db->order_by('dogovor');
        $data['query'] = $this->db->get("industry.firm_overview");
        $this->load->view("billing_view", $data);
        $this->load->view("right");
    }

    function firm_not_closed()
    {
        $this->left();
        $this->db->where('is_closed is null', null, FALSE);
        $this->db->where('firm_closed', "FALSE");
        $this->db->order_by('dogovor');
        $data['query'] = $this->db->get("industry.firm_overview");
        $this->load->view("billing_view", $data);
        $this->load->view("right");
    }


    function firm()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['r'] = $this->db->get('industry.firm_view')->row();

        $sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
        $data['period'] = $this->db->query($sql);
        $sql = "Select industry.is_closed(" . $this->uri->segment(3) . ") as closed";
        $data['is_closed'] = $this->db->query($sql)->row();
        $this->left("#" . $data['r']->dogovor);
        $this->load->view("firm_view", $data);
        $this->execute("points");
        $this->load->view("right");
    }

    function firm_edit()
    {
        $firm_id = $this->uri->segment(3);
        $sql = "SELECT firm.id,firm.dogovor,firm.address, firm.name,  firm.telefon, firm.rnn, firm.dogovor_date, firm.is_pot, firm.period_id FROM industry.firm WHERE  firm.id=" . $this->uri->segment(3);
        $this->db->where('id', $this->uri->segment(3));
        $data['r'] = $this->db->get('industry.firm');

        $data['firm'] = $this->get_firm_info_by_id($firm_id);

        $sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
        $data['period'] = $this->db->query($sql);
        $this->db->order_by('name');
        $data['firm_subgroup'] = $this->db->get('industry.firm_subgroup');
        $this->db->order_by('name');
        $data['bank'] = $this->db->get('industry.bank');
        $this->db->order_by('name');
        $data['is_pot'] = $this->db->get('industry.poter');
        $this->db->order_by('id');
        $data['period'] = $this->db->get('industry.period');
        $this->db->order_by('name');
        $data['user'] = $this->db->get('industry.user');
        $this->db->order_by('name');
        $data['firm_otrasl'] = $this->db->get('industry.firm_otrasl');
        $this->db->order_by('name');
        $data['firm_power_group'] = $this->db->get('industry.firm_power_group');
        $this->db->order_by('name');
        $data['ture'] = $this->db->get('industry.ture');
        $this->left();
        $this->load->view("firm_edit", $data);
        $this->load->view("right");
    }

    function firm_edition()
    {
        $this->db->where('id', $this->uri->segment(3));
        if ($_POST['phase_a'] == '') {
            $_POST['phase_a'] = NULL;
        }
        if ($_POST['phase_b'] == '') {
            $_POST['phase_b'] = NULL;
        }
        if ($_POST['phase_c'] == '') {
            $_POST['phase_c'] = NULL;
        }
        $this->db->update('industry.firm', $_POST);
        redirect("billing/firm/" . $this->uri->segment(3));
    }

    function close_firm()
    {
        $sql = "SELECT industry.close_firm(" . $this->uri->segment(3) . ")";
        $this->db->query($sql);
        redirect("billing/firm/" . $this->uri->segment(3));
    }

    function full_close_firm()
    {
        $s = "";
        if (strlen(trim($_POST['vremenno'])) == 0) {
            $s = "1";
        } else {
            $s = "2";
        }
        $sql = "update industry.firm set firm_closed= not firm_closed, close_type = " . $s . " where id =" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect("billing/firm/" . $this->uri->segment(3));
    }

    function open_firm()
    {
        $sql = "SELECT industry.open_firm(" . $this->uri->segment(3) . ")";
        $this->db->query($sql);
        redirect("billing/firm/" . $this->uri->segment(3));
    }

    function add_firm()
    {
        $data['banks'] = $this->db->get('industry.bank');
        $data['subgroups'] = $this->db->get('industry.firm_subgroup');
        $this->left();
        $this->load->view("add_firm_view", $data);
        $this->load->view("right");
    }

    function adding_firm()
    {
        $_POST['dogovor_date'] = $_POST['year'] . "." . $_POST['month'] . "." . $_POST['day'];
        unset($_POST['year']);
        unset($_POST['day']);
        unset($_POST['month']);
        $this->db->insert("industry.firm", $_POST);
        $this->index();
    }

    function firm_search_by()
    {
        $sql = "select distinct firm_id from industry.billing_point_ex where ";
        if ($_POST['type'] != '1') $sql = "select  * from industry.firm_overview where firm_id in ( " . $sql;
        $str = $_POST['str'];
        $this->session->set_userdata(array('poisk' => $_POST['type']));
        if ($_POST['type'] == '1') {
            $sql .= " dogovor = " . $str;
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                redirect("billing/firm/" . $query->row()->firm_id);
            } else {
                redirect("billing/");
            }
        } else {
            $arr = explode(" ", $str);
            $first = true;
            if ($_POST['type'] == 2) {
                $t = "billing_point_address";
            }
            if ($_POST['type'] == 3) {
                $t = "firm_address";
            }
            if ($_POST['type'] == 4) {
                $t = "rnn";
            }
            if ($_POST['type'] == 5) {
                $t = "tp_name";
            }
            if ($_POST['type'] == 6) {
                $t = "telefon";
            }
            if ($_POST['type'] == 7) {
                $t = "firm_name";
            }
            if ($_POST['type'] == 8) {
                $t = "billing_point_name";
            }
            if ($_POST['type'] == 9) {
                $t = "gos_nomer";
            }
            // if ($_POST['type']==10) {$t="firm_bin";}
            if ($_POST['type'] == 10) {
                $t = "bin";
            }
            foreach ($arr as $a) {
                trim($a);
                if ($first == FALSE) {
                    $sql .= " and $t " . ($_POST['type'] != 5 ? "ilike '%$a%'" : "='$a'");
                    $first = FALSE;
                }
                if ($first == TRUE) {
                    $sql .= " $t " . ($_POST['type'] != 5 ? "ilike '%$a%'" : "='$a'");
                    $first = FALSE;
                }
            }
            $sql .= ") order by dogovor";
            $this->left();
            $data['query'] = $this->db->query($sql);
            $this->load->view("billing_view", $data);
            $this->load->view("right");
        }
    }

    function gd()
    {
        phpinfo();
    }

    //// СПРАВОЧНИКИ
    function streets()
    {
        $this->db->order_by("name");
        $data['query'] = $this->db->get("common_info.street");
        $this->left();
        $this->load->view("sprav/streets_view", $data);
        $this->load->view("right");
    }

    function adding_streets()
    {
        if (trim($_POST['name']) != "")
            $this->db->insert('common_info.street', $_POST);
        redirect("billing/streets");
    }

    function counter_types()
    {
        $this->db->order_by("name");
        $data['query'] = $this->db->get("industry.counter_type");
        $this->left();
        $this->load->view("sprav/counter_types_view", $data);
        $this->load->view("right");
    }

    function adding_counter_types()
    {
        if (trim($_POST['name']) != "")
            $this->db->insert('industry.counter_type', $_POST);
        redirect("billing/counter_types");
    }

    function tp()
    {
        $this->db->order_by("name");
        $data['query'] = $this->db->get("industry.tp");
        $this->left();
        $this->load->view("sprav/tp_view", $data);
        $this->load->view("right");

    }

    function edit_tp()
    {
        $this->db->order_by("name");
        $this->db->where('id', $this->uri->segment(3));
        $data['query'] = $this->db->get("industry.tp")->row();
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("sprav/tp_edit", $data);
        $this->load->view("right");

    }

    function edition_tp()
    {
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('industry.tp', $_POST);
        redirect('billing/edit_tp/' . $this->uri->segment(3));
    }

    function adding_tp()
    {
        if (trim($_POST['name']) != "") {
            $_POST['ture_id'] = isset($_POST['ture_id']) ? $_POST['ture_id'] : 0;
            $this->db->insert('industry.tp', $_POST);
        }
        redirect("billing/tp");
    }

    function banks()
    {
        $this->db->order_by("name");
        $data['query'] = $this->db->get("industry.bank");
        $this->left();
        $this->load->view("sprav/banks_view", $data);
        $this->load->view("right");

    }

    function bank_edit()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['bank_id'] = $this->uri->segment(3);
        $data['bank'] = $this->db->get('industry.bank')->row();
        $this->left();
        $this->load->view('sprav/bank_edit', $data);
        $this->load->view('right');
    }

    function edition_bank()
    {
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('industry.bank', $_POST);
        redirect('billing/banks');
    }

    function adding_banks()
    {
        $this->db->insert('industry.bank', $_POST);
        redirect("billing/banks");
    }

    ///// конец справочники
    function points()
    {
        $this->db->where('firm_id', $this->uri->segment(3));
        $result = $this->db->get("industry.point_list");
        if ($result->num_rows() > 0) {
            $data['result'] = $result;
            $this->load->view("points_view", $data);
        } else {
            echo "Нету точек учета <br><br>";
        }
        $this->execute("add_point");
    }

    function add_point()
    {
        $data['firm_id'] = $this->uri->segment(3);
        $this->db->order_by('name');
        $data['tps'] = $this->db->get('industry.tp');
        $this->load->view("add_billing_point", $data);
    }
    /*
	function adding_point()
	{
		$this->db->insert("industry.billing_point",$_POST);
		redirect("billing/firm/".$_POST['firm_id']);
	}
	*/

    #установка точки учета
    function adding_point()
    {
        $this->db->insert("industry.billing_point", $_POST);
        $bill_id = $this->db->insert_id();
        $this->db->where('id', (int)$_POST['firm_id']);
        $bill_period_id = $this->db->get('industry.firm')->row()->period_id;
        $poteri_source = array(
            'bill_id' => $bill_id,
            'period_id' => $bill_period_id
        );
        $this->db->insert("industry.poteri_period", $poteri_source);
        redirect("billing/firm/" . $_POST['firm_id']);
    }

    #end of установка точки учета

    function close_billing_point()
    {
        $this->check_billing_point($this->uri->segment(3));
        $sql = "update industry.billing_point set deleted_point=true where id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect("billing/firm/" . $this->uri->segment(4));
    }

    function tp_billing_point()
    {
        $sql = "update industry.billing_point set in_tp= not in_tp where id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect("billing/firm/" . $this->uri->segment(4));
    }

    public function get_firm_info_by_id($firm_id)
    {
        $this->db->where("id", $firm_id);
        return $this->db->get("industry.firm")->row();
    }

    public function get_firm_info_by_point_id($point_id)
    {
        $this->db->where("id", $point_id);
        $firm_id = $this->db->get("industry.billing_point")->row()->firm_id;
        return $this->get_firm_info_by_id($firm_id);
    }

    public function get_firm_info_by_counter_id($counter_id)
    {
        $this->db->where("id", $counter_id);
        $point_id = $this->db->get("industry.counter")->row()->point_id;
        return $this->get_firm_info_by_point_id($point_id);
    }

    public function get_firm_info_by_values_set_id($values_set_id)
    {
        $this->db->where("id", $values_set_id);
        $counter_id = $this->db->get("industry.values_set")->row()->counter_id;
        return $this->get_firm_info_by_counter_id($counter_id);
    }

    function point()
    {
        $point_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_point_id($point_id);

        $sql = "SELECT * FROM industry.billing_point where id=" . $point_id;
        $data['point_data'] = $this->db->query($sql)->row();
        $sql = "SELECT counter.*,counter_type.name as type from industry.counter,industry.counter_type where counter.type_id=counter_type.id and  point_id=" . $point_id . " order by data_finish";
        $data['query'] = $this->db->query($sql);

        $sql = "select * from industry.counter where data_start is null and point_id=" . $point_id;
        $query = $this->db->query($sql);
        $this->left();

        if ($query->num_rows() > 0) $data['snyat'] = 'yes'; else $data['snyat'] = 'false';
        $this->load->view("counters_view", $data);
        if ($data['snyat'] == "false")
            $this->execute("add_counter");
        $this->execute("nadbavka_ot");
        $this->execute("sovm_otn");
        $this->execute("sovm_by_counter");
        $this->execute("add_nagruz");
        $this->execute("nagruz_dob2");
        $this->load->view("right");
    }
    /*
	function add_nagruz()
	{
		$data['point_id']=$this->uri->segment(3);
		$sql="SELECT * FROM industry.billing_point where id=".$this->uri->segment(3);
		$data['point_data']=$this->db->query($sql)->row();

		$this->load->view('nagruzz',$data);
	}
	*/

    #страница потерь
    function add_nagruz()
    {
        $data['point_id'] = $this->uri->segment(3);
        $bill_id = (int)$this->uri->segment(3);
        $this->db->where('bill_id', $bill_id);
        $this->db->order_by('period_id');
        $data['bp_options'] = $this->db->query(
            "select 
			  poteri_period.*,
			  period.name as period_name  
			  from industry.poteri_period
			  join industry.period on period.id = poteri_period.period_id
			  where bill_id =  $bill_id"
        )->result();
        $sql = "SELECT * FROM industry.billing_point WHERE id=" . $data['point_id'];
        $data['point_data'] = $this->db->query($sql)->row();
        $this->load->view('nagruzz', $data);
    }

    #end of страница потерь

    function nagruz_dob2()
    {
        $sql = "SELECT value::integer FROM industry.sprav WHERE name='current_period'";
        $a = $this->db->query($sql)->row()->value;
        $sql = "SELECT * FROM industry.current_poteri where id=" . $this->uri->segment(3);
        $data['nagruz'] = $this->db->query($sql);
        $sql = "SELECT * FROM industry.poteri where billing_point_id=" . $this->uri->segment(3) . " and period_id=" . $a;
        $data['nagruz1'] = $this->db->query($sql);

        $this->load->view('nagruzka_view', $data);
        $this->execute("nagruz_dob1");
    }

    function nagruz_dob1()
    {
        $data['point_id'] = $this->uri->segment(3);
        $sql = "SELECT * FROM industry.billing_point where id=" . $this->uri->segment(3);
        $data['point_data'] = $this->db->query($sql)->row();

        $this->load->view('nagruz_dob', $data);
    }

    /*
	function adding_nagruz()
	{
		$sql="SELECT value::integer FROM industry.sprav WHERE name='current_period'";
		$a=$this->db->query($sql)->row()->value;
		$_POST['period_id']=$a;


		$this->db->where('id',$_POST['billing_point_id']);
		$this->db->update('industry.billing_point',
			array(

			'napr'=>$_POST['napr'],
		'sopr'=>$_POST['sopr'],
		'dlina'=>$_POST['dlina'],
		'snom'=>$_POST['snom'],
		'rkz'=>$_POST['rkz'],
		'tgf'=>$_POST['tgf']
			)
		);

		$this->db->where('id',$_POST['point_data']);
		$data['point_data']=$this->db->get('industry.billing_point')->row();
		$data['napr']=$_POST['napr'];
		$data['sopr']=$_POST['sopr'];
		$data['dlina']=$_POST['dlina'];
		$data['snom']=$_POST['snom'];
		$data['rkz']=$_POST['rkz'];
		$data['tgf']=$_POST['tgf'];

		redirect("billing/point/".$_POST['billing_point_id']);
	}
	*/

    #добавление потерь
    function adding_nagruz()
    {
        $sql = "SELECT value::INTEGER FROM industry.sprav WHERE name='current_period'";
        $period_id = $this->db->query($sql)->row()->value;

        $this->db->where('bill_id', $_POST['billing_point_id']);
        $this->db->where('period_id', $period_id);
        $is_added = $this->db->get('industry.poteri_period')->num_rows();
        if ($is_added) {
            $this->db->where('bill_id', $_POST['billing_point_id']);
            $this->db->where('period_id', $period_id);
            $this->db->update('industry.poteri_period',
                array(
                    'napr' => $_POST['napr'],
                    'sopr' => $_POST['sopr'],
                    'dlina' => $_POST['dlina'],
                    'snom' => $_POST['snom'],
                    'rkz' => $_POST['rkz'],
                    'tgf' => $_POST['tgf']
                )
            );
        } else {
            $this->db->insert('industry.poteri_period',
                array(
                    'bill_id' => (int)$_POST['billing_point_id'],
                    'period_id' => $period_id,
                    'napr' => $_POST['napr'],
                    'sopr' => $_POST['sopr'],
                    'dlina' => $_POST['dlina'],
                    'snom' => $_POST['snom'],
                    'rkz' => $_POST['rkz'],
                    'tgf' => $_POST['tgf']
                )
            );
        }
        redirect("billing/point/" . $_POST['billing_point_id']);
    }

    #end of добавление потерь

    function adding_dob()
    {
        $sql = "SELECT value::integer FROM industry.sprav WHERE name='current_period'";
        $a = $this->db->query($sql)->row()->value;
        $_POST['period_id'] = $a;


        $this->db->where('billing_point_id', $_POST['point_data']);
        $data['point_data'] = $this->db->get('industry.poteri')->row();
        $data['wakt'] = $_POST['wakt'];
        $data['holostoi'] = $_POST['holostoi'];

        $_POST['tariff_value'] = 18.44;
        $data['data'] = $_POST['data'];


        $this->db->insert("industry.poteri", $_POST);

        redirect("billing/point/" . $_POST['billing_point_id']);

    }

    function delete_nagruz()
    {
        $query = $this->db->query("select * from industry.poteri where id=" . $this->uri->segment(3));
        $id = $query->row()->billing_point_id;
        $this->db->query("delete from industry.poteri where id=" . $this->uri->segment(3));
        redirect("billing/point/" . $id);
    }

    function break_counter()
    {
        $sql = "select * from industry.counter where data_finish is null and point_id=" . $_POST['point_id'];
        $query = $this->db->query($sql);

        $data_snyatiya = $_POST['day'] . "." . $_POST['month'] . "." . $_POST['year'];
        $sql = "update industry.counter set data_finish='" . $data_snyatiya . "' where id=" . $query->row()->id;
        $this->db->query($sql);
        redirect("billing/point/" . $_POST['point_id']);
    }

    function add_counter()
    {
        $sql = "SELECT * from industry.counter_type order by name";
        $data['types'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view("add_counter_view", $data);
    }

    function adding_counter()
    {
        $sql = "select * from industry.counter where data_finish is  null and  point_id=" . $_POST['point_id'];
        $query = $this->db->query($sql);
        if ($query->num_rows() != 0) return;
        $_POST['data_start'] = date("Y-m-d", mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));
        unset($_POST['year']);
        unset($_POST['day']);
        unset($_POST['month']);
        $this->db->insert("industry.counter", $_POST);
        redirect("billing/point/" . $_POST['point_id']);
    }

    function counter()
    {
        $counter_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_counter_id($counter_id);
        $this->db->where("id", $counter_id);
        $data['point_id'] = $this->db->get("industry.counter")->row()->point_id;
        $sql = "select values_set.id,tariff.type_name as type from industry.values_set,industry.tariff where tariff.id=values_set.tariff_id and  counter_id=" . $counter_id;
        $data['query'] = $this->db->query($sql);
        $data['counter_id'] = $this->uri->segment(3);
        $this->left();
        $this->load->view("counter_view", $data);
        $this->load->view("right");
    }

    function delete_values_set()
    {
        $sql = "delete from industry.values_set where id = " . $this->uri->segment(3);
        $this->db->query($sql);
        redirect("billing/counter/" . $this->uri->segment(4));
    }

    function change_counter()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['counter'] = $this->db->get('industry.counter')->row();
        $this->db->order_by('name');
        $data['counter_type'] = $this->db->get('industry.counter_type');
        $this->left();
        $this->load->view("counter_edit", $data);
        $this->load->view("right");
    }

    function changing_counter()
    {
        if ($_POST['data_gos_proverki'] == '') $_POST['data_gos_proverki'] = NULL;
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('industry.counter', $_POST);
        redirect('billing/counter/' . $this->uri->segment(3));
    }

    function add_sets()
    {
        $sql = "select * from industry.counter where data_finish is null and id=" . $this->uri->segment(3);
        $query = $this->db->query($sql);
        if ($query->num_rows() == 0) {
            echo $this->red("billing/counter/" . $this->uri->segment(3));
            return;
        }

        $sql = "select * from industry.tariff";
        $data['tariff'] = $this->db->query($sql);
        $data['counter_id'] = $this->uri->segment(3);
        $this->left();
        $this->load->view("add_sets_view", $data);
        $this->load->view("right");
    }

    function adding_sets()
    {
        $sql = "select * from industry.counter where data_finish is null and id=" . $_POST['counter_id'];
        $query = $this->db->query($sql);
        if ($query->num_rows() == 0) return;
        $this->db->insert("industry.values_set", $_POST);
        redirect("billing/counter/" . $_POST['counter_id']);
    }

    function values_sets()
    {
        $values_set_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_values_set_id($values_set_id);
        $this->db->where("firm_id", $data['firm']->id);
        $this->db->where("period_id", $this->get_cpi());
        $is_closed = $this->db->get("industry.firm_close")->num_rows();

        $this->db->where("id", $values_set_id);
        $data['counter_id'] = $this->db->get("industry.values_set")->row()->counter_id;
        $sql = "select * from industry.counter where id=(select counter_id from industry.values_set where id=" .
            $values_set_id . ")";
        $data['counter_data'] = $this->db->query($sql)->row();
        $data['sets_id'] = $values_set_id;
        $sql = "SELECT tariff_id  from industry.values_set where id=" . $values_set_id;
        $type_id = $this->db->query($sql)->row()->tariff_id;
        $sql = "SELECT name from industry.tariff where id=" . $type_id;
        $data['sets_type'] = $this->db->query($sql)->row()->name;
        $sql = "SELECT * from industry.counter_value where values_set_id=" . $values_set_id . "  order by data";
        $data['query'] = $this->db->query($sql);
        $this->left();
        $this->load->view("values_sets_view", $data);
        if ($is_closed == 0) {
            $this->execute("add_pokazanie");
        }
        $this->execute("nadbavka_ab");
        $this->execute("akt");
        $this->execute("sovm_ab");
        $this->load->view("right");
    }

    function add_pokazanie()
    {
        $sql = "select * from industry.counter where data_finish is null and id=(select counter_id from industry.values_set where id=" . $this->uri->segment(3) . ")";
        $query = $this->db->query($sql);
        if ($query->num_rows() == 0) return;
        $data['set_id'] = $this->uri->segment(3);
        $this->load->view("add_pokazanie_view", $data);
    }

    function adding_pokazanie()
    {
        $this->session->set_userdata(array('day' => $_POST['day'], 'month' => $_POST['month'], 'year' => $_POST['year']));
        $data = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
        if (!checkdate($_POST['month'], $_POST['day'], $_POST['year'])) {
            echo $this->red("billing/values_sets/" . $_POST['set_id']);
            return;
        }
        $_POST['data'] = date("Y-m-d", mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));
        unset($_POST['year']);
        unset($_POST['day']);
        unset($_POST['month']);
        $_POST['uroven'] = 0;
        $this->db->insert("industry.counter_value", $_POST);
        redirect("billing/values_sets/" . $_POST['values_set_id']);
    }

    function delete_pokazanie()
    {
        $query = $this->db->query("select * from industry.counter_value where id=" . $this->uri->segment(3));
        $id = $query->row()->values_set_id;
        $this->db->query("delete from industry.counter_value where id=" . $this->uri->segment(3));
        redirect("billing/values_sets/" . $id);
    }

    function nadbavka_ot()
    {
        $sql = "SELECT * FROM industry.current_ot_nadbavka where billing_point_id=" . $this->uri->segment(3);
        $data['nadbavka'] = $this->db->query($sql);
        $this->load->view('nadbavka_view', $data);
        $this->execute("add_ot_nadbavka");
    }

    function add_ot_nadbavka()
    {
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('add_nadbavka_view', $data);
    }

    function adding_ot_nadbavka()
    {
        $sql = "SELECT value::integer FROM industry.sprav WHERE name='current_period'";
        $a = $this->db->query($sql)->row()->value;
        $_POST['period_id'] = $a;
        $this->db->insert("industry.nadbavka_otnositelnaya", $_POST);
        redirect("billing/point/" . $_POST['billing_point_id']);
    }

    function nadbavka_ab()
    {
        $sql = "SELECT * FROM industry.current_ab_nadbavka where values_set_id=" . $this->uri->segment(3);
        $data['nadbavka'] = $this->db->query($sql);
        $this->load->view('nadbavka_absolutnaya_view', $data);
        $this->execute("add_ab_nadbavka");
    }

    function add_ab_nadbavka()
    {
        $data['vs_id'] = $this->uri->segment(3);
        $this->load->view('add_nadbavka_absolutnaya_view', $data);
    }

    function adding_ab_nadbavka()
    {
        $_POST['tariff_value'] = 4;
        $_POST['uroven'] = 0;
        $this->db->insert("industry.nadbavka_absolutnaya", $_POST);
        redirect("billing/values_sets/" . $_POST['values_set_id']);
    }

    function sovm_otn()
    {
        $sql = "SELECT * FROM industry.sovm_uchet where child_point_id=" . $this->uri->segment(3);
        $data['query'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('sovmestnyy_uchet_view', $data);
        $this->execute("add_sovm_otn");
    }

    function add_sovm_otn()
    {
        $sql = 'select id,dogovor||\'  \'||name as firm_info from industry.firm  order by dogovor';
        $data['firms'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('add_sovmestnyy_uchet_view', $data);
    }

    function adding_sovm_otn()
    {
        $this->db->insert("industry.sovmestnyy_uchet", $_POST);
        redirect("billing/point/" . $_POST['child_point_id']);
    }

    function delete_sovm_otn()
    {
        $query = $this->db->query("select * from industry.sovmestnyy_uchet where id=" . $this->uri->segment(3));
        $id = $query->row()->child_point_id;
        $this->db->query("delete from industry.sovmestnyy_uchet where id=" . $this->uri->segment(3));
        redirect("billing/point/" . $id);
    }

    function sovm_ab()
    {
        $sql = "SELECT * FROM industry.sovm_ab where values_set_id=" . $this->uri->segment(3);
        $data['query'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('sovmestnyy_absolutnyy_view', $data);
        $this->execute("add_sovm_ab");
    }

    function add_sovm_ab()
    {
        $sql = 'select id,dogovor||\'  \'||name as firm_info from industry.firm order by dogovor';
        $data['firms'] = $this->db->query($sql);
        $data['values_set_id'] = $this->uri->segment(3);
        $this->load->view('add_sovmestnyy_ab_view', $data);
    }

    function adding_sovm_ab()
    {
        $this->db->insert("industry.sovm_absolutnyy", $_POST);
        redirect("billing/values_sets/" . $_POST['values_set_id']);
    }

    function delete_sovm_ab()
    {
        $query = $this->db->query("select * from industry.sovm_absolutnyy where id=" . $this->uri->segment(3));
        $id = $query->row()->values_set_id;
        $this->db->query("delete from industry.sovm_absolutnyy where id=" . $this->uri->segment(3));
        redirect("billing/values_sets/" . $id);
    }

    function sovm_by_counter()
    {
        $sql = "SELECT * FROM industry.sovm_by_counter where billing_point_id=" . $this->uri->segment(3);
        $data['query'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('sovm_by_counter_view', $data);
        $this->execute("add_sovm_by_counter");
    }

    function add_sovm_by_counter()
    {
        $sql = 'select id,dogovor||\'  \'||name as firm_info from industry.firm  order by dogovor';
        $data['firms'] = $this->db->query($sql);
        $data['point_id'] = $this->uri->segment(3);
        $this->load->view('add_sovm_by_counter_view', $data);
    }

    function adding_sovm_by_counter()
    {
        $this->db->insert("industry.sovm_by_counter_value", $_POST);
        redirect("billing/point/" . $_POST['billing_point_id']);
    }

    function delete_sovm_by_counter()
    {
        $query = $this->db->query("select * from industry.sovm_by_counter_value where id=" . $this->uri->segment(3));
        $id = $query->row()->billing_point_id;
        $this->db->query("delete from industry.sovm_by_counter_value where id=" . $this->uri->segment(3));
        redirect("billing/point/" . $id);
    }

    function delete_ot_nadbavka()
    {
        $id = $this->db->query("select billing_point_id from industry.nadbavka_otnositelnaya where id=" . $this->uri->segment(3))->row()->billing_point_id;
        $this->db->query("delete from industry.nadbavka_otnositelnaya where id=" . $this->uri->segment(3));
        redirect("billing/point/" . $id);
    }

    function delete_ab_nadbavka()
    {
        $id = $this->db->query("select values_set_id from industry.nadbavka_absolutnaya where id=" . $this->uri->segment(3))->row()->values_set_id;
        $this->db->query("delete from industry.nadbavka_absolutnaya where id=" . $this->uri->segment(3));
        redirect("billing/values_sets/" . $id);
    }

    /*
	function vedomost()
	{
		$this->load->library("pdf/pdf");

		$this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 9);

        // add a page
        $this->pdf->AddPage('L');

		$sql="SELECT * FROM industry.firm WHERE id=".$_POST['firm_id'];
		$data['firm']=$this->db->query($sql)->row();




		if (isset($_POST['fast_met'])){
            $sql = "SELECT * FROM industry.firm_vedomost WHERE firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
            $data['vedomost'] = $this->db->query($sql);
            $sql = "SELECT * FROM industry.firm_itog_vedomost where firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
            $data['itogo'] = $this->db->query($sql)->row();
            $data['met'] = 'fast';
        } else {
            $sql = "SELECT * FROM industry.vedomost WHERE firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
            $data['vedomost'] = $this->db->query($sql);
            $sql = "SELECT * FROM industry.vedomost_itog where firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
            $data['itogo'] = $this->db->query($sql)->row();
            $data['met'] = 'old';
        }


		$sql="SELECT * FROM industry.firm_oplata_itog 	where firm_id=".$_POST['firm_id']." and period_id=".$_POST['period_id'];
		if ($this->db->query($sql)->num_rows()>0)
		{
			$data['oplata_value']=	$this->db->query($sql)->row()->oplata_value;
		}
		else
			$data['oplata_value']=0;
		$string=$this->load->view("reports/vedomost",$data,TRUE);
		$this->pdf->writeHTML($string);

        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');

	}*/

    function vedomost()
    {


        $sql = "SELECT * FROM industry.firm WHERE id=" . $_POST['firm_id'];
        $data['firm'] = $this->db->query($sql)->row();

        $sql = "SELECT * FROM industry.vedomost WHERE firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
        $data['vedomost'] = $this->db->query($sql);
        $sql = "SELECT * FROM industry.vedomost_itog where firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
        $data['itogo'] = $this->db->query($sql)->row();
        $data['met'] = 'old';

        #fine
        $data['is_fine'] = $this->db->query("select count(*) as is_fine from industry.fine_firm where firm_id = {$_POST['firm_id']} and period_id = {$_POST['period_id']}")->row()->is_fine;
        if ($data['is_fine']) {
            $this->db->where('id', $_POST['period_id']);
            $giveout_date = $this->db->get('industry.period')->row()->end_date;

            #день выдачи, если за прошлый месяц ведомость - последний день того месяца
            if ((date('n')) != (date('n', strtotime($giveout_date)))) {
                $giveout_day = date('d', strtotime($giveout_date));
            } else {
                $giveout_day = date('d');
                $giveout_date = date('Y-m-d');
            }

            $data['giveout_date'] = $giveout_date;
            $data['fine_giveout_value'] = $this->db->query("select * from industry.fine_calc_firm({$_POST['firm_id']}, {$_POST['period_id']},{$giveout_day})")->row()->fine_calc_firm;
        } else {
            $data['fine_giveout_value'] = 0;
        }

        #end of fine

        $data['period_id'] = $_POST['period_id'];

        if ($data['period_id'] < 116) {
            $data['fine_giveout_value'] = 0;
        }

        $sql = "SELECT * FROM industry.firm_oplata_itog 	where firm_id=" . $_POST['firm_id'] . " and period_id=" . $_POST['period_id'];
        if ($this->db->query($sql)->num_rows() > 0) {
            $data['oplata_value'] = $this->db->query($sql)->row()->oplata_value;
        } else {
            $data['oplata_value'] = 0;
        }


        $this->load->library("pdf/pdf");
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        $this->pdf->SetFont('dejavusans', '', 9);
        $this->pdf->AddPage('L');

        $string = $this->load->view("reports/vedomost", $data, TRUE);

        $this->pdf->writeHTML($string);
        $this->pdf->Output('example_001.pdf', 'I');


        #$this->load->view("reports/vedomost", $data);
    }

    function pre_akt_sverki()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['r'] = $this->db->get('industry.firm')->row();
        $sql = "SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
        $data['current_period'] = $this->db->query($sql)->row()->current_period;
        $data['period'] = $this->db->get('industry.period');
        $data['firm_id'] = $this->uri->segment(3);
        $this->left();
        $this->load->view("pre_akt_sverki", $data);
        $this->load->view("right");
    }

    function akt_sverki()
    {
        $sql = "select * from industry.vedomost where firm_id={$_POST['firm_id']} and  period_id between {$_POST['start_period_id']} and {$_POST['finish_period_id']}";
        $data['akt'] = $this->db->query($sql);
        $this->load->view('reports/akt_sverki', $data);
    }

    function pre_report_to_nalogovaya()
    {
        $sql = "SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
        $data['current_period'] = $this->db->query($sql)->row()->current_period;
        $this->db->order_by("id");
        $data['period'] = $this->db->get('industry.period');
        $this->left();
        $this->load->view("pre_report_to_nalgovaya", $data);
        $this->load->view("right");
    }

    function report_to_nalogovaya()
    {
        $sql = "select * from industry.report_to_nalogovaya where period_id>={$_POST['start_period_id']} and  period_id <={$_POST['finish_period_id']}";
        $data['nalog'] = $this->db->query($sql);
        $this->load->view('reports/report_to_nalogovaya', $data);
    }

    function report_prom()
    {
        $sql = "select * from industry.prom";
        $data['prom'] = $this->db->query($sql);
        $this->load->view('reports/prom', $data);
    }

    function report_budjet()
    {
        $sql = "select * from industry.budjet";
        $data['prom'] = $this->db->query($sql);
        $this->load->view('reports/budjet', $data);
    }

    function report_neprom()
    {
        $sql = "select * from industry.neprom";
        $data['prom'] = $this->db->query($sql);
        $this->load->view('reports/neprom', $data);
    }

    function report_to_nalogovaya_alt()
    {
        //$sql="select * from industry.report_to_nalogovaya where period_id>=59 and  period_id <=61";
        $sql = "select id as fid from industry.firm";
        $data['nalog'] = $this->db->query($sql);
        $this->load->view('reports/report_to_nalogovaya_alt', $data);
    }

    function pre_schetfactura()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['r'] = $this->db->get('industry.firm');
        $sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
        $data['period'] = $this->db->query($sql);

        $firm_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_id($firm_id);

        $this->left();
        $this->load->view("pre_schetfactura", $data);
        $this->load->view("right");
    }

    function pre_schetfactura2()
    {
        $data['firm_id'] = $_POST['firm_id'];
        $data['period_id'] = $_POST['period_id'];
        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $data['r'] = $this->db->get('industry.schetfactura_date');
        $this->db->where('id', $_POST["firm_id"]);
        $data['firm'] = $this->db->get('industry.firm')->row();

        $this->db->where('period_id', $_POST['period_id']);
        $data['max_schet_number'] = $this->db->get("shell.max_schet_number")->row()->schet_number;

        $this->left();
        $this->load->view("pre_schetfactura2", $data);
        $this->load->view("right");
    }

    /*
	function schetfactura()
	{
		if (isset($_POST['akt_vypolnenyh_rabot']))
				$data['akt_vypolnenyh_rabot']="Акт выполненых работ";
				else
				$data['akt_vypolnenyh_rabot']="";
		//$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('id',$_POST['firm_id']);
		$this->db->update('industry.firm',
			array(
				'edit1'=>$_POST['edit1'],
				'edit2'=>$_POST['edit2'],
				'edit3'=>$_POST['edit3'],
				'edit4'=>$_POST['edit4'],
				'edit5'=>$_POST['edit5'],
				'edit6'=>$_POST['edit6'],
				'edit7'=>$_POST['edit7'],
				'dog2'=>$_POST['dog2'],
				'edit8'=>$_POST['edit8']
			)
		);

		#FINE
		$this->db->where('period_id', $_POST['period_id']);
		$this->db->where('firm_id', $_POST['firm_id']);
		$isset_fine = $this->db->get("industry.fine_source_data")->num_rows();
		if ((isset($isset_fine)) and ($isset_fine > 0)) {
			$this->db->where('period_id', $_POST['period_id']);
			$this->db->where('firm_id', $_POST['firm_id']);
			$data['fine_value'] = $this->db->get("industry.fine_source_data")->row()->fine_value;
		}
		#END FINE

		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$this->db->update('industry.schetfactura_date',
			array(
			'date'=>$_POST['data_schet'],
			'schet_nakl'=>$_POST['schet_nakl'],
			'data_nakl'=>$_POST['data_nakl']

			)
		);
		$this->load->plugin('chislo');

		//$dt_zp="select * from industry.period where id=".$_POST['period_id'];
		//$data['dtqr']=$this->db->query($dt_zp)->result();

		$sql="SELECT * FROM industry.org_info";
		$data['org']=$this->db->query($sql)->row();
		$sql="select * from industry.schetfactura where tariff_value<>0 and firm_id=".$_POST['firm_id'].' and period_id='.$_POST['period_id'];
		$data['s']=$this->db->query($sql)->result();

		$dt_zp="select * from industry.period where id=".$_POST['period_id']."";
		$data['dtqr']=$this->db->query($dt_zp)->result();

		$this->db->where('firm_id',$_POST['firm_id']);
		$this->db->where('period_id',$_POST['period_id']);
		$data['schetfactura_date']=$this->db->get('industry.schetfactura_date')->row();
		$this->db->where('id',$_POST['firm_id']);
		$data['firm']=$this->db->get('industry.firm')->row();
		$data['dog2']=$_POST['dog2'];
		$data['edit1']=$_POST['edit1'];
		$data['edit2']=$_POST['edit2'];
		$data['edit3']=$_POST['edit3'];
		$data['edit4']=$_POST['edit4'];
		$data['edit5']=$_POST['edit5'];
		$data['edit6']=$_POST['edit6'];
		$data['edit7']=$_POST['edit7'];
		$data['schet_nakl']=$_POST['schet_nakl'];
		$data['data_nakl']=$_POST['data_nakl'];
		$data['edit8']=$_POST['edit8'];
		$data['data_schet']=$_POST['data_schet'];
		$data['schet2']=$_POST['schet2'];
		$this->db->where('id',$_POST['period_id']);
		$data['period']=$this->db->get('industry.period')->row();
		$this->db->where('id',$data['firm']->bank_id);
		$data['bank']=$this->db->get('industry.bank')->row();

		$this->db->where('period_id',$_POST['period_id']);
		$this->db->where('firm_id',$_POST['firm_id']);
		$data['itog']=$this->db->get("industry.vedomost_itog")->row();

		if (!isset($_POST['html']))
		{
			if (isset($_POST['podpisyvaet'])) {
				$string = $this->load->view("reports/schetfactura_new2",$data,TRUE);

				$this->load->library("pdf/pdf");

				$this->pdf->SetSubject('TCPDF Tutorial');
				$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
				$this->pdf->SetAutoPageBreak(TRUE);
				// set font
				$this->pdf->SetFont('dejavusans', '', 9);

				// add a page
				$this->pdf->AddPage('P');

				$this->pdf->writeHTML($string);

				//Close and output PDF document
				$this->pdf->Output('example_001.pdf', 'I');
			}
			if (isset($_POST['podpisyvaet2'])) {
				$string = $this->load->view("reports/schetfactura_new3",$data,TRUE);


				$this->load->library("pdf/pdf");

				$this->pdf->SetSubject('TCPDF Tutorial');
				$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
				$this->pdf->SetAutoPageBreak(TRUE);
				// set font
				$this->pdf->SetFont('dejavusans', '', 9);

				// add a page
				$this->pdf->AddPage('P');

				$this->pdf->writeHTML($string);

				//Close and output PDF document
				$this->pdf->Output('example_001.pdf', 'I');
			}


			if (isset($_POST['akt_vypolnenyh_rabot']))
			{
			$this->load->view("reports/avp3",$data);

			}
			if (isset($_POST['nakladnaya'])) {
			    $this->load->view("reports/nakladnaya",$data);
			}
		}
		else {
			$string=$this->load->view("reports/schetfactura_new",$data,TRUE);


			$this->load->library("pdf/pdf");

			$this->pdf->SetSubject('TCPDF Tutorial');
			$this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
			$this->pdf->SetAutoPageBreak(TRUE);
			// set font
			$this->pdf->SetFont('dejavusans', '', 9);

			// add a page
			$this->pdf->AddPage('P');

			$this->pdf->writeHTML($string);

			//Close and output PDF document
			$this->pdf->Output('example_001.pdf', 'I');
			}


	}*/

    function schetfactura()
    {
        if (isset($_POST['akt_vypolnenyh_rabot']))
            $data['akt_vypolnenyh_rabot'] = "Акт выполненых работ";
        else
            $data['akt_vypolnenyh_rabot'] = "";
        //$this->db->where('period_id',$_POST['period_id']);
        $this->db->where('id', $_POST['firm_id']);
        $this->db->update('industry.firm', array(
                'edit1' => $_POST['edit1'],
                'edit2' => $_POST['edit2'],
                'edit3' => $_POST['edit3'],
                'edit4' => $_POST['edit4'],
                'edit5' => $_POST['edit5'],
                'edit6' => $_POST['edit6'],
                'edit7' => $_POST['edit7'],
                'dog2' => $_POST['dog2'],
                'edit8' => $_POST['edit8']
            )
        );

//        echo "<pre>";
//        var_dump($_POST['firm_id']);
//        var_dump($_POST['period_id']);
//        echo "</pre>";
//        die();

        if ($_POST['period_id'] >= 116) {
            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('firm_id', $_POST['firm_id']);
            $data['do_fine'] = $this->db->get("industry.fine_firm")->num_rows();
        } else {
            $data['do_fine'] = 0;
        }


        if ($data['do_fine']) {
            $data['fine_value'] = $this->db->query("select * from industry.fine_calc_firm({$_POST['firm_id']}, {$_POST['period_id']})")->row()->fine_calc_firm;

            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('firm_id', $_POST['firm_id']);
            $isset_fine = $this->db->get('industry.fine_saldo')->num_rows();
            if ((isset($isset_fine)) and ($isset_fine > 0)) {
                $this->db->where('period_id', $_POST['period_id']);
                $this->db->where('firm_id', $_POST['firm_id']);
                $data['fine_saldo'] = $this->db->get('industry.fine_saldo')->row()->value;
            }

            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('firm_id', $_POST['firm_id']);
            $isset_fine = $this->db->get('industry.fine_firm_oplata_itog')->num_rows();
            if ((isset($isset_fine)) and ($isset_fine > 0)) {
                $this->db->where('period_id', $_POST['period_id']);
                $this->db->where('firm_id', $_POST['firm_id']);
                $data['fine_oplata'] = $this->db->get('industry.fine_firm_oplata_itog')->row()->fine_oplata_value;
            } else {
                $data['fine_oplata'] = 0;
            }
        } else {

        }


        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $isset_fine = $this->db->get('industry.firm_oplata_itog')->num_rows();
        if ((isset($isset_fine)) and ($isset_fine > 0)) {
            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('firm_id', $_POST['firm_id']);
            $data['oplata'] = $this->db->get('industry.firm_oplata_itog')->row()->oplata_value;
        } else {
            $data['oplata'] = 0;
        }
        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $data['saldo'] = $this->db->get('industry.saldo')->row()->value;

        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $this->db->update('industry.schetfactura_date', array(
                'date' => $_POST['data_schet'],
                'schet_nakl' => $_POST['schet_nakl'],
                'data_nakl' => $_POST['data_nakl']
            )
        );
        $this->load->plugin('chislo');

        //$dt_zp="select * from industry.period where id=".$_POST['period_id'];
        //$data['dtqr']=$this->db->query($dt_zp)->result();

        $sql = "SELECT * FROM industry.org_info";
        $data['org'] = $this->db->query($sql)->row();
        #$sql = "select * from industry.firm_schetfactura where tariff_value<>0 and firm_id=" . $_POST['firm_id'] . ' and period_id=' . $_POST['period_id'];
        $sql = "select * from industry.schetfactura where tariff_value<>0 and firm_id=" . $_POST['firm_id'] . ' and period_id=' . $_POST['period_id'];
        $data['s'] = $this->db->query($sql)->result();

        $dt_zp = "select * from industry.period where id=" . $_POST['period_id'] . "";
        $data['dtqr'] = $this->db->query($dt_zp)->result();

        $this->db->where('firm_id', $_POST['firm_id']);
        $this->db->where('period_id', $_POST['period_id']);
        $data['schetfactura_date'] = $this->db->get('industry.schetfactura_date')->row();
        $this->db->where('id', $_POST['firm_id']);
        $data['firm'] = $this->db->get('industry.firm')->row();
        $data['dog2'] = $_POST['dog2'];
        $data['edit1'] = $_POST['edit1'];
        $data['edit2'] = $_POST['edit2'];
        $data['edit3'] = $_POST['edit3'];
        $data['edit4'] = $_POST['edit4'];
        $data['edit5'] = $_POST['edit5'];
        $data['edit6'] = $_POST['edit6'];
        $data['edit7'] = $_POST['edit7'];
        $data['schet_nakl'] = $_POST['schet_nakl'];
        $data['data_nakl'] = $_POST['data_nakl'];
        $data['edit8'] = $_POST['edit8'];
        $data['data_schet'] = $_POST['data_schet'];
        $data['schet2'] = $_POST['schet2'];
        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period')->row();
        $this->db->where('id', $data['firm']->bank_id);
        $data['bank'] = $this->db->get('industry.bank')->row();

        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $data['itog'] = $this->db->get("industry.vedomost_itog")->row();
        #$data['itog'] = $this->db->get("industry.firm_itog_vedomost")->row();

        $this->load->library("pdf/pdf");
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        $this->pdf->SetFont('dejavusans', '', 9);
        $this->pdf->AddPage('P');

        if (!isset($_POST['html'])) {
            if (isset($_POST['podpisyvaet'])) {
                #$this->load->view("reports/schetfactura_new2", $data);
                $string = $this->load->view("reports/schetfactura_new2", $data, TRUE);
                $this->pdf->writeHTML($string);
                $this->pdf->Output('example_001.pdf', 'I');
            }
            if (isset($_POST['podpisyvaet2'])) {
                #$this->load->view("reports/schetfactura_new3", $data);
                $string = $this->load->view("reports/schetfactura_new3", $data, TRUE);
                $this->pdf->writeHTML($string);
                $this->pdf->Output('example_001.pdf', 'I');
            }
            if (isset($_POST['akt_vypolnenyh_rabot'])) {
                $this->load->view("reports/avp3", $data);
            }
            if (isset($_POST['nakladnaya'])) {
                $this->load->view("reports/nakladnaya", $data);
            }
        } else {
            $string = $this->load->view("reports/schetfactura_new", $data, TRUE);
            $this->pdf->writeHTML($string);
            $this->pdf->Output('example_001.pdf', 'I');
        }
    }

    function pre_schetoplata()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['r'] = $this->db->get('industry.firm');
        $sql = "SELECT period.*,case when sprav.value is not null then 'selected' else '' end  as checked FROM industry.period left join industry.sprav on period.id=sprav.value::integer and sprav.name='current_period' order by id";
        $data['period'] = $this->db->query($sql);

        $firm_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_id($firm_id);

        $this->left();
        $this->load->view("pre_schetoplata", $data);
        $this->load->view("right");
    }

    function pre_schetoplata2()
    {
        $data['firm_id'] = $_POST['firm_id'];
        $data['period_id'] = $_POST['period_id'];
        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_id', $_POST['firm_id']);
        $data['r'] = $this->db->get('industry.schetfactura_date');

        $data['firm'] = $this->get_firm_info_by_id($data['firm_id']);

        $sql = "select distinct value as tariff_value from industry.tariff_value ";
        $data['tariffs'] = $this->db->query($sql);

        $this->left();
        $this->load->view("pre_schetoplata2", $data);
        $this->load->view("right");
    }

    function schetoplata()
    {
        $sql = "SELECT * FROM industry.org_info";
        $data['org'] = $this->db->query($sql)->row();
        $this->db->where('firm_id', $_POST['firm_id']);
        $this->db->where('period_id', $_POST['period_id']);
        $data['schetfactura_date'] = $this->db->get('industry.schetfactura_date')->row();
        $this->load->plugin('chislo');
        $this->db->where('id', $_POST['firm_id']);
        $data['firm'] = $this->db->get('industry.firm')->row();
        $data['number'] = $_POST['number_schet'] == "" ? $data['schetfactura_date']->id : $_POST['number_schet'];
        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period')->row();

        $this->db->where('id', $data['firm']->bank_id);
        $data['bank'] = $this->db->get("industry.bank")->row();
        $data['schet'] = !isset($_POST['schet']) ? "  НА ОПЛАТУ" : "-ФАКТУРА";


        if ($_POST['type'] == "by_tenge") {
            $tariff_value = $_POST['tariff_value'];
            $tariff_kvt = $_POST['tariff'];
            $buf;
            for ($j = 0; $j < $_POST['tariff_count']; $j++) {
                if ($tariff_value[$j] > 0)
                    $buf[$j] = $tariff_kvt[$j] / $tariff_value[$j] / ((100 + $data['period']->nds) / 100);
                else
                    $buf[$j] = $tariff_kvt[$j];
            }
            $data['tariff_kvt'] = $buf;
        } else
            $data['tariff_kvt'] = $_POST['tariff'];
        $data['tariff_value'] = $_POST['tariff_value'];
        $data['tariff_count'] = $_POST['tariff_count'];
        $data['data_schet'] = $_POST['data_schet'];

        if (isset($_POST['podpisyvaet'])) {
            $this->load->view("reports/schetoplata2", $data);
        } else
            if (isset($_POST['podpisyvaet2'])) {
                $this->load->view("reports/schetoplata3", $data);
            } else
                $this->load->view("reports/schetoplata", $data);
    }

    function akt()
    {
        $query = "SELECT * FROM industry.current_akt WHERE values_set_id=" . $this->uri->segment(3);
        $data['query'] = $this->db->query($query);
        $this->load->view("akt_view", $data);
        $this->execute("add_akt");
    }

    function delete_akt()
    {
        $id = $this->db->query("select values_set_id from industry.akt where id=" . $this->uri->segment(3))->row()->values_set_id;
        $this->db->query("delete from industry.akt where id=" . $this->uri->segment(3));
        redirect("billing/values_sets/" . $id);
    }

    function add_akt()
    {
        $data['vs_id'] = $this->uri->segment(3);
        $this->load->view("add_akt", $data);
    }

    function adding_akt()
    {

        $_POST['tariff_value'] = 4;
        $this->db->insert("industry.akt", $_POST);
        redirect("billing/values_sets/" . $_POST['values_set_id']);
    }

    function edit_pokaz()
    {
        $firm_id = $this->uri->segment(3);
        $data['firm'] = $this->get_firm_info_by_id($firm_id);
        $this->db->where("firm_id", $firm_id);
        $this->db->where("period_id", $this->get_cpi());
        $data['is_closed'] = $this->db->get("industry.firm_close")->num_rows();

        $this->db->where("firm_id", $firm_id);
        $data['pokaz'] = $this->db->get("industry.counter_add_pokaz");
        $data['firm_id'] = $firm_id;
        $this->db->where("firm_id", $firm_id);
        $this->db->order_by("name");
        $data['point_list'] = $this->db->get("industry.point_list")->result();
        $this->left();
        $this->load->view("edit_pokaz", $data);
        $this->load->view("right");
    }

    function delete_pokazanie2()
    {
        $query = $this->db->query("select * from industry.counter_value where id=" . $this->uri->segment(3));
        $id = $query->row()->values_set_id;
        $query = $this->db->query("select distinct firm_id from industry.counter_add_pokaz where values_set_id=$id");
        $firm_id = $query->row()->firm_id;
        $this->db->query("delete from industry.counter_value where id=" . $this->uri->segment(3));
        redirect("billing/edit_pokaz/$firm_id#$id");
    }

    function adding_pokazanie2()
    {
        $sql = "SELECT firm_id from industry.firmid_by_values_set where values_set_id=" . $_POST['values_set_id'];
        $w = $this->db->query($sql)->row()->firm_id;

        $this->session->set_userdata(array('day' => $_POST['day'], 'month' => $_POST['month'], 'year' => $_POST['year']));
        $data = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
        if (!checkdate($_POST['month'], $_POST['day'], $_POST['year'])) {
            redirect("billing/edit_pokaz/" . $w);
        }
        $_POST['data'] = date("Y-m-d", mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));
        unset($_POST['year']);
        unset($_POST['day']);
        unset($_POST['month']);
        $_POST['uroven'] = 0;
        $this->db->insert("industry.counter_value", $_POST);

        redirect("billing/edit_pokaz/$w#" . ($this->uri->segment(3) + 1));
    }

    //! Отчеты
    function reports()
    {
        $this->left();
        $this->load->view("reports");
        $this->load->view("right");
    }

    function doljniki_za_period_form()
    {
        $sql = "SELECT * from industry.period order by id";
        $data['periods'] = $this->db->query($sql);
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("reports/form/doljniki_za_period", $data);
        $this->load->view("right");
    }

    function doljniki_za_period()
    {
        $data['org_info'] = $this->db->get("industry.org_info")->row();
        $sql = 'select * from industry.period where id=' . $_POST['period_id'];
        $data['period'] = $this->db->query($sql)->row();
        $data['use_ture'] = '0';
        $sql = 'select * from industry.doljniki_za_period where 
			period_id=' . $_POST['period_id'];
        if ($_POST['type'] == 2) {
            $sql .= " and 
			coalesce(\"doljniki_za_period\".debet_value,0) - 
			coalesce(\"doljniki_za_period\".kredit_value,0)	+
			coalesce(\"doljniki_za_period\".nachisleno,0) -
			coalesce(\"doljniki_za_period\".oplata_value,0)>0 ";
            $data['nazv'] = " дебиторов ";
        }
        if ($_POST['type'] == 3) {
            $sql .= " and 
			coalesce(\"doljniki_za_period\".debet_value,0) - 
			coalesce(\"doljniki_za_period\".kredit_value,0)	+
			coalesce(\"doljniki_za_period\".nachisleno,0) -
			coalesce(\"doljniki_za_period\".oplata_value,0)<0 ";
            $data['nazv'] = " кредиторов ";
        }
        if ($_POST['ture_id'] != -1) {
            $data['use_ture'] = '1';
            $sql .= " and firm_ture_id= " . $_POST['ture_id'];
            $this->db->where("id", $_POST['ture_id']);
            $name = $this->db->get("industry.ture")->row()->name;
            $data['ture'] = " <br>по ТУРЭ $name";
        }
        $data['sql_result'] = $this->db->query($sql);
        $this->load->view("reports/doljniki_za_period", $data);
    }

    function vih_7_re_form()
    {
        $sql = "SELECT * from industry.period order by id";
        $data['periods'] = $this->db->query($sql);
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("reports/form/vih_7_re_form", $data);
        $this->load->view("right");
    }

    function vih_7_re()
    {
        $data['org_info'] = $this->db->get("industry.org_info")->row();
        $sql = 'select * from industry.period where id=' . $_POST['period_id'];
        $data['period'] = $this->db->query($sql)->row();
        $data['use_ture'] = '0';
        $sql = 'select * from industry."7-re" where 
			period_id=' . $_POST['period_id'];
        if ($_POST['type'] == 2)
            $sql .= " and 
			coalesce(\"7-re\".debet_value,0) - 
			coalesce(\"7-re\".kredit_value,0)	+
			coalesce(\"7-re\".nachisleno,0) -
			coalesce(\"7-re\".oplata_value,0)>0 ";
        if ($_POST['type'] == 3)
            $sql .= " and 
			coalesce(\"7-re\".debet_value,0) - 
			coalesce(\"7-re\".kredit_value,0)	+
			coalesce(\"7-re\".nachisleno,0) -
			coalesce(\"7-re\".oplata_value,0)<0 ";
        if ($_POST['ture_id'] != -1) {
            $data['use_ture'] = '1';
            $sql .= " and firm_ture_id= " . $_POST['ture_id'];
            $this->db->where("id", $_POST['ture_id']);
            $name = $this->db->get("industry.ture")->row()->name;
            $data['ture'] = " <br>по ТУРЭ $name";
        }
        $data['sql_result'] = $this->db->query($sql);
        $this->load->view("reports/7-re", $data);
    }

    function vih_2_re()
    {

        $data['org_info'] = $this->db->get("industry.org_info")->row();
        $sql = 'select * from industry.period where id=' . $_POST['period_id'];
        $data['period'] = $this->db->query($sql)->row();
        if ($_POST['ture_id'] != -1) {
            $this->db->where('id', $_POST['ture_id']);
            $data['ture'] = $this->db->get("industry.ture")->row()->name;
            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('firm_ture_id', $_POST['ture_id']);
            $data['sql_result'] = $this->db->get('industry."2-re_by_ture"');
        } else {
            $data['ture'] = NULL;
            $this->db->where('period_id', $_POST['period_id']);
            $data['sql_result'] = $this->db->get("industry.\"2-re\"");
        }
        $this->load->view("reports/2-re", $data);
    }

    function vih_2_re_form()
    {
        $this->db->order_by("name");
        $data['ture'] = $this->db->get("industry.ture");
        $sql = "SELECT * from industry.period order by id";
        $data['periods'] = $this->db->query($sql);
        $this->left();
        $this->load->view("reports/form/vih_2_re_form", $data);
        $this->load->view("right");
    }

    function vih_analiz_kredit_debit()
    {
        $sql = 'select * from industry.analiz_of_change_debet_kredit where period_id=4';
        $data['analiz'] = $this->db->query($sql)->result();
        $this->load->view('reports/analiz_of_change_debet_kredit', $data);
    }

    function rashod_electro()
    {
        $sql = "SELECT * FROM industry.firm where id=" . $this->uri->segment(3);
        $data['firm_data'] = $this->db->query($sql)->row();
        $sql = "SELECT * FROM industry.rashod_electro where firm_id=" . $this->uri->segment(3);
        $data['points_data'] = $this->db->query($sql);
        $this->load->view('reports/rashod_electro', $data);
    }

    function counters_by_type()
    {
        $sql = "select * from industry.counters_by_type where counter_data_finish is null order by counter_type_id,ture_id,dogovor";
        $data['counters'] = $this->db->query($sql);
        $this->load->view('reports/counters_by_type', $data);
    }

    function reported_firms_form()
    {
        $this->db->order_by("id");
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("reports/form/reported_firms_form", $data);
        $this->load->view("right");
    }

    function reported_firms()
    {
        $this->db->where('id', $_POST['ture_id']);
        $data["ture"] = $this->db->get('industry.ture')->row();
        $sql = "select * from industry.reported_firms where firm_close_id is " . (!$_POST['reported_or_notreported'] ? " not " : "") . " null and ture_id=" . $_POST['ture_id'] . "  order by dogovor";
        $data['type'] = $_POST['reported_or_notreported'];
        $data['firms'] = $this->db->query($sql);
        $this->load->view('reports/reported_firms', $data);
    }

    function pre_dolgi()
    {
        $this->db->order_by("id");
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("pre_dolgi", $data);
        $this->load->view("right");
    }

    function dolgi()
    {
        $data['we'] = $this->db->get("industry.org_info")->row();
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->db->order_by("dogovor");
        $this->db->where("dolg::numeric(24,2)>", 0);
        $this->db->where("firm_ture_id", $_POST['ture_id']);
        $data['firms'] = $this->db->get('industry.dolgi');

        $this->db->where("id", $_POST['ture_id']);
        $data['ture'] = $this->db->get('industry.ture')->row();

        $this->load->view('reports/dolgi', $data);
    }

    function pre_subabonent()
    {
        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $this->db->order_by("id");
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("pre_subabonent", $data);
        $this->load->view("right");
    }

    function subabonent()
    {
        $this->db->where('period_id', $_POST['period_id']);
        if ($_POST['ture_id'] != -1)
            $this->db->where('ture_id', $_POST['ture_id']);
        $data['firms'] = $this->db->get('industry.subabonent');

        $this->load->view('reports/subabonent', $data);
    }

    function pre_snyatie_counter_value()
    {
        $this->db->order_by("id");
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view("reports/form/snyatie_counter_value", $data);
        $this->load->view("right");
    }

    function snyatie_counter_value()
    {
        $data['we'] = $this->db->query("select * from industry.org_info")->row();
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->db->where('id', $_POST['ture_id']);
        $data['values'] = $this->db->get("industry.snyatie_counter_value");
        $this->load->view('reports/snyatie_counter_value', $data);
    }

    function pre_list_of_firms()
    {
        $data['users'] = $this->db->get("industry.user");
        $this->left();
        $this->load->view("pre_list_of_firms", $data);
        $this->load->view("right");
    }

    function list_of_firms()
    {
        if ($_POST['user_id'] != -1)
            $this->db->where("user_id", $_POST['user_id']);

        $data['firms'] = $this->db->get("industry.list_of_firms");

        $this->load->view('reports/list_of_firms', $data);
    }

    function user_list()
    {
        $data['users'] = $this->db->get('industry.user');
        $this->load->view("user_list", $data);
    }

    function oborotka()
    {
        $firm_id = $this->uri->segment(3);
        $this->db->where('firm_id', $firm_id);
        $data['oborotka'] = $this->db->get('industry.oborotka')->row();
        $data['firm'] = $this->get_firm_info_by_id($firm_id);
        $this->left();
        $this->load->view("oborotka", $data);
        $this->load->view("right");
    }

    function firm_oplata()
    {
        $firm_id = $this->uri->segment(3);
        $this->db->where('firm_id', $firm_id);
        $data['firm_oplata'] = $this->db->get('industry.firm_oplata')->result();

        $data['firm'] = $this->get_firm_info_by_id($firm_id);

        $this->left();
        $this->load->view("firm_oplata", $data);
        $this->load->view("right");
    }

    function delete_counter()
    {
        $sql = "select point_id from industry.counter where id=" . $this->uri->segment(3);
        $point_id = $this->db->query($sql)->row()->point_id;
        $sql = "select industry.delete_counter(" . $this->uri->segment(3) . ") as is_deleted;";
        $is_deleted = $this->db->query($sql)->row()->is_deleted;
        $this->session->set_flashdata('is_deleted', $is_deleted);
        redirect("billing/point/" . $point_id);
    }

    private function check_billing_point($bill_id)
    {
        $bill_id = $this->uri->segment(3);
        $period_id = $this->get_cpi($bill_id);
        $this->db->where("bill_id", $bill_id);
        $this->db->where("period_id", $period_id);
        $n = $this->db->get("industry.nadbavka_info");
        if ($n->num_rows > 0) {
            die("V dannyi period na tochke ucheta nahodyatsya nadbavki!");
        }
        $this->db->where("bill_id", $bill_id);
        $this->db->where("period_id", $period_id);
        $sbp = $this->db->get("industry.sovm_billing_point");
        if ($sbp->num_rows > 0) {
            die("Na tochke ucheta imeutsya sovmesntye uchety!");
        }
        $this->db->where("bill_id", $bill_id);
        $unfc = $this->db->get("industry.unfinished_counter");
        if ($unfc->num_rows > 0) {
            die("Na tochke ucheta imeutsya nesnyatye schetchiki!");
        }
    }

    private function get_cpi()
    {
        return $this->db->query("select * from industry.current_period_id()")->row()->current_period_id;
    }

    function delete_billing_point()
    {
        $this->check_billing_point($this->uri->segment(3));
        $sql = "select firm_id from industry.billing_point where id=" . $this->uri->segment(3);
        $firm_id = $this->db->query($sql)->row()->firm_id;
        $sql = "select count(*) as count from industry.counter where point_id=" . $this->uri->segment(3);
        $count = $this->db->query($sql)->row()->count;
        if ($count == 0) {
            $count = -1;
            #poteri
            $this->db->query("DELETE FROM industry.poteri_period WHERE bill_id = " . $this->uri->segment(3));
            #end poteri
            $sql = "delete from industry.billing_point where id=" . $this->uri->segment(3);
            $this->db->query($sql);
        }

        $this->session->set_flashdata('is_deleted', $count);
        redirect("billing/firm/" . $firm_id);
    }

    function edit_billing_point()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['point'] = $this->db->get('industry.billing_point')->row();
        $this->db->order_by('name');
        $data['tp'] = $this->db->get('industry.tp');
        $data['power_group'] = $this->db->get('industry.firm_power_group');
        $data['point_id'] = $this->uri->segment(3);
        $this->left();
        $this->load->view('edit_billing_point', $data);
        $this->load->view('right');
    }

    function edition_billing_point()
    {
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('industry.billing_point', $_POST);
        redirect("billing/edit_billing_point/" . $this->uri->segment(3));
    }

    function edit_permission()
    {
        $sql = "select * from industry.user where id=" . $this->uri->segment(3);
        $data['perm'] = $this->db->query($sql)->row_array();
        $this->left();
        $this->load->view('edit_permission', $data);
        $this->load->view("right");
    }

    function edition_permission()
    {
        $sql = "select * from industry.user where id=" . $_POST['id'];
        $perm = $this->db->query($sql)->row_array();
        $this->db->where('id', $_POST['id']);
        unset($perm['id']);
        unset($perm['password']);
        foreach ($perm as $key => $_f) {
            if (($key != 'name') and ($key != 'login') and ($key != 'profa')) {
                if (isset($_POST[$key]))
                    $f[$key] = 't';
                else $f[$key] = 'f';
            }
        }
        $this->db->update('industry.user', $f);
        redirect('billing/edit_permission/' . $_POST['id']);
    }

    //! DBASE для работы с DBF
    function to_date($var)
    {
        $year = substr($var, 0, 4);
        $month = substr($var, 4, 2);
        $day = substr($var, 6, 2);
        return $day . "." . $month . "." . $year;
    }

    function dbase()
    {
        $this->db->query("delete from industry.oplata_buf");
        $period = $this->db->query("select * from industry.period where id = industry.current_period_id()")->row();
        $sql = "";
        set_time_limit(0);
        $db = dbase_open("c:/oplata/OPLATA.dbf", 0);

        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                $rec = dbase_get_record_with_names($db, $i);

                $year = substr($rec['DATA'], 0, 4);
                $month = substr($rec['DATA'], 4, 2);
                $day = substr($rec['DATA'], 6, 2);

                $data = mktime(0, 0, 0, $month, $day, $year);
                $data = date("Y-m-d", $data);
                if (($data >= $period->begin_date) and ($data <= $period->end_date)) {
                    $rec['DATA'] = $this->to_date($rec['DATA']);
                    $rec['DATA_V'] = $this->to_date($rec['DATA_V']);

                    if (strlen(trim($rec['VO'])) == 0) $rec['VO'] = 0;

                    $rec['UN_NOM'] = iconv(mb_detect_encoding($rec['UN_NOM'], mb_detect_order(), true), "UTF-8", $rec['UN_NOM']);
                    $rec['N_DOKUM'] = iconv(mb_detect_encoding($rec['N_DOKUM'], mb_detect_order(), true), "UTF-8", $rec['N_DOKUM']);

                    $sql .= "\nINSERT INTO industry.oplata_buf(
					 data, un_nom, dog, data_v, n_dokum, sum, schet, vo) values 
					 ('{$rec['DATA']}','{$rec['UN_NOM']}',{$rec['DOG']},
					 '{$rec['DATA_V']}','{$rec['N_DOKUM']}',{$rec['SUM']},
					 '{$rec['SCHET']}',{$rec['VO']});\n";
                }
            }
            dbase_close($db);

            $this->db->query($sql);

            $d["d"] = $this->db->get('industry.oplata_unknown_dogovor');
            $d["s"] = $this->db->get('industry.oplata_unknown_schet');
            $this->load->view("oplata/import", $d);
        } else {
            echo "Oplata.dbf is busy!";
        }
    }

    function oplata_import()
    {
        $this->db->query(
            "
		delete from industry.oplata where data between  
		   (select period.begin_date from industry.period
                 left join industry.sprav on sprav.name='current_period'
					where period.id=sprav.value) 
					 and (select period.end_date from industry.period
                 left join industry.sprav on sprav.name='current_period'
					where period.id=sprav.value) ;
		insert into industry.oplata 
			(firm_id,data,document_number,payment_number_id,value,nds)
			select industry.firm_id_by_dogovor(dog) as firm_id,data,n_dokum, industry.schet_id_by_name(schet),
			sum/(1+industry.current_nds()/100),industry.current_nds() from industry.oplata_buf 
			where industry.firm_id_by_dogovor(dog) is not null and industry.schet_id_by_name(schet) is not null"
        );
        $this->db->query(
            "DELETE FROM industry.fine_oplata WHERE data BETWEEN
               (SELECT period.begin_date FROM industry.period
                     LEFT JOIN industry.sprav ON sprav.name='current_period'
                        WHERE period.id=sprav.value)
                         AND (SELECT period.end_date FROM industry.period
                     LEFT JOIN industry.sprav ON sprav.name='current_period'
                        WHERE period.id=sprav.value) ;
             INSERT INTO industry.fine_oplata
                (firm_id,data,document_number,payment_number_id,value,nds)
                SELECT 
                    industry.firm_id_by_dogovor(dog) AS firm_id,
					--industry.firm_id_by_nomer1c(nomer1c) AS firm_id,
                    data,
                    n_dokum, 
                    industry.schet_id_by_name(schet),
                    sum/(1+industry.current_nds()/100),
                    industry.current_nds() 
                FROM industry.oplata_buf
                WHERE 
				industry.firm_id_by_dogovor(dog) IS NOT NULL 
				--firm_id_by_nomer1c(nomer1c) IS NOT NULL 
                AND industry.schet_id_by_name(schet) IS NOT NULL
                AND oplata_buf.vo = 4"
        );
        #$this->db->query("select * from industry.load_oplata()");
        redirect("billing");
    }

    function jpeg()
    {

    }

    function gd_info()
    {
        gd_info();
    }

    function com()
    {
        $xls = new COM("Excel.Application");
        $xls->Application->Visible = 1;
        $xls->Workbooks->Add();
        $range = $xls->Range("A1");
        $range->Value = "Проба записи";
        // Сохраняем документ
        $xls->Workbooks[1]->SaveAs("c:/test.xls");
        $xls->Quit();
        $xls->Release();
        $xls = Null;
        $range = Null;
        echo "Hello";
    }

    function change_password()
    {
        $this->left();
        $this->load->view('sprav/change_password');
        $this->load->view('right');
    }

    function changing_password()
    {
        $sql = "select * from industry.user where login='{$this->session->userdata('login')}' and password=md5('{$_POST['old_pass']}')";
        $count = $this->db->query($sql)->num_rows();
        if ($count > 0) {
            if ($_POST['new_pass_1'] == $_POST['new_pass_2']) {
                $this->db->where('id', $this->session->userdata('id'));
                $this->db->update('industry.user', array('password' => md5($_POST['new_pass_1'])));
                $this->session->set_flashdata('ischanged', 'yes');
            } else
                $this->session->set_flashdata('ischanged', 'not_ident');
        } else $this->session->set_flashdata('ischanged', 'old_pass_error');
        redirect('billing/change_password');
    }

    // работа с оплатой

    function org_info()
    {
        $this->left();
        $data['org_info'] = $this->db->get("industry.org_info")->row();
        $this->load->view('sprav/org_info', $data);
        $this->load->view('right');
    }

    function changing_org_info()
    {
        $this->db->update('industry.org_info', $_POST);
        $this->session->set_flashdata('is_changed', 'изменено');
        redirect("billing/org_info");
    }

    function pre_oplata_info()
    {
        $this->left();
        $this->load->view('pre_oplata_info');
        $this->load->view('right');
    }

    function oplata_info()
    {
        $sql = "select * from industry.oplata_info where oplata_data between '{$_POST['begin_date']}' and '{$_POST['end_date']}';";
        $data['oplata_info'] = $this->db->query($sql);
        $this->load->view('reports/oplata_info', $data);
    }

    function oplata()
    {
        if ($this->session->userdata('begin_data') == "") {
            $sql = "select period.* from industry.period 
					left join industry.sprav on sprav.name='current_period'
						where period.id=sprav.value";
            $period = $this->db->query($sql)->row();
            $this->session->set_userdata(array('begin_data' => $period->begin_date, 'end_data' => $period->end_date));
        }

        /*FINE*/
        $this->db->where('data >= ', $this->session->userdata('begin_data'));
        $this->db->where('data <= ', $this->session->userdata('end_data'));
        $data['fine_oplata'] = $this->db->get("industry.fine_oplata_edit")->result();
        /*END FINE*/

        $sql = "select * from industry.oplata_edit where data between '" . $this->session->userdata('begin_data') . "' and '" . $this->session->userdata('end_data') . "'";
        $data['oplata'] = $this->db->query($sql);
        $this->load->view('oplata/index', $data);
    }

    function oplata_po_schetam()
    {
        if (!isset($_POST['start'])) {
            $this->db->where('period_id', $_POST['period_id']);
            $data['oplata'] = $this->db->get('industry.oplata_po_schetam');
        } else {
            $sql = "select * from industry.oplata_po_schetam where data between '{$_POST['start']}' and 
								'{$_POST['end']}'";
            $data['oplata'] = $this->db->query($sql);
        }

        $this->load->view("oplata/po_schetam", $data);
    }

    function pre_platejnye_dokumenty()
    {
        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view("pre_platejnye_dokumenty", $data);
        $this->load->view("right");
    }

    function platejnye_dokumenty()
    {
        $this->db->where('period_id', $_POST['period_id']);
        $data['oplata'] = $this->db->get('industry.platejnye_dokumenty');
        $this->load->view("oplata/platejnye_dokumenty", $data);
    }

    function oplata_svod()
    {
        $this->db->where('period_id', $_POST['period_id']);
        $data['oplata'] = $this->db->get("industry.oplata_svod");
        $this->load->view('oplata/svod', $data);
    }

    function pre_oplata_svod()
    {
        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view("pre_oplata_svod", $data);
        $this->load->view("right");
    }

    function pre_oplata_po_schetam()
    {
        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view("pre_oplata_po_schetam", $data);
        $this->load->view("right");
    }

    function change_oplata_period()
    {
        $this->session->set_userdata($_POST);
        redirect('billing/oplata');
    }

    /*
	function adding_oplata()
    {
        $sql = "SELECT count(*) FROM industry.firm WHERE dogovor=" . $_POST['dogovor'];
        $count = $this->db->query($sql)->row()->count;

        $sql = "SELECT id,name FROM industry.firm WHERE dogovor=" . $_POST['dogovor'];
        $query = $this->db->query($sql);
        if ($count > 0) {

            $current_nds = $this->db->query("select * from industry.current_nds()")->row()->current_nds;

            $firm_id = $this->db->query($sql)->row()->id;
            $firm_name = $this->db->query($sql)->row()->name;
            $data['firm_id'] = $firm_id;
            $data['nds'] = $_POST['nds'];
            $sql = "SELECT count(*)  FROM industry.payment_number WHERE number='" . $_POST['payment_number'] . "'";
            $count = $this->db->query($sql)->row()->count;
            $sql = "SELECT id FROM industry.payment_number WHERE number='" . $_POST['payment_number'] . "'";
            echo $sql;
            $query = $this->db->query($sql);
            if ($count > 0) {
                $data['payment_number_id'] = $query->row()->id;
                $this->session->set_userdata(
                    array('data' => $_POST['data'],
                        'number' => $_POST['payment_number']
                    )
                );
                $data['value'] = $_POST['value'] / (($current_nds + 100) / 100);
                $data['data'] = $_POST['data'];
                $data['document_number'] = $_POST['document_number'];


                #деление оплаты начислений и пени
                $firm_fine_saldo = $this->db->query(
                    "select
                      (fine_saldo.value - coalesce(sum(fine_oplata.value*((100+fine_oplata.nds)/100)),0)) as itogo_saldo
                    from industry.fine_saldo
                    join industry.period on period.id = fine_saldo.period_id
                    left join industry.fine_oplata on fine_oplata.firm_id = fine_saldo.firm_id
                      and fine_oplata.data >= period.begin_date and fine_oplata.data <= period.end_date
                    where fine_saldo.firm_id={$firm_id} and fine_saldo.period_id = industry.current_period_id()
                    group by fine_saldo.value"
                )->row()->itogo_saldo;

                $day_opl = 0;
                $day_fine_opl = 0;

                $sum_bez_nds = $data['value'];
                $sum_with_nds = $data['value'] * (($current_nds + 100) / 100);

                $opl_dif = $firm_fine_saldo - $sum_with_nds;

                if ($opl_dif >= 0) {
                    $day_fine_opl = $sum_bez_nds;
                    echo "<br>opl_dif: $opl_dif > 0";
                    $day_opl = 0;
                } else {
                    $day_fine_opl = $firm_fine_saldo / (($current_nds + 100) / 100);
                    $day_opl = ($opl_dif * (-1)) / (($current_nds + 100) / 100);
                    echo "<br>opl_dif: $opl_dif < 0";
                    $opl_dif = 0;
                }

                if (((int)$day_opl) > 0) {
                    $data['value'] = $day_opl;
                    $this->db->insert('industry.oplata', $data);
                }

                if (((int)$day_fine_opl) > 0) {
                    $data['value'] = $day_fine_opl;
                    $this->db->insert('industry.fine_oplata', $data);
                }


//                $this->db->insert('industry.oplata', $data);

                $this->session->set_flashdata('added', 'true');
                $this->session->set_flashdata('firm_name', $firm_name);

            }
        }
        redirect('billing/oplata');
    }*/

    #метод переключения
    function adding_oplata()
    {

        $sql = "select count(*) from industry.firm where dogovor=" . $_POST['dogovor'];
        $count = $this->db->query($sql)->row()->count;
        $current_nds = $this->db->query("select * from industry.current_nds()")->row()->current_nds;
        $sql = "select id,name from industry.firm where dogovor=" . $_POST['dogovor'];
        $query = $this->db->query($sql);
        if ($count > 0) {
            $firm_id = $this->db->query($sql)->row()->id;
            $firm_name = $this->db->query($sql)->row()->name;
            $data['firm_id'] = $firm_id;
            $data['nds'] = $_POST['nds'];
            $sql = "select count(*)  from industry.payment_number where number='" . $_POST['payment_number'] . "'";
            $count = $this->db->query($sql)->row()->count;
            $sql = "select id from industry.payment_number where number='" . $_POST['payment_number'] . "'";
            echo $sql;
            $query = $this->db->query($sql);
            if ($count > 0) {
                $data['payment_number_id'] = $query->row()->id;
                $this->session->set_userdata(array(
                    'data' => $_POST['data'], 'number' => $_POST['payment_number']
                ));
                $data['value'] = $_POST['value'] / (($current_nds + 100) / 100);
                $data['data'] = $_POST['data'];
                $data['document_number'] = $_POST['document_number'];
                if ($_POST['pay_type'] == 1) {
                    $this->db->insert('industry.oplata', $data);
                } else {
                    $this->db->insert('industry.fine_oplata', $data);
                }
                $this->session->set_flashdata('added', 'true');
                $this->session->set_flashdata('firm_name', $firm_name);

            }
        }
        redirect('billing/oplata');
    }

    function pre_svod_po_tp()
    {
        $data['ture'] = $this->db->get("industry.ture");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_svod_po_tp', $data);
        $this->load->view('right');
    }

    function svod_po_tp()
    {

        $this->load->library("pdf/pdf");

        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 7);

        // add a page
        $this->pdf->AddPage();
        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period');
        $this->db->where('ture_id', $_POST['ture_id']);
        $this->db->where('period_id', $_POST['period_id']);
        $data['ture'] = $this->db->get('industry.svod_po_tp');
        $this->db->where('id', $_POST['ture_id']);
        $data['ture_name'] = $this->db->get('industry.ture')->row()->name;
        $string = $this->load->view("reports/svod_po_tp", $data, TRUE);

        $this->pdf->writeHTML($string);

        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');
    }

    function graph_test()
    {

        $this->load->library("pdf/pdf");

        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        // set font
        $this->pdf->SetFont('dejavusans', '', 7);

        // add a page
        $this->pdf->AddPage();

        $data['1'] = 23;
        $string = $this->load->view("reports/graph_test", $data, TRUE);

        $this->pdf->writeHTML($string);

        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');
    }

    function pre_multi_tariff_count()
    {
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view('pre_multi_tariff_count', $data);
        $this->load->view('right');
    }

    function multi_tariff_count()
    {
        if ($_POST['ture_id'] != -1) {
            $this->db->where('id', $_POST['ture_id']);
            $data['ture'] = $this->db->get('industry.ture')->row();
            $this->db->where('ture_id', $_POST['ture_id']);
        }
        if ($_POST['dop'] == 1) {
            $data['nado'] = 'true';
        } else {
            $data['nado'] = 'false';
        }

        //! ТОО
        $sql_too = "select * from industry.multi_tariff_count where 
   firm_id not in (
   select billing_point.firm_id from industry.sovm_by_counter_value  
   left join industry.billing_point on 
	sovm_by_counter_value.billing_point_id=billing_point.id
	left join industry.sprav on sprav.name='current_period'
	where sovm_by_counter_value.period_id=sprav.value::integer
   )    and is_too=true and firm_group_id<>21";
        if ($_POST['ture_id'] != -1) {
            $sql_too .= " and ture_id=" . $_POST['ture_id'];
        }
        $data['too'] = $this->db->query($sql_too);

        //	$this->db->where('firm_group_id<>21',NULL,false);
        //	$this->db->where('is_too','true');
        //	$data['too']=$this->db->get("industry.multi_tariff_count");
        //! Субабоненты
        $sql = "select * from industry.multi_tariff_count where 
   firm_id in (
   select billing_point.firm_id from industry.sovm_by_counter_value  
   left join industry.billing_point on 
	sovm_by_counter_value.billing_point_id=billing_point.id
	left join industry.sprav on sprav.name='current_period'
	where sovm_by_counter_value.period_id=sprav.value::integer
   )    ";
        if ($_POST['ture_id'] != -1) {
            $sql .= " and ture_id=" . $_POST['ture_id'];
        }
        $data['sub'] = $this->db->query($sql);

        //! Гос
        $sql_gos = "select * from industry.multi_tariff_count where 
   firm_id  not in (
   select billing_point.firm_id from industry.sovm_by_counter_value  
   left join industry.billing_point on 
	sovm_by_counter_value.billing_point_id=billing_point.id 
	left join industry.sprav on sprav.name='current_period'
	where sovm_by_counter_value.period_id=sprav.value::integer
   )   and  firm_group_id=21  ";

        if ($_POST['ture_id'] != -1) {
            $sql_gos .= " and  ture_id=" . $_POST['ture_id'];
        }

        $data['gos'] = $this->db->query($sql_gos);

        //! ИП
        $sql_ip = "select * from industry.multi_tariff_count where 
   firm_id  not in (
   select billing_point.firm_id from industry.sovm_by_counter_value  
   left join industry.billing_point on 
	sovm_by_counter_value.billing_point_id=billing_point.id
	left join industry.sprav on sprav.name='current_period'
	where sovm_by_counter_value.period_id=sprav.value::integer
   )   and  firm_group_id<>21 and is_ip=true ";

        if ($_POST['ture_id'] != -1) {
            $sql_ip .= " and  ture_id=" . $_POST['ture_id'];
        }

        $data['ip'] = $this->db->query($sql_ip);

        //! Прочие
        $sql_last = "select * from industry.multi_tariff_count where 
   firm_id not in (
   select billing_point.firm_id from industry.sovm_by_counter_value  
   left join industry.billing_point on 
	sovm_by_counter_value.billing_point_id=billing_point.id
	left join industry.sprav on sprav.name='current_period'
	where sovm_by_counter_value.period_id=sprav.value::integer
   )    and is_too=false and is_ip=false and firm_group_id<>21";
        if ($_POST['ture_id'] != -1) {
            $sql_last .= " and ture_id=" . $_POST['ture_id'];
        }
        $data['last_firm'] = $this->db->query($sql_last);

        //! Конец заполнения данных

        $data['_POST'] = $_POST;
        $this->load->view('reports/multi_tariff_count', $data);
    }

    function pre_svod_saldo_po_ture()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_svod_saldo_po_ture', $data);
        $this->load->view('right');
    }

    function svod_saldo_po_ture()
    {
        $this->load->library("pdf/pdf");


        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->db->where('period_id', $_POST['period_id']);
        $data['ture'] = $this->db->get('industry.svod_saldo_po_ture');
        $string = $this->load->view("reports/svod_saldo_po_ture", $data, TRUE);

        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set font
        $this->pdf->SetFont('dejavusans', '', 10);

        // add a page
        $this->pdf->AddPage();
        $this->pdf->writeHTML($string);

        //Close and output PDF document
        $this->pdf->Output('example_001.pdf', 'I');
    }

    function pre_energo_24()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_energo_24', $data);
        $this->load->view('right');
    }

    function last_edit()
    {
        $this->left();
        $this->db->where("id", $this->uri->segment(3));
        $data['point'] = $this->db->get("industry.billing_point");
        $this->load->view('last_edit', $data);
        $this->load->view('right');
    }

    function last_edition()
    {
        $sql = "update industry.billing_point set last_gos_proverka='{$_POST['value']}',last_plan_proverka='{$_POST['value2']}' where id={$this->uri->segment(3)}";
        $this->db->query($sql);
        redirect("billing/last_edit/{$this->uri->segment(3)}");
    }

    function energo_24()
    {
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->db->where('period_id', $_POST['period_id']);
        $data['energo'] = $this->db->get('industry.energo_24');
        $this->load->view('reports/energo_24', $data);
    }

    function naryad_zadanie_po_ture()
    {
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->db->where('ture_id', $_POST['ture_id']);
        //$this->db->where('phase',$_POST['phase']);
        if ($_POST["type"] == 1)
            $this->db->where('in_tp', 'true');
        $data['naryad'] = $this->db->get('industry.naryad_zadanie_po_ture');
        $this->db->where('id', $_POST['ture_id']);
        $data['ture'] = $this->db->get('industry.ture')->row();
        $this->load->view('reports/naryad_zadanie_po_ture', $data);
    }

    function pre_naryad_zadanie_po_ture()
    {
        $data['ture'] = $this->db->get("industry.ture");
        $this->left();
        $this->load->view('pre_naryad_zadanie_po_ture', $data);
        $this->load->view('right');
    }

    function pre_oborotka_with_predoplata()
    {
        $data['ture'] = $this->db->get("industry.ture");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_oborotka_with_predoplata', $data);
        $this->load->view('right');
    }

    function oborotka_with_predoplata()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period')->row();
        $this->db->where('period_id', $_POST['period_id']);
        $this->db->where('firm_ture_id', $_POST['ture_id']);

        $data['oborotka'] = $this->db->get("industry.oborotka_with_predoplata");
        $this->load->view('reports/oborotka_with_predoplata', $data);
    }

    function pre_svod_oplat_po_firmam()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_svod_oplat_po_firmam', $data);
        $this->load->view('right');
    }

    function svod_oplat_po_firmam()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $this->db->order_by('dogovor');
        $this->db->where('period_id', $_POST['period_id']);
        $data['svod'] = $this->db->get("industry.svod_oplat_po_firmam");
        $this->load->view('reports/svod_oplat_po_firmam', $data);
    }

    function pre_poleznyy_otpusk()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_poleznyy_otpusk', $data);
        $this->load->view('right');
    }

    function poleznyy_otpusk()
    {
        $data['org_info'] = $this->db->get('industry.org_info')->row();
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period");
        $this->db->where('period_id', $_POST['period_id']);
        if ($_POST['type'] == 2)
            $this->db->where('is_too', 'TRUE');
        if ($_POST['type'] == 3)
            $this->db->where('is_too', 'FALSE');
        if ($_POST['type'] == 2 or $_POST['type'] == 3) {
            $data['otpusk'] = $this->db->get('industry.poleznyy_otpusk_too');
        } else {
            $data['otpusk'] = $this->db->get('industry.poleznyy_otpusk');
        }
        #added
        #twodiff tariffs
        /*$data['otpusk']=$this->db->get('industry.poleznyy_otpusk_two_tariffs');*/
        #added
        $this->load->view('reports/poleznyy_otpusk', $data);
    }

    function copy_user()
    {
        $this->db->where('id', '7');
        $user = $this->db->get("industry.user")->row_array();
        unset($user['id']);
        unset($user['name']);
        $this->db->insert('industry.user', $user);
    }

    function pre_perehod()
    {
        $last_ua = $this->db->get("industry.last_user_actions")->result();
        $last_user_actions = array();
        foreach ($last_ua as $lua) {
            $last_user_actions[$lua->class_method] = $lua->max_action_time;
        }
        $data['last_user_actions'] = $last_user_actions;
        $this->left();
        $this->load->view('pre_perehod', $data);
        $this->load->view('right');
    }

    function perehod()
    {
        $this->db->query("select industry.goto_next_period_fine();");
        $array = array(1 => 'Переход в следующий месяц прошел успешно!');
        $this->session->set_flashdata('success', $array);
        redirect("billing");
    }

    function oplata_delete()
    {
        $sql = "delete from industry.oplata where id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect('billing/oplata');
    }

    function statisticheskiy_otchet()
    {
        $data['otchet'] = $this->db->get("industry.statisticheskiy_otchet");
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->load->view("reports/statisticheskiy_otchet", $data);
    }

    function statisticheskiy_otchet_new()
    {
        $data['otchet'] = $this->db->get("industry.stat_otchet_new");
        $data['period_name'] = $this->db->query("select industry.current_period() as current_period")->row();
        $this->load->view("reports/stat_otchet", $data);
    }

    function billing_point_info()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['firm'] = $this->db->get("industry.firm")->row();
        $this->db->where('firm_id', $this->uri->segment(3));
        $data['info'] = $this->db->get("industry.billing_point_info");
        $this->load->view("reports/billing_point_info", $data);
    }

    function billing_point_info_all()
    {
        $data['info'] = $this->db->get("industry.billing_point_info_all");
        $this->load->view("reports/billing_point_info_all", $data);
    }

    function pre_oborotno_svodnaya_vedomost()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_oborotno_svodnaya_vedomost', $data);
        $this->load->view('right');
    }

    function oborotno_svodnaya_vedomost()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period_name'] = $this->db->get('industry.period')->row()->name;
        $this->db->where("period_id", $_POST['period_id']);
        $data['oborotka'] = $this->db->get("industry.oborotno_svodnaya_vedomost");
        $this->load->view("reports/oborotno_svodnaya_vedomost", $data);
    }

    function isRussian($text)
    {
        return preg_match('/[А-Яа-яЁё]/u', $text);
    }

    function perenos_rek1()
    {
        $db = dbase_open("c:/oplata/rekv.dbf", 2);
        if ($db) {
            header('Content-Type: text/html; charset="utf-8"');
            $this->db->order_by("dog");
            $nach = $this->db->get("industry.perenos_rekvizit");
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $russian_letters = array("А", "О", "Е", "С", "Х", "Р", "Т", "Н", "К", "В", "М");
            $english_letters = array("A", "O", "E", "C", "X", "P", "T", "H", "K", "B", "M");
            $incorrected_bins = array();
            $ei_mfo = array();
            $i = 0;
            $e = 0;

            $db2 = dbase_open("c:/oplata/rekv.dbf", 2);
            foreach ($nach->result() as $n) {

                //находим некорректные БИКи и МФО банков
                if ((mb_strlen(trim($n->mfo), 'UTF-8') != 8) and ($n->mfo != '0000000000')) {
                    $ei_mfo[$n->dog]['len'] = mb_strlen(trim($n->mfo), 'UTF-8');
                    $ei_mfo[$n->dog]['mfo'] = trim($n->mfo);
                    $ei_mfo[$n->dog]['bank'] = trim($n->bank);
                    $e++;
                }

                //обнуляем пустые МФО
                if (($n->mfo == '0000000000')) {
                    $n->mfo = '';
                }

                //находим некорректные БИНы организаций
                if ((mb_strlen(trim($n->bin), 'UTF-8') != 12) and (mb_strlen(trim($n->bin), 'UTF-8') != 0)) {
                    $incorrected_bins[$i]['dog'] = $n->dog;
                    $incorrected_bins[$i++]['bin'] = $n->bin;
                    $e++;
                }

                //обнуляем пустые БИНы
                if ((mb_strlen(trim($n->bin), 'UTF-8') == 0)) {
                    $n->bin = '';
                }

                //заменяем кириллицу на латиницу в МФО
                $n->mfo = str_replace($russian_letters, $english_letters, $n->mfo);

                //вдруг пропущен символ
                if ($this->isRussian($n->mfo)) {
                    echo "{$n->mfo} contains russian letters<br>";
                    $e++;
                }

                //пропуск цикла если есть ошибки
                if ($e != 0) {
                    $e = 0;
                    continue;
                }

                dbase_add_record($db2,
                    array(
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->name)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->dog)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->rnn)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->direct)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adres)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->schet)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->tel)), 'cp866', 'utf-8'),
                        $this->d2($n->data),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->sub)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bank)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->mfo)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->korr)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adresbank)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->otrasl)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bin)), 'cp866', 'utf-8'),
                        "0" . $n->dog1
                    )
                );
            }
            dbase_close($db2);

            echo "<b>Договора с некорректными БИНами:</b><br>";
            foreach ($incorrected_bins as $ib) {
                echo $ib['dog'] . ": " . $ib['bin'] . "<br>";
            }
            echo "<br>";
            echo "<b>Банки с некорректными МФО:</b><br>";
            foreach ($ei_mfo as $key => $ib) {
                echo $key . ": " . $ib['bank'] . ": " . $ib['mfo'] . ":" . $ib['len'] . "<br>";
            }
        } else {
            echo "DBF file is busy!";
        }
    }

    function perenos_nach()
    {
        set_time_limit(0);
        @$db = dbase_open("c:/oplata/schet.dbf", 2);
        if ($db) {
            $this->db->where('period_id', $this->get_cpi());
            $nach = $this->db->get("industry.schetfactura_to_1c");
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);
            $db2 = dbase_open("c:/oplata/schet.dbf", 2);
            foreach ($nach->result() as $n) {
                if ($n->dog1 == 0) {
                    $array_error[] = "У договора #{$n->dog} некорректный номер 1C: {$n->dog1}";
                    continue;
                }

                if ($n->beznds == 0) {
                    $array_error[] = "Счет-фактуры, выписанная договору #{$n->dog}, нулевая";
                    continue;
                }

                if (strlen(trim($n->nomer)) == 0) {
                    $array_error[] = "Номер счет-фактуры, выписанной договору #{$n->dog}, некорректный: {$n->nomer}";
                    continue;
                }

                dbase_add_record($db2,
                    array(
                        $n->dog,
                        $n->kvt,
                        $n->tarif, $n->beznds, $n->nds, $n->snds, $n->nomer, $this->d2($n->data), "0" . $n->dog1
                    )
                );
            }
            dbase_close($db2);
            $array = array(1 => 'Перенос прошел успешно!');
            $this->session->set_flashdata('success', $array);
            redirect('billing/pre_perehod');
        } else {
            $array = array(1 => 'Перенос не возможен. Закройте файл schet.dbf!');
            $this->session->set_flashdata('error', $array);
            redirect('billing/pre_perehod');
        }
    }

    function firm_poter()
    {
        $sql = "SELECT * from industry.firm_with_poter_full";
        $data['firm'] = $this->db->query($sql);
        $this->load->view("reports/firm_poter", $data);
    }

    function pre_analiz_obwii()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_obwii', $data);
        $this->load->view('right');
    }

    function analiz_obwii()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_obwii");
        $this->load->view("reports/analiz_obwii", $data);
    }


    function pre_diff_tariff_controll()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_diff_tariff_controll', $data);
        $this->load->view('right');
    }

    function diff_tariff_controll()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.diff_tariff_controll");
        $this->load->view("reports/diff_tariff_controll", $data);
    }

    function pre_diff_tariff_spisok()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_diff_tariff_spisok', $data);
        $this->load->view('right');
    }

    function diff_tariff_spisok()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.diff_tariff_spisok");
        $this->load->view("reports/diff_tariff_spisok", $data);
    }

    function pre_diff_tariff_controll_3()
    {
        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_diff_tariff_controll_3', $data);
        $this->load->view('right');
    }

    function diff_tariff_controll_3()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);

        $data['diff'] = $this->db->get("industry.diff_tariff_controll_3");
        $this->load->view("reports/diff_tariff_controll_3", $data);
    }

    function pre_diff_tariff_spisok_3()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_diff_tariff_spisok_3', $data);
        $this->load->view('right');
    }

    function diff_tariff_spisok_3()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        if ($_POST["type"] == 2)
            $this->db->where("is_ip", "true");
        if ($_POST["type"] == 3)
            $this->db->where("is_too", "true");
        $data['diff'] = $this->db->get("industry.diff_tariff_spisok_3");
        $this->load->view("reports/diff_tariff_spisok_3", $data);
    }

    function nachislenie_v_buhgalteriu()
    {


        set_time_limit(0);
        $db = dbase_open("c:/oplata/nach.dbf", 2);

        if ($db) {
            $nach = $this->db->query("select *from industry.nachislenie_v_buhgalteriu union all select * from industry.nach_fine  order by \"NOMERSCHET\", \"NPENI\"");
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $db2 = dbase_open("c:/oplata/nach.dbf", 2);
            foreach ($nach->result() as $n) {
                dbase_add_record($db2,
                    array(
                        $this->d2($n->DATA),
                        $n->NAIM,
                        $n->DOG, $n->NACH, $n->NACH_SUM, $n->NACH_NDS, $n->KVT, $n->NOMERSCHET, $this->d2($n->KONDATA), $n->NPENI
                    ));
            }

            dbase_close($db2);
        } else
            echo "База не открыта";
    }

    #added
    function pre_nachislenie_v_analiz()
    {
        $this->db->order_by("id", "desc");
        $data['period'] = $this->db->get("industry.period");
        $data['current_period'] = $this->db->query("select sprav.value from industry.sprav where sprav.name = 'current_period'")->row();
        $this->left();
        $this->load->view("pre_nachislenie_v_analiz", $data);
        $this->load->view("right");
    }

    function nachislenie_v_analiz()
    {
        $this->db->where("period_id", $_POST['period_id']);
        $met = $this->input->post('met');
        switch ($met) {
            case 'old':
                $nach = $this->db->get("industry.analiz_po_tp");
                break;
            case 'new':
                $nach = $this->db->get("industry.analiz_po_tp_by_bill_light");
                break;
            default:
                $nach = $this->db->get("industry.analiz_po_tp");
                break;
        }

        set_time_limit(0);
        $db = dbase_open("c:/oplata/anal_tp.dbf", 2);

        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $db2 = dbase_open("c:/oplata/anal_tp.dbf", 2);
            foreach ($nach->result() as $n) {
                dbase_add_record($db2,
                    array(
                        $n->ture_id,
                        iconv("utf-8", "windows-1251", $n->tp_name),
                        $n->kvt, 0, 0
                    )
                );
            }

            dbase_close($db2);
        } else
            echo "База не открыта";
        redirect("billing");
    }

#end added

    function pre_graph()
    {
        $sql = "SELECT value::integer as current_period FROM industry.sprav WHERE name='current_period'";
        $data['current_period'] = $this->db->query($sql)->row()->current_period;

        $this->db->order_by("id");
        $data['period'] = $this->db->get("industry.period");
        $data['firm_id'] = $this->uri->segment(3);
        $this->left();
        $this->load->view('pre_graph', $data);
        $this->load->view('right');
    }

    function closer_number($max)
    {
        $p = 1;
        $pow = 1;
        $pow2 = 100;
        while ($p < $max) {
            $pow = $pow * 10;
            $pow2 = $pow * 10;
            $p = $pow;
            while (($pow2 > $p) and ($max > $p)) {
                $p += $pow;
            }
        }
        $p += $pow;
        $numbers = "0";
        for ($j = 1; $j < 11; $j++) {
            $numbers .= ", " . $this->f_d_graph($p * $j / 10);
        }
        return $numbers;
    }

    function graph()
    {
        $periods = "";
        $i = "";
        $fi = $_POST["start_period_id"];
        $ei = $_POST["finish_period_id"];
        $first = 1;
        $sql = "select * from industry.graph where firm_id ={$this->uri->segment(3)} and period_id between $fi and $ei";

        $this->db->where("id", $this->uri->segment(3));
        $data['firm_info'] = $this->db->get("industry.firm")->row();
        $res = $this->db->query($sql);
        $max = 0;
        foreach ($res->result() as $p) {
            if ($max < $p->itogo_kvt) {
                $max = $p->itogo_kvt;
            }
            if ($first == 1) {
                $periods .= "'" . $this->shortdate($p->period_begin_date) . "'";
                $i .= $this->f_d_graph($p->itogo_kvt);
                $first = 2;
            } else {
                $periods .= ", '" . $this->shortdate($p->period_begin_date) . "' ";
                $i .= ', ' . $this->f_d_graph($p->itogo_kvt);
            }
        }


        $data['periods'] = $periods;
        $data['itogo_kvt'] = $i;
        $data['numbers'] = $this->closer_number($max);
        $this->load->view('reports/graph', $data);
    }

    function pre_nadbavka_info()
    {
        $this->left();
        $this->db->order_by('id');
        $data['periods'] = $this->db->get("industry.period");
        $data['users'] = $this->db->get("industry.user");
        $this->load->view("pre_nadbavka_info", $data);
        $this->load->view("right");
    }

    function nadbavka_info()
    {
        $this->db->where("period_id", $_POST['period_id']);
        if ($_POST['user_id'] != -1) {
            $this->db->where("user_id", $_POST['user_id']);
        }
        $data['firms'] = $this->db->get("industry.nadbavka_info");
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        if ($_POST['user_id'] != -1) {
            $this->db->where("id", $_POST['user_id']);
            $data['user'] = $this->db->get("industry.user")->row()->name;
        } else {
            $data['user'] = '-1';
        }
        $this->load->view("reports/nadbavka_info", $data);
    }

    function pre_ne_potrebil()
    {
        $this->left();
        $this->db->order_by('id');
        $data['periods'] = $this->db->get("industry.period");
        $data['users'] = $this->db->get("industry.user");
        $this->load->view("pre_ne_potrebil", $data);
        $this->load->view("right");
    }

    function ne_potrebil()
    {
        $this->db->where("period_id", $_POST['period_id']);
        if ($_POST['user_id'] != -1) {
            $this->db->where("user_id", $_POST['user_id']);
        }
        $data['firms'] = $this->db->get("industry.ne_potrebil");
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        if ($_POST['user_id'] != -1) {
            $this->db->where("id", $_POST['user_id']);
            $data['user'] = $this->db->get("industry.user")->row()->name;
        } else {
            $data['user'] = '-1';
        }
        $this->load->view("reports/ne_potrebil", $data);
    }

    function docs()
    {
        $this->db->order_by("name");
        $data['query'] = $this->db->get("industry.docs");
        $this->left();
        $this->load->view("sprav/docs_view", $data);
        $this->load->view("right");

    }

    function docs_edit()
    {
        $this->db->where('id', $this->uri->segment(3));
        $data['docs_id'] = $this->uri->segment(3);
        $data['doc'] = $this->db->get('industry.docs')->row();
        $this->left();
        $this->load->view('sprav/docs_edit', $data);
        $this->load->view('right');
    }

    function edition_docs()
    {
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('industry.docs', $_POST);
        redirect('billing/docs');
    }

    function adding_docs()
    {
        $this->db->insert('industry.docs', $_POST);
        redirect("billing/docs");
    }

    function docs_register()
    {
        $data['docs'] = $this->db->get('industry.docs');
        $data['firm_id'] = $this->uri->segment(3);
        $this->db->where('firm_id', $this->uri->segment(3));
        $data['info'] = $this->db->get('industry.docs_reg_sprav');
        $this->db->where('firm_id', $this->uri->segment(3));
        $this->db->where('deleted_point', "false");
        $data['points'] = $this->db->get('industry.billing_point');
        $this->left();
        $this->load->view("docs_register", $data);
        $this->load->view("right");
    }

    function docs_register_form()
    {
        $firm_id = $_POST['firm_id'];
        unset($_POST['firm_id']);
        $point_id = $_POST['point_id'];
        unset($_POST['point_id']);
        while ((isset($_POST)) && !($_POST == null)) {
            $doc = current($_POST);
            $pr = $this->db->query("select * from industry.docs_register where firm_id =" . $firm_id . " and doc_id = " . $doc . " and point_id = " . $point_id)->row();
            if ((isset($pr)) && !($pr == null)) {
                $this->db->query("update industry.docs_register set data_reg = " . ((isset($_POST['data' . $doc . '_v'])) ? "'" . $_POST['data' . $doc . '_v'] . "'" : "null") . ", register = " . ((isset($_POST['doc' . $doc . '_per'])) ? "true" : "false") . " where firm_id =" . $firm_id . " and doc_id = " . $doc . " and point_id = " . $point_id);
            } else {
                $this->db->query("insert into industry.docs_register (firm_id, doc_id, data_reg, register, point_id) values(" . $firm_id . ", " . $doc . "," . ((isset($_POST['data' . $doc . '_v'])) ? "'" . $_POST['data' . $doc . '_v'] . "'" : "null") . ", " . ((isset($_POST['doc' . $doc . '_per'])) ? "true" : "false") . ", " . $point_id . ")");
            }
            unset($_POST['doc_' . $doc]);
            if (isset($_POST['data' . $doc . '_chek'])) {
                unset($_POST['data' . $doc . '_chek']);
            }
            if (isset($_POST['data' . $doc . '_v'])) {
                unset($_POST['data' . $doc . '_v']);
            }
            if (isset($_POST['doc' . $doc . '_per'])) {
                unset($_POST['doc' . $doc . '_per']);
            }
            //unset($_POST);
        }
        redirect("billing/docs_register/" . $firm_id);
    }

    function pre_ip_obshiy()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_ip_obshiy', $data);
        $this->load->view('right');
    }

    function ip_obshiy()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['ip'] = $this->db->get("industry.ip_obshiy_tar");
        $this->load->view("reports/ip_obshiy", $data);
    }

    function pre_analiz_mnogourovneviy_spisok()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_mnogourovneviy_spisok', $data);
        $this->load->view('right');
    }

    function analiz_mnogourovneviy_spisok()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_mnogourovneviy_spisok");
        $this->load->view("reports/analiz_mnogourovneviy_spisok", $data);
    }

    function pre_analiz_mnogourovneviy()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_mnogourovneviy', $data);
        $this->load->view('right');
    }

    function analiz_mnogourovneviy()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_mnogourovneviy");
        $this->load->view("reports/analiz_mnogourovneviy", $data);
    }

    function pre_analiz_diff_tarif()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_diff_tarif', $data);
        $this->load->view('right');
    }

    function analiz_diff_tarif()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_diff_tarif");
        $this->load->view("reports/analiz_diff_tarif", $data);
    }

    function pre_analiz_diff_tarif_spisok()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_diff_tarif_spisok', $data);
        $this->load->view('right');
    }


    function analiz_diff_tarif_spisok()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_diff_tarif_spisok");
        $this->load->view("reports/analiz_diff_tarif_spisok", $data);
    }

    function pre_analiz_100750()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_analiz_100750', $data);
        $this->load->view('right');
    }


    function analiz_100750()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get("industry.period")->row();
        $data['org'] = $this->db->get("industry.org_info")->row();
        $this->db->where("period_id", $_POST['period_id']);
        $data['diff'] = $this->db->get("industry.analiz_diff_tarif_spisok");
        $this->load->view("reports/analiz_100750", $data);
    }

    function pre_akt_snyatiya_pokazaniy()
    {
        $this->db->order_by('id');
        $data['period'] = $this->db->get('industry.period');
        $this->left();
        $this->load->view("pre_akt_snyatiya_pokazaniy", $data);
        $this->load->view("right");
    }

    function akt_snyatiya_pokazaniy()
    {
        $this->db->where("id", $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period');
        $this->db->where("period_id", $_POST['period_id']);
        $this->db->order_by('dogovor');
        $data['vedomost'] = $this->db->get('industry.akt_snyatiya_pokazaniy');
        $this->load->view("reports/akt_snyatiya_pokazaniy", $data);
    }


    function pre_svod()
    {
        $this->db->order_by('id');
        $data['period'] = $this->db->get('industry.period');
        $this->left();
        $this->load->view("pre_svod", $data);
        $this->load->view("right");
    }

    function svod()
    {
        $where = "";
        if ($_POST["type"] == 2) $where = " where firm.is_too=true and (firm.subgroup_id<6 or firm.subgroup_id>9) ";
        if ($_POST["type"] == 3) $where = " where firm.is_ip=true ";
        if ($_POST["type"] == 4) $where = " where firm.subgroup_id>=6 and firm.subgroup_id<=9 ";

        $sql = "";
        $params = 'firm.name as firm_name ';
        $php = 'echo "<tr><td>".$j++."</td><td>".$r->dogovor."</td><td align=left>".$r->firm_name."</td><td>".$r->subgroup."</td>";';
        $period_head = "<tr><td>№</td><td>Дог.</td><td>Предприятие</td><td>Подгруппа</td>";
        for ($j = $_POST['start_period_id']; $j <= $_POST['finish_period_id']; $j++) {
            $tablename = "\"" . $j . "\"";
            $columnname = "\"col_" . $j . "\"";
            $sql .= " left join industry.\"7-re\" as $tablename 
				on $tablename.period_id=$j and $tablename.firm_id=firm.id ";
            $params .= ", coalesce($tablename.itogo_kvt,0) as $columnname ";
            $php .= 'echo "<td align=right>".dottozpt($r->col_' . $j . ')."</td>";';
            $this->db->where('id', $j);
            $period_head .= "<td>" . $this->db->get('industry.period')->row()->name . "</td>";
        }
        $sql = "select $params,firm_subgroup.name as subgroup,firm.dogovor from industry.firm left join industry.firm_subgroup on firm.subgroup_id=firm_subgroup.id " . $sql . " $where order by firm.dogovor";
        $data['res'] = $this->db->query($sql);
        $data['php'] = $php;
        $data['period_head'] = $period_head;
        $this->load->view("reports/svod", $data);
    }

    function pre_svod_new()
    {
        $this->db->order_by('id');
        $data['period'] = $this->db->get('industry.period');
        $data['groups'] = $this->db->get('industry.firm_subgroup');
        $this->left();
        $this->load->view("pre_svod_new", $data);
        $this->load->view("right");
    }

    function svod_new()
    {
        $j = $_POST['start_period_id'];
        $i = $_POST['finish_period_id'];

        $sql1 = "select period.id,period.begin_date,period.name from industry.period where period.id >= $j and period.id <= $i ORDER BY period.id ";
        $sql2 = "select * from industry.firm_subgroup";
        $sql3 = "select count(firm.id) as firm_count from industry.firm";

        $data['periodi'] = $this->db->query($sql1);
        $data['firm_gruops'] = $this->db->query($sql2);
        $data['firm_count'] = $this->db->query($sql3);
        $data['start_period_id'] = $j;
        $data['finish_period_id'] = $i;

        $this->load->view("reports/svod_new", $data);
    }

    function new_svod_kvt_po_godam_ajax()
    {
        if (isset($_POST['_offset'])) {
            $_offset = $_POST['_offset'];
        } else {
            $_offset = 0;
        }

        if (isset($_POST['_group'])) {
            $_group = $_POST['_group'];
        } else {
            $_group = 17;
        }

        if (isset($_POST['_start_period_id'])) {
            $_start_period_id = $_POST['_start_period_id'];
        } else {
            $_start_period_id = 12;
        }

        if (isset($_POST['_finish_period_id'])) {
            $_finish_period_id = $_POST['_finish_period_id'];
        } else {
            $_finish_period_id = 85;
        }

        //$sql0 = "select period.id from industry.period ORDER BY period.id ";

        //$periodi = $this->db->query($sql0);

        $periodi_svod = array();

        $period_id = $_start_period_id;

        for ($period_id = $_start_period_id; $period_id <= $_finish_period_id; $period_id++) {
            $periodi_svod[$period_id] = 0;
        }

        $sql1 = "select firm.id,firm.name,firm.dogovor from industry.firm where firm.subgroup_id = " . $_group . " ORDER BY firm.dogovor LIMIT 1 OFFSET " . $_offset;

        $result = null;
        $res1 = $this->db->query($sql1);
        $result = $res1->result();

        if ($result) {
            //echo '<pre>';
            //print_r($result[0]->id);
            //echo '</pre>';

            $sql2 = "SELECT sum(my_round(_vedomost.itogo_kvt)::numeric(24,2)) AS itogo_kvt, period.id AS period_id
				FROM industry._vedomost
				LEFT JOIN industry.period ON _vedomost.new_data >= period.begin_date AND _vedomost.new_data <= period.end_date
				where _vedomost.firm_id = " . $result[0]->id . "
				group by period_id";

            $result_kvt = null;
            $res2 = $this->db->query($sql2);
            $result_kvt = $res2->result();

            //echo '------<pre>';
            //print_r($result_kvt);
            //echo '</pre>';

            $svod = array();

            foreach ($result_kvt as $kvt) {
                if (!isset($periodi_svod[$kvt->period_id])) continue;
                $periodi_svod[$kvt->period_id] = $kvt->itogo_kvt;
            }


            $content = '<tr>
						<td><center>' . ($_offset + 1) . '</center></td>
						<td><center>' . $result[0]->dogovor . '</center></td>
						<td>' . $result[0]->name . '</td>';

            $first = true;
            $tmp = '';

            foreach ($periodi_svod as $period_id => $kvt) {
                $tmp .= '<td><center>' . $kvt . '</center></td>';
            }

            echo $content . $tmp;

            //echo '<pre>';
            //print_r($svod);
            //echo '</pre>';

        } else
            echo 0;

        //$this->load->view("reports/svod",$data);
    }

    function svod_kvt_po_godam()
    {
        $sql1 = "select period.id,period.begin_date,period.name from industry.period ORDER BY period.id ";
        $sql2 = "select count(firm.id) as firm_count from industry.firm";

        $data['periodi'] = $this->db->query($sql1);
        $data['firm_count'] = $this->db->query($sql2);

        $this->load->view("reports/svod_kvt_po_godam", $data);
    }

    function svod_kvt_po_godam_ajax()
    {
        if (isset($_POST['_offset'])) {
            $_offset = $_POST['_offset'];
        } else {
            $_offset = 0;
        }

        $sql0 = "select period.id from industry.period ORDER BY period.id ";

        $periodi = $this->db->query($sql0);

        $periodi_svod = array();

        foreach ($periodi->result() as $period) {
            $periodi_svod[$period->id] = 0;
        }

        $sql1 = "select firm.id,firm.name,firm.dogovor from industry.firm ORDER BY firm.dogovor LIMIT 1 OFFSET " . $_offset;

        $result = null;
        $res1 = $this->db->query($sql1);
        $result = $res1->result();

        if ($result) {
            //echo '<pre>';
            //print_r($result[0]->id);
            //echo '</pre>';

            $sql2 = "SELECT t2.name as tp_name, _vedomost.bill_id, _vedomost.bill_name, sum(my_round(_vedomost.itogo_kvt)::numeric(24,2)) AS itogo_kvt, period.id AS period_id
				FROM industry._vedomost
				LEFT JOIN industry.period ON _vedomost.new_data >= period.begin_date AND _vedomost.new_data <= period.end_date
				LEFT JOIN industry.billing_point p2 ON p2.id = _vedomost.bill_id
				LEFT JOIN industry.tp t2 ON t2.id = p2.tp_id
				where _vedomost.firm_id = " . $result[0]->id . "
				group by tp_name, _vedomost.bill_id, _vedomost.bill_name, period_id";

            $result_tu = null;
            $res2 = $this->db->query($sql2);
            $result_tu = $res2->result();

            //echo '<pre>';
            //print_r($result_tu);
            //echo '</pre>';

            $svod = array();

            foreach ($result_tu as $tu) {
                $svod[$tu->bill_id]['tu_name'] = $tu->bill_name;
                $svod[$tu->bill_id]['tu_tp'] = $tu->tp_name;
                if (!isset($svod[$tu->bill_id]['periodi_svod'])) {
                    $svod[$tu->bill_id]['periodi_svod'] = $periodi_svod;
                }

                $svod[$tu->bill_id]['periodi_svod'][$tu->period_id] = $tu->itogo_kvt;
            }

            $tu_count = count($svod);
            if ($tu_count == 0) $tu_count = 1;

            $content = '<tr>
						<td rowspan="' . $tu_count . '"><center>' . ($_offset + 1) . '</center></td>
						<td rowspan="' . $tu_count . '"><center>' . $result[0]->dogovor . '</center></td>
						<td rowspan="' . $tu_count . '">' . $result[0]->name . '</td>';

            $first = true;
            $tmp = '';

            foreach ($svod as $tu_id => $tu) {
                if (!$first) {
                    $tmp .= '<tr>';
                }

                $tmp .= '<td>' . $tu['tu_tp'] . '</td>
						<td>' . $tu['tu_name'] . '</td>';

                foreach ($tu['periodi_svod'] as $period => $kvt) {
                    $tmp .= '<td><center>' . $kvt . '</center></td>';
                }

                $tmp .= '</tr>';
                $first = false;
            }

            echo $content . $tmp;

            //echo '<pre>';
            //print_r($svod);
            //echo '</pre>';

        } else
            echo 0;

        //$this->load->view("reports/svod",$data);
    }


    function pre_svod_750()
    {
        $this->db->order_by('id');
        $data['period'] = $this->db->get('industry.period');
        $this->left();
        $this->load->view("pre_svod_750", $data);
        $this->load->view("right");
    }

    function svod_750()
    {
        $where = "";
        if ($_POST["type"] == 2) $where = " where firm.is_too=true and (firm.subgroup_id<6 or firm.subgroup_id>9) ";
        if ($_POST["type"] == 3) $where = " where firm.is_ip=true ";
        if ($_POST["type"] == 4) $where = " where firm.subgroup_id>=6 and firm.subgroup_id<=9 ";

        $sql = "";
        $params = 'firm.name as firm_name ';
        $php = 'echo "<tr><td>".$j++."</td><td>".$r->dogovor."</td><td align=left>".$r->firm_name."</td><td>".$r->subgroup."</td>";';
        $period_head = "<tr><td>№</td><td>Дог.</td><td>Предприятие</td><td>Подгруппа</td>";
        for ($j = $_POST['start_period_id']; $j <= $_POST['finish_period_id']; $j++) {
            $tablename = "\"" . $j . "\"";
            $columnname = "\"col_" . $j . "\"";
            $sql .= " left join industry.\"7-re\" as $tablename 
				on $tablename.period_id=$j and $tablename.firm_id=firm.id ";
            $params .= ", coalesce($tablename.itogo_kvt,0) as $columnname ";
            $php .= 'echo "<td><table border=0px><tr><td align=right>".dottozpt($r->col_' . $j . ')."</td></tr>";';

            $php .= 'echo "<tr><td align=right>".dottozpt(($r->col_' . $j . ')/(24000))."</td></tr></table></td>";';
            $this->db->where('id', $j);
            $period_head .= "<td colspan=2>" . $this->db->get('industry.period')->row()->name . "</td>";
        }
        $sql = "select $params,firm_subgroup.name as subgroup,firm.dogovor from industry.firm left join industry.firm_subgroup on firm.subgroup_id=firm_subgroup.id " . $sql . " $where order by firm.dogovor";
        $data['res'] = $this->db->query($sql);
        $data['php'] = $php;
        $data['period_head'] = $period_head;
        $this->load->view("reports/svod_750", $data);
    }

    function add_akt_with_tariff()
    {
        $this->left();
        $data['firm_id'] = $this->uri->segment(3);
        $sql = "select 
akt_with_tariff.kvt,
akt_with_tariff.data,
akt_with_tariff.tariff_value,
tariff.name,
firm.id as firm_id,
billing_point.name as point_name,
akt_with_tariff.id as id
 from industry.akt_with_tariff  
left join industry.counter on akt_with_tariff.counter_id=counter.id
left join industry.tariff on tariff.id=akt_with_tariff.tariff_id
left join industry.billing_point on billing_point.id=counter.point_id
left join industry.firm on firm.id=billing_point.firm_id
left join industry.sprav on sprav.name='current_period'
left join industry.period on sprav.value::integer=period.id

where akt_with_tariff.data between period.begin_date and period.end_date and firm.id={$this->uri->segment(3)}";
        $data['akts'] = $this->db->query($sql);
        $sql = "select * from industry.billing_point where firm_id=" . $this->uri->segment(3);
        $data['points'] = $this->db->query($sql);

        $sql = "select tariff.id as tariff_id,
tariff.name ||' '||tariff_period.data||' '||value as name,
value
 from industry.tariff 
left join industry.tariff_period on tariff.id=tariff_period.tariff_id
left join industry.tariff_value on tariff_value.tariff_period_id=tariff_period.id order by tariff.name,tariff_period.data";
        $data['tariffs'] = $this->db->query($sql);
        $sql = "select counter.gos_nomer,counter.id  as counter_id,billing_point.name from industry.firm
left join industry.billing_point on firm.id=billing_point.firm_id
left join industry.counter on counter.point_id=billing_point.id
where firm_id={$this->uri->segment(3)} and data_finish is null";
        $data['counters'] = $this->db->query($sql);
        $this->load->view("add_akt_with_tariff", $data);
        $this->load->view("right");
    }

    function adding_akt_with_tariff()
    {
        $ex = explode("|", $_POST['tariff']);
        $array = array('kvt' => $_POST['kvt'], 'data' => $_POST['data'], 'counter_id' => $_POST['counter_id'], 'tariff_id' => $ex[0], 'tariff_value' => $ex[1]);
        $this->db->insert('industry.akt_with_tariff', $array);
        redirect("billing/add_akt_with_tariff/" . $this->uri->segment(3));
    }

    function delete_akt_with_tariff()
    {
        $sql = "delete from industry.akt_with_tariff where id=" . $this->uri->segment(4);
        $this->db->query($sql);
        redirect("billing/add_akt_with_tariff/" . $this->uri->segment(3));
    }

    function pre_holostoy_hod()
    {
        $data['period'] = $this->db->get("industry.period");
        $this->left();
        $this->load->view('pre_holostoy_hod', $data);
        $this->load->view('right');
    }

    function holostoy_hod()
    {
        $sql = "SELECT * FROM industry.holostoy_hod where period_id=" . $_POST['period_id'];
        $data['hol'] = $this->db->query($sql);
        $this->load->view("reports/holostoy_hod", $data);
    }

    function dispetcherskaya()
    {
        $sql = "SELECT * FROM industry.dispetcherskaya";
        $data['disp'] = $this->db->query($sql);
        $this->load->view("dispetcherskaya", $data);
    }

    #13-06-2018
    public function pre_manual_sf()
    {
        $data['firms'] = $this->db->query("select *from industry.firm order by firm.dogovor")->result();
        $data['period'] = $this->db->query("select * from industry.period order by period.id desc")->result();
        $this->left();
        $this->load->view("pre_manual_sf", $data);
        $this->load->view("right");
    }

    public function manual_sf()
    {
        $this->db->where('firm_id', $_POST['firm_id']);
        $this->db->where('period_id', $_POST['period_id']);
        $data['schetfactura_date'] = $this->db->get("industry.schetfactura_date")->row();

        $this->db->where('id', $_POST['period_id']);
        $data['period'] = $this->db->get('industry.period')->row();

        $this->db->where('id', $_POST['firm_id']);
        $data['firm'] = $this->db->get('industry.firm')->row();

        $this->db->where('id', $data['firm']->bank_id);
        $data['bank'] = $this->db->get('industry.bank')->row();

        $data['org'] = $this->db->get("industry.org_info")->row();

        $data['lines'] = array();
        $i = 0;
        if ((isset($_POST['kvt1'], $_POST['tarif1'])) and (!empty($_POST['kvt1'])) and (!empty($_POST['tarif1']))) {
            $data['lines'][$i]['kvt'] = $_POST['kvt1'];
            $data['lines'][$i]['tariff'] = $_POST['tarif1'];
            $i++;
        };
        if ((isset($_POST['kvt2'], $_POST['tarif2'])) and (!empty($_POST['kvt2'])) and (!empty($_POST['tarif2']))) {
            $data['lines'][$i]['kvt'] = $_POST['kvt2'];
            $data['lines'][$i]['tariff'] = $_POST['tarif2'];
            $i++;
        };
        if ((isset($_POST['kvt3'], $_POST['tarif3'])) and (!empty($_POST['kvt3'])) and (!empty($_POST['tarif3']))) {
            $data['lines'][$i]['kvt'] = $_POST['kvt3'];
            $data['lines'][$i]['tariff'] = $_POST['tarif3'];
        };

        $data['schet2'] = $_POST['schet2'];
        $data['data_schet'] = $_POST['data_schet'];
        $data['dog2'] = $_POST['dog2'];
        $data['edit1'] = $_POST['edit1'];
        $data['edit2'] = $_POST['edit2'];
        $data['edit3'] = $_POST['edit3'];
        $data['edit4'] = $_POST['edit4'];
        $data['edit5'] = $_POST['edit5'];
        $data['edit6'] = $_POST['edit6'];
        $data['edit7'] = $_POST['edit7'];
        $data['edit8'] = $_POST['edit8'];

        $this->load->library("pdf/pdf");
        $this->pdf->SetSubject('TCPDF Tutorial');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $this->pdf->SetAutoPageBreak(TRUE);
        $this->pdf->SetFont('dejavusans', '', 9);
        $this->pdf->AddPage('P');
        $string = $this->load->view('manual_sf', $data, TRUE);
        $this->pdf->writeHTML($string);
        $this->pdf->Output('example_001.pdf', 'I');

    }

    /*FINE*/
    /*начальная страница по пене*/
    public function fine_info()
    {
        if (isset($_POST['add_ref_rate'])) {
            $rate_data = $_POST['rate_data'];
            $rate_value = (float)$_POST['rate_value'];
            if ((strlen($rate_data) == 10) and ($rate_value > 0) and ($rate_value < 100)) {
                if ($this->db->insert('industry.ref_rate', array('data' => $rate_data, 'value' => $rate_value))) {
                    echo "Значение добавлено!";
                }
            } else {
                echo "Неверные значения!";
            }
        }

        if (isset($_POST['add_ref_coef'])) {
            $coef_data = $_POST['coef_data'];
            $coef_value = (float)$_POST['coef_value'];
            if ((strlen($coef_data) == 10) and ($coef_value > 0) and ($coef_value < 100)) {
                if ($this->db->insert('industry.ref_coef', array('data' => $coef_data, 'value' => $coef_value))) {
                    echo "Значение добавлено!";
                }
            } else {
                echo "Неверные значения!";
            }
        }

        $data['periods'] = $this->db->query(
            "select distinct(period.id) as id,
                period.name
             from industry.fine_saldo
             join industry.period on period.id = fine_saldo.period_id and fine_saldo.period_id-1< industry.current_period_id()
             order by period.id desc"
        )->result();

        $this->db->order_by('data');
        $data['ref_rates'] = $this->db->get('industry.ref_rate')->result();

        $this->db->order_by('data');
        $data['ref_coefs'] = $this->db->get('industry.ref_coef')->result();

        $this->left();
        $this->load->view('fine/fine_info', $data);
        $this->load->view('right');
    }

    public function pre_fine_akt_sverki()
    {
        $data['firm_id'] = (int)$this->uri->segment(3);
        $data['period'] = $this->db->query(
            "select * from industry.period
            where period.id <= industry.current_period_id()
            and extract(year from period.end_date) = industry.get_current_year()
            order by period.id desc"
        )->result();
        $this->left();
        $this->load->view('fine/pre_fine_akt_sverki', $data);
        $this->load->view('right');
    }

    public function fine_akt_sverki()
    {
        if ($_POST['period_id_start'] > $_POST['period_id_end']) {
            exit("Стартовая дата превышает конечную!");
        }

        $firm_id = $this->uri->segment(3);

        $this->db->where('id', $firm_id);
        $data['firm'] = $this->db->get("industry.firm")->row();

        $this->db->where('id', $_POST['period_id_end']);
        $data['period_end'] = $this->db->get("industry.period")->row();

        $this->db->where('start_period_id', $_POST['period_id_start']);
        $this->db->where('end_period_id', $_POST['period_id_end']);
        $this->db->where('firm_id', $firm_id);
        $isset_akt = $this->db->get("industry.fine_akt_sverki")->num_rows();
        $data['data_akta'] = date('d.m.Y');

        $ins_arr = array(
            'start_period_id' => $_POST['period_id_start'],
            'end_period_id' => $_POST['period_id_end'],
            'firm_id' => $firm_id,
            'data' => date('Y-m-d')
        );

        $this->db->where('start_period_id', $_POST['period_id_start']);
        $this->db->where('end_period_id', $_POST['period_id_end']);
        $this->db->where('firm_id', $firm_id);
        $isset_akt = $this->db->get('industry.fine_akt_sverki')->num_rows();

        if ($isset_akt == 0) {
            $this->db->insert('industry.fine_akt_sverki', $ins_arr);
            $data['akt_number'] = $this->db->insert_id();
        } else {
            $this->db->where('start_period_id', $_POST['period_id_start']);
            $this->db->where('end_period_id', $_POST['period_id_end']);
            $this->db->where('firm_id', $firm_id);
            $data['akt_number'] = $this->db->get("industry.fine_akt_sverki")->row()->id;

            $this->db->where('start_period_id', $_POST['period_id_start']);
            $this->db->where('end_period_id', $_POST['period_id_end']);
            $this->db->where('firm_id', $firm_id);
            $this->db->update('industry.fine_akt_sverki', array('data' => $data['data_akta']));
        }


        $data['org'] = $this->db->get("industry.org_info")->row();

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id >=', $_POST['period_id_start']);
        $this->db->where('period_id <=', $_POST['period_id_end']);
        $data['akt'] = $this->db->get("industry.fine_akt_sverki_source")->result();
        $data['firm_id'] = $firm_id;


        $this->load->view('fine/fine_akt_sverki', $data);
    }

    public function current_year_calendar()
    {
        $data['calendar'] = $this->db->get('industry.current_year_calendar')->result();
        $this->left();
        $this->load->view("fine/current_year_calendar", $data);
        $this->load->view('right');
    }

    public function change_calendar_day()
    {
        $day_id = $this->uri->segment(3);
        $this->db->where('day_id', $day_id);
        $is_off = $this->db->get('industry.fine_calendar')->row()->is_off;
        $is_off = $is_off == 0 ? 1 : 0;

        $this->db->where('day_id', $day_id);
        $this->db->update('industry.fine_calendar', array('is_off' => $is_off));

        redirect("billing/current_year_calendar");
    }

    /*подробное начисление пени для организации*/
    public function fine()
    {
        $url_firm_id = $this->uri->segment(3);
        $period_id = $this->db->query('select * from industry.current_period_id()')->row()->current_period_id;
        $data['fine_periods'] = $this->db->query(
            "select period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.firm_id = $url_firm_id and fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['fine_firm_oplata_periods'] = $this->db->query(
            "select distinct(period.id),
            period.name
            from industry.period
            join industry.fine_firm_oplata on fine_firm_oplata.period_id = period.id and fine_firm_oplata.firm_id = $url_firm_id
            group by period.name, period.id"
        )->result();

        $this->db->where('firm_id', $url_firm_id);
        $this->db->where('period_id', $period_id);
        $data['fine_saldo'] = $this->db->get("industry.fine_saldo")->row();

        $period_id = $this->db->query("select * from industry.current_period_id()")->row()->current_period_id;
        $this->db->where('period_id', $period_id);
        $this->db->where('firm_id', (int)$url_firm_id);
        $data['fine_firm'] = $this->db->get('industry.fine_firm')->row();
        $data['current_period'] = $period_id;
        $this->db->where('id', (int)$url_firm_id);
        $data['firm_info'] = $this->db->get('industry.firm')->row();

        $this->left();
        $this->load->view("fine/pre_firm_fine", $data);
        $this->load->view("right");
    }

    public function fine_all_firm_options()
    {
        $data['options'] = $this->db->query(
            "select
            firm_group.\"name\" as group_name,
            firm_subgroup.\"name\" as subgroup_name,
            firm.dogovor,
            firm.name as firm_name,
            fine_firm.*
            from industry.firm
            join industry.firm_subgroup on firm_subgroup.id = firm.subgroup_id
            join industry.firm_group on firm_group.id = firm_subgroup.group_id
            join industry.fine_firm on fine_firm.firm_id = firm.id and fine_firm.period_id = industry.current_period_id()
            order by 
            firm_group.\"name\",
            firm_subgroup.\"name\",
            firm.dogovor"
        )->result();

        $this->left();
        $this->load->view('fine/fine_all_firm_options', $data);
        $this->load->view("right");
    }

    public function change_fine_parameter()
    {
        if (isset($_POST['change_is_fine'])) {
            unset($_POST['change_is_fine']);

            if (isset($_POST['is_fine'])) {
                $_POST['is_fine'] = 'true';
            } else {
                $_POST['is_fine'] = 'false';
            }

            $period_id = $_POST['period_id'];
            $firm_id = $_POST['firm_id'];
            $this->db->where('period_id', $period_id);
            $this->db->where('firm_id', $firm_id);
            $is_fine_added = $this->db->get('industry.fine_firm')->num_rows();

            if ($is_fine_added == 0) {

            } else {
                $this->db->where('period_id', $period_id);
                $this->db->where('firm_id', $firm_id);
                $this->db->update('industry.fine_firm', array(
                    'is_fine' => $_POST['is_fine'],
                    'border_day' => $_POST['border_day'],
                    'is_calendar' => $_POST['is_calendar']
                ));
            }
            redirect('billing/fine/' . $firm_id);
        }
    }

    /*ведомость пени по фирме*/
    public function fine_firm()
    {
        $firm_id = (int)$this->uri->segment(3);

        $url_period = $this->uri->segment(4);
        if ($url_period) {
            $period_id = (int)$url_period;
        } else {
            $period_id = (int)$_POST['period_id'];
        }
        $this->db->where('id', $period_id);
        $data['period_info'] = $this->db->get('industry.period')->row();

        $this->db->where('id', $period_id - 1);
        $data['prev_period_info'] = $this->db->get('industry.period')->row();

        $data['current_ref_rate'] = $this->get_current_ref_rate($period_id);
        $data['other_ref_rate'] = $this->get_other_ref_rate($period_id);

        $data['current_ref_coef'] = $this->get_current_ref_coef($period_id);
        $data['other_ref_coef'] = $this->get_other_ref_coef($period_id);


        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $fine_firm_info = $this->db->get("industry.fine_firm")->row();
        $data['border_day'] = $fine_firm_info->border_day;

        if ($fine_firm_info->is_calendar == 0) {
            $data['border_day'] = $this->db->query("select * from industry.get_working_day({$period_id},{$data['border_day']})")->row()->get_working_day;
        }

        $pre_month_days = $this->db->query(
            "SELECT 
               fine_calendar.\"day\" as calendar_day,
               fine_calendar.is_off,
               weekday.*
              FROM industry.fine_calendar
              JOIN industry.period 
                ON EXTRACT(MONTH FROM period.begin_date) = fine_calendar.\"month\"
                AND EXTRACT(YEAR FROM period.begin_date) = fine_calendar.\"year\"
              JOIN industry.weekday ON fine_calendar.day_of_week = weekday.day_number
              WHERE period.id = $period_id
              ORDER BY day_id"
        )->result();

        $data['month_days'] = array();
        foreach ($pre_month_days as $pre_month_day) {
            $data['month_days'][$pre_month_day->calendar_day]['is_off'] = $pre_month_day->is_off;
            $data['month_days'][$pre_month_day->calendar_day]['day_shortname'] = $pre_month_day->day_shortname;
        }

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $firm_veds = $this->db->get("industry.fine_source_data")->row();

        $data['fine_saldo'] = $this->db->query(
            "select * from industry.fine_saldo where period_id = $period_id and firm_id = $firm_id"
        )->row();

        if (!empty($firm_veds)) {
            $data['firm_veds'] = $firm_veds;
            $firm_veds->oplata = $this->get_oplata_fine($firm_id, $period_id);
            $this->load->view("fine/fine_firm", $data);
        } else {
            var_dump($firm_veds);
            echo "Ошибка. Обратитесь к программисту";
            session_start();
        }
    }

    /* ведомость пени по всем незакрытым должникам*/
    public function fine_all_firms()
    {
        $period_id = (int)$_POST['period_id'];
        $this->db->where('id', $period_id);
        $data['period'] = $this->db->get('industry.period')->row();
        $data['fine_arr'] = $this->fine_calc_firms($period_id);
        $this->load->view("fine/fine_all_firms", $data);
    }

    /*оплаты пени по организации*/
    public function fine_firm_oplata()
    {
        $firm_id = (int)$this->uri->segment(3);

        $this->db->where('id', $firm_id);
        $data['firm_info'] = $this->db->get("industry.firm")->row();

        $url_period = $this->uri->segment(4);
        if ($url_period) {
            $period_id = (int)$url_period;
        } else {
            $period_id = (int)$_POST['period_id'];
        }

        $this->db->where('firm_id', $firm_id);
        $this->db->where('period_id', $period_id);
        $data['fine_firm_oplata'] = $this->db->get('industry.fine_firm_oplata')->result();

        $this->db->where('id', $period_id);
        $data['period_info'] = $this->db->get("industry.period")->row();

        $this->left();
        $this->load->view("fine/fine_firm_oplata", $data);
        $this->load->view("right");
    }

    /*сальдо пени*/
    public function fine_saldo_origin()
    {
        $url_firm_id = $this->uri->segment(3);
        if ($url_firm_id) {
            $firm_id = (int)$url_firm_id;
        } else {
            $firm_id = (int)$_POST['period_id'];
        }

        $this->db->where('id', $firm_id);
        $data['firm_info'] = $this->db->get("industry.firm")->row();

        $this->db->where('firm_id', $firm_id);
        $data['fine_saldo_origin'] = $this->db->get('industry.fine_saldo_origin')->result();

        $this->left();
        $this->load->view("fine/fine_saldo_origin", $data);
        $this->load->view("right");
    }

    /*считает пеню для всех организаций*/
    private function fine_calc_firms($period_id)
    {
        $this->db->where('period_id', $period_id);
        $this->db->order_by('fine_value', 'DESC');
        $firms = $this->db->get("industry.fine_total")->result();

        $fine_arr = array();
        foreach ($firms as $firm) {
            if ($firm->fine_value > 0) {
                $fine_arr[$firm->firm_id]['name'] = $firm->name;
                $fine_arr[$firm->firm_id]['dogovor'] = $firm->dogovor;
                $fine_arr[$firm->firm_id]['saldo'] = round($firm->saldo_value, 2);
                $fine_arr[$firm->firm_id]['nach'] = round($firm->nach, 2);
                $fine_arr[$firm->firm_id]['fs_start'] = $firm->fs_start_value;
                $fine_arr[$firm->firm_id]['fs_end'] = $firm->fs_end_value;
                $fine_arr[$firm->firm_id]['fo'] = $firm->fo_value;
                $fine_arr[$firm->firm_id]['fine'] = $firm->fine_value;
            }

        }
        return $fine_arr;
    }

    /*получение действующей ставки рефинансирования на начало периода*/
    private function get_current_ref_rate($period_id)
    {
        return $this->db->query(
            "select ref_rate.value from industry.ref_rate, industry.period 
             where ref_rate.data<=period.begin_date and period.id = $period_id
             order by ref_rate.data desc limit 1"
        )->row()->value;
    }

    /*получение ставок рефинансирования, начавших свое действие в течение периода*/
    private function get_other_ref_rate($period_id)
    {
        $other_ref_rate = $this->db->query(
            "select EXTRACT(DAY FROM ref_rate.data)::INTEGER as day, ref_rate.value 
             from industry.ref_rate, industry.period 
             where ref_rate.data>=period.begin_date and ref_rate.data<=period.end_date 
             and period.id = $period_id
             order by ref_rate.data desc"
        )->result();
        $rate_buf = array();
        foreach ($other_ref_rate as $rate) {
            $rate_buf[(int)($rate->day)] = (float)($rate->value);
        }
        return $rate_buf;
    }

    /*получение действующего коэффициента для расчета пени на начало периода*/
    private function get_current_ref_coef($period_id)
    {
        return $this->db->query(
            "select ref_coef.value from industry.ref_coef, industry.period 
             where ref_coef.data<=period.begin_date and period.id = $period_id
             order by ref_coef.data desc limit 1"
        )->row()->value;
    }

    /*получение коэффициентов для расчета пени, начавших свое действие в течение периода*/
    private function get_other_ref_coef($period_id)
    {
        $other_ref_coef = $this->db->query(
            "select EXTRACT(DAY FROM ref_coef.data)::INTEGER as day, ref_coef.value 
             from industry.ref_coef, industry.period 
             where ref_coef.data>=period.begin_date and ref_coef.data<=period.end_date 
             and period.id = $period_id
             order by ref_coef.data desc"
        )->result();
        $coef_buf = array();
        foreach ($other_ref_coef as $coef) {
            $coef_buf[(int)($coef->day)] = (float)($coef->value);
        }
        return $coef_buf;
    }

    /*оплаты организации по дням*/
    private function get_oplata_fine($firm_id, $period_id)
    {
        /*извлекаем последний день месяца*/
        $this->db->where('id', $period_id);
        $end_date = $this->db->get('industry.period')->row()->end_date;
        $end_day = get_day_number($end_date);
        /*извлекаем все оплаты организации*/
        $oplata_list_ar = $this->db->query(
            "SELECT oplata.data as data,
             SUM((oplata.value * (100::numeric + oplata.nds))/100)::NUMERIC as value
             FROM industry.oplata, industry.period
             WHERE firm_id=$firm_id
             and oplata.data >=period.begin_date
             and oplata.data <=period.end_date
             and period.id = $period_id
             GROUP BY oplata.data"
        )->result();
        /*заносим оплаты в ассоц. массив*/
        $oplata_arr = array();

        for ($i = 0; $i <= $end_day; $i++) {
            $oplata_arr[$i] = 0;
        }

        foreach ($oplata_list_ar as $oplata_list) {
            if (($oplata_list->data !== NULL) and ($oplata_list->value !== NULL)) {
                $buf = explode('-', $oplata_list->data);
                $buf = $buf[2];
                $oplata_arr[(int)$buf] = (float)$oplata_list->value;
            }
        }
        ksort($oplata_arr);
        return $oplata_arr;
    }

    public function pre_fine_7_re()
    {
        $data['periods'] = $this->db->query(
            "select 
                distinct(period.id),
                period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['ture'] = $this->db->get("industry.ture")->result();

        $this->left();
        $this->load->view('fine/pre_fine_7_re', $data);
        $this->load->view('right');
    }

    public function fine_7_re()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period_name'] = $this->db->get("industry.period")->row()->name;

        if ($_POST['ture_id'] == '-1') {
            $this->db->where('period_id', $_POST['period_id']);
        } else {
            $this->db->where('id', $_POST['ture_id']);
            $data['ture_name'] = $this->db->get("industry.ture")->row()->name;

            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('ture_id', $_POST['ture_id']);
        }
        $data['re'] = $this->db->get("industry.fine_7_re")->result();

        $this->load->view('fine/fine_7_re', $data);
    }

    public function pre_fine_2_re()
    {
        $data['periods'] = $this->db->query(
            "select 
                distinct(period.id),
                period.*
             from industry.period
             join industry.fine_firm on fine_firm.period_id = period.id
             where fine_firm.is_fine = TRUE
             order by period.id desc"
        )->result();

        $data['ture'] = $this->db->get("industry.ture")->result();
        $this->left();
        $this->load->view('fine/pre_fine_2_re', $data);
        $this->load->view('right');
    }

    public function fine_2_re()
    {
        $this->db->where('id', $_POST['period_id']);
        $data['period_name'] = $this->db->get("industry.period")->row()->name;

        if ($_POST['ture_id'] == '-1') {
            $this->db->where('period_id', $_POST['period_id']);
            $data['re'] = $this->db->get("industry.fine_2_re")->result();
        } else {
            $this->db->where('period_id', $_POST['period_id']);
            $this->db->where('ture_id', $_POST['ture_id']);
            $data['re'] = $this->db->get("industry.fine_2_re_ture")->result();
            $this->db->where('id', $_POST['ture_id']);
            $data['ture_name'] = $this->db->get("industry.ture")->row()->name;
        }

        $this->load->view('fine/fine_2_re', $data);
    }

    function fine_oplata_delete()
    {
        $sql = "DELETE FROM industry.fine_oplata WHERE id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect('billing/oplata');
    }

    public function pre_diff_tariff_year()
    {
        $data['period_years'] = $this->db->get("industry.period_years")->result();
        $this->left();
        $this->load->view('other_reports/pre_diff_tariff_year', $data);
        $this->load->view('right');
    }

    public function diff_tariff_year()
    {
        $period_year = $_POST['period_year'];
        $this->db->where('period_year', $period_year);
        $data['report'] = $this->db->get("industry.report_diff_tariff_sum_year")->result();
        $this->load->view("other_reports/diff_tariff_year", $data);
    }

    public function pre_analiz_m_year()
    {
        $data['period_years'] = $this->db->get("industry.period_years")->result();
        $this->left();
        $this->load->view('other_reports/pre_analiz_m_year', $data);
        $this->load->view('right');
    }

    public function analiz_m_year()
    {
        $data['org'] = $this->db->query("SELECT * FROM industry.org_info")->row();
        $period_year = $_POST['period_year'];
        $this->db->where('period_year', $period_year);
        $data['diff'] = $this->db->get("industry.analiz_m_year")->result();

        $this->load->view("other_reports/analiz_m_year", $data);
    }

    public function pre_kvt_year()
    {
        $data['period_years'] = $this->db->get("industry.period_years")->result();
        $this->left();
        $this->load->view("other_reports/kvt_year/pre_kvt_year", $data);
        $this->load->view("right");
    }

    public function kvt_year()
    {
        $this->db->where('period_year', $_POST['period_year']);
        $data['report'] = $this->db->get("industry.kvt_year")->result();
        $this->load->view("other_reports/kvt_year/kvt_year", $data);
    }

    #годовой свод по начислениям
    public function pre_tenge_year()
    {
        $data['period_years'] = $this->db->get("industry.period_years")->result();
        $this->left();
        $this->load->view("other_reports/tenge_year/pre_tenge_year", $data);
        $this->load->view("right");
    }

    public function tenge_year()
    {
        $this->db->where('period_year', $_POST['period_year']);
        $data['report'] = $this->db->get("industry.tenge_year")->result();
        $this->load->view("other_reports/tenge_year/tenge_year", $data);
    }

    #конец годовой свод по начислениям

    public function double_oplata()
    {
        $data['do'] = $this->db->get("industry.double_oplata")->result();
        $this->left();
        $this->load->view("oplata/double_oplata", $data);
        $this->load->view("right");
    }

    public function do_delete()
    {
        $sql = "delete from industry.oplata where id=" . $this->uri->segment(3);
        $this->db->query($sql);
        redirect('billing/double_oplata');
    }

    private function export_to_excel($view_name, $data, $title = "Example")
    {
        $title .= '_' . date("Ymd");
        $title .= '.xls';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$title}");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        $this->load->view($view_name, $data);
    }

    public function pre_billing_info_dop()
    {
        $data['ture'] = $this->db->get("industry.ture")->result();
        $this->left();
        $this->load->view("other_reports/billing_info_dop/pre", $data);
        $this->load->view("right");
    }

    public function billing_info_dop()
    {
        $ture_id = $_POST['ture_id'];
        if ($ture_id != -1) {
            $this->db->where("ture_id", $ture_id);
        }
        $data['report'] = $this->db->get("industry.billing_info_dop")->result();

        if (isset($_POST['to_excel'])) {
            $this->export_to_excel("other_reports/billing_info_dop/report", $data, 'Информация по точкам учета');
        } else {
            $this->load->view("other_reports/billing_info_dop/report", $data);
        }
    }

    function tariff_list()
    {
        $this->db->where("period_id = industry.current_period_id()");
        $data['tariff_list'] = $this->db->get('industry.tariff_current_value')->result();
        $this->left();
        $this->load->view('tariff_list', $data);
        $this->load->view('right');
    }

    function adding_tariff()
    {
        $this->db->insert("industry.tariff", $_POST);
        redirect("billing/tariff_list");
    }

    function tariff()
    {
        $tariff_id = $this->uri->segment(3);
        $this->db->where("tariff_id", $tariff_id);
        $this->db->order_by("data");
        $this->db->order_by("kvt");
        $data['tariff_info'] = $this->db->get("industry.tariff_list")->result();

        $this->db->where("id", $tariff_id);
        $data['tariff'] = $this->db->get("industry.tariff")->row();

        $this->left();
        $this->load->view('tariff_view', $data);
        $this->load->view('right');
    }

    public function adding_tariff_value()
    {
        $tariff_id = $this->uri->segment(3);
        $value = $_POST['value'];
        $kvt = $_POST['kvt'];
        $data = $_POST['data'];
        if ($kvt == 0) {
            die("kvt ne dolzhny byt' men'she nulya");
        }

        $this->db->where("tariff_id", $tariff_id);
        $this->db->where("period_id = industry.current_period_id()");
        $prev_values = $this->db->get("industry.tariff_current_value")->row();

        if ($data < $prev_values->tariff_data) {
            die("date error");
        }

        $this->db->insert("industry.tariff_period", array(
            'tariff_id' => $tariff_id,
            'data' => $data
        ));

        $tariff_period_id = $this->db->insert_id();

        $this->db->insert("industry.tariff_value", array(
            'tariff_period_id' => $tariff_period_id,
            'kvt' => $kvt,
            'value' => $value
        ));


        redirect("billing/tariff/{$tariff_id}");
    }

    public function delete_tariff_value()
    {
        $tariff_value_id = $this->uri->segment(3);

        $this->db->where("id", $tariff_value_id);
        $tariff_period_id = $this->db->get("industry.tariff_value")->row()->tariff_period_id;

        $this->db->where("id", $tariff_period_id);
        $tariff_id = $this->db->get("industry.tariff_period")->row()->tariff_id;

        $this->db->where("id", $tariff_value_id);
        $this->db->delete("industry.tariff_value");

        $this->db->where("tariff_period_id", $tariff_period_id);
        $tariff_period_num = $this->db->get("industry.tariff_value")->num_rows;

        if ($tariff_period_num == 0) {
            $this->db->where("id", $tariff_period_id);
            $this->db->delete("industry.tariff_period");
        }

        redirect("billing/tariff/{$tariff_id}");
    }

    public function delete_tariff()
    {
        $tariff_id = $this->uri->segment(3);
        $this->db->where("tariff_id", $tariff_id);
        $tariff_period_nums = $this->db->get("industry.tariff_period")->num_rows;
        if ($tariff_period_nums != 0) {
            exit("Can't drop this tariff. It has values!");
        }

        $this->db->where("id", $tariff_id);
        $this->db->delete("industry.tariff");
        redirect("billing/tariff_list");
    }

    public function export_rekvizit_schet()
    {
        /*проверка наличия дублирующихся номеров СФ*/
        $this->db->where('period_id', $this->get_cpi());
        $dup_schet_number = $this->db->get("shell.dup_schet_number");
        if ($dup_schet_number->num_rows > 0) {
            $array_error = array(1 => 'Имеются повторяющиеся номера счетов-фактур!');
            foreach ($dup_schet_number->result() as $d) {
                $array_error[] = "№{$d->dogovor}: номер счета-фактуры: {$d->schet_number}";
            }
            $this->session->set_flashdata('error', $array_error);
            redirect('billing/pre_perehod');
        }

        $db = dbase_open("c:/oplata/rschet.dbf", 2);

        $this->db->where("period_id", $this->get_cpi());
        $this->db->delete("industry.uploaded_invoice");

        if ($db) {
            for ($i = 1; $i < dbase_numrecords($db) + 1; $i++) {
                dbase_delete_record($db, $i);
            }
            dbase_pack($db);
            dbase_close($db);

            $this->db->where('period_id', $this->get_cpi());
            $this->db->where('kvt > 0');
            $nach = $this->db->get("shell.export_rekvizit_schet");

            $russian_letters = array("А", "О", "Е", "С", "Х", "Р", "Т", "Н", "К", "В", "М");
            $english_letters = array("A", "O", "E", "C", "X", "P", "T", "H", "K", "B", "M");
            $array_error = array();

            $db = dbase_open("c:/oplata/rschet.dbf", 2);
            foreach ($nach->result() as $n) {
                //проверка номера 1С
                if ($n->dog1 == 0) {
                    $array_error[] = "№{$n->dog}: некорректный номер 1C - {$n->dog1}";
                    continue;
                }

                //находим некорректные БИКи и МФО банков
                if ((mb_strlen(trim($n->mfo), 'UTF-8') != 8) and ($n->mfo != '0000000000')) {
                    $array_error[] = "№{$n->dog}: неверный БИК банка - {$n->mfo}";
                    continue;
                }

                //обнуляем пустые МФО
                if (($n->mfo == '0000000000')) {
                    $n->mfo = '';
                }

                //находим некорректные БИНы организаций
                if (mb_strlen(trim($n->bin), 'UTF-8') != 12) {
                    $array_error[] = "№{$n->dog}:{$n->name} - неверный БИН - {$n->bin}";
                    continue;
                }

                //обнуляем пустые БИНы
                if ((mb_strlen(trim($n->bin), 'UTF-8') == 0)) {
                    $n->bin = '';
                }

                //заменяем кириллицу на латиницу в МФО
                $n->mfo = str_replace($russian_letters, $english_letters, $n->mfo);

                //вдруг пропущен символ
                if ($this->isRussian($n->mfo)) {
                    $array_error[] = "№{$n->dog}:{$n->name} - БИК банка содержит кириллицу - {$n->mfo}";
                    continue;
                }

                //проверка расчетного счета
                if (($n->mfo <> '') && (mb_strlen($n->schet, 'UTF-8') <> 20)) {
                    $array_error[] = "№{$n->dog}:{$n->name} - некорректный расчетный счет - {$n->schet}";
                    continue;
                }

                if (($n->mfo == '') && (mb_strlen($n->schet, 'UTF-8') <> 20)) {
                    $n->schet = '';
                }

                //проверка начисления
                if ($n->beznds == 0) {
                    $array_error[] = "№{$n->dog}:{$n->name} - нулевая счет-фактура";
                    continue;
                }

                //проверка номера СФ
                if (strlen(trim($n->nomer)) == 0) {
                    $array_error[] = "№{$n->dog}:{$n->name} - некорректный номер счет-фактуры - {$n->nomer}";
                    continue;
                }

                $sf = array(
                    'name' => $n->name,
                    'dog' => $n->dog,
                    'kvt' => $n->kvt,
                    'tarif' => $n->tarif,
                    'beznds' => $n->beznds,
                    'nds' => $n->nds,
                    'snds' => $n->snds,
                    'nomer' => $n->nomer,
                    'firm_id' => $n->firm_id,
                    'period_id' => $n->period_id,
                );

                $this->db->insert("industry.uploaded_invoice", $sf);

                dbase_add_record($db,
                    array(
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->name)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->dog)), 'cp866', 'utf-8'),
                        $this->d2($n->dog_data),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->bin)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->mfo)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->schet)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->adres)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->direct)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->sub)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->kvt)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->tarif)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->beznds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->nds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->snds)), 'cp866', 'utf-8'),
                        mb_convert_encoding(str_replace('  ', ' ', trim($n->nomer)), 'cp866', 'utf-8'),
                        $this->d2($n->sf_data),
                        $n->dog1
                    )
                );
            }
            dbase_close($db);

            $this->db->where("id", $this->get_cpi());
            $current_period = $this->db->get("industry.period")->row();
            $current_period = explode("-", $current_period->end_date);
            $month = $current_period[1];

            if (!copy("c:/oplata/rschet.dbf", "c:/oplata/kges{$month}.dbf")) {
                $array_error[] = "Не удалось скопировать файл rschet.dbf";
            }

            $array_success[] = 'Перенос прошел успешно!';
            $this->session->set_flashdata('success', $array_success);
            $this->session->set_flashdata('error', $array_error);
            redirect('billing/pre_perehod');
        } else {
            echo "DBF file is busy!";
        }
    }

    public function uploaded_invoice($period_id = null)
    {
        $period_id = is_null($period_id) ? $this->get_cpi() : $period_id;

        $this->db->where("period_id", $period_id);
        $this->db->order_by("nomer");
        $data['invoice'] = $this->db->get("industry.uploaded_invoice")->result();

        $this->db->where("id", $period_id);
        $data['period'] = $this->db->get("industry.period")->row();

        $this->load->view("other_reports/uploaded_invoice", $data);
    }

    public function kontragent_rek()
    {
        $data['report'] = $this->db->get("shell.kontragent_rek")->result();
        $this->load->view("other_reports/kontragent_rek", $data);
    }

    public function sf_verification()
    {
        switch ($_POST['kvt_type']) {
            case '-1':
                $this->db->where('kvt < 0');
                break;
            case '1':
                $this->db->where('kvt > 0');
                break;
        }
        $data['report'] = $this->db->get("shell.sf_verification")->result();
        $this->load->view("other_reports/sf_verification", $data);
    }

    public function pre_sf_verification()
    {
        $this->left();
        $this->load->view("other_reports/pre_sf_verification");
        $this->load->view("right");
    }

    public function migration()
    {
        $data['report'] = $this->db->get("shell.migration")->result();
        $this->export_to_excel("other_reports/migration", $data, "кокшетау_гэс");
//        $this->load->view("other_reports/migration", $data);
    }
}

?>