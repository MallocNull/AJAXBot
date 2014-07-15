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
    class Bot {
        static string tmp;

        static List<NavigationNode> navigationList = new List<NavigationNode>();
        static List<Response> responseList = new List<Response>();

        public static void loadNavigationList() {
            navigationList = new List<NavigationNode>();
            var r = Query.Reader("SELECT * FROM `navigate`", _G.spawnNewConnection());
            while(r.Read()) {
                navigationList.Add(new NavigationNode(
                    r.GetInt32("findtype"),
                    r.GetString("locator"),
                    r.GetInt32("action"),
                    r.GetString("parameter")));
            }
            r.Close();
        }

        public static void loadResponseList() {
            responseList = new List<Response>();
            var r = Query.Reader("SELECT * FROM `responses`", _G.spawnNewConnection());
            while(r.Read()) {
                responseList.Add(new Response(
                    r.GetString("conditions"),
                    r.GetInt32("respid"),
                    r.GetString("parameters"),
                    r.GetInt32("cooldown")));
            }
            r.Close();
        }

        static void Main(string[] args) {
            _G.loadDatabaseInfo();
            _G.conn = _G.spawnNewConnection();
            _G.errconn = _G.spawnNewConnection();

            _G.loadConfig();
            loadResponseList();

            tmp = "DELETE FROM resptypes WHERE ";
            foreach(Type t in ResponseCaller.getResponseTypes()) {
                string[] typeInfo = (string[])t.GetMethod("getInfo").Invoke(null, null);
                tmp += "name<>'" + typeInfo[0] + "' AND ";
                if((Int64)Query.Scalar("SELECT COUNT(*) FROM `resptypes` WHERE `name`='" + typeInfo[0] + "'", _G.conn) > 0)
                    Query.Quiet("UPDATE `resptypes` SET friendlyname='" + Sanitizer.Sanitize(typeInfo[1]) + "',description='" + Sanitizer.Sanitize(typeInfo[2]) + "' WHERE name='" + typeInfo[0] + "'", _G.conn);
                else
                    Query.Quiet("INSERT INTO `resptypes` (name,friendlyname,description) VALUES ('" + typeInfo[0] + "','" + Sanitizer.Sanitize(typeInfo[1]) + "','" + Sanitizer.Sanitize(typeInfo[2]) + "')", _G.conn);
            }
            tmp = tmp.Substring(0, tmp.Length - 5);
            Query.Quiet(tmp, _G.conn);

            tmp = "DELETE FROM conditions WHERE ";
            foreach(Type t in ConditionChecker.getConditions()) {
                string[] typeInfo = (string[])t.GetMethod("getInfo").Invoke(null, null);
                tmp += "name<>'" + typeInfo[0] + "' AND ";
                if((Int64)Query.Scalar("SELECT COUNT(*) FROM `conditions` WHERE `name`='" + typeInfo[0] + "'", _G.conn) > 0)
                    Query.Quiet("UPDATE `conditions` SET friendlyname='" + Sanitizer.Sanitize(typeInfo[1]) + "' WHERE name='" + typeInfo[0] + "'", _G.conn);
                else
                    Query.Quiet("INSERT INTO `conditions` (name,friendlyname) VALUES ('" + typeInfo[0] + "','" + Sanitizer.Sanitize(typeInfo[1]) + "')", _G.conn);
            }
            tmp = tmp.Substring(0, tmp.Length - 5);
            Query.Quiet(tmp, _G.conn);

            _G.driver = new FirefoxDriver();
            foreach(NavigationNode node in navigationList)
                node.performNavigation(_G.driver);
            try {
                (new WebDriverWait(_G.driver, new TimeSpan(0, 0, 300))).Until(ExpectedConditions.ElementExists(By.Id("inputField")));
            } catch(Exception e) {
                _G.criticalError("Navigation to chat failed! Fix instructions.", true);
            }

            _G.startThread(Pulse.pulseThread);

            Console.WriteLine(_G.propername +" has started successfully.");

            while(true) ;
        }
    }
}
