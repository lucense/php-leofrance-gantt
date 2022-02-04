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
    
   //echo "DENTRO<pre>"; print_r($e); die();

   echo "Errore connessione recupero dati!";
   echo "<div style='text-align:center;'><h2>Errore connessione recupero dati!</h2></div>";
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
//echo "<pre>"; print_r($xml);die();
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
        $arrParent = array('id'=>array(),'parent'=>array());
        
        foreach ($tab->LIN as $lin) {
            
            $value = (string) $lin->FLD;
            $arr_value = explode(';',$value);
            
            list($nome,$d_inizio,$d_fine,$descrizione,$categoria,$livello,$parent) = $arr_value;

           if($parent != ''){
                $arrParent['id'][] = $i;
                $arrParent['parent'][] = $parent;
            }
            $i++;
        }

        $i = 0;
        $arrOld = array();
        foreach ($tab->LIN as $lin) {

            $value = (string) $lin->FLD;
            $arr_value = explode(';',$value);
           
            //echo $value.' - '.$i;

            list($nome,$d_inizio,$d_fine,$descrizione,$categoria,$livello,$parent) = $arr_value;

            
                $d_inizio =  str_replace( '/', '-',(string) trim($d_inizio));
                $d_fine =  str_replace( '/', '-',(string) trim($d_fine));

                $inizio = strtotime($d_inizio) *1000;
                $fine = strtotime($d_fine) *1000;
                
                $date_start = date_create_from_format('d-m-Y',$d_inizio);
                $date_end = date_create_from_format('d-m-Y',$d_fine);

                $diff = (array) date_diff($date_start,$date_end);
                //echo $arrParent['parent'][array_search($parent,$arrParent['parent'])].' - nome '.$nome.' - parent '.$parent."<br>";

                $id_parent = '';

                if($parent != ''){
                    $id_parent = array_search($parent,$arrParent['parent']);
                }

                $arr_data[] = array(
                    "id" => $i
                    ,"name" => $nome
                    ,"descrizione" => $descrizione
                    ,"livello" => $livello
                    ,"parent" => $parent
                    ,"canWrite" =>  false
                    ,"progress" => 0
                    ,"duration" => (int) $diff['days'] -1
                    ,"progressByWorklog" => false
                    ,"relevance" => 0
                    ,"type" => ""
                    ,"typeId" => ""
                    ,"description" => ""
                    , "level" =>  0
                    ,"status" => $categoria //STATUS_ACTIVE
                    ,"depends" => (string) $id_parent
                    ,"start" =>$inizio
                    ,"end" => $fine
                    ,"startIsMilestone" => false
                    ,"endIsMilestone" => false
                    ,"collapsed" => false
                    ,"hasChild" => (in_array($nome,$arrParent['parent']) !== false ? true : false)
                    ,"assigs" => array()
                );
                
                $i++;
                $arrOld[] = $nome;
           
             
        }

    }
    //die($i);
    $json['tasks'] = $arr_data;

    $json_data = json_encode($json);

    require_once('core.php');

} else {
    echo "<div style='text-align:center;'><h2>Recupero dati non riuscito oppure nessun dato presente per la generazione del Gantt</h2></div>";
}

