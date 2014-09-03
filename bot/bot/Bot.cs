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
        static List<Response> indResponseList = new List<Response>();

        public static void loadNavigationList() {
            List<NavigationNode> tmpList = new List<NavigationNode>();
            var tmp = _G.spawnNewConnection();
            var r = Query.Reader("SELECT * FROM `navigate`", tmp);
            while(r.Read()) {
                tmpList.Add(new NavigationNode(
                    r.GetInt32("findtype"),
                    r.GetString("locator"),
                    r.GetInt32("action"),
                    r.GetString("parameter")));
            }
            r.Close();
            tmp.Close();
            navigationList = tmpList;
        }

        public static void loadResponseList() {
            List<Response> tmpListDep = new List<Response>();
            List<Response> tmpListInd = new List<Response>();
            var tmp = _G.spawnNewConnection();
            var r = Query.Reader("SELECT * FROM `responses`", tmp);
            while(r.Read()) {
                if(!r.GetBoolean("independent"))
                    tmpListDep.Add(new Response(
                        r.GetString("conditions"),
                        r.GetInt32("respid"),
                        r.GetString("parameters"),
                        r.GetInt32("cooldown")));
                else
                    tmpListInd.Add(new Response(
                        r.GetString("conditions"),
                        r.GetInt32("respid"),
                        r.GetString("parameters"),
                        r.GetInt32("cooldown")));
            }
            r.Close();
            tmp.Close();
            responseList = tmpListDep;
            indResponseList = tmpListInd;
        }

        static void Main(string[] args) {
            Console.Write("Loading database info ... ");
            _G.loadDatabaseInfo();
            Console.WriteLine("OK");

            Console.Write("Spawning database connections ... ");
            _G.conn = _G.spawnNewConnection();
            _G.errconn = _G.spawnNewConnection();
            Console.WriteLine("OK");

            Console.Write("Loading bot configuration ... ");
            _G.loadConfig();
            Console.WriteLine("OK");

            Console.Write("Loading response list ... ");
            loadResponseList();
            Console.WriteLine("OK");

            Console.Write("Updating response types on database ... ");
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
            Console.WriteLine("OK");

            Console.Write("Updating conditions on database ... ");
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
            Console.WriteLine("OK");

            Console.Write("Spawning web driver ... ");
            _G.driver = new FirefoxDriver();
            Console.WriteLine("OK");

            while(true) {
                try {
                    Console.Write("Navigating to chat ... ");
                    foreach(NavigationNode node in navigationList)
                        node.performNavigation(_G.driver);
                    try {
                        (new WebDriverWait(_G.driver, new TimeSpan(0, 0, 300))).Until(ExpectedConditions.ElementExists(By.Id("inputField")));
                    } catch(Exception e) {
                        _G.criticalError("Navigation to chat failed! Fix instructions.", true);
                    }
                    Console.WriteLine("OK");

                    _G.startThread(Pulse.pulseThread);

                    // TODO add autonomous thread start

                    Chat.reloadContext(_G.driver);

                    Console.WriteLine(_G.propername + " has started successfully.");

                    DateTime lastAction = new DateTime(0);

                    while(Chat.isChatting(_G.driver)) {
                        Message msg = Chat.waitForNewMessage(_G.driver);
                        if(msg == null) break;
                        /*if(msg.msg == "!dump") {
                            foreach(Response r in responseList)
                                Chat.sendMessage("IF "+ r.condstr +" THEN "+ r.responseType.Name);
                        }*/
                        if(msg.msg == "!update") {
                            Bot.loadResponseList();
                            Chat.sendMessage("response list updated");
                        }

                        foreach(Response response in indResponseList) {
                            if(response.triggerResponse(msg)) break;
                        }

                        foreach(Response response in responseList) {
                            if((DateTime.Now - lastAction).TotalSeconds >= _G.defaultCooldown) {
                                if(response.triggerResponse(msg)) {
                                    lastAction = DateTime.Now;
                                    break;
                                }
                            }
                        }
                    }

                    _G.stopAllThreads();

                    Console.WriteLine("Restarting bot ...");
                } catch(Exception err) {
                    _G.criticalError("Main thread experienced unexpected fatal error! Details: "+ err.Message +" "+ err.StackTrace, true);
                }
            }
        }
    }
}
