<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

class GoogleCalenderController extends Controller
{
    public $data;
    protected $client;


    public function __construct()
    {
//        $client = new Google_Client();
//        $client->setAuthConfig('client_secret.json');
//        $client->addScope(Google_Service_Calendar::CALENDAR);
//
//        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
//        $client->setHttpClient($guzzleClient);
//        
//        $this->client = $client;
    
    }

    public function index()
    {

            $rurl = route('admin.cal');   
            $this->client->setRedirectUri($rurl);

            if (!isset($_GET['code'])) {
                $auth_url = $this->client->createAuthUrl();
                $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);

                header("Location: ".$filtered_url, TRUE);     //return redirect($filtered_url);
                exit();
                    
            }

            $this->client->authenticate($_GET['code']);
            $access_token = $this->client->getAccessToken();

            $this->client->setAccessToken($access_token);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';

            $dynamic_dates = session('set_dates');
            $expired_date = session('expired_date');
            $employee_name = session('employee_name');
            $description = "This is reminder for Employee Insurance is going to Expired on {$expired_date}";
    
            foreach ($dynamic_dates as $key => $date) {
            
                // $startDate = date('Y-m-d', strtotime($date));
                // $endDate = date('Y-m-d', strtotime("+1 day", $date));

                $startDate = new \DateTime($date);
                $endDate = new \DateTime($date);
                
                //add 1 hour to start date so make end time
                $time_end  =   $endDate->add(new \DateInterval('PT1H'));

                //Google Format
                $time_start  =  $startDate->format(\DateTime::RFC3339);
                $time_end = $time_end->format(\DateTime::RFC3339);

                $event = new Google_Service_Calendar_Event([
                    'colorId' => 2,
                    'summary' => $employee_name,
                    'description' => $description,
                    'start' => ['dateTime' => $time_start],
                    'end' => ['dateTime' => $time_end],
                    'reminders' => ['useDefault' => true],
                ]);

                $results = $service->events->insert($calendarId, $event);
                $results_events[] = $results;

            }

            if (empty($results_events)) {
                return redirect()->route('admin.employees_insurances')->with('success', 'Error Occured while doing operation.');
            }
            return redirect()->route('admin.employees_insurances')->with('success', 'New Insurance Policy and it is reminder dates are successfully added with Google Calender.');   

    }

                                  

}
