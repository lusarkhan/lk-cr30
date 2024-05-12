<style> 
	.mybox {position: relative;display: inline-block;font-size: 16pt;} 
	select {display: inline-block; height: 30px;width: 150px;outline: none;color: #000000; border: 1px solid #ccc;border-radius: 5px;box-shadow: 1px 1px 2px #999;background: #eee;font-size: 16pt;}
	.mybox .myarrow{width: 23px;height: 28px;position: absolute;display: inline-block;top: 1px;right: 3px;background: #eee;pointer-events: none;}
        .page_INPUT_mid   { font-family: Verdana, Arial;font-size: 16pt;color: #000000; border: 1 solid #808080; height: 30px;}
</style>
<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
function Max_T4S($cIn_Val) {return iconv('cp1251', 'UTF-8',$cIn_Val);}
function Max_T4B($cIn_Val) {return iconv('UTF-8', 'cp1251',$cIn_Val);}
$db = '10.3.1.130:C:\Tools\Base_FB\Pred_Zapis.fdb';
$username = 'USER';
$password = 'password';
$dbh_zapis = ibase_connect($db, $username, $password);
if (isset($_SESSION['LS'])) $Max_LS = $_SESSION['LS']; else $Max_LS = "";
if (isset($_SESSION['FIO'])) $Max_FIO = $_SESSION['FIO']; else $Max_FIO = "";
if (isset($_SESSION['PHONE_NUMBER'])) $Max_Tel = $_SESSION['PHONE_NUMBER']; else $Max_Tel = "";
if ($dbh_zapis != true) 
	{
		echo Max_T4S('Проводятся профилактические работы! Повторите работу позднее ...').PHP_EOL;
	}
else if ((strlen($Max_LS) == 0) or (strlen($Max_FIO) == 0) or (strlen($Max_Tel) == 0))
	{
		echo Max_T4S('В личном кабинете заполнены не все данные: для записи необходим лицевой счет, ФИО и номер телефона для связи').PHP_EOL;
	}
else
	{
	      if (isset($_POST['SP']))
			{
				if ($_POST['SP'] == 1)
					{
						unset($_POST['Spis_LK_Dates']);
						unset($_POST['Spis_LK_Times']);
					}
				if ($_POST['SP'] == 2)
					{
						unset($_POST['Spis_LK_Times']);
					}
				if ($_POST['SP'] == 3)
					{
						$end_text_page = 'Ошибка! Запись не зарегистрирована!';
						$sql = 'select * from  ADD_ZAPIS_LK(20,null,'.$_POST['IDZ'].','.chr(39).$_POST['Spis_LK_Dates'].chr(39).','.chr(39).mb_substr(trim($_SESSION['FIO']),0,49).chr(39).','.chr(39).trim($_SESSION['PHONE_NUMBER']).chr(39).','.chr(39).trim($_SESSION['LS']).chr(39).');';
						//echo $sql;
						$rc = ibase_query($dbh_zapis, Max_T4B($sql));while ($row = ibase_fetch_row($rc)) {$end_text_page = 'Запись зарегистрирована!';} ibase_free_result($rc);
						//$end_text_page = $sql;
					}
				if ($_POST['SP'] == 4)
					{
						$end_text_page = 'Ошибка! Запись не отменена!';
						$sql = 'select * from  Max_Cancel_Zapis_LK(20,null,'.$_POST['IDZ'].','.chr(39).$_POST['DTZ'].chr(39).');';
						$rc = ibase_query($dbh_zapis, $sql);while ($row = ibase_fetch_row($rc)) {$end_text_page = 'Запись отменена!';} ibase_free_result($rc);
						//$end_text_page = $sql;
					}
			}
		echo '  <FORM NAME="Form1" ACTION="/personal/preentry/" METHOD="POST">'.PHP_EOL;
		if (isset($_POST['Date_Today'])) $Date_Today = $_POST['Date_Today']; else $Date_Today = Date('d.m.Y');
		if (isset($_POST['Date_NextMonth'])) $Date_NextMonth = $_POST['Date_NextMonth']; else $Date_NextMonth = Date('d.m.Y',mktime(0, 0, 0, substr($Date_Today,3,2) + 1, 1, substr($Date_Today,6,4)));
		echo '   <INPUT TYPE="HIDDEN" NAME="SP" VALUE="">'.PHP_EOL;
		echo '   <INPUT TYPE="HIDDEN" NAME="scr1" VALUE="">'.PHP_EOL;
		echo '   <INPUT TYPE="HIDDEN" NAME="IDZ" VALUE="">'.PHP_EOL;
		echo '   <INPUT TYPE="HIDDEN" NAME="DTZ" VALUE="">'.PHP_EOL;
		$sql = 'select p.id, p.name_4lk from podrs p, podrs_dop pd where p.id=pd.id_podr and pd.id_user_replace is null and pd.dt_beg<='.chr(39).$Date_Today.chr(39).'  and (pd.dt_end>'.chr(39).$Date_Today.chr(39).' or pd.dt_end is null) and p.id in (select distinct wd.id_podr from windows w, windows_dop wd where w.id=wd.id_window and wd.id_user_replace is null and wd.dt_beg<='.chr(39).$Date_Today.chr(39).'  and (wd.dt_end>'.chr(39).$Date_Today.chr(39).' or wd.dt_end is null)) order by 2';
		$rc = ibase_query($dbh_zapis, $sql);
		$Max_LK_Podr_Count = 0;
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_LK_Podr_Count = $Max_LK_Podr_Count + 1;
				$Max_LK_Podr_ID[$Max_LK_Podr_Count] = $row[0];
				$Max_LK_Podr_Name[$Max_LK_Podr_Count] = $row[1];
			}
		ibase_free_result($rc);
		//echo 'Spis_LK_Dates = '.$_POST['Spis_LK_Dates'].PHP_EOL;
		//echo 'Spis_LK_Times = '.$_POST['Spis_LK_Times'].PHP_EOL;
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 10px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% Style="text-align: center;background: #496791;color: #fff;font-size: 32pt;">'.Max_T4S('Электронная очередь - АО "Цифровые Решения"').'</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
		$sql = 'select z.id, (select * from Max_date_to_Char(z.date_zapis)) date_zapis, r.hour_beg, r.min_beg, r.hour_end, r.min_end, z.date_zapis, (select p.name_4lk from windows_dop wd, podrs p where p.id=wd.id_podr and wd.id_window=r.id_window and wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null)) name_podr
				from ZZ_DAY_ZAP_'.substr($Date_Today,6,4).'_'.substr($Date_Today,3,2).' z, Rezh_rab r, Rezh_rab_dop rd
				where z.date_zapis>'.chr(39).$Date_Today.chr(39).' and z.id_user_add = 20 and z.id_user_block is null and z.id_user_del is null and z.is_zap=1 and z.ls='.trim($_SESSION['LS']).'
				and z.id_Rezh_rab=r.id and r.id_window in (select distinct wd.id_window from windows_dop wd where wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null))
				and r.id=rd.id_Rezh_rab and rd.id_type_zapis=1 and rd.id_user_replace is null and rd.dt_beg<=z.date_zapis  and (rd.dt_end>z.date_zapis or rd.dt_end is null) 
			    union all
			    select z.id, (select * from Max_date_to_Char(z.date_zapis)) date_zapis, r.hour_beg, r.min_beg, r.hour_end, r.min_end, z.date_zapis, (select p.name_4lk from windows_dop wd, podrs p where p.id=wd.id_podr and wd.id_window=r.id_window and wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null)) name_podr
				from ZZ_DAY_ZAP_'.substr($Date_NextMonth,6,4).'_'.substr($Date_NextMonth,3,2).' z, Rezh_rab r, Rezh_rab_dop rd
				where z.date_zapis>'.chr(39).$Date_Today.chr(39).' and z.id_user_add = 20 and z.id_user_block is null and z.id_user_del is null and z.is_zap=1 and z.ls='.trim($_SESSION['LS']).'
				and z.id_Rezh_rab=r.id and r.id_window in (select distinct wd.id_window from windows_dop wd where wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null))
				and r.id=rd.id_Rezh_rab and rd.id_type_zapis=1 and rd.id_user_replace is null and rd.dt_beg<=z.date_zapis  and (rd.dt_end>z.date_zapis or rd.dt_end is null) 
			   order by 7, 3, 4
			';
		$Max_Kol_Zap = 0;
		$rc = ibase_query($dbh_zapis, $sql);
		while ($row = ibase_fetch_row($rc)) 
			{
				$Max_Kol_Zap = $Max_Kol_Zap + 1;
				$Max_Make_Zapis_ID_Zapis_1[$Max_Kol_Zap] = $row[0];
				$Max_Make_Zapis_Date_Zapis_1[$Max_Kol_Zap] = $row[1];
				$Max_Make_Zapis_HB_1[$Max_Kol_Zap] = $row[2];
				$Max_Make_Zapis_MB_1[$Max_Kol_Zap] = $row[3];
				$Max_Make_Zapis_HE_1[$Max_Kol_Zap] = $row[4];
				$Max_Make_Zapis_ME_1[$Max_Kol_Zap] = $row[5];
				$Max_Make_Zapis_Podr_1[$Max_Kol_Zap] = $row[7];
				$Max_Make_Zapis_HM_HM_1[$Max_Kol_Zap] = str_pad($row[2], 2, '0', STR_PAD_LEFT).':'.str_pad($row[3], 2, '0', STR_PAD_LEFT).' - '.str_pad($row[4], 2, '0', STR_PAD_LEFT).':'.str_pad($row[5], 2, '0', STR_PAD_LEFT);
			}
		if ($Max_Kol_Zap>0)
			{
				echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
				echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
				echo '     <TD WIDTH=100% colspan=3 ALIGN="Center" CLASS="page_INPUT_mid">'.Max_T4S('Вы записаны на прием').'</TD>'.PHP_EOL;
				echo '    </TR>'.PHP_EOL;
				for ($i_LK_Zapis = 1; $i_LK_Zapis <= $Max_Kol_Zap; $i_LK_Zapis++) 
					{
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Отделение').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.Max_T4S($Max_Make_Zapis_Podr_1[$i_LK_Zapis]).'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Дата и время записи').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$Max_Make_Zapis_Date_Zapis_1[$i_LK_Zapis].' '.$Max_Make_Zapis_HM_HM_1[$i_LK_Zapis].'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Лицевой счет ').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['LS'].'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Фамилия Имя Отчество ').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['FIO'].'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Адрес ').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['ADRESS'].'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Телефон').'</TD>'.PHP_EOL;
						echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
						echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['PHONE_NUMBER'].'</TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
						echo '    </TR>'.PHP_EOL;
						echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
						echo '     <TD WIDTH=100% colspan=3 ALIGN="Center" CLASS="page_INPUT_mid"><INPUT type="BUTTON" name="Cancel_Zapis_Save_Button" value="'.Max_T4S('Отменить запись на прием').'" CLASS="page_INPUT_mid" STYLE="color: #0000FF; width: 100%;" onclick="Max_Cancel_Zapis('.$Max_Make_Zapis_ID_Zapis_1[$i_LK_Zapis].','.chr(39).$Max_Make_Zapis_Date_Zapis_1[$i_LK_Zapis].chr(39).')"></TD>'.PHP_EOL;
						echo '    </TR>'.PHP_EOL;
					}
				echo '   </TABLE>'.PHP_EOL;
			}
		else
			{
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Выберите отделение ...').'</TD>'.PHP_EOL; 
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_LK_Podrs" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(1)">';
		if (isset($_POST['Spis_LK_Podrs'])) $Spis_LK_Podrs = $_POST['Spis_LK_Podrs']; 
		for ($i_LK_Podrs = 1; $i_LK_Podrs <= $Max_LK_Podr_Count; $i_LK_Podrs++) 
			{
				if ($i_LK_Podrs == 1) {if (!isset($_POST['Spis_LK_Podrs'])) $Spis_LK_Podrs = $Max_LK_Podr_ID[$i_LK_Podrs];}
				echo '<OPTION VALUE="'.$Max_LK_Podr_ID[$i_LK_Podrs].'"';if ($Spis_LK_Podrs == $Max_LK_Podr_ID[$i_LK_Podrs]) echo ' selected';echo '>'.Max_T4S($Max_LK_Podr_Name[$i_LK_Podrs]).'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=2 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		$Max_Make_Zapis_Count = 0;
		$Max_Date_Zapis_Count = 0;
		$sql = 'select z.id, (select * from Max_date_to_Char(z.date_zapis)) date_zapis, r.hour_beg, r.min_beg, r.hour_end, r.min_end, z.date_zapis from ZZ_DAY_ZAP_'.substr($Date_Today,6,4).'_'.substr($Date_Today,3,2).' z, Rezh_rab r, Rezh_rab_dop rd
				where z.date_zapis>'.chr(39).$Date_Today.chr(39).' and z.id_user_add is null and z.id_user_block is null and z.id_user_del is null and z.is_zap=0
				and z.id_Rezh_rab=r.id and r.id_window in (select distinct wd.id_window from windows_dop wd where wd.id_podr='.$Spis_LK_Podrs.' and wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null))
				and r.id=rd.id_Rezh_rab and rd.id_type_zapis=1 and rd.id_user_replace is null and rd.dt_beg<=z.date_zapis  and (rd.dt_end>z.date_zapis or rd.dt_end is null) 
			    union all
			    select z.id, (select * from Max_date_to_Char(z.date_zapis)) date_zapis, r.hour_beg, r.min_beg, r.hour_end, r.min_end, z.date_zapis from ZZ_DAY_ZAP_'.substr($Date_NextMonth,6,4).'_'.substr($Date_NextMonth,3,2).' z, Rezh_rab r, Rezh_rab_dop rd
				where z.date_zapis>'.chr(39).$Date_Today.chr(39).' and z.id_user_add is null and z.id_user_block is null and z.id_user_del is null and z.is_zap=0
				and z.id_Rezh_rab=r.id and r.id_window in (select distinct wd.id_window from windows_dop wd where wd.id_podr='.$Spis_LK_Podrs.' and wd.id_user_replace is null and wd.dt_beg<=z.date_zapis  and (wd.dt_end>z.date_zapis or wd.dt_end is null))
				and r.id=rd.id_Rezh_rab and rd.id_type_zapis=1 and rd.id_user_replace is null and rd.dt_beg<=z.date_zapis  and (rd.dt_end>z.date_zapis or rd.dt_end is null) 
			   order by 7, 3, 4
			';
		//echo $sql;
		$rc = ibase_query($dbh_zapis, $sql);
		while ($row = ibase_fetch_row($rc)) 
			{
		    		$Max_Make_Zapis_Count = $Max_Make_Zapis_Count + 1;
				$Max_Make_Zapis_ID_Zapis[$Max_Make_Zapis_Count] = $row[0];
				$Max_Make_Zapis_Date_Zapis[$Max_Make_Zapis_Count] = $row[1];
				$Max_Make_Zapis_HB[$Max_Make_Zapis_Count] = $row[2];
				$Max_Make_Zapis_MB[$Max_Make_Zapis_Count] = $row[3];
				$Max_Make_Zapis_HE[$Max_Make_Zapis_Count] = $row[4];
				$Max_Make_Zapis_ME[$Max_Make_Zapis_Count] = $row[5];
				$Max_Make_Zapis_HMHM[$Max_Make_Zapis_Count] = $row[2]*1000000 + $row[3]*10000 + $row[4]*100 + $row[5];
				$Max_Make_Zapis_HM_HM[$Max_Make_Zapis_Count] = str_pad($row[2], 2, '0', STR_PAD_LEFT).':'.str_pad($row[3], 2, '0', STR_PAD_LEFT).' - '.str_pad($row[4], 2, '0', STR_PAD_LEFT).':'.str_pad($row[5], 2, '0', STR_PAD_LEFT);
				$Max_Date_New  = 1;
				for ($i_LK_Dates = 1; $i_LK_Dates <= $Max_Date_Zapis_Count; $i_LK_Dates++) if ($Max_Date_Zapiss[$i_LK_Dates] == $row[1]) $Max_Date_New  = 0;
				if ($Max_Date_New  == 1) {$Max_Date_Zapis_Count = $Max_Date_Zapis_Count + 1;$Max_Date_Zapiss[$Max_Date_Zapis_Count] = $row[1];}
			}
		ibase_free_result($rc);
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Выберите дату обслуживания ...').'</TD>'.PHP_EOL; 
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_LK_Dates" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(2)">';
		if (isset($_POST['Spis_LK_Dates'])) $Spis_LK_Dates = $_POST['Spis_LK_Dates']; 
		for ($i_LK_Dates = 1; $i_LK_Dates <= $Max_Date_Zapis_Count; $i_LK_Dates++) 
			{
				if ($i_LK_Dates == 1) {if (!isset($_POST['Spis_LK_Dates'])) $Spis_LK_Dates = $Max_Date_Zapiss[$i_LK_Dates];}
				echo '<OPTION VALUE="'.$Max_Date_Zapiss[$i_LK_Dates].'"';if ($Spis_LK_Dates == $Max_Date_Zapiss[$i_LK_Dates]) echo ' selected';echo '>'.Max_T4S($Max_Date_Zapiss[$i_LK_Dates]).'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		$Max_Time_Zapis_Count = 0;
		for ($i_LK_Zapiss = 1; $i_LK_Zapiss <= $Max_Make_Zapis_Count; $i_LK_Zapiss++) if ($Spis_LK_Dates == $Max_Make_Zapis_Date_Zapis[$i_LK_Zapiss])
			{
				$Max_Time_New  = 1;
				for ($i_LK_Times = 1; $i_LK_Times <= $Max_Time_Zapis_Count; $i_LK_Times++) if ($Max_Time_Zapiss_ID[$i_LK_Times] == $Max_Make_Zapis_HMHM[$i_LK_Zapiss]) $Max_Time_New  = 0;
				if ($Max_Time_New  == 1) {$Max_Time_Zapis_Count = $Max_Time_Zapis_Count + 1;$Max_Time_Zapiss_ID[$Max_Time_Zapis_Count] = $Max_Make_Zapis_HMHM[$i_LK_Zapiss];$Max_Time_Zapiss_Name[$Max_Time_Zapis_Count] = $Max_Make_Zapis_HM_HM[$i_LK_Zapiss]; $Max_Time_Zapiss_ID_Zapis[$Max_Time_Zapis_Count] = $Max_Make_Zapis_ID_Zapis[$i_LK_Zapiss];}
			}
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=2 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=50% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Выберите время обслуживания ...').'</TD>'.PHP_EOL; 
		echo '     <TD WIDTH=50% ALIGN="Center" CLASS="page_INPUT_mid">';
		echo '<div class="mybox" style="width: 100%;"><span class="myarrow"></span><SELECT NAME="Spis_LK_Times" CLASS="row_1_light_mid" STYLE="width: 100%;" onchange="SMAX(0)">';
		$Max_Id_Zapis_4Time = 0;
		if (isset($_POST['Spis_LK_Times'])) $Spis_LK_Times = $_POST['Spis_LK_Times']; 
		for ($i_LK_Times = 1; $i_LK_Times <= $Max_Time_Zapis_Count; $i_LK_Times++) 
			{
				if ($i_LK_Times == 1) {if (!isset($_POST['Spis_LK_Times'])) $Spis_LK_Times = $Max_Time_Zapiss_ID[$i_LK_Times];}
				echo '<OPTION VALUE="'.$Max_Time_Zapiss_ID[$i_LK_Times].'"';if ($Spis_LK_Times == $Max_Time_Zapiss_ID[$i_LK_Times]) {echo ' selected';$Max_Id_Zapis_4Time = $Max_Time_Zapiss_ID_Zapis[$i_LK_Times];}echo '>'.Max_T4S($Max_Time_Zapiss_Name[$i_LK_Times]).'</OPTION>';
			} 
		echo '</SELECT></div></TD>'.chr(13);
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=3 Style="text-align: center;background: #496791;color: #fff;font-size: 16pt;">'.Max_T4S('Прошу проверить данные ниже').'</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Лицевой счет ').'</TD>'.PHP_EOL;
		echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['LS'].'</TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Фамилия Имя Отчество ').'</TD>'.PHP_EOL;
		echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['FIO'].'</TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Адрес ').'</TD>'.PHP_EOL;
		echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['ADRESS'].'</TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Right" CLASS="page_INPUT_mid">'.Max_T4S('Телефон').'</TD>'.PHP_EOL;
		echo '     <TD WIDTH=2% ALIGN="Right" CLASS="page_INPUT_mid">&nbsp</TD>'.PHP_EOL;
		echo '     <TD WIDTH=49% ALIGN="Left" CLASS="page_INPUT_mid">'.$_SESSION['PHONE_NUMBER'].'</TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=3 Style="font-size: 16pt;">&nbsp</TD>'.PHP_EOL; 
		echo '    </TR>'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% colspan=3 ALIGN="Center" CLASS="page_INPUT_mid"><INPUT type="BUTTON" name="Save_Zapis_Button" value="'.Max_T4S('Записаться на прием').'" CLASS="page_INPUT_mid" STYLE="font-size: 16pt; color: #0000FF; width: 100%;" onclick="Max_Do_Zapis('.$Max_Id_Zapis_4Time.')"></TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
			}
		echo '   <TABLE WIDTH=100% CELLSPACING="0" CELLPADDING="0" BORDER="0" ALIGN="center" STYLE="margin-bottom: 5px;">'.PHP_EOL;
		echo '    <TR CLASS="row_1_light_mid" ALIGN="Center">'.PHP_EOL;
		echo '     <TD WIDTH=100% ALIGN="Center" CLASS="page_INPUT_mid">'.Max_T4S('<a href="https://lk.cr30.ru/personal"><strong>Вернуться в личный кабинет</strong></a>').'</TD>'.PHP_EOL;
		echo '    </TR>'.PHP_EOL;
		echo '   </TABLE>'.PHP_EOL;
		echo '  </FORM>'.PHP_EOL;
		echo '<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">'.PHP_EOL;
		if (isset($_POST['scr1'])) echo 'window.scroll(0,'.$_POST['scr1'].');'.PHP_EOL;
		echo 'function SMAX(in_val)'.PHP_EOL;
		echo '  {'.PHP_EOL;
		echo '    document.Form1.scr1.value=document.body.scrollTop;';
		echo '    document.Form1.SP.value=in_val;';
		echo '    document.Form1.submit();'.PHP_EOL;
		echo '  }'.PHP_EOL;
		echo 'function Max_Do_Zapis(in_val)'.PHP_EOL;
		echo '  {'.PHP_EOL;
		echo '    		document.Form1.IDZ.value=in_val;';
		echo '    		SMAX(3);'.PHP_EOL;
		echo '  }'.PHP_EOL;
		echo 'function Max_Cancel_Zapis(in_val, in_val2)'.PHP_EOL;
		echo '  {'.PHP_EOL;
		echo '    		document.Form1.IDZ.value = in_val;'.PHP_EOL;
		echo '    		document.Form1.DTZ.value = in_val2;'.PHP_EOL;
		echo '    		SMAX(4);'.PHP_EOL;
		echo '  }'.PHP_EOL;
		echo '</SCRIPT>'.PHP_EOL;
	}
?>
