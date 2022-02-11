<?php
require_once('config.php');

function HTMLToRGB($htmlCode){
  if($htmlCode[0] == '#')
    $htmlCode = substr($htmlCode, 1);

  if (strlen($htmlCode) == 3)
  {
    $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
  }

  $r = hexdec($htmlCode[0] . $htmlCode[1]);
  $g = hexdec($htmlCode[2] . $htmlCode[3]);
  $b = hexdec($htmlCode[4] . $htmlCode[5]);

  return $b + ($g << 0x8) + ($r << 0x10);
}

function RGBToHSL($RGB) {
  $r = 0xFF & ($RGB >> 0x10);
  $g = 0xFF & ($RGB >> 0x8);
  $b = 0xFF & $RGB;

  $r = ((float)$r) / 255.0;
  $g = ((float)$g) / 255.0;
  $b = ((float)$b) / 255.0;

  $maxC = max($r, $g, $b);
  $minC = min($r, $g, $b);

  $l = ($maxC + $minC) / 2.0;

  if($maxC == $minC)
  {
    $s = 0;
    $h = 0;
  }
  else
  {
    if($l < .5)
    {
      $s = ($maxC - $minC) / ($maxC + $minC);
    }
    else
    {
      $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
    }
    if($r == $maxC)
      $h = ($g - $b) / ($maxC - $minC);
    if($g == $maxC)
      $h = 2.0 + ($b - $r) / ($maxC - $minC);
    if($b == $maxC)
      $h = 4.0 + ($r - $g) / ($maxC - $minC);

    $h = $h / 6.0; 
  }

  $h = (int)round(255.0 * $h);
  $s = (int)round(255.0 * $s);
  $l = (int)round(255.0 * $l);

  return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
}

$arrColorSelected = (isset($_REQUEST['selectColor']) ? $_REQUEST['selectColor'] : array() );
//var_dump($arrColorSelected);
$arrCollegamento = (isset($_REQUEST['selectCollegamento']) ? $_REQUEST['selectCollegamento'] : array() );
$is_filter = (isset($_REQUEST['is_filter']) ? $_REQUEST['is_filter'] : 0 );

if(isset($_REQUEST['key'])){
  $key=$_REQUEST['key'];
  //die();
} else {
  $key="GNT220100000002";
  $key="GNT220200000003";
  //$key="GNT220100000003";
}

$options = array(
    "trace" => 1,
     "exception" => 0,
    'login'    => USER,
    'password' => PASSWORD,
);

try{
    $client = new SoapClient(URL_ERP, $options);
} catch (SoapFault $e) {
   echo "<div style='text-align:center;'><h2>Errore connessione recupero dati!</h2></div>";
}        

$CContext["codeLang"] = CODE_LANG;
$CContext["poolAlias"] = POOL_ALIAS;
$CContext["requestConfig"] = REQUEST_CONFIG;
$subprog = METODO_DATI_GANTT_YINFGANTT;                                                
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
$titolo = $result->resultXml;


try{
    $client = new SoapClient(URL_ERP, $options);
} catch (SoapFault $e) {
   echo "<div style='text-align:center;'><h2>Errore connessione recupero dati!</h2></div>";
}        

$CContext["codeLang"] = CODE_LANG;
$CContext["poolAlias"] = POOL_ALIAS;
$CContext["requestConfig"] = REQUEST_CONFIG;
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

$json = array(
 'canWrite'=> false
, 'canDelete'=> false
, 'canDelete'=> false
, 'canWriteOnParent'=> false
, 'canAdd'=> false
);

