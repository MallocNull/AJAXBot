/*
 * MODIFY THIS FILE TO FIT YOUR SERVER'S CONFIGURATION!
 */

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
    static class _GG {
        const string serveraddr = "ADDR";
        const string dbuser = "NAME";
        const string dbpass = "PWD";
        const string dbname = "DATABASE_NAME";

        static List<Thread> runningThreads = new List<Thread>();

        public static string timezone = "+00:00";
        public static bool observeDST = false;
        public static string username = "";
        public static string propername = "AJAX Bot";

        public static int defaultCooldown = 300;

        public static MySqlConnection conn;

        public static bool isDaylightSavings() {
            return (observeDST) ? TimeZoneInfo.GetSystemTimeZones().First(o => o.DisplayName.ToLower().Contains("central time")).IsDaylightSavingTime(DateTime.UtcNow) : false;
        }

        public static DateTime getLocalTimeFromUTC() {
            return getLocalTimeFromUTC(DateTime.UtcNow);
        }

        public static DateTime getLocalTimeFromUTC(DateTime utcTime) {
            return (new DateTimeOffset(utcTime)).ToOffset(TimeSpan.Parse(timezone).Add(TimeSpan.FromHours(isDaylightSavings() ? 1 : 0))).DateTime;
        }

        public static void updateHeartbeat() {
            string beat = getLocalTimeFromUTC().ToString();
            Query.Quiet("UPDATE `updater` SET `heartbeat`='" + beat + "' WHERE `id`=1", conn);
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
        }

        public static void checkUpdates() {
            var r = Query.Reader("SELECT * FROM `updater` WHERE `id`=1", conn);
            r.Read();
            if(r.GetBoolean("responses"))
                Bot.loadResponseList();
            //if(r.GetBoolean("autonomous")) TODO implement with autonomous

            if(r.GetBoolean("config"))
                loadConfig();
            r.Close();
        }

        public static void pulseThread() {
            DateTime t = new DateTime(0);

            while(true) {
                if((DateTime.Now - t).TotalSeconds > 30) {
                    updateHeartbeat();
                    checkUpdates();
                    t = DateTime.Now;
                    Console.WriteLine("pulsed");
                }
            }
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
