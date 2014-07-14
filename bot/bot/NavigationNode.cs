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

namespace bot {
    class NavigationNode {
        public enum findTypes {
            GOTOURL, BYLINKTEXT,
            BYID, BYNAME,
            BYCLASS, BYTAG, 
        };

        public enum actionTypes {
            CLICK, TYPE,
            TEXTSELECT,
            INDEXSELECT
        };

        findTypes findtype;
        string locator;
        actionTypes action;
        string parameter;

        public NavigationNode() { }

        public NavigationNode(int ft, string l, int a, string p) {
            findtype = (findTypes)ft;
            locator = l;
            action = (actionTypes)a;
            parameter = p;
        }

        public bool performNavigation(FirefoxDriver d) {
            IWebElement e = null;
            try {
                switch(findtype) {
                    case findTypes.GOTOURL:
                        d.Navigate().GoToUrl(locator);
                        break;
                    case findTypes.BYLINKTEXT:
                        e = (new WebDriverWait(d, new TimeSpan(0, 0, 900)).Until(ExpectedConditions.ElementExists(By.LinkText(locator))));
                        break;
                    case findTypes.BYID:
                        e = (new WebDriverWait(d, new TimeSpan(0, 0, 900)).Until(ExpectedConditions.ElementExists(By.Id(locator))));
                        break;
                    case findTypes.BYNAME:
                        e = (new WebDriverWait(d, new TimeSpan(0, 0, 900)).Until(ExpectedConditions.ElementExists(By.Name(locator))));
                        break;
                    case findTypes.BYCLASS:
                        e = (new WebDriverWait(d, new TimeSpan(0, 0, 900)).Until(ExpectedConditions.ElementExists(By.ClassName(locator))));
                        break;
                    case findTypes.BYTAG:
                        e = (new WebDriverWait(d, new TimeSpan(0, 0, 900)).Until(ExpectedConditions.ElementExists(By.TagName(locator))));
                        break;
                }
            } catch(Exception err) {
                _G.logError("Failed to find element "+ locator +" "+ findtype.ToString());
                return false;
            }

            if(e != null) {
                switch(action) {
                    case actionTypes.CLICK:
                        e.Click();
                        break;
                    case actionTypes.TYPE:
                        e.SendKeys(parameter);
                        break;
                    case actionTypes.TEXTSELECT:
                        (new SelectElement(e)).SelectByText(parameter);
                        break;
                    case actionTypes.INDEXSELECT:
                        (new SelectElement(e)).SelectByIndex(Int32.Parse(parameter));
                        break;
                }
            }

            return true;
        }
    }
}
