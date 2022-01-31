<?php
require_once('config.php');



//$client = new SoapClient(URL_ERP, $optionsAuth);

$options = array(
    "trace" => 1,
     "exception" => 0,
    'login'    => USER,
    'password' => PASSWORD,
);
try{
    $client = new SoapClient(URL_ERP, $options);
} catch (SoapFault $e) {
    
   echo "DENTRO<pre>"; print_r($e); die();
}        
//init context
$CContext["codeLang"] = CODE_LANG;
//sage 11
//$CContext["codeUser"] = $this->config['codeUser'];
//$CContext["password"] = $this->config['password'];
$CContext["poolAlias"] = POOL_ALIAS;
$CContext["requestConfig"] = REQUEST_CONFIG;
//name method
//<FLD NAME=\"YDATMOD\">" . $dataUltimaModifica . "</FLD>
$subprog = METODO_DATI_GANTT;
                               
                                
$xmlInput = '<PARAM> 
     <GRP ID="GRP1" >
             <FLD NAME="YKEY" >'.$key.'</FLD>
     </GRP>
    <TAB ID="GRP2" >
         <LIN>
                   <FLD NAME="YSTR" ></FLD>
            </LIN>
        </TAB>
    </PARAM>
';

$result = $client->run($CContext, $subprog, $xmlInput);                       
$xml = simplexml_load_string($result->resultXml);
$status = (int) trim($result->status);
//echo "<pre>"; //var_dump($xml);

$json = array(
 'canWrite'=> false
, 'canDelete'=> false
, 'canDelete'=> false
, 'canWriteOnParent'=> false
, 'canAdd'=> false
);
//$status = 0;
if ($status == 1) {
                                                           
    foreach ($xml->TAB as $tab) {
        //var_dump($lin);
        //var_dump($lin->TAB);
        $i = 0;
        $arr_data = array();
        foreach ($tab->LIN as $lin) {
            $value = (string) $lin->FLD;
            //var_dump($value);
            
            $arr_value = explode(';',$value);
           
            $d_inizio =  str_replace( '/', '-',(string) trim($arr_value[1]));
            $d_fine =  str_replace( '/', '-',(string) trim($arr_value[2]));
            $nome = $arr_value[0];

            $inizio = strtotime($d_inizio) *1000;
            $fine = strtotime($d_fine) *1000;
            
            $date_start = date_create_from_format('d-m-Y',$d_inizio);
            $date_end = date_create_from_format('d-m-Y',$d_fine);

            $diff = (array) date_diff($date_start,$date_end);

            $arr_data[] = array(
                "id" => $i
                ,"name" => $nome
                ,"canWrite" =>  false
                ,"progress" => 0
                ,"duration" => (int) $diff['days'] -1
                ,"progressByWorklog" => false
                ,"relevance" => 0
                ,"type" => ""
                ,"typeId" => ""
                ,"description" => ""
                , "level" =>  0
                ,"status" => "STATUS_ACTIVE"
                ,"depends" => ""
                ,"start" =>$inizio
                ,"end" => $fine
                ,"startIsMilestone" => false
                ,"endIsMilestone" => false
                ,"collapsed" => false
                ,"hasChild" => false
                ,"assigs" => array()
            );
            
            $i++;
        }
    }
    
    $json['tasks'] = $arr_data;

    $json_data = json_encode($json);

    require_once('core.php');

} else {
    echo "<div style='text-align:center;'><h2>Recupero dati non riuscito oppure nessun dato presente per la generazione del Gantt</h2></div>";
}

