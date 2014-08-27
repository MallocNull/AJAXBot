using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Reflection;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium;
using OpenQA.Selenium.Internal;
using OpenQA.Selenium.Support.UI;

namespace bot.responses {
    class sendmsg {
        static private Random rng = new Random();

        static public string[] getInfo() {
            return new string[] {typeof(sendmsg).Name, "Send Message",
                "Sends a message or messages. New lines seperate strings, causing the bot to pick one randomly. To break up a string into multiple messages, seperate clauses with the accent grave (`). {0} is replaced by the message sender's name. {1} is replaced by a random user that's logged into the chat."};
        }

        static public void performOperation(string parameters, Message msg) {
            string[] parts = parameters.Split('\n');
            string selected = (parts.Length>1)?parts[rng.Next(parts.Length)]:parts[0];
            parts = selected.Split('`');

            List<string> onlineUsers = new List<string>();
            foreach(IWebElement elem in _G.driver.FindElement(By.Id("onlineList")).FindElements(By.XPath("*")))
                onlineUsers.Add(elem.Text);

            foreach(string part in parts)
                Chat.sendMessage(String.Format(part,msg.name,onlineUsers[rng.Next(0,onlineUsers.Count)]));
        }
    }
}