if ($status == 1) {
         


  foreach ($xml->TAB as $tab) {
        //var_dump($lin);
        //var_dump($lin->TAB);
        $i = 0;
        $arr_data = array();
        $arrColor = array();
        $arrPadri = array();
        $selectColor = "<select id='selectColor' name='selectColor[]' multiple>";
        $selectCollegamento = "<select id='selectCollegamento' name='selectCollegamento[]' multiple>";

        $arrTot = array();

        //creo array generale da xml e creo le select dei filtri
        foreach ($tab->LIN as $lin) {
          $value = (string) $lin->FLD;
          $arr_value = explode('|',$value);
          
          list($nome,$d_inizio,$d_fine,$descrizione,$color,$livello,$parent,$colorTag) = $arr_value;
          $arrTot[$nome] = array('id'=>$i
            , 'nome' => trim($nome)
            ,'color'=> trim($color)
            ,'parent'=> trim($parent)
            ,'d_inizio'=> trim($d_inizio)
            ,'d_fine'=> trim($d_fine)
            ,'descrizione'=> trim($descrizione)
            ,'livello'=> trim($livello)
            ,'colorTag'=> trim($colorTag)
          );
          //var_dump($nome);
          if(in_array($colorTag,$arrColor) === false){

            $arrColor[] = $colorTag;
            
            if( (!empty($arrColorSelected) && in_array($colorTag,$arrColorSelected) !== false && $is_filter)
                ||empty($arrColorSelected) && !$is_filter
            ){
                $selected = 'selected';
            } else{
                $selected  = "";
            }
            
            $selectColor .= "<option ".$selected." style='background-color:".$color."' value='".trim($colorTag)."'>".trim($colorTag)."</option>";
          }

           
          if($parent != '' && in_array($parent,$arrPadri) === false){
            $arrPadri[] = trim($parent);
            if( (!empty($arrCollegamento) && in_array($parent,$arrCollegamento) !== false && $is_filter)
                ||empty($arrCollegamento) && !$is_filter
            ){
                $selected = 'selected';
            } else{
                $selected  = "";
            }
            
            $selectCollegamento .= "<option ".$selected."  value='".trim($parent)."'>".trim($parent)."</option>";
          }

          $i++;
        }

        $_SESSION['arrTot'] = $arrTot;

        $selectColor .= "</select>";
        $selectCollegamento .= "</select>";

        $arrParent = $arrTot;

        $arrayClean = array();

        //verifico colori
        foreach($arrTot as $k => $a){
          
          if( $is_filter && in_array($a['colorTag'],$arrColorSelected) === false ){

          } else {
            $arrayClean[$k] = $a;
          }

        }

        $arrayClean2 = array();
        //verifico collegamenti
        foreach($arrayClean as $k => $a){
          
          if( $a['parent'] != '') {
           
            if($is_filter){
              if (  in_array($a['parent'],$arrCollegamento) === false){
    
              } else {
                if(!isset($arrayClean2[$a['parent']])){
                  $arrayClean2[$a['parent']] = $arrTot[$a['parent']];
              
                }
                $arrayClean2[$k] = $a;
    
              }
            } else {
              $arrayClean2[$k] = $a;
            }
          } 

        }

        //riordino l'array per le dipendenze
        $arrayFinal = array();
        $arrParent = array();
        $i =1;
        foreach($arrayClean2 as $k => $a){
            
          $arrayFinal[$i] = $a;
          $arrParent[$k] = $i;
          $i++;
        }



        $css = '';
        $arr_data = array();
        //echo '<pre>';var_dump($arrTot); die;
        foreach ($arrayFinal as $k => $a) {

              
              $nome = $a['nome'];
              //var_dump( $nome.'.$i');

              $id = $a['id'];
              $parent = $a['parent'];
              $livello = $a['livello'];
              $color = $a['color'];
              $descrizione = $a['descrizione'];

              $d_inizio =  str_replace( '/', '-',(string) trim($a['d_inizio']));
              $d_fine =  str_replace( '/', '-',(string) trim($a['d_fine']));

              $inizio = strtotime($d_inizio) *1000;
              $fine = strtotime($d_fine) *1000;
              
              $date_start = date_create_from_format('d-m-Y',$d_inizio);
              $date_end = date_create_from_format('d-m-Y',$d_fine);

              $diff = (array) date_diff($date_start,$date_end);

              $rgb = HTMLToRGB($color);
              $hsl = RGBToHSL($rgb);
              //echo $hsl->lightness."<br>";
              if($hsl->lightness > 200) {
                  $colorFont = '#fff';
              } else {
                  $colorFont = '#000';
              }

              $css .= ".colorByStatus .taskStatusSVG[status='".trim($nome)."']{
                  fill: ".$color.";
                }
                .colorCategory[status='".trim($nome)."']{
                  background-color: ".$color.";
                  color: ".$colorFont.";
                }
                "; 

              $id_parent = '';

              if($parent != '' && isset($arrParent[$parent])){
                $id_parent = $arrParent[$parent];
                //$id_parent = 1;
              }  

              $arr_data[] = array(
                  "id" => $id
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
                  ,"status" => trim($nome) //STATUS_ACTIVE
                  ,"depends" => (string) $id_parent
                  ,"start" =>$inizio
                  ,"end" => $fine
                  ,"startIsMilestone" => false
                  ,"endIsMilestone" => false
                  ,"collapsed" => false
                  ,"hasChild" => true//(in_array($nome,$arrParent['parent']) !== false ? true : false)
                  ,"assigs" => array()
              );
            
        }

      }  

     // var_dump($arr_data);die;
    //echo "<pre>"; var_dump($arrColorSelected);
    //die();
    $json['tasks'] = $arr_data;

    $json_data = json_encode($json);

    //echo "<pre>"; var_dump($json_data);die();
    
} 

