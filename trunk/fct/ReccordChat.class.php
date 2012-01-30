<?php
class ReccordChat{
	
	Private $clan_chat;
	Private $member_chat;
	Private $date_chat;
	Private $text_chat;
	Private $id_chat;
	
	Private $DAYS_EN			= array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	Private $DAYS_FR			= array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	Private $MONTH_EN			= array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'Augustus', 'September', 'October', 'November', 'December');
	Private $MONTH_FR			= array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	
	const EMOTICON_HAPPY		= '<img src="./img/emoticons/happy.png" alt="Happy" title="Content" />';
	const EMOTICON_UNHAPPY		= '<img src="./img/emoticons/unhappy.png" alt="Unhappy" title="Pas content" />';
	const EMOTICON_WINK			= '<img src="./img/emoticons/wink.png" alt="Wink" title="clin d\'oeil" />';
	
	
	Public function __construct(array $data){
		Foreach($data as $key=>$value){
			switch($key){
				case 'member_chat':	$this->$key = strval($value);		break;
				case 'date_chat':	$this->$key = strtotime($value);	break;
				case 'text_chat':	$this->$key = strval($value);		break;
				case 'id_chat':		$this->$key = intval($value);		break;
			}
		}
	}
	
	//Les GETS
	//========
	public function GetID(){		return $this->id_chat;}
	Public function GetAuteur(){	return $this->member_chat;}
	Public function GetDate(){
			$temp = date('d/m/Y à H:i:s', $this->date_chat);
			$temp = str_replace($this->DAYS_EN, $this->DAYS_FR, $temp);
			$temp = str_replace($this->MONTH_EN, $this->MONTH_FR, $temp);
			return $temp;
	}
	Public function GetText(){
		$this->text_chat = str_replace(array("\r\n", "\n", "\r"), '<br />', $this->text_chat);
		$this->text_chat = str_replace(array(';-)', ';)'), self::EMOTICON_WINK, $this->text_chat);
		$this->text_chat = str_replace(array(':-)', ':)'), self::EMOTICON_HAPPY, $this->text_chat);
		$this->text_chat = str_replace(array(':-(', ':('), self::EMOTICON_UNHAPPY, $this->text_chat);
		return $this->text_chat;
	}
}
?>