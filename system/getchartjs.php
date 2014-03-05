<?php
//ini_set('display_errors', '1');
//error_reporting(E_ALL);
require_once("../models/config.php");
$TempString = GetChartData($_GET["id"]);
?>
var showChart = function () {

        var cx1 = new CanvasXpress('canvas1',
          {
            market : [
              {
                symbol : '',
                data : [
                 <?php echo $TempString; ?>
                ]
              }
            ]
          },
          {'colors': ['rgb(0,100,217)'],
          'graphOrientation': 'vertical',
          'graphType': 'Candlestick',
          'resizerPosition': 'bottom',
          'resizerType': 'samples',
		  'autoScaleFont': false,
          'showLegend': false,
          'smpLabelRotate': 90,
		  'adjustAspectRatio':true,
		  'stockIndicators':[''],
		  'timeFormat':'isoDateTime'}
        );
	cx1.disableMenu = true;
	
	cx1.initializeDimensions();
	cx1.resizable = false;
    window.addEventListener('resize', resizeCanvas, false);
    function resizeCanvas() {
		if(window.innerWidth > 800)
		{
            cx1.setDimensions(window.innerWidth - 330,500,false);
		}else{
			cx1.setDimensions(window.innerWidth-50,500,false);
		}		
		cx1.initializeDimensions();
		
	}
    resizeCanvas();
      }
	  showChart();