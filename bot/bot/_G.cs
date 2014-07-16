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
    static class _G {
        static string[] dbinfo = new string[4];

        static List<Thread> runningThreads = new List<Thread>();

        public static FirefoxDriver driver;
        /*
         * TODO cross-thread driver protection
         */

        public static string timezone = "+00:00";
        public static bool observeDST = false;
        public static string username = "";
        public static string propername = "AJAX Bot";

        public static int defaultCooldown = 300;

        public static MySqlConnection conn;
        public static MySqlConnection errconn;

        public static bool loadDatabaseInfo() {
            try {
                System.IO.StreamReader r = new System.IO.StreamReader("dbinfo.txt");
                for(int i = 0; i < 4; i++)
                    dbinfo[i] = r.ReadLine();
            } catch(Exception e) {
                criticalError("Error attempting to read from dbinfo.txt: " + e.Message + "\n\nProper format:\nSERVER ADDRESS\nUSERNAME\nPASSWORD\nDATABASE_NAME");
                return false;
            }
            return true;
        }

        public static void criticalError(string err, bool log = false) {
            if(log)
                logError(err);
            Console.WriteLine(err);
            Console.WriteLine("Press any key to quit.");
            Console.ReadKey();
            Environment.FailFast(err);
        }

        public static bool isDaylightSavings() {
            return (observeDST) ? TimeZoneInfo.GetSystemTimeZones().First(o => o.DisplayName.ToLower().Contains("central time")).IsDaylightSavingTime(DateTime.UtcNow) : false;
        }

        public static DateTime getLocalTimeFromUTC() {
            return getLocalTimeFromUTC(DateTime.UtcNow);
        }

        public static DateTime getLocalTimeFromUTC(DateTime utcTime) {
            return (new DateTimeOffset(utcTime)).ToOffset(TimeSpan.Parse(timezone).Add(TimeSpan.FromHours(isDaylightSavings() ? 1 : 0))).DateTime;
        }

        public static void loadConfig() {
            var r = Query.Reader("SELECT * FROM `config` WHERE `id`=1", conn);
            r.Read();
            defaultCooldown = r.GetInt32("cooldown");
            username = r.GetString("username");
            propername = r.GetString("name");
            timezone = r.GetString("timezone");
            observeDST = r.GetBoolean("dst");
            r.Close();
            Bot.loadNavigationList();
        }

        public static MySqlConnection spawnNewConnection() {
            MySqlConnection tmp;
            try {
                tmp = new MySqlConnection("SERVER=" + dbinfo[0] + ";DATABASE=" + dbinfo[3] + ";UID=" + dbinfo[1] + ";PASSWORD=" + dbinfo[2] + ";");
                tmp.Open();
            } catch(Exception e) {
                criticalError("Could not open database connection!");
                return null;
            }
            return tmp;
        }

        public static void logError(string err) {
            Query.Quiet("INSERT INTO `error` (`time`,`msg`) VALUES ('"+ getLocalTimeFromUTC() +" UTC"+ timezone +"','"+err+"')", errconn);
        }

        public static int indexOfNth(string str, char c, int index) {
            int fcount = 0;
            for(int i = 0; i < str.Length; i++) {
                if(str[i] == c) fcount++;
                if(fcount == index) 
                    return i;
            }
            return -1;
        }

        public delegate void threadFunc();
        public static void startThread(threadFunc t) {
            runningThreads.Add((new Thread(new ThreadStart(t))));
            runningThreads.Last().Start();
        }

        public static void stopAllThreads() {
            foreach(Thread t in runningThreads) {
                t.Abort();
                t.Join();
            }
            runningThreads.Clear();
        }
    }
}
