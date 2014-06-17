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
        public static MySqlConnection conn;

        static void Main(string[] args) {
            conn = new MySqlConnection("SERVER="+ _G.serveraddr +";DATABASE="+ _G.dbname +";UID="+ _G.dbuser +";PASSWORD="+ _G.dbpass +";");
            conn.Open();

            foreach(Type t in ResponseCaller.getResponseTypes()) {
                string[] typeInfo = (string[])t.GetMethod("getInfo").Invoke(null, null);
                try { (new MySqlCommand("UPDATE `resptypes` SET friendlyname='" + typeInfo[1].Replace("'", "\\'") + "',description='" + typeInfo[2].Replace("'", "\\'") + "' WHERE name='" + typeInfo[0] + "'", conn)).ExecuteNonQuery(); } catch(Exception e) { }
                try { (new MySqlCommand("INSERT INTO `resptypes` (name,friendlyname,description) VALUES ('" + typeInfo[0] + "','" + typeInfo[1].Replace("'", "\\'") + "','" + typeInfo[2].Replace("'", "\\'") + "')", conn)).ExecuteNonQuery(); } catch(Exception e) { }
            }


        }
    }
}
