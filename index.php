<?php 

if(isset($_REQUEST['key'])){
  $key=$_REQUEST['key'];
  //die();
} else {
  //$key="GNT220100000002";
  $key="GNT220200000003";
}

?>
<!DOCTYPE HTML>
<html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Gantt - <?= $key ?></title>

  <link rel=stylesheet href="platform.css" type="text/css">
  <link rel=stylesheet href="libs/jquery/dateField/jquery.dateField.css" type="text/css">

  <link rel=stylesheet href="gantt.css" type="text/css">
  <link rel=stylesheet href="ganttPrint.css" type="text/css" media="print">
  <link rel=stylesheet href="libs/jquery/valueSlider/mb.slider.css" type="text/css" media="print">

  <script src="libs/jquery-3.1.1.min.js"></script>
  <script src="libs/jquery-ui-1.12.1.min.js"></script>

  <script src="libs/jquery/jquery.livequery.1.1.1.min.js"></script>
  <script src="libs/jquery/jquery.timers.js"></script>
 
  <script src="libs/utilities.js"></script>
  <script src="libs/forms.js"></script>
  <script src="libs/date.js"></script>
  <script src="libs/dialogs.js"></script>
  <script src="libs/layout.js"></script>
  <script src="libs/i18nJs.js"></script>
  <script src="libs/jquery/dateField/jquery.dateField.js"></script>
  <script src="libs/jquery/JST/jquery.JST.js"></script>
  <script src="libs/jquery/valueSlider/jquery.mb.slider.js"></script>

  <script type="text/javascript" src="libs/jquery/svg/jquery.svg.min.js"></script>
  <script type="text/javascript" src="libs/jquery/svg/jquery.svgdom.1.8.js"></script>


  <script src="ganttUtilities.js"></script>
  <script src="ganttTask.js"></script>
  <script src="ganttDrawerSVG.js"></script>
  <script src="ganttZoom.js"></script>
  <script src="ganttGridEditor.js"></script>
  <script src="ganttMaster.js"></script>  

</head>
<body style="background-color: #fff;">

<div id="workSpace" style="padding:0px; overflow-y:auto; overflow-x:hidden;border:1px solid #e5e5e5;position:relative;margin:0 5px"></div>

<style>
  .resEdit {
    padding: 15px;
  }

  .resLine {
    width: 95%;
    padding: 3px;
    margin: 5px;
    border: 1px solid #d0d0d0;
  }

  body {
    overflow: hidden;
  }

  .ganttButtonBar h1{
    color: #000000;
    font-weight: bold;
    font-size: 28px;
    margin-left: 10px;
  }

  input[disabled], select[disabled], textarea[disabled] {
    color: black;
  }

</style>

<form id="gimmeBack" style="display:none;" action="../gimmeBack.jsp" method="post" target="_blank"><input type="hidden" name="prj" id="gimBaPrj"></form>

<?php 
  require_once('controller.php');
?>



</body>
</html>