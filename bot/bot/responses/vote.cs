using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium;
using OpenQA.Selenium.Internal;
using OpenQA.Selenium.Support.UI;

namespace bot.responses {
    class vote {
        static private Random rng = new Random();

        static public string[] getInfo() {
            return new string[] {typeof(vote).Name, "Call Vote",
                "Calls a vote in the chat."};
        }

        static private bool voteCalled = false;
        static private DateTime voteStarted = DateTime.Now;
        static private List<string> voters = new List<string>();
        static private string param = "";

        static public void performOperation(string parameters, Message msg) {
            string[] pars = parameters.Split('\n');

            if(msg.msg.ToLower() != "!vote") {
                if(!voteCalled || (DateTime.Now - voteStarted).TotalSeconds > 120) {
                    voters.Clear();
                    voteCalled = true;
                    param = msg.msg.Split(' ')[1];
                    voteStarted = DateTime.Now;
                    Chat.sendMessage(pars[0]);
                }
            } else {
                Console.WriteLine((DateTime.Now - voteStarted).TotalSeconds);
                if(voteCalled && (DateTime.Now - voteStarted).TotalSeconds < 120 && !voters.Contains(msg.name)) {
                    voters.Add(msg.name);
                    int people = _G.driver.FindElement(By.Id("onlineList")).FindElements(By.XPath("*")).Count;
                    if(voters.Count >= people / 3) {
                        Chat.sendMessage(String.Format(pars[1], param));
                        Chat.sendMessage(String.Format(pars[2], param));
                    } else
                        Chat.sendMessage(voters.Count + "/" + people / 3 + " votes recorded.");
                } else {
                    if(voteCalled && (DateTime.Now - voteStarted).TotalSeconds > 120) {
                        Chat.sendMessage("Vote has expired!");
                        voteCalled = false;
                    }
                }
            }
        }
    }
}
