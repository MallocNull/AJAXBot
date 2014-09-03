using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace bot.responses {
    class poker {
        class PokerContext {
            class Dice {
                static private Random rng = new Random();
                static public int[] Roll() {
                    return new int[] { rng.Next(6) + 1, rng.Next(6) + 1, rng.Next(6) + 1
                                     , rng.Next(6) + 1, rng.Next(6) + 1};
                }
            }

            public int playerMoney;
            public int botMoney;

            public int currentBet;

            public PokerContext() {
                playerMoney = botMoney = 1000;
                currentBet = 1;
            }

            public int[] CheckHand(int[] hand) {
                int i;
                for(i = 1; i < 5; i++)
                    if(hand[i - 1] != hand[i] - 1) break;
                if(i == 5) return new int[] { 4, hand.Sum() };

                Dictionary<int, int> groupList = new Dictionary<int, int>();
                for(i = 1; i < 5; i++) {
                    if(hand[i] == hand[i - 1]) {
                        if(groupList.ContainsKey(hand[i]))
                            groupList[hand[i]]++;
                        else
                            groupList.Add(hand[i], 2);
                    }
                }

                if(groupList.Keys.Count == 1) {
                    switch(groupList.Values.ElementAt(0)) {
                        case 2:
                            return new int[] { 1, groupList.Keys.ElementAt(0) };
                        case 3:
                            return new int[] { 3, groupList.Keys.ElementAt(0) };
                        case 4:
                            return new int[] { 6, groupList.Keys.ElementAt(0) };
                        case 5:
                            return new int[] { 7, groupList.Keys.ElementAt(0) };
                    }
                } else if(groupList.Keys.Count == 2) {
                    if(groupList.Values.ElementAt(0) == 2 && groupList.Values.ElementAt(1) == 2)
                        return new int[] { 2, groupList.Keys.ElementAt(0) + groupList.Keys.ElementAt(1) };
                    else
                        return new int[] { 5, groupList.Keys.ElementAt(0) + groupList.Keys.ElementAt(1) };
                }

                return new int[]{0, hand[4]};
            }

            public string GetDieFaceFromInt(int face) {
                switch(face) {
                    case 1:
                        return "9";
                    case 2:
                        return "10";
                    case 3:
                        return "J";
                    case 4:
                        return "Q";
                    case 5:
                        return "K";
                    case 6:
                        return "A";
                }

                return "WHAT";
            }

            public string GetDiceRollAsString(int[] hand) {
                string tmp = "";
                for(int i = 0; i < 5; i++)
                    tmp += GetDieFaceFromInt(hand[i]) + ((i == 4) ? "" : " ");
                return tmp;
            }

            public string GetHandNameFromInt(int handresult) {
                switch(handresult) {
                    case 0:
                        return "High Card";
                    case 1:
                        return "Pair";
                    case 2:
                        return "Two Pair";
                    case 3:
                        return "Three of a Kind";
                    case 4:
                        return "Straight";
                    case 5:
                        return "Full House";
                    case 6:
                        return "Four of a Kind";
                    case 7:
                        return "Five of a Kind";
                }

                return "WHAT";
            }

            public void PlayerWins(string playerName) {
                Chat.sendMessage(playerName + " wins! " + _G.propername + " loses " + pokerContexts[playerName].currentBet);
                pokerContexts[playerName].botMoney -= pokerContexts[playerName].currentBet;
                pokerContexts[playerName].playerMoney += pokerContexts[playerName].currentBet;
            }

            public void BotWins(string playerName) {
                Chat.sendMessage(_G.propername + " wins! " + playerName + " loses " + pokerContexts[playerName].currentBet);
                pokerContexts[playerName].playerMoney -= pokerContexts[playerName].currentBet;
                pokerContexts[playerName].botMoney += pokerContexts[playerName].currentBet;
            }

            public void Tie(string playerName) {
                Chat.sendMessage(_G.propername + " tied with " + playerName);
            }

            public void PerformTurn(string playerName) {
                int[] yourHand = Dice.Roll();
                Array.Sort(yourHand);
                int[] yourResults = CheckHand(yourHand);
                Chat.sendMessage(playerName + " rolled " + GetDiceRollAsString(yourHand) + " (" + GetHandNameFromInt(yourResults[0]) + ")");

                int[] botHand = Dice.Roll();
                Array.Sort(botHand);
                int[] botResults = CheckHand(botHand);
                Chat.sendMessage(_G.propername + " rolled " + GetDiceRollAsString(botHand) + " (" + GetHandNameFromInt(botResults[0]) + ")");

                if(botResults[0] > yourResults[0])
                    BotWins(playerName);
                else if(yourResults[0] > botResults[0])
                    PlayerWins(playerName);
                else {
                    if(botResults[1] > yourResults[1])
                        BotWins(playerName);
                    else if(yourResults[1] > botResults[1])
                        PlayerWins(playerName);
                    else
                        Tie(playerName);
                }
            }

            public void Raise(int amount) {
                if(amount > 0)
                    currentBet += amount;
                Chat.sendMessage("Bet raised to " + currentBet);
            }

            public void Bet(int amount) {
                if(amount > 0)
                    currentBet = amount;
                Chat.sendMessage("Bet set at " + amount);
            }
        }

        static private Random rng = new Random();
        static private Dictionary<string, PokerContext> pokerContexts = new Dictionary<string,PokerContext>();

        static public string[] getInfo() {
            return new string[] {typeof(poker).Name, "Dice Poker",
                "A sample module that allows a user to play against an AI in dice poker. Takes no arguments. Should accept the commands !register, !bet, !raise, !roll, !check, !help"};
        }

        static public void performOperation(string parameters, Message msg) {
            try {
                if(msg.msg.ToLower().Trim() == "!register") {
                    if(!pokerContexts.ContainsKey(msg.name)) {
                        pokerContexts.Add(msg.name, new PokerContext());
                        Chat.sendMessage("You are now registered, and have $1000. The default bet is $1. You can !raise #, !bet #, !roll, or !check the standings.");
                    } else {
                        Chat.sendMessage("You are already registered.");
                    }
                } else {
                    if(pokerContexts.ContainsKey(msg.name)) {
                        PokerContext ctx = pokerContexts[msg.name];
                        switch(msg.msg.ToLower().Trim()) {
                            case "!help":
                                Chat.sendMessage("You can !raise #, !bet #, !roll, or !check the standings.");
                                break;
                            case "!check":
                                Chat.sendMessage(msg.name + " has $" + ctx.playerMoney + ". " + _G.propername + " has $" + ctx.botMoney + ". Both sides are currently betting $" + ctx.currentBet);
                                break;
                            case "!roll":
                                ctx.PerformTurn(msg.name);
                                break;
                        }

                        if(msg.msg.ToLower().Trim().StartsWith("!bet")) {
                            try {
                                ctx.Bet(Int32.Parse(msg.msg.Substring(msg.msg.IndexOf(' ') + 1)));
                            } catch(Exception e) { }
                        }

                        if(msg.msg.ToLower().Trim().StartsWith("!raise")) {
                            try {
                                ctx.Raise(Int32.Parse(msg.msg.Substring(msg.msg.IndexOf(' ') + 1)));
                            } catch(Exception e) { }
                        }
                    } else {
                        Chat.sendMessage("You are not registered. Register with !register.");
                    }
                }
            } catch(Exception e) {
                Console.WriteLine(e.Message + " " + e.StackTrace);
            }
        }
    }
}
