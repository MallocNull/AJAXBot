using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium;
using OpenQA.Selenium.Internal;
using OpenQA.Selenium.Support.UI;
using SuperSocket;
using SuperSocket.SocketBase;
using SuperWebSocket;
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



        public NavigationNode() { }

        public void performNavigation(FirefoxDriver d) {

        }
    }
}
