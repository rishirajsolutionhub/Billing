<?php  

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller 
{	
	public function __construct()
	{
		parent::__construct();
		$this->data['page_title'] = 'Stores';
		$this->load->model('model_reports');
	}

	/* 
    * It redirects to the report page
    * and based on the year, all the orders data are fetch from the database.
    */
	public function index()
	{
		if(!in_array('viewReports', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
		
		$today_year = date('Y');

		if($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		$parking_data = $this->model_reports->getOrderData($today_year);
		$this->data['report_years'] = $this->model_reports->getOrderYear();
		

		$final_parking_data = array();
		$final_parking_profit_data = array();
		foreach ($parking_data as $k => $v) {
			
			if(count($v) > 1) {
				$total_amount_earned = array();
				$total_profit_earned = array();
				foreach ($v as $k2 => $v2) {
					if($v2) {
						$total_amount_earned[] = $v2['gross_amount'];
						$total_profit_earned[] = $v2['profit'];						
					}
				}
				$final_parking_data[$k] = array_sum($total_amount_earned);
				$final_parking_profit_data[$k] = array_sum($total_profit_earned);
			}
			else {
				$final_parking_data[$k] = 0;
				$final_parking_profit_data[$k] = 0;	
			}
			
		}
		
		$this->data['selected_year'] = $today_year;
		$this->data['company_currency'] = $this->company_currency();
		$this->data['sale_results'] = $final_parking_data;
		$this->data['profit_results'] = $final_parking_profit_data;

		$this->render_template('reports/index', $this->data);
	}
}	