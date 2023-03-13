<?php

  error_reporting(E_ALL);

  //We should always have a player ID as a URL parameter
  $gameName=$_GET["gameName"];
  $playerID=$_GET["playerID"];

  //We should also have some information on the type of command
  $mode = $_GET["mode"];
  $buttonInput = isset($_GET["buttonInput"]) ? $_GET["buttonInput"] : "";//The player that is the target of the command - e.g. for changing health total
  $cardID = isset($_GET["cardID"]) ? $_GET["cardID"] : "";
  $chkCount = isset($_GET["chkCount"]) ? $_GET["chkCount"] : 0;
  $chkInput = [];
  for($i=0; $i<$chkCount; ++$i)
  {
    $chk = isset($_GET[("chk" . $i)]) ? $_GET[("chk" . $i)] : "";
    if($chk != "") array_push($chkInput, $chk);
  }

  //First we need to parse the game state from the file
  include "ZoneGetters.php";
  include "ParseGamestate.php";
  include "../HostFiles/Redirector.php";
  include "DecisionQueue.php";
  include "../WriteLog.php";
  include "../Libraries/UILibraries2.php";
  include "../CardDictionary.php";
  $makeCheckpoint = 0;

  ProcessCommand($playerID, $mode, $cardID, $buttonInput);

  //Now we can process the command
  function ProcessCommand($playerID, $mode, $cardID, $buttonInput)
  {
    switch($mode)
    {
      case 1:{ //CHOOSECARD OR REMOVEDECKCARD
        $myDQ = &GetZone($playerID, "DecisionQueue");
          $options = explode(",", $myDQ[1]);
          $found = -1;
          for($i=0; $i<count($options) && $found == -1; ++$i)
          {
            if($cardID == $options[$i]) $found = $i;
          }
          if($found >= 0) {
            //Player actually has the card, now do the effect
            //First remove it from their hand
            unset($options[$found]);
            $options = array_values($options);
          }
          if($myDQ[0] == "CHOOSECARD"){ //If we're ADDING cards
              if(CardType($cardID) == "E" || CardType($cardID) == "W")
             {
                $char = &GetZone($playerID, "Character");
                array_push($char, $cardID);
              }
              else {
                $deck = &GetZone($playerID, "Deck");
                array_push($deck, $cardID);
              }
              WriteLog("You added " . CardLink($cardID, $cardID) . " to your deck.");
          }
          if($myDQ[0] == "REMOVEDECKCARD"){ //If we're REMOVING cards
              $deck = &GetZone($playerID, "Deck");
              for($i=0; $i<count($deck)-1; ++$i)
              {
                if($deck[$i] == $cardID)
                {
                  unset($deck[$i]);
                  $deck = array_values($deck);
                  continue;
                }
              }
              WriteLog("You removed " . CardLink($cardID, $cardID) . " from your deck.");
          }
          if($myDQ[0] == "SHOP"){
            //WriteLog($cardID);
            $encounter = &GetZone($playerID, "Encounter");
            $cost = getShopCost($cardID);
            //WriteLog("cost: " . $cost . ", total: " . $encounter["Gold"]);
            if($cardID == "CardBack")
            {
              $newShop = $myDQ[1];
              ClearPhase($playerID); //Clear the screen and keep going
              ContinueDecisionQueue($playerID, "");
              PrependDecisionQueue("SHOP", $playerID, $newShop);
              break;
            }
            else if($encounter["Gold"] < $cost)
            {
              $newShop = $myDQ[1];
              WriteLog("You cannot afford to buy " . CardLink($cardID, $cardID) . ".");
              ClearPhase($playerID); //Clear the screen and keep going
              ContinueDecisionQueue($playerID, "");
              PrependDecisionQueue("SHOP", $playerID, $newShop);
              break;
            }
            else
            {
              if(CardType($cardID) == "E" || CardType($cardID) == "W")
              {
                $char = &GetZone($playerID, "Character");
                array_push($char, $cardID);
              }
              else {
                $deck = &GetZone($playerID, "Deck");
                array_push($deck, $cardID);
              }
              WriteLog("You spent " . $cost . " gold and added " . CardLink($cardID, $cardID) . " to your deck.");
              $encounter["Gold"] -= $cost;
              $newShop = "";
              for($j=0;$j<count($options);++$j){
                if($j != 0) $newShop.=",";
                $newShop.=$options[$j];
              }
              $newShop.=",CardBack";
              //WriteLog($newShop);
              ClearPhase($playerID); //Clear the screen and keep going
              ContinueDecisionQueue($playerID, "");
              PrependDecisionQueue("SHOP", $playerID, $newShop);
              break;
            }
          }
          ClearPhase($playerID); //Clear the screen and keep going
          ContinueDecisionQueue($playerID, "");
          break;
      }

      case 2: //BUTTONINPUT
        $myDQ = &GetZone($playerID, "DecisionQueue");
        if($myDQ[0] == "CHOOSECARD"){
          if($buttonInput == "Reroll")
          {

          }
        }
        if($myDQ[0] == "SHOP"){
          $encounter = &GetZone($playerID, "Encounter");
          $health = &GetZone($playerID, "Health");
          WriteLog($buttonInput);
          WriteLog($encounter["Gold"]);
          WriteLog($encounter["shopHealCost"]);
          if($buttonInput == "shop_heal"){
            $health = &GetZone($playerID, "Health");
            $gain = (20 - $health[0] > 10 ? 10 : 20 - $health[0]);
            if($gain < 0) $gain = 0;
            if($gain = 0){
              WriteLog("You are already very health. You and the healer enjoy a polite conversation, but there's no need to hire them.");
            }
            else if($encounter["Gold"] <= $encounter["shopHealCost"]){
              WriteLog("The local healer patches your wounds. You feel better prepared for your journey ahead! You heal $gain health.");
              $health[0] += $gain;
              $encounter["shopHealCost"] += 1;
            }
            else{
              WriteLog("You can't afford the services of a healer. You will have to tend to your wounds another time.");
              // "You don't have enough gold for that."
            }
          }
          else if($buttonInput == "shop_remove"){

          }
        }
        else{
          ClearPhase($playerID);
          ContinueDecisionQueue($playerID, $buttonInput);
        }
        break;
      case 99: //Pass
        PassInput();
        break;
      case 10000:
        RevertGamestate();
        $skipWriteGamestate = true;
        WriteLog("Player " . $playerID . " undid their last action.");
        break;
      case 10001:
        RevertGamestate("preBlockBackup.txt");
        $skipWriteGamestate = true;
        WriteLog("Player " . $playerID . " undid their blocks.");
        break;
      default:
        break;
      }
    }

  $changeMade = true;
  while($changeMade)
  {
    $changeMade = false;
    for($i=1; $i<=$numPlayers; ++$i)
    {
      if($i == $playerID) continue;
      $dq = &GetZone($i, "DecisionQueue");
      if(count($dq) > 0)
      {
        if($dq[0] == "CHOOSECARD")
        {
          $options = explode(",", $dq[1]);
          $pick = GetELEPick($options);
          ProcessCommand($i, 1, $pick);
          $changeMade = true;
        }
      }
    }
  }

  include "WriteGamestate.php";

  if($makeCheckpoint) MakeGamestateBackup();

  header("Location: " . $redirectPath . "/Roguelike/NextEncounter.php?gameName=$gameName&playerID=" . $playerID);

  exit;

?>
