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
    class Bot {
        static string tmp;

        static List<Response> responseList = new List<Response>();

        public static void loadResponseList() {
            responseList = new List<Response>();
            var r = Query.Reader("SELECT * FROM `responses`", _G.conn);
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
            _G.conn = new MySqlConnection("SERVER="+ _G.serveraddr +";DATABASE="+ _G.dbname +";UID="+ _G.dbuser +";PASSWORD="+ _G.dbpass +";");
            _G.conn.Open();

            _G.loadConfig();

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

            _G.updateHeartbeat();

            _G.startThread(_G.pulseThread);

            Console.WriteLine(_G.propername +" has started successfully.");

            while(true) ;
        }
    }
}
