<?php
// Turn off all error reporting
error_reporting(0); 

$PREVIOUS_FRIDAY = "previous friday";
$NEXT_WEDNESDAY = "next wednesday";
$FOURTEEN_DAYS_OFFSET = "+14 days";
$data = array();

for($i=1;$i<=12;$i++){

    /* Get months of the year */
    $data[$i]['month'] = date('F', mktime(0,0,0,$i, 1, date('Y')));
    
    /* Get starting day of the month */
    $start_date = date("Y")."-".$i."-01";

    /* Get Bonus day for the month */
    $bonus_day = date('Y-m-d',strtotime($start_date . $FOURTEEN_DAYS_OFFSET));

    /* Check if the bonus day is a weekend */
    $is_bonus_day_a_weekend = is_weekend($bonus_day);

    /* If weekend, assign bonus day to the next wednesday */
    if($is_bonus_day_a_weekend){
        $data[$i]['bonus_date'] = date('Y-m-d',strtotime($bonus_day . $NEXT_WEDNESDAY));
    }else{
        $data[$i]['bonus_date'] = $bonus_day;
    }
    /* Get last day of the month */
    $last_day_of_the_month = date("Y-m-t", strtotime($start_date));

    /* Check if the last day is a weekend */
    $is_salary_day_a_weekend = is_weekend($last_day_of_the_month);

    /* If weekend, assign salary day to the last working weekday */
    if($is_salary_day_a_weekend){
        $data[$i]['salary_date'] = date('Y-m-d',strtotime($last_day_of_the_month . $PREVIOUS_FRIDAY));
    }else{
        $data[$i]['salary_date'] = $last_day_of_the_month;
    }
}
generate_CSV($data);

/* Function to write to a CSV File */
function generate_CSV($data){
    /* Get filename as an arg from cli */
    $file_name = getopt("f:");
    $help = getopt("h:");
    if($help){
        echo "This script generates a .csv file that contains dates they need to pay salaries to their sales department.\nThe format to generate the file would be: php Employees.php -f example \n";
        exit;
    }
    if(!$file_name){
        echo "Try adding a filename along in the format 'php Employees.php -f filename'\n";
        exit;
    }

    /* Setting headers for the file */
    $headers = ["Month", "Bonus Date", "Salary Date"];

    $csvName = strip_tags(str_replace(".","",$file_name[f])).".csv";
    
    $fileHandle = fopen($csvName, 'w') or die('Can\'t create .csv file, try again later.');

    /* Add the headers */
    fputcsv($fileHandle, $headers);

    /* Add the data */
    foreach ($data as $item) {
        fputcsv($fileHandle, $item);
    }
    /* Close file */
    fclose($fileHandle);

}
/* Function to check if the date passed is a weekend */
function is_weekend($date) {
     if(date('N', strtotime($date)) >= 6){
         return true;
     }else{
         return false;
     }
}
?>