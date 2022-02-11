<?php
session_start();
$arrTot = $_SESSION['arrTot'];

$arrColorSelected = (isset($_REQUEST['selectColor']) ? $_REQUEST['selectColor'] : array() );
$arrCollegamento = (isset($_REQUEST['selectCollegamento']) ? $_REQUEST['selectCollegamento'] : array() );

if($_REQUEST['function'] == 'getSelectCollegamento'){
    
    $selectCollegamento = "<select id='selectCollegamento' name='selectCollegamento[]' multiple>";
    $arrPadri = array();  
    
    foreach($arrTot as $k => $a){

        $parent = isset($a['parent']) ? $a['parent'] : '';
        $colorTag = $a['colorTag'];

        if($parent != '' && in_array($parent,$arrPadri) === false && ( in_array($colorTag,$arrColorSelected) === true || in_array($arrTot[$parent]['colorTag'],$arrColorSelected) === true  || empty($arrColorSelected) )){
            $arrPadri[] = trim($parent);
            if( !empty($arrCollegamento) && in_array($parent,$arrCollegamento) !== false
            ){
                $selected = 'selected';
            } else{
                $selected  = "";
            }
            
            $selectCollegamento .= "<option ".$selected."  value='".trim($parent)."'>".trim($parent)."</option>";
        }
    }

    $selectCollegamento .= '</select>';

    echo json_encode(array('esito'=>'OK', 'select'=>$selectCollegamento));
    exit;

}
if($_REQUEST['function'] == 'getSelectColor'){

    $selectColor = "<select id='selectColor' name='selectColor[]' multiple>";
    $arrColor = array();

    $arrColored = array();
    foreach($arrTot as $k => $a){

        $parent = $a['parent'];
        $nome = $a['nome'];
        $colorTag = $a['colorTag'];

        if(in_array($parent,$arrCollegamento) === true || in_array($nome,$arrCollegamento) || empty($arrCollegamento) ){
            $arrColored[] = $colorTag;
        }
        
    }

    foreach($arrTot as $k => $a){
        
        $parent = $a['parent'];
        $colorTag = $a['colorTag'];
        $color = $a['color'];

        if(in_array($colorTag,$arrColor) === false && in_array($colorTag,$arrColored) === true){

            $arrColor[] = $colorTag;
            
            if( (!empty($arrColorSelected) && in_array($colorTag,$arrColorSelected) === true  )
                
            ){
                $selected = 'selected';
            } else{
                $selected  = "";
            }
            
            $selectColor .= "<option ".$selected." style='background-color:".$color."' value='".trim($colorTag)."'>".trim($colorTag)."</option>";
        }
    }

    $selectColor .= '</select>';
    echo json_encode(array('esito'=>'OK', 'select'=>$selectColor));
    exit;
}
