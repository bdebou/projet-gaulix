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
	
	const CODE_EMO_HAPPY		= ':-)';
	const EMOTICON_HAPPY		= '<img src="./img/emoticons/happy.png" height="15px" alt="Content" title="Content" />';
	const CODE_EMO_UNHAPPY		= ':-(';
	const EMOTICON_UNHAPPY		= '<img src="./img/emoticons/unhappy.png" height="15px" alt="Pas content" title="Pas content" />';
	const CODE_EMO_WINK			= ';-)';
	const EMOTICON_WINK			= '<img src="./img/emoticons/wink.png" height="15px" alt="clin d\'oeil" title="clin d\'oeil" />';
	
	const CODE_IC_PIERRE		= '+respierre+';
	const ICONE_PIERRE			= '<img src="./img/icones/ic_ResPie.png" height="15px" alt="Ressource Pierre" title="Ressource Pierre" />';
	const CODE_IC_BOIS			= '+resbois+';
	const ICONE_BOIS			= '<img src="./img/icones/ic_ResBoi.png" height="15px" alt="Ressource Bois" title="Ressource Bois" />';
	const CODE_IC_OR			= '+resor+';
	const ICONE_OR				= '<img src="./img/icones/ic_ResOr.png" height="15px" alt="Ressource Or" title="Ressource Or" />';
	const CODE_IC_NOURRITURE	= '+resnourriture+';
	const ICONE_NOURRITURE		= '<img src="./img/icones/ic_ResNou.png" height="15px" alt="Ressource Nourriture" title="Ressource Nourriture" />';
	const CODE_IC_ATTAQUE		= '+att+';
	const ICONE_ATTAQUE			= '<img src="./img/icones/ic_attaque.png" height="15px" alt="Attaque" title="Attaque" />';
	const CODE_IC_DEFENSE		= '+def+';
	const ICONE_DEFENSE			= '<img src="./img/icones/ic_defense.png" height="15px" alt="Défense" title="Défense" />';
	const CODE_IC_DISTANCE		= '+dis+';
	const ICONE_DISTANCE		= '<img src="./img/icones/ic_distance.png" height="15px" alt="Distance" title="Distance" />';
	
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
		$this->text_chat = str_replace(array(self::CODE_EMO_WINK, ';)'), self::EMOTICON_WINK, $this->text_chat);
		$this->text_chat = str_replace(array(self::CODE_EMO_HAPPY, ':)'), self::EMOTICON_HAPPY, $this->text_chat);
		$this->text_chat = str_replace(array(self::CODE_EMO_UNHAPPY, ':('), self::EMOTICON_UNHAPPY, $this->text_chat);
		
		$this->text_chat = str_ireplace(self::CODE_IC_PIERRE, self::ICONE_PIERRE, $this->text_chat);
		$this->text_chat = str_ireplace(self::CODE_IC_BOIS, self::ICONE_BOIS, $this->text_chat);
		$this->text_chat = str_ireplace(self::CODE_IC_OR, self::ICONE_OR, $this->text_chat);
		$this->text_chat = str_ireplace(self::CODE_IC_NOURRITURE, self::ICONE_NOURRITURE, $this->text_chat);
		
		$this->text_chat = str_ireplace(self::CODE_IC_ATTAQUE, self::ICONE_ATTAQUE, $this->text_chat);
		$this->text_chat = str_ireplace(self::CODE_IC_DEFENSE, self::ICONE_DEFENSE, $this->text_chat);
		$this->text_chat = str_ireplace(self::CODE_IC_DISTANCE, self::ICONE_DISTANCE, $this->text_chat);
		
		return $this->text_chat;
	}
}
?>