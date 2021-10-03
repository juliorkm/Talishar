<?php

  function MONAbilityCost($cardID)
  {
    switch($cardID)
    {
      case "MON001": case "MON002": return 2;
      case "MON029": case "MON030": case "MON031": return 0;
      case "MON061": return 1;
      case "MON090": return 0;
      case "MON105": case "MON106": return 1;
      case "MON108": return 1;
      case "MON153": case "MON154": return 0;
      case "MON155": return 1;
      case "MON188": return 1;
      case "MON229": return 3;
      case "MON238": case "MON239": return 0;
      case "MON240": return 3;
      case "MON245": return 3;
      case "MON281": case "MON282": case "MON283": return 0;
      default: return 0;
    }
  }

  function MONAbilityType($cardID, $index=-1)
  {
    global $currentPlayer, $mainPlayer, $defPlayer;
    switch($cardID)
    {
      case "MON001": case "MON002": return "I";
      case "MON029": case "MON030": return "AR";
      case "MON031": return "AA";
      case "MON061": return "I";
      case "MON090": return "A";
      case "MON105": case "MON106": return "AA";
      case "MON108": return "A";
      case "MON153": case "MON154": return "A";
      case "MON155": return "AA";
      case "MON188": return "I";
      case "MON229": return "AA";
      case "MON230": return "A";
      case "MON238": return "I";
      case "MON239": case "MON240": return "A";
      case "MON245": return $currentPlayer == $mainPlayer ? "I" : "";
      case "MON281": case "MON282": case "MON283": return $currentPlayer == $defPlayer ? "I" : "";
      default: return "";
    }
  }

  function MONHasGoAgain($cardID)
  {
    global $defPlayer, $CS_ArcaneDamageTaken;
    switch($cardID)
    {
      case "MON034": return true;
      case "MON062": return true;
      case "MON081": case "MON082": case "MON083": return true;
      case "MON101": case "MON102": case "MON103": return true;
      case "MON109": return true;
      case "MON110": case "MON111": case "MON112": return true;
      case "MON113": case "MON114": case "MON115": return true;
      case "MON116": case "MON117": case "MON118": return true;
      case "MON157": return true;
      case "MON162": case "MON163": case "MON164": return true;
      case "MON165": case "MON166": case "MON167": return true;
      case "MON180": case "MON181": case "MON182":
        return GetClassState($defPlayer, $CS_ArcaneDamageTaken) > 0;
      case "MON193": return true;
      case "MON199": return count(GetSoul($defPlayer)) > 0;
      case "MON200": case "MON201": case "MON202": return true;
      case "MON212": case "MON213": case "MON214": return true;
      case "MON231": return true;
      case "MON260": case "MON261": case "MON262": return true;
      case "MON266": case "MON267": case "MON268": return true;
      case "MON269": case "MON270": case "MON271": return true;
      case "MON296": case "MON297": case "MON298": return true;
      case "MON299": case "MON300": case "MON301": return true;
      case "MON302": return true;
      default: return false;
    }
  }

  function MONAbilityHasGoAgain($cardID)
  {
    switch($cardID)
    {
      case "MON090": return true;
      case "MON108": return true;
      case "MON153": case "MON154": return true;
      case "MON230": return true;
      case "MON239": return true;
      default: return false;
    }
  }

  function MONEffectAttackModifier($cardID)
  {
    global $mainPlayer, $CS_NumNonAttackCards;
    switch($cardID)
    {
      case "MON034": return 1;
      case "MON081": return 3;
      case "MON082": return 2;
      case "MON083": return 1;
      case "MON108": return 1;
      case "MON109": return 2;
      case "MON110": return 3;
      case "MON111": return 2;
      case "MON112": return 1;
      case "MON113": return 3;
      case "MON114": return 2;
      case "MON115": return 1;
      case "MON116": return 4;
      case "MON117": return 3;
      case "MON118": return 2;
      case "MON165": case "MON166": case "MON167": return 1;
      case "MON168": case "MON169": case "MON170": return 1;
      case "MON174": case "MON175": case "MON176": return GetClassState($mainPlayer, $CS_NumNonAttackCards);
      case "MON193": return 1;
      case "MON200": return 3;
      case "MON201": return 2;
      case "MON202": return 1;
      case "MON239": return 1;
      case "MON260-1": case "MON261-1": case "MON262-1": return 2;
      case "MON263": case "MON264": case "MON265": return 3;
      case "MON269": case "MON270": case "MON271": return 1;
      case "MON296": return 3;
      case "MON297": return 2;
      case "MON298": return 1;
      case "MON299": return 3;
      case "MON300": return 2;
      case "MON301": return 1;
      default: return 0;
    }
  }

  function MONCombatEffectActive($cardID, $attackID)
  {
    switch($cardID)
    {
      case "MON034": return CardType($attackID) == "W";
      case "MON081": case "MON082": case "MON083": return CardType($attackID) == "AA";
      case "MON090": return CardClass == "ILLUSIONIST" && CardType($attackID) == "AA";
      case "MON108": return CardType($attackID) == "W";
      case "MON109": return CardSubtype($attackID) == "Axe";
      case "MON110": case "MON111": case "MON112": return CardType($attackID) == "W";
      case "MON113": case "MON114": case "MON115": return CardType($attackID) == "W";
      case "MON116": case "MON117": case "MON118": return true;
      case "MON153": case "MON154": return CardClass($attackID) == "RUNEBLADE" || CardTalent($attackID) == "SHADOW";
      case "MON165": return CardType($attackID) == "AA" && CardCost($attackID) <= 2;
      case "MON166": return CardType($attackID) == "AA" && CardCost($attackID) <= 1;
      case "MON167": return CardType($attackID) == "AA" && CardCost($attackID) == 0;
      case "MON168": case "MON169": case "MON170": return true;
      case "MON174": case "MON175": case "MON176": return true;
      case "MON193": return CardType($attackID) == "AA";
      case "MON195": case "MON196": case "MON197": return true;
      case "MON200": case "MON201": case "MON202": return CardType($attackID) == "AA";
      case "MON239": return CardType($attackID) == "AA" && AttackValue($attackID) <= 3;
      case "MON260-1": case "MON260-2": return CardType($attackID) == "AA" && CardCost($attackID) <= 2;
      case "MON261-1": case "MON261-2": return CardType($attackID) == "AA" && CardCost($attackID) <= 1;
      case "MON262-1": case "MON262-2": return CardType($attackID) == "AA" && CardCost($attackID) <= 0;
      case "MON263": case "MON264": case "MON265": return true;
      case "MON269": case "MON270": case "MON271": return CardType($attackID) == "W";
      case "MON278": case "MON279": case "MON280": return true;
      case "MON296": case "MON297": case "MON298": return CardType($attackID) == "AA" && AttackValue($attackID) <= 3;
      case "MON299": case "MON300": case "MON301": return CardType($attackID) == "AA";
      default: return false;
    }
  }

  function MONCardTalent($cardID)
  {
    $number = intval(substr($cardID, 3));
    if($number <= 87) return "LIGHT";
    else if($number >= 119 && $number <= 220) return "SHADOW";
    else return "NONE";
  }

?>

