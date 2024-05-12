<style> 
	.mybox {position: relative;display: inline-block;font-size: 16pt;} 
	select {display: inline-block; height: 30px;width: 150px;outline: none;color: #000000; border: 1px solid #ccc;border-radius: 5px;box-shadow: 1px 1px 2px #999;background: #eee;font-size: 16pt;}
	.mybox .myarrow{width: 23px;height: 28px;position: absolute;display: inline-block;top: 1px;right: 3px;background: #eee;pointer-events: none;}
        .page_INPUT_mid   { font-family: Verdana, Arial;font-size: 16pt;color: #000000; border: 1 solid #808080; height: 30px;}
	A {color: #3E4FA0; text-decoration: none;} A:visited {color: #3E4FA0;} A:active {color: #3E4FA0;} A:hover {text-decoration: underline;} 
</style>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
function Max_T4S($cIn_Val) {return iconv('cp1251', 'UTF-8',$cIn_Val);}
function Max_T4B($cIn_Val) {return iconv('UTF-8', 'cp1251',$cIn_Val);}
require("/var/lk_service/Login_Who_Serves.php");
if ($dbh_WhoServ) 
	{
		echo ' <body bgcolor="#7996A3">'.PHP_EOL;
		echo '  <FORM NAME="Form1" ACTION="" METHOD="POST">'.PHP_EOL;
		echo '   <INPUT TYPE="HIDDEN" NAME="scr1" VALUE="">'.PHP_EOL;
		echo '   <INPUT TYPE="HIDDEN" NAME="SP" VALUE="">'.PHP_EOL;
		if (isset($_POST['SP']))
			{
				if ($_POST['SP'] == 1)
					{
						unset($_POST['Spis_Street']);
						unset($_POST['Spis_House']);
					}
				if ($_POST['SP'] == 2)
					{
						unset($_POST['Spis_House']);
					}
			}
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 10px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo Max_T4S('     <TD WIDTH=100% Style="text-align: center;background: #7996A3;color: #3E4FA0;font-size: 32pt;">АО "Цифровые Решения" - Кто Вас обслуживает</TD>').PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo Max_T4S('     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">Выберите населенный пункт ...</TD>').PHP_EOL; 
		$sql = 'select a.id_address_space, a.name_address_space||'.chr(39).' '.chr(39).'||a.type_address_space nas, (select aaa.name_address_space||'.chr(39).' '.chr(39).'||aaa.type_address_space from ADDRESS_SPACE aaa where aaa.id_address_space=a.id_parent) rod, 1 so from ADDRESS_SPACE a where a.id_parent in (select aa.id_address_space from ADDRESS_SPACE aa where aa.id_parent=30849 and aa.id_address_space not in (38740,38837))'.
			' union all'.
			' select a.id_address_space, a.name_address_space||'.chr(39).' '.chr(39).'||a.type_address_space nas, '.chr(39).chr(39).' rod, 0 so from ADDRESS_SPACE a where a.id_address_space in (38740,38837)'.
			' order by 4,2,3';
		$rc = ibase_query($dbh_WhoServ, $sql);
		$Max_NasPunkt_Count = 0;
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_NasPunkt_Count = $Max_NasPunkt_Count + 1;
				$Max_NasPunkt_ID[$Max_NasPunkt_Count] = Max_T4S($row[0]);
				$Max_NasPunkt_Name[$Max_NasPunkt_Count] = Max_T4S($row[1]);
				$Max_RodPunct_Name[$Max_NasPunkt_Count] = Max_T4S($row[2]);
				if ($Max_RodPunct_Name[$Max_NasPunkt_Count] != '') $Max_RodPunct_Name[$Max_NasPunkt_Count] = ' - '.$Max_RodPunct_Name[$Max_NasPunkt_Count];
			}
		ibase_free_result($rc);
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_NasPunkt" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(1)">';
		if (isset($_POST['Spis_NasPunkt'])) $Spis_NasPunkt = $_POST['Spis_NasPunkt']; 
		for ($i_NasPunkt = 1; $i_NasPunkt <= $Max_NasPunkt_Count; $i_NasPunkt++) 
			{
				if ($i_NasPunkt == 1) {if (!isset($_POST['Spis_NasPunkt'])) $Spis_NasPunkt = $Max_NasPunkt_ID[$i_NasPunkt];}
				echo '<OPTION VALUE="'.$Max_NasPunkt_ID[$i_NasPunkt].'"';if ($Spis_NasPunkt == $Max_NasPunkt_ID[$i_NasPunkt]) echo ' selected';echo '>'.$Max_NasPunkt_Name[$i_NasPunkt].$Max_RodPunct_Name[$i_NasPunkt].'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=2 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo Max_T4S('     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">Выберите улицу ...</TD>').PHP_EOL; 
		$sql = 'select a.id_address_space, a.name_address_space||'.chr(39).' '.chr(39).'||a.type_address_space ul from ADDRESS_SPACE a where a.id_parent='.$Spis_NasPunkt.' order by 2';
		$rc = ibase_query($dbh_WhoServ, $sql);
		$Max_Street_Count = 0;
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_Street_Count = $Max_Street_Count + 1;
				$Max_Street_ID[$Max_Street_Count] = Max_T4S($row[0]);
				$Max_Street_Name[$Max_Street_Count] = Max_T4S($row[1]);
			}
		ibase_free_result($rc);
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_Street" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(2)">';
		if (isset($_POST['Spis_Street'])) $Spis_Street = $_POST['Spis_Street']; 
		for ($i_Street = 1; $i_Street <= $Max_Street_Count; $i_Street++) 
			{
				if ($i_Street == 1) {if (!isset($_POST['Spis_Street'])) $Spis_Street = $Max_Street_ID[$i_Street];}
				echo '<OPTION VALUE="'.$Max_Street_ID[$i_Street].'"';if ($Spis_Street == $Max_Street_ID[$i_Street]) echo ' selected';echo '>'.$Max_Street_Name[$i_Street].'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=2 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo Max_T4S('     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">Выберите дом ...</TD>').PHP_EOL; 
		if (!(isset($Spis_Street))) $Spis_Street = 0;
		$sql = 'select h.id_houses, h.nomer, h.liter, h.korpus, h.adres, lpad(h.nomer, 10, '.chr(39).'0'.chr(39).') from houses h where h.id_address_space = '.$Spis_Street.' order by 6,2,3,4';
		$rc = ibase_query($dbh_WhoServ, $sql);
		$Max_House_Count = 0;
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_House_Count = $Max_House_Count + 1;
				$Max_House_ID[$Max_House_Count] = Max_T4S($row[0]);
				$Max_House_Nomer[$Max_House_Count] = Max_T4S($row[1]);
				$Max_House_Liter[$Max_House_Count] = Max_T4S($row[2]);
				$Max_House_Korpus[$Max_House_Count] = Max_T4S($row[3]);
				$Max_House_Adres[$Max_House_Count] = Max_T4S($row[4]);
				$Max_House_Full[$Max_House_Count] = $Max_House_Nomer[$Max_House_Count];
				If ($Max_House_Liter[$Max_House_Count] != '') $Max_House_Full[$Max_House_Count] = $Max_House_Full[$Max_House_Count].$Max_House_Liter[$Max_House_Count];
				If ($Max_House_Korpus[$Max_House_Count] != '') $Max_House_Full[$Max_House_Count] = $Max_House_Full[$Max_House_Count].Max_T4S(' корп.').$Max_House_Korpus[$Max_House_Count];
			}
		ibase_free_result($rc);
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_House" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(3)">';
		if (isset($_POST['Spis_House'])) $Spis_House = $_POST['Spis_House']; 
		for ($i_House = 1; $i_House <= $Max_House_Count; $i_House++) 
			{
				if ($i_House == 1) {if (!isset($_POST['Spis_House'])) $Spis_House = $Max_House_ID[$i_House];}
				echo '<OPTION VALUE="'.$Max_House_ID[$i_House].'"';if ($Spis_House == $Max_House_ID[$i_House]) echo ' selected';echo '>'.$Max_House_Full[$i_House].'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=2 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="1" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% ALIGN="Center" colspan=2 CLASS="page_INPUT_mid" Style="text-align: center;font-size: 24pt; font-weight: bold;">';for ($i_House = 1; $i_House <= $Max_House_Count; $i_House++) {if ($Spis_House == $Max_House_ID[$i_House]) echo Max_T4S('Полный адрес дома: ').$Max_House_Adres[$i_House];}echo '</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo Max_T4S('     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">Информация о поставщике</TD>').PHP_EOL; 
		echo Max_T4S('     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">Предоставляемые услуги поставщиком на доме</TD>').PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		if (!(isset($Spis_House))) $Spis_House = 0;
		$sql = 'select p.vn_ras, p.id_providers, p.name_providers, p.inn, p.ur_adres, p.fak_adres, p.tel, p.email, p.sait, p.rezh_rab, s.name_services from house_services h, providers p, services s where h.id_services=s.id_services and h.id_providers=p.id_providers and h.id_houses='.$Spis_House.' order by 1,2,11';
		$rc = ibase_query($dbh_WhoServ, $sql);
		$Max_Usl_Count = 0;
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_Usl_Count = $Max_Usl_Count + 1;
				$Max_HS_VR[$Max_Usl_Count] = Max_T4S($row[0]);
				$Max_HS_IP[$Max_Usl_Count] = Max_T4S($row[1]);
				$Max_HS_NP[$Max_Usl_Count] = Max_T4S($row[2]);
				$Max_HS_INN[$Max_Usl_Count] = Max_T4S($row[3]);
				$Max_HS_UA[$Max_Usl_Count] = Max_T4S($row[4]);
				$Max_HS_FA[$Max_Usl_Count] = Max_T4S($row[5]);
				$Max_HS_TEL[$Max_Usl_Count] = Max_T4S($row[6]);
				$Max_HS_EM[$Max_Usl_Count] = Max_T4S($row[7]);
				$Max_HS_S[$Max_Usl_Count] = Max_T4S($row[8]);
				$Max_HS_RR[$Max_Usl_Count] = Max_T4S($row[9]);
				$Max_HS_USL[$Max_Usl_Count] = Max_T4S($row[10]);
				
			}
		ibase_free_result($rc);
		$Max_Post_Count = 0;
		for ($i_House_Usl = 1; $i_House_Usl <= $Max_Usl_Count; $i_House_Usl++) 
			{
				if ($i_House_Usl == 1) 
					{
						$Max_Post_Count = $Max_Post_Count + 1;
						$Max_HSP_VR[$Max_Post_Count] = $Max_HS_VR[$i_House_Usl];
						$Max_HSP_IP[$Max_Post_Count] = $Max_HS_IP[$i_House_Usl];
						$Max_HSP_NP[$Max_Post_Count] = $Max_HS_NP[$i_House_Usl];
						$Max_HSP_INN[$Max_Post_Count] = $Max_HS_INN[$i_House_Usl];
						$Max_HSP_UA[$Max_Post_Count] = $Max_HS_UA[$i_House_Usl];
						$Max_HSP_FA[$Max_Post_Count] = $Max_HS_FA[$i_House_Usl];
						$Max_HSP_TEL[$Max_Post_Count] = $Max_HS_TEL[$i_House_Usl];
						$Max_HSP_EM[$Max_Post_Count] = $Max_HS_EM[$i_House_Usl];
						$Max_HSP_S[$Max_Post_Count] = $Max_HS_S[$i_House_Usl];
						$Max_HSP_RR[$Max_Post_Count] = $Max_HS_RR[$i_House_Usl];
						$Max_HSP_KOL[$Max_Post_Count] = 1;

					}
				else
					{
						if ($Max_HS_IP[$i_House_Usl] != $Max_HS_IP[$i_House_Usl - 1])
							{
								$Max_Post_Count = $Max_Post_Count + 1;
								$Max_HSP_VR[$Max_Post_Count] = $Max_HS_VR[$i_House_Usl];
								$Max_HSP_IP[$Max_Post_Count] = $Max_HS_IP[$i_House_Usl];
								$Max_HSP_NP[$Max_Post_Count] = $Max_HS_NP[$i_House_Usl];
								$Max_HSP_INN[$Max_Post_Count] = $Max_HS_INN[$i_House_Usl];
								$Max_HSP_UA[$Max_Post_Count] = $Max_HS_UA[$i_House_Usl];
								$Max_HSP_FA[$Max_Post_Count] = $Max_HS_FA[$i_House_Usl];
								$Max_HSP_TEL[$Max_Post_Count] = $Max_HS_TEL[$i_House_Usl];
								$Max_HSP_EM[$Max_Post_Count] = $Max_HS_EM[$i_House_Usl];
								$Max_HSP_S[$Max_Post_Count] = $Max_HS_S[$i_House_Usl];
								$Max_HSP_RR[$Max_Post_Count] = $Max_HS_RR[$i_House_Usl];
								$Max_HSP_KOL[$Max_Post_Count] = 1;
							}
						else
							{
								$Max_HSP_KOL[$Max_Post_Count] = $Max_HSP_KOL[$Max_Post_Count] + 1;
							}
					}
			}
		$Max_Kol_partner = 0;
		for ($i = 1; $i <= $Max_Post_Count; $i++) 
			{
				if (($Max_HSP_VR[$i] == 1) and ($Max_Kol_partner == 0))
					{
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo Max_T4S('     <TD WIDTH=100% ALIGN="Center" colspan=2 CLASS="page_INPUT_mid">Партнеры, оказывающие услуги в доме</TD>').PHP_EOL;
						$Max_Kol_partner = 1;
					}
				echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
				echo '     <TD WIDTH=50% ALIGN="Center" rowspan='.$Max_HSP_KOL[$i].' CLASS="page_INPUT_mid">';
				echo $Max_HSP_NP[$i].'<br>';
				if ($Max_HSP_INN[$i] != '' ) echo Max_T4S('ИНН ').$Max_HSP_INN[$i].'<br>';
				if ($Max_HSP_UA[$i] != '' ) echo Max_T4S('Юр.адрес ').$Max_HSP_UA[$i].'<br>';
				if ($Max_HSP_FA[$i] != '' ) echo Max_T4S('Факт.адрес  ').$Max_HSP_FA[$i].'<br>';
				if ($Max_HSP_TEL[$i] != '' ) echo Max_T4S('Телефоны ').$Max_HSP_TEL[$i].'<br>';
				if ($Max_HSP_EM[$i] != '' ) echo 'E-mail '.$Max_HSP_EM[$i].'<br>';
				if ($Max_HSP_S[$i] != '' ) echo Max_T4S('Сайт ').$Max_HSP_S[$i].'<br>';
				if ($Max_HSP_RR[$i] != '' ) echo Max_T4S('Режим работы').'<br>'.str_replace('[br]','<br>',$Max_HSP_RR[$i]);
				echo '</TD>'.PHP_EOL; 
				$Max_Kol_temp = 0;
				for ($j = 1; $j <= $Max_Usl_Count; $j++) if ($Max_HS_IP[$j] == $Max_HSP_IP[$i])
					{
						if ($Max_Kol_temp != 0) echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						$Max_Kol_temp = 1;
						echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
						echo $Max_HS_USL[$j];
						echo '</TD>'.PHP_EOL; 
						echo '    </TR>'.PHP_EOL;
					}
			}
		echo '   </TABLE>'.PHP_EOL;
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
                echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
                echo Max_T4S('     <TD WIDTH=100% ALIGN="Center" CLASS="page_INPUT_mid"><a href="https://host.ru">Вернуться на главную страницу</a></TD>').PHP_EOL;
                echo '    </TR>'.PHP_EOL;
                echo '   </TABLE>'.PHP_EOL;
		echo '  </FORM>'.PHP_EOL;
		echo ' </body>'.PHP_EOL;
		echo '<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">'.PHP_EOL;
		if (isset($_POST['scr1'])) echo 'window.scroll(0,'.$_POST['scr1'].');'.PHP_EOL;
		echo 'function SMAX(in_val)'.PHP_EOL;
		echo '  {'.PHP_EOL;
		echo '    document.Form1.scr1.value=document.body.scrollTop;';
		echo '    document.Form1.SP.value=in_val;';
		echo '    document.Form1.submit();'.PHP_EOL;
		echo '  }'.PHP_EOL;
		echo '</SCRIPT>'.PHP_EOL;
	}
ibase_commit($dbh_WhoServ);
require("/var/lk_service/Close_Who_Serves.php");
?>
