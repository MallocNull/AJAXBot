using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium;
using OpenQA.Selenium.Internal;
using OpenQA.Selenium.Support.UI;
using MySql.Data.MySqlClient;
using System.Threading;
using System.Reflection;

namespace bot {
    class Chat {
        static int messageDivSize;
        static int currentMessage;

        public static void reloadContext(FirefoxDriver d) {
            List<IWebElement> chatdata = d.FindElement(By.Id("chatList")).FindElements(By.TagName("div")).ToList();
            messageDivSize = chatdata.Count;
            foreach(IWebElement we in chatdata) {
                if(Int32.Parse(we.GetAttribute("id").Substring(we.GetAttribute("id").LastIndexOf('_') + 1)) > currentMessage)
                    currentMessage = Int32.Parse(we.GetAttribute("id").Substring(we.GetAttribute("id").LastIndexOf('_') + 1));
            }
            if(d.FindElement(By.Id("audioButton")).GetAttribute("class").ToLower() == "button")
                d.FindElement(By.Id("audioButton")).Click();
        }

        public static void sendMessage(string text) {
            sendMessage(text, _G.driver);
        }

        public static void sendMessage(string text, FirefoxDriver d) {
            // TODO protection context corrections
            if(isChatting(d)) {
                d.FindElement(By.Id("inputField")).SendKeys(text);
                d.FindElement(By.Id("submitButton")).Click();
                try { Thread.Sleep(500); } catch(Exception e) { }
            }
        }

        public static bool isChatting(FirefoxDriver d) {
            try {
                DateTime startCheck = DateTime.Now;
                while(d.FindElement(By.Id("statusIconContainer")).GetAttribute("class") == "statusContainerAlert") {
                    if((DateTime.Now - startCheck).TotalSeconds > 30)
                        return false;
                }
            } catch(Exception err) {
                return false;
            }
            return true;
        }

        public static Message waitForNewMessage(FirefoxDriver d) {
            // TODO protection context addition
            int temp = currentMessage;

            if(messageDivSize >= 50) {
                DateTime start = DateTime.Now;
                d.Navigate().Refresh();
                try {
                    (new WebDriverWait(d, new TimeSpan(0, 0, 60))).Until(ExpectedConditions.ElementExists(By.Id("inputField")));
                } catch(Exception e) { }
                reloadContext(d);
            }

            Console.WriteLine("Waiting for msg id greater than " + currentMessage);

            bool ischat = true;
            while(ischat = isChatting(d)) {
                try {
                    List<IWebElement> chatdata = d.FindElement(By.Id("chatList")).FindElements(By.TagName("div")).ToList();
                    bool found = false;
                    foreach(IWebElement we in chatdata) {
                        int nodeID = Int32.Parse(we.GetAttribute("id").Substring(we.GetAttribute("id").LastIndexOf('_') + 1));
                        if(nodeID > currentMessage) {
                            currentMessage = nodeID;
                            found = true;
                            break;
                        }
                    }
                    if(found) break;
                    try { Thread.Sleep(1000); } catch(Exception e) { }
                } catch(Exception e) { }
            }

            if(ischat) {
                messageDivSize++;
                String msg;
                while(true) {
                    try {
                        msg = d.FindElement(By.Id("ajaxChat_m_" + (currentMessage))).Text.Substring(11);
                        break;
                    } catch(Exception err) { }
                }
                Console.WriteLine(msg);

                try {
                    return new Message(msg.Substring(0, msg.IndexOf(':')), msg.Substring(msg.IndexOf(':') + 2));
                } catch(Exception err) {
                    return new Message("","");
                }
            } else return null;
        }
    }
}
