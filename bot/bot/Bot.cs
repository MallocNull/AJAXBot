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
    class Bot : StringSanitizer {
        public static MySqlConnection conn;
        static string tmp;

        static void Main(string[] args) {
            conn = new MySqlConnection("SERVER="+ _G.serveraddr +";DATABASE="+ _G.dbname +";UID="+ _G.dbuser +";PASSWORD="+ _G.dbpass +";");
            conn.Open();

            tmp = "DELETE FROM resptypes WHERE ";
            foreach(Type t in ResponseCaller.getResponseTypes()) {
                string[] typeInfo = (string[])t.GetMethod("getInfo").Invoke(null, null);
                tmp += "name<>'"+ typeInfo[0] +"' AND ";
                Query.Quiet("UPDATE `resptypes` SET friendlyname='" + Sanitize(typeInfo[1]) + "',description='" + Sanitize(typeInfo[2]) + "' WHERE name='" + typeInfo[0] + "'", conn);
                Query.Quiet("INSERT INTO `resptypes` (name,friendlyname,description) VALUES ('" + typeInfo[0] + "','" + Sanitize(typeInfo[1]) + "','" + Sanitize(typeInfo[2]) + "')", conn);
            }

            
            tmp = tmp.Substring(0, tmp.Length - 5);
            Query.Quiet(tmp, conn);

            while(true) ;
        }
    }
}